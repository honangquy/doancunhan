<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;
use App\Models\AssignmentNotification;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\VaiTroNguoiDung;
use App\Models\NguoiDung;
use App\Mail\ReviewerAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail as MailFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReviewerAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:CHAIR']);
    }

    /**
     * Display assignment management interface
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get conferences managed by this chair
            $conferences = DB::table('hoithao as h')
                ->join('vaitronguoidung as vr', 'h.conference_id', '=', 'vr.conference_id')
                ->where('vr.user_id', $user->user_id)
                ->where('vr.role_code', 'CHAIR')
                ->where('h.status', 'ACTIVE')
                ->select('h.*')
                ->get();

            // Debug: Log conferences found
            Log::info('Chair conferences found:', [
                'user_id' => $user->user_id,
                'conferences_count' => $conferences->count(),
                'conferences' => $conferences->toArray()
            ]);

            $selectedConference = $request->get('conference_id', $conferences->first()->conference_id ?? null);
            
            // Debug: Log selected conference
            Log::info('Selected conference:', [
                'selected' => $selectedConference,
                'request_param' => $request->get('conference_id')
            ]);

            return view('chair.assignments.index', compact('conferences', 'selectedConference'));
            
        } catch (\Exception $e) {
            Log::error('Error in ReviewerAssignmentController@index: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu.');
        }
    }

    /**
     * Get conference papers with bidding statistics
     */
    public function getConferencePapers($conferenceId)
    {
        try {
            $userId = Auth::id();

            // Verify chair access
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập hội thảo này'
                ], 403);
            }

            // Debug logging
            Log::info('Loading papers for conference:', ['conference_id' => $conferenceId]);

            // Get papers with bidding statistics
            $papers = DB::table('baibao as b')
                ->leftJoin('nguoidung as submitter', 'b.submitter_id', '=', 'submitter.user_id')
                ->leftJoin('reviewer_bidding as rb', 'b.paper_id', '=', 'rb.paper_id')
                ->leftJoin('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
                ->where('b.conference_id', $conferenceId)
                ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW']) // Show all submitted papers
                ->select(
                    'b.paper_id',
                    'b.conference_id',
                    'b.track_id',
                    'b.submitter_id',
                    'b.title',
                    'b.abstract',
                    'b.keywords',
                    'b.file_path',
                    'b.current_version_id',
                    'b.status_code',
                    'b.created_at',
                    'submitter.full_name as submitted_by_name',
                    DB::raw('COUNT(DISTINCT rb.user_id) as total_bidders'),
                    DB::raw('AVG(rb.bidding_value) as avg_bid'),
                    DB::raw('MAX(rb.bidding_value) as max_bid'),
                    DB::raw('COUNT(DISTINCT CASE WHEN rb.coi = true THEN rb.user_id END) as coi_count'),
                    DB::raw('COUNT(DISTINCT ra.user_id) as assigned_reviewers')
                )
                ->groupBy(
                    'b.paper_id',
                    'b.conference_id',
                    'b.track_id',
                    'b.submitter_id',
                    'b.title',
                    'b.abstract',
                    'b.keywords',
                    'b.file_path',
                    'b.current_version_id',
                    'b.status_code',
                    'b.created_at',
                    'submitter.full_name'
                )
                ->orderBy('b.title')
                ->get();
                
            Log::info('Papers loaded successfully:', ['count' => $papers->count()]);

            return response()->json([
                'success' => true,
                'papers' => $papers
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting conference papers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách bài báo'
            ], 500);
        }
    }

    /**
     * Get bidding details for a specific paper
     */
    public function getPaperBiddings($paperId)
    {
        try {
            $userId = Auth::id();

            // Verify access
            $paper = BaiBao::find($paperId);
            if (!$paper) {
                return response()->json(['success' => false, 'message' => 'Bài báo không tồn tại'], 404);
            }

            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $paper->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
            }

            // Get bidding details
            $biddings = DB::table('reviewer_bidding as rb')
                ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
                ->leftJoin('reviewer_assignments as ra', function($join) use ($paperId) {
                    $join->on('rb.user_id', '=', 'ra.user_id')
                         ->where('ra.paper_id', '=', $paperId);
                })
                ->where('rb.paper_id', $paperId)
                ->select(
                    'rb.*',
                    'n.full_name',
                    'n.email',
                    'ra.id as assignment_id',
                    'ra.status as assignment_status',
                    DB::raw('CASE WHEN ra.id IS NOT NULL THEN true ELSE false END as is_assigned')
                )
                ->orderBy('rb.bidding_value', 'desc')
                ->orderBy('rb.coi')
                ->get();

            return response()->json([
                'success' => true,
                'biddings' => $biddings,
                'paper' => $paper
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting paper biddings: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Manual assignment of reviewers
     */
    public function manualAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paper_id' => 'required|exists:baibao,paper_id',
            'reviewer_ids' => 'required|array|min:1',
            'reviewer_ids.*' => 'required|exists:nguoidung,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $paperId = $request->input('paper_id');
            $reviewerIds = $request->input('reviewer_ids');

            // Verify access
            $paper = BaiBao::find($paperId);
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $paper->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }

            DB::beginTransaction();

            foreach ($reviewerIds as $reviewerId) {
                // Check for COI
                $hasCOI = ReviewerBidding::where('user_id', $reviewerId)
                    ->where('paper_id', $paperId)
                    ->where('coi', true)
                    ->exists();

                if ($hasCOI) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể phân công reviewer có COI'
                    ], 400);
                }

                // Check if already assigned
                $existingAssignment = ReviewerAssignment::where('user_id', $reviewerId)
                    ->where('paper_id', $paperId)
                    ->exists();

                if ($existingAssignment) {
                    continue; // Skip if already assigned
                }

                // Get bidding info for metadata
                $bidding = ReviewerBidding::where('user_id', $reviewerId)
                    ->where('paper_id', $paperId)
                    ->first();

                // Create assignment
                $assignment = ReviewerAssignment::create([
                    'user_id' => $reviewerId,
                    'paper_id' => $paperId,
                    'conference_id' => $paper->conference_id,
                    'assigned_by' => $userId,
                    'assignment_method' => 'MANUAL',
                    'status' => 'PENDING',
                    'assigned_at' => now(),
                    'assignment_metadata' => [
                        'bid_value' => $bidding->bidding_value ?? 0,
                        'coi_status' => $bidding->coi ?? false,
                        'assigned_timestamp' => now()->toISOString()
                    ]
                ]);

                // Send email notification
                $reviewer = NguoiDung::find($reviewerId);
                if ($reviewer && $reviewer->email) {
                    try {
                        MailFacade::to($reviewer->email)->send(new ReviewerAssigned($assignment));
                    } catch (\Exception $e) {
                        \Log::warning('Failed to send assignment email to reviewer: ' . $e->getMessage());
                    }
                }

                // Create notification
                AssignmentNotification::create([
                    'assignment_id' => $assignment->id,
                    'notification_type' => 'ASSIGNMENT',
                    'status' => 'PENDING'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã phân công thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in manual assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Auto-assignment algorithm
     */
    public function autoAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paper_id' => 'required|exists:baibao,paper_id',
            'reviewer_count' => 'required|integer|min:1|max:5',
            'min_bid' => 'required|integer|min:0|max:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $paperId = $request->input('paper_id');
            $reviewerCount = $request->input('reviewer_count');
            $minBid = $request->input('min_bid');

            // Verify access
            $paper = BaiBao::find($paperId);
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $paper->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }

            // Get available reviewers with workload balancing
            $availableReviewers = DB::table('reviewer_bidding as rb')
                ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
                ->leftJoin('reviewer_assignments as existing_ra', function($join) use ($paperId) {
                    $join->on('rb.user_id', '=', 'existing_ra.user_id')
                         ->where('existing_ra.paper_id', '=', $paperId);
                })
                ->leftJoin(
                    DB::raw('(SELECT user_id, COUNT(*) as current_workload 
                             FROM reviewer_assignments 
                             WHERE conference_id = ' . $paper->conference_id . ' 
                             GROUP BY user_id) as workload'), 
                    'rb.user_id', '=', 'workload.user_id'
                )
                ->where('rb.paper_id', $paperId)
                ->where('rb.coi', false)
                ->where('rb.bidding_value', '>=', $minBid)
                ->whereNull('existing_ra.id') // Not already assigned to this paper
                ->select(
                    'rb.user_id', 
                    'rb.bidding_value', 
                    'n.full_name',
                    DB::raw('COALESCE(workload.current_workload, 0) as current_workload')
                )
                // Advanced scoring: bid_value * 100 - current_workload * 10 (prioritize high bids but balance workload)
                ->orderByRaw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) DESC')
                ->limit($reviewerCount)
                ->get();

            if ($availableReviewers->count() < $reviewerCount) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ reviewer phù hợp. Cần {$reviewerCount}, chỉ tìm được {$availableReviewers->count()}",
                    'available_count' => $availableReviewers->count(),
                    'available_reviewers' => $availableReviewers->map(function($r) {
                        return [
                            'name' => $r->full_name,
                            'bid' => $r->bidding_value,
                            'current_workload' => $r->current_workload
                        ];
                    })
                ], 400);
            }

            DB::beginTransaction();

            foreach ($availableReviewers as $reviewer) {
                $assignment = ReviewerAssignment::create([
                    'user_id' => $reviewer->user_id,
                    'paper_id' => $paperId,
                    'conference_id' => $paper->conference_id,
                    'assigned_by' => $userId,
                    'assignment_method' => 'AUTO',
                    'status' => 'PENDING',
                    'assigned_at' => now(),
                    'assignment_metadata' => [
                        'bid_value' => $reviewer->bidding_value,
                        'coi_status' => false,
                        'assigned_timestamp' => now()->toISOString(),
                        'auto_assignment_criteria' => [
                            'min_bid' => $minBid,
                            'reviewer_count' => $reviewerCount
                        ]
                    ]
                ]);

                // Send email notification
                $reviewerUser = NguoiDung::find($reviewer->user_id);
                if ($reviewerUser && $reviewerUser->email) {
                    try {
                        MailFacade::to($reviewerUser->email)->send(new ReviewerAssigned($assignment));
                    } catch (\Exception $e) {
                        \Log::warning('Failed to send auto-assignment email to reviewer: ' . $e->getMessage());
                    }
                }

                // Create notification
                AssignmentNotification::create([
                    'assignment_id' => $assignment->id,
                    'notification_type' => 'ASSIGNMENT',
                    'status' => 'PENDING'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã tự động phân công {$availableReviewers->count()} reviewer!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in auto assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Remove assignment
     */
    public function removeAssignment($assignmentId)
    {
        try {
            $userId = Auth::id();
            
            $assignment = ReviewerAssignment::find($assignmentId);
            if (!$assignment) {
                return response()->json(['success' => false, 'message' => 'Phân công không tồn tại'], 404);
            }

            // Verify access
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $assignment->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa phân công thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Get assignment statistics for conference
     */
    public function getAssignmentStatistics($conferenceId)
    {
        try {
            $userId = Auth::id();

            // Verify access
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasAccess) {
                Log::warning('Chair access denied for statistics', [
                    'user_id' => $userId,
                    'conference_id' => $conferenceId
                ]);
                return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
            }

            Log::info('Getting statistics for conference', [
                'user_id' => $userId,
                'conference_id' => $conferenceId
            ]);

            $stats = [
                'total_papers' => BaiBao::where('conference_id', $conferenceId)
                    ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])->count(),
                'papers_with_assignments' => DB::table('baibao as b')
                    ->join('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
                    ->where('b.conference_id', $conferenceId)
                    ->distinct('b.paper_id')
                    ->count(),
                'total_assignments' => ReviewerAssignment::where('conference_id', $conferenceId)->count(),
                'assignment_status_breakdown' => ReviewerAssignment::where('conference_id', $conferenceId)
                    ->groupBy('status')
                    ->selectRaw('status, count(*) as count')
                    ->get()
                    ->pluck('count', 'status'),
                'total_bidders' => ReviewerBidding::where('conference_id', $conferenceId)
                    ->distinct('user_id')
                    ->count(),
                'coi_declarations' => ReviewerBidding::where('conference_id', $conferenceId)
                    ->where('coi', true)
                    ->count()
            ];

            Log::info('Statistics calculated', [
                'conference_id' => $conferenceId,
                'stats' => $stats
            ]);

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting assignment statistics: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }
}