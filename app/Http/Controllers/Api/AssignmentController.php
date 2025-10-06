<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Models\PhanCongPhanBien;
use App\Models\Models\BaiBao;
use App\Models\Models\TieuBan;
use App\Models\Models\Bidding;
use App\Models\Models\COI;
use App\Models\NguoiDung;

class AssignmentController extends Controller
{
    /**
     * Manual assignment (Chair/Admin assigns reviewer to paper)
     * POST /api/assignments
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'paper_id' => 'required|integer|exists:BaiBao,paper_id',
            'reviewer_id' => 'required|integer|exists:NguoiDung,user_id',
            'deadline' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if paper exists
        $paper = BaiBao::with('tieuBan')->find($request->paper_id);
        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Paper not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            if (!$paper->tieuBan || !$this->isTrackChair($user, $paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can assign reviewers.'
                ], 403);
            }
        }

        // Check if reviewer exists and has reviewer role
        $reviewer = NguoiDung::find($request->reviewer_id);
        if (!$reviewer || !$this->isReviewer($reviewer)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reviewer or user does not have reviewer role'
            ], 422);
        }

        // Check for COI
        $coi = COI::where('paper_id', $request->paper_id)
            ->where('reviewer_id', $request->reviewer_id)
            ->first();

        if ($coi) {
            // Check if COI is confirmed
            $decision = DB::table('XuLyCOI')
                ->where('coi_id', $coi->coi_id)
                ->where('decision', 'CONFIRMED')
                ->first();

            if ($decision) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign: Confirmed conflict of interest exists'
                ], 403);
            }
        }

        // Check if reviewer is an author
        $isAuthor = DB::table('TacGiaBaiBao')
            ->where('paper_id', $request->paper_id)
            ->where('user_id', $request->reviewer_id)
            ->exists();

        if ($isAuthor) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign: Reviewer is an author of this paper'
            ], 403);
        }

        // Check if already assigned
        $existingAssignment = PhanCongPhanBien::where('paper_id', $request->paper_id)
            ->where('reviewer_id', $request->reviewer_id)
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Reviewer already assigned to this paper'
            ], 409);
        }

        // Create assignment
        $assignment = PhanCongPhanBien::create([
            'paper_id' => $request->paper_id,
            'reviewer_id' => $request->reviewer_id,
            'assigned_by' => $user->user_id,
            'assigned_at' => now(),
            'deadline' => $request->deadline,
            'status_code' => 'INVITED',
            'token' => Str::uuid(),
        ]);

        $assignment->load(['paper', 'reviewer', 'assignedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Reviewer assigned successfully',
            'data' => [
                'assignment_id' => $assignment->assignment_id,
                'paper_id' => $assignment->paper_id,
                'paper_title' => $assignment->paper->title ?? 'N/A',
                'reviewer_id' => $assignment->reviewer_id,
                'reviewer_name' => $assignment->reviewer->full_name ?? 'N/A',
                'reviewer_email' => $assignment->reviewer->email ?? 'N/A',
                'assigned_by' => $assignment->assignedBy->full_name ?? 'N/A',
                'assigned_at' => $assignment->assigned_at,
                'deadline' => $assignment->deadline,
                'status' => $assignment->status_code,
            ]
        ], 201);
    }

    /**
     * Auto-assign reviewers to papers (Smart algorithm)
     * POST /api/assignments/auto-assign
     */
    public function autoAssign(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can trigger auto-assignment'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'paper_id' => 'nullable|integer|exists:BaiBao,paper_id',
            'conference_id' => 'nullable|integer|exists:HoiThao,conference_id',
            'reviewers_per_paper' => 'nullable|integer|min:1|max:10',
            'deadline' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $reviewersPerPaper = $request->get('reviewers_per_paper', 3);
        $deadline = $request->deadline;
        $assignments = [];
        $errors = [];

        // Get papers to assign
        $papersQuery = BaiBao::with('tieuBan');

        if ($request->has('paper_id')) {
            $papersQuery->where('paper_id', $request->paper_id);
        } elseif ($request->has('conference_id')) {
            $papersQuery->whereHas('tieuBan', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        $papers = $papersQuery->get();

        foreach ($papers as $paper) {
            // Get current assignments for this paper
            $currentAssignments = PhanCongPhanBien::where('paper_id', $paper->paper_id)->count();
            $neededReviewers = $reviewersPerPaper - $currentAssignments;

            if ($neededReviewers <= 0) {
                continue; // Already has enough reviewers
            }

            // Get paper authors (to exclude)
            $authorIds = DB::table('TacGiaBaiBao')
                ->where('paper_id', $paper->paper_id)
                ->pluck('user_id')
                ->toArray();

            // Get reviewers with COI (to exclude)
            $coiReviewerIds = COI::where('paper_id', $paper->paper_id)
                ->whereHas('decision', function($q) {
                    $q->where('decision', 'CONFIRMED');
                })
                ->pluck('reviewer_id')
                ->toArray();

            // Get already assigned reviewers (to exclude)
            $assignedReviewerIds = PhanCongPhanBien::where('paper_id', $paper->paper_id)
                ->pluck('reviewer_id')
                ->toArray();

            // Combine exclusions
            $excludeIds = array_unique(array_merge($authorIds, $coiReviewerIds, $assignedReviewerIds));

            // Get biddings for this paper with scores
            $biddings = Bidding::where('paper_id', $paper->paper_id)
                ->whereNotIn('user_id', $excludeIds)
                ->with('biddingValue')
                ->get();

            // Score biddings (EAGER=4, WILLING=3, NEUTRAL=2, UNWILLING=1)
            $scoredReviewers = $biddings->map(function($bid) {
                $scores = [
                    'EAGER' => 4,
                    'WILLING' => 3,
                    'NEUTRAL' => 2,
                    'UNWILLING' => 1,
                    'CONFLICT' => 0,
                ];
                return [
                    'reviewer_id' => $bid->user_id,
                    'score' => $scores[$bid->bidding_code] ?? 0,
                    'bidding_code' => $bid->bidding_code,
                ];
            })->filter(function($item) {
                return $item['score'] > 1; // Exclude UNWILLING and CONFLICT
            })->sortByDesc('score')->values();

            // Get reviewer workload (assignments count)
            $reviewerWorkloads = DB::table('PhanCongPhanBien')
                ->select('reviewer_id', DB::raw('COUNT(*) as workload'))
                ->groupBy('reviewer_id')
                ->pluck('workload', 'reviewer_id')
                ->toArray();

            // Adjust scores based on workload (prefer reviewers with less work)
            $scoredReviewers = $scoredReviewers->map(function($item) use ($reviewerWorkloads) {
                $workload = $reviewerWorkloads[$item['reviewer_id']] ?? 0;
                $item['workload'] = $workload;
                $item['adjusted_score'] = $item['score'] - ($workload * 0.5); // Reduce score by workload
                return $item;
            })->sortByDesc('adjusted_score')->values();

            // If not enough bidders, get all reviewers
            if ($scoredReviewers->count() < $neededReviewers) {
                // Get all reviewers not in exclude list
                $allReviewers = DB::table('VaiTroNguoiDung')
                    ->where('role_code', 'REVIEWER')
                    ->whereNotIn('user_id', $excludeIds)
                    ->pluck('user_id')
                    ->toArray();

                foreach ($allReviewers as $reviewerId) {
                    if (!$scoredReviewers->contains('reviewer_id', $reviewerId)) {
                        $workload = $reviewerWorkloads[$reviewerId] ?? 0;
                        $scoredReviewers->push([
                            'reviewer_id' => $reviewerId,
                            'score' => 1, // Neutral score
                            'bidding_code' => 'AUTO',
                            'workload' => $workload,
                            'adjusted_score' => 1 - ($workload * 0.5),
                        ]);
                    }
                }

                // Re-sort after adding new reviewers
                $scoredReviewers = $scoredReviewers->sortByDesc('adjusted_score')->values();
            }

            // Assign top N reviewers
            $assignedCount = 0;
            foreach ($scoredReviewers->take($neededReviewers) as $reviewer) {
                try {
                    $assignment = PhanCongPhanBien::create([
                        'paper_id' => $paper->paper_id,
                        'reviewer_id' => $reviewer['reviewer_id'],
                        'assigned_by' => $user->user_id,
                        'assigned_at' => now(),
                        'deadline' => $deadline,
                        'status_code' => 'INVITED',
                        'token' => Str::uuid(),
                    ]);

                    $assignments[] = [
                        'assignment_id' => $assignment->assignment_id,
                        'paper_id' => $paper->paper_id,
                        'paper_title' => $paper->title,
                        'reviewer_id' => $reviewer['reviewer_id'],
                        'bidding_code' => $reviewer['bidding_code'],
                        'score' => $reviewer['adjusted_score'],
                    ];

                    $assignedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'paper_id' => $paper->paper_id,
                        'reviewer_id' => $reviewer['reviewer_id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            if ($assignedCount < $neededReviewers) {
                $errors[] = [
                    'paper_id' => $paper->paper_id,
                    'message' => "Only assigned {$assignedCount} of {$neededReviewers} needed reviewers",
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto-assignment completed',
            'data' => [
                'total_assignments' => count($assignments),
                'assignments' => $assignments,
                'errors' => $errors,
            ]
        ]);
    }

    /**
     * Unassign reviewer from paper
     * DELETE /api/assignments/{assignment_id}
     */
    public function destroy($assignment_id)
    {
        $user = auth()->user();

        $assignment = PhanCongPhanBien::with('paper.tieuBan')->find($assignment_id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            if (!$assignment->paper->tieuBan || !$this->isTrackChair($user, $assignment->paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can unassign reviewers.'
                ], 403);
            }
        }

        // Check if review already submitted
        $reviewExists = DB::table('PhanBien')
            ->where('assignment_id', $assignment_id)
            ->exists();

        if ($reviewExists) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unassign: Review already submitted'
            ], 403);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reviewer unassigned successfully'
        ]);
    }

    /**
     * Get assignments for a paper (Admin/Chair only)
     * GET /api/papers/{paper_id}/assignments
     */
    public function paperAssignments($paper_id)
    {
        $user = auth()->user();

        // Check if paper exists
        $paper = BaiBao::with('tieuBan')->find($paper_id);
        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Paper not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            if (!$paper->tieuBan || !$this->isTrackChair($user, $paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can view assignments.'
                ], 403);
            }
        }

        // Get all assignments for this paper
        $assignments = PhanCongPhanBien::where('paper_id', $paper_id)
            ->with(['reviewer', 'assignedBy', 'reviews'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        $data = $assignments->map(function($assignment) {
            return [
                'assignment_id' => $assignment->assignment_id,
                'reviewer_id' => $assignment->reviewer_id,
                'reviewer_name' => $assignment->reviewer->full_name ?? 'N/A',
                'reviewer_email' => $assignment->reviewer->email ?? 'N/A',
                'assigned_by' => $assignment->assignedBy->full_name ?? 'N/A',
                'assigned_at' => $assignment->assigned_at,
                'deadline' => $assignment->deadline,
                'status' => $assignment->status_code,
                'review_submitted' => $assignment->reviews->isNotEmpty(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Assignments retrieved successfully',
            'data' => $data
        ]);
    }

    /**
     * Get current reviewer's assignments
     * GET /api/my-assignments
     */
    public function myAssignments(Request $request)
    {
        $user = auth()->user();

        // Check if user is a reviewer
        if (!$this->isReviewer($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only reviewers can access this endpoint'
            ], 403);
        }

        // Build query
        $query = PhanCongPhanBien::where('reviewer_id', $user->user_id)
            ->with([
                'paper.tieuBan.hoiThao',
                'assignedBy',
                'reviews'
            ])
            ->orderBy('assigned_at', 'desc');

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status_code', $request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $assignments = $query->paginate($perPage);

        $data = $assignments->map(function($assignment) {
            return [
                'assignment_id' => $assignment->assignment_id,
                'paper_id' => $assignment->paper_id,
                'paper_title' => $assignment->paper->title ?? 'N/A',
                'track_name' => $assignment->paper->tieuBan->name ?? 'N/A',
                'conference_name' => $assignment->paper->tieuBan->hoiThao->title ?? 'N/A',
                'assigned_by' => $assignment->assignedBy->full_name ?? 'N/A',
                'assigned_at' => $assignment->assigned_at,
                'deadline' => $assignment->deadline,
                'status' => $assignment->status_code,
                'review_submitted' => $assignment->reviews->isNotEmpty(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Your assignments retrieved successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $assignments->currentPage(),
                'per_page' => $assignments->perPage(),
                'total' => $assignments->total(),
                'last_page' => $assignments->lastPage(),
            ]
        ]);
    }

    /**
     * Accept or reject assignment
     * PUT /api/assignments/{assignment_id}/accept
     */
    public function acceptAssignment(Request $request, $assignment_id)
    {
        $user = auth()->user();

        $assignment = PhanCongPhanBien::find($assignment_id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        // Check if user is the assigned reviewer
        if ($assignment->reviewer_id != $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only accept/reject your own assignments'
            ], 403);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'accept' => 'required|boolean',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update status
        if ($request->accept) {
            $assignment->status_code = 'ACCEPTED';
            $message = 'Assignment accepted successfully';
        } else {
            $assignment->status_code = 'DECLINED';
            $message = 'Assignment declined';
        }

        $assignment->save();

        $assignment->load(['paper', 'assignedBy']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'assignment_id' => $assignment->assignment_id,
                'paper_title' => $assignment->paper->title ?? 'N/A',
                'status' => $assignment->status_code,
            ]
        ]);
    }

    /**
     * Get assignment statistics (Admin only)
     * GET /api/assignment/statistics
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access assignment statistics'
            ], 403);
        }

        // Build query
        $query = PhanCongPhanBien::query();

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by track
        if ($request->has('track_id')) {
            $query->whereHas('paper.tieuBan', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }

        // Total assignments
        $totalAssignments = $query->count();

        // Assignments by status
        $byStatus = DB::table('PhanCongPhanBien as pcpb')
            ->select('status_code', DB::raw('COUNT(*) as count'))
            ->groupBy('status_code');

        if ($request->has('conference_id')) {
            $byStatus->join('BaiBao as bb', 'pcpb.paper_id', '=', 'bb.paper_id')
                ->join('TieuBan as tb', 'bb.track_id', '=', 'tb.track_id')
                ->where('tb.conference_id', $request->conference_id);
        }

        $byStatus = $byStatus->get();

        // Papers with assignments
        $papersWithAssignments = PhanCongPhanBien::query();
        if ($request->has('conference_id')) {
            $papersWithAssignments->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }
        $papersWithAssignments = $papersWithAssignments->distinct('paper_id')->count('paper_id');

        // Average assignments per paper
        $avgAssignmentsPerPaper = $papersWithAssignments > 0 
            ? round($totalAssignments / $papersWithAssignments, 2) 
            : 0;

        // Reviewers with assignments
        $reviewersWithAssignments = PhanCongPhanBien::query();
        if ($request->has('conference_id')) {
            $reviewersWithAssignments->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }
        $reviewersWithAssignments = $reviewersWithAssignments->distinct('reviewer_id')->count('reviewer_id');

        // Average assignments per reviewer
        $avgAssignmentsPerReviewer = $reviewersWithAssignments > 0 
            ? round($totalAssignments / $reviewersWithAssignments, 2) 
            : 0;

        // Assignments with reviews
        $assignmentsWithReviews = DB::table('PhanCongPhanBien as pcpb')
            ->join('PhanBien as pb', 'pcpb.assignment_id', '=', 'pb.assignment_id');

        if ($request->has('conference_id')) {
            $assignmentsWithReviews->join('BaiBao as bb', 'pcpb.paper_id', '=', 'bb.paper_id')
                ->join('TieuBan as tb', 'bb.track_id', '=', 'tb.track_id')
                ->where('tb.conference_id', $request->conference_id);
        }

        $assignmentsWithReviews = $assignmentsWithReviews->count();

        // Review completion rate
        $reviewCompletionRate = $totalAssignments > 0 
            ? round(($assignmentsWithReviews / $totalAssignments) * 100, 2) 
            : 0;

        return response()->json([
            'success' => true,
            'message' => 'Assignment statistics retrieved successfully',
            'data' => [
                'total_assignments' => $totalAssignments,
                'by_status' => $byStatus,
                'papers_with_assignments' => $papersWithAssignments,
                'average_assignments_per_paper' => $avgAssignmentsPerPaper,
                'reviewers_with_assignments' => $reviewersWithAssignments,
                'average_assignments_per_reviewer' => $avgAssignmentsPerReviewer,
                'assignments_with_reviews' => $assignmentsWithReviews,
                'review_completion_rate' => $reviewCompletionRate . '%',
            ]
        ]);
    }

    // Helper methods
    private function isAdmin($user)
    {
        return DB::table('VaiTroNguoiDung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();
    }

    private function isReviewer($user)
    {
        return DB::table('VaiTroNguoiDung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'REVIEWER')
            ->exists();
    }

    private function isTrackChair($user, $trackId)
    {
        $track = TieuBan::find($trackId);
        return $track && $track->chair_id == $user->user_id;
    }
}
