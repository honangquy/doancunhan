<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;
use App\Models\User;
use App\Models\TacGiaBaiBao;
use App\Services\AutoAssignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    protected $autoAssignService;

    public function __construct(AutoAssignService $autoAssignService)
    {
        $this->autoAssignService = $autoAssignService;
    }

    /**
     * Dashboard phân công phản biện
     * GET /chair/assignments
     */
    public function index(Request $request)
    {
        $conferenceId = $request->input('conference_id');

        // Lấy danh sách conferences mà user là CHAIR
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) {
            $query->where('user_id', auth()->id())
                  ->where('role_code', 'CHAIR');
        })->get();

        if (!$conferenceId && $conferences->isNotEmpty()) {
            $conferenceId = $conferences->first()->conference_id;
        }

        $conference = null;
        $papers = collect();
        $statistics = null;

        if ($conferenceId) {
            $conference = HoiThao::findOrFail($conferenceId);

            // Kiểm tra quyền CHAIR
            /** @var \App\Models\User $user */
            $user = auth()->user();
            if (!$user->hasRoleForConference('CHAIR', $conferenceId)) {
                abort(403, 'Bạn không có quyền CHAIR cho hội thảo này');
            }

            // Get statistics
            $statistics = $this->autoAssignService->getAssignmentStatistics($conferenceId);

            // Query papers với filters
            $papersQuery = BaiBao::where('conference_id', $conferenceId)
                ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                ->with(['tacGias', 'activeAssignments', 'reviewerBiddings']);

            // Filter by assignment status
            if ($request->has('assignment_status')) {
                $status = $request->input('assignment_status');
                $maxReviewers = config('assignment.max_reviewers_per_paper', 3);

                if ($status === 'not_assigned') {
                    $papersQuery->has('activeAssignments', '=', 0);
                } elseif ($status === 'partial') {
                    $papersQuery->has('activeAssignments', '>', 0)
                               ->has('activeAssignments', '<', $maxReviewers);
                } elseif ($status === 'full') {
                    $papersQuery->has('activeAssignments', '>=', $maxReviewers);
                }
            }

            // Filter by minimum bidders
            if ($request->has('min_bidders')) {
                $minBidders = (int) $request->input('min_bidders');
                $papersQuery->has('reviewerBiddings', '>=', $minBidders);
            }

            // Search by title or author
            if ($request->has('search') && $request->input('search')) {
                $search = $request->input('search');
                $papersQuery->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhereHas('tacGias', function ($q2) use ($search) {
                          $q2->where('full_name', 'LIKE', "%{$search}%");
                      });
                });
            }

            $papers = $papersQuery->orderBy('paper_id')->paginate(20);

            // Enhance papers with assignment data
            foreach ($papers as $paper) {
                $paper->assigned_count = $paper->activeAssignments->count();
                $paper->needs_more = $paper->needsMoreReviewers();
                $paper->remaining_slots = $paper->remaining_reviewer_slots;
            }
        }

        return view('chair.assignments.index', compact(
            'conferences',
            'conference',
            'papers',
            'statistics'
        ));
    }

    /**
     * Chi tiết bidding cho một bài báo
     * GET /chair/papers/{id}/bidding
     */
    public function showPaperBidding($paperId)
    {
        $paper = BaiBao::with(['tacGias', 'activeAssignments.reviewer', 'reviewerBiddings.reviewer'])
            ->findOrFail($paperId);

        // Kiểm tra quyền CHAIR
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $paper->conference_id)) {
            abort(403);
        }

        // Lấy danh sách tác giả
        $authorIds = $paper->tacGias->pluck('user_id')->toArray();

        // Lấy danh sách đã được assigned
        $assignedReviewerIds = $paper->activeAssignments->pluck('user_id')->toArray();

        // Lấy bidding data với enhanced information
        $biddings = $paper->reviewerBiddings->map(function ($bidding) use ($authorIds, $assignedReviewerIds) {
            $reviewer = $bidding->reviewer;

            return [
                'reviewer_id' => $reviewer->user_id,
                'reviewer_name' => $reviewer->full_name,
                'reviewer_email' => $reviewer->email,
                'reviewer_affiliation' => $reviewer->affiliation,
                'bidding_value' => $bidding->bidding_value,
                'bidding_label' => $bidding->bidding_label,
                'bidding_color' => $bidding->bidding_color,
                'coi' => $bidding->coi,
                'coi_reason' => $bidding->coi_reason,
                'is_author' => in_array($reviewer->user_id, $authorIds),
                'is_assigned' => in_array($reviewer->user_id, $assignedReviewerIds),
                'current_workload' => $reviewer->reviewer_workload,
                'max_workload' => config('assignment.max_papers_per_reviewer', 5),
                'can_assign' => !in_array($reviewer->user_id, $authorIds)
                             && !$bidding->coi
                             && !in_array($reviewer->user_id, $assignedReviewerIds)
                             && $reviewer->canAcceptMorePapers(),
            ];
        });

        $maxReviewers = config('assignment.max_reviewers_per_paper', 3);
        $currentCount = $paper->activeAssignments->count();

        return response()->json([
            'success' => true,
            'paper' => [
                'paper_id' => $paper->paper_id,
                'title' => $paper->title,
                'authors' => $paper->tacGias->pluck('full_name')->toArray(),
            ],
            'assignment_info' => [
                'max_reviewers' => $maxReviewers,
                'current_count' => $currentCount,
                'remaining' => max(0, $maxReviewers - $currentCount),
            ],
            'biddings' => $biddings,
        ]);
    }

    /**
     * Phân công reviewers cho một bài
     * POST /chair/papers/{id}/assign
     */
    public function assignReviewers(Request $request, $paperId)
    {
        $request->validate([
            'reviewer_ids' => 'required|array|min:1',
            'reviewer_ids.*' => 'required|integer|exists:nguoidung,user_id',
        ]);

        $paper = BaiBao::findOrFail($paperId);

        // Kiểm tra quyền CHAIR
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $paper->conference_id)) {
            abort(403);
        }

        $reviewerIds = $request->input('reviewer_ids');
        $maxReviewers = config('assignment.max_reviewers_per_paper', 3);
        $currentCount = $paper->activeAssignments()->count();

        // Kiểm tra giới hạn
        if ($currentCount >= $maxReviewers) {
            return response()->json([
                'success' => false,
                'message' => 'Bài báo đã đủ số reviewer tối đa'
            ], 400);
        }

        if (count($reviewerIds) + $currentCount > $maxReviewers) {
            return response()->json([
                'success' => false,
                'message' => "Chỉ có thể phân công thêm " . ($maxReviewers - $currentCount) . " reviewer"
            ], 400);
        }

        // Lấy danh sách tác giả
        $authorIds = DB::table('TacGiaBaiBao')
            ->where('paper_id', $paperId)
            ->pluck('user_id')
            ->toArray();

        // Lấy danh sách đã assigned
        $assignedIds = $paper->activeAssignments()->pluck('user_id')->toArray();

        $created = [];
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($reviewerIds as $reviewerId) {
                // Validate từng reviewer
                if (in_array($reviewerId, $authorIds)) {
                    $errors[] = "Reviewer ID {$reviewerId} là tác giả của bài này";
                    continue;
                }

                if (in_array($reviewerId, $assignedIds)) {
                    $errors[] = "Reviewer ID {$reviewerId} đã được phân công rồi";
                    continue;
                }

                // Kiểm tra xem reviewer có phải là CHAIR của hội thảo này không
                $isChair = DB::table('vaitronguoidung')
                    ->where('user_id', $reviewerId)
                    ->where('conference_id', $paper->conference_id)
                    ->where('role_code', 'CHAIR')
                    ->exists();

                if ($isChair) {
                    $errors[] = "Reviewer ID {$reviewerId} là chủ tịch hội thảo, không thể phân công làm reviewer.";
                    continue;
                }

                // Kiểm tra COI
                $bidding = ReviewerBidding::where('paper_id', $paperId)
                    ->where('user_id', $reviewerId)
                    ->first();

                if ($bidding && $bidding->coi) {
                    $errors[] = "Reviewer ID {$reviewerId} có COI với bài này";
                    continue;
                }

                // Kiểm tra workload
                $reviewer = User::find($reviewerId);
                if (!$reviewer->canAcceptMorePapers()) {
                    $errors[] = "Reviewer ID {$reviewerId} đã đạt giới hạn số bài";
                    continue;
                }

                // Tạo assignment
                $assignment = ReviewerAssignment::create([
                    'user_id' => $reviewerId,
                    'paper_id' => $paperId,
                    'conference_id' => $paper->conference_id,
                    'assigned_by' => auth()->id(),
                    'assignment_method' => ReviewerAssignment::METHOD_MANUAL,
                    'status' => ReviewerAssignment::STATUS_PENDING,
                    'assigned_at' => now(),
                    'assignment_metadata' => [
                        'bidding_value' => $bidding->bidding_value ?? null,
                    ]
                ]);

                $created[] = $assignment;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($created) . ' phân công mới đã được tạo',
                'assignments_created' => count($created),
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Assignment failed for paper {$paperId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy phân công một reviewer
     * DELETE /chair/papers/{paperId}/reviewers/{reviewerId}
     */
    public function unassignReviewer($paperId, $reviewerId)
    {
        $paper = BaiBao::findOrFail($paperId);

        // Kiểm tra quyền CHAIR
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $paper->conference_id)) {
            abort(403);
        }

        $assignment = ReviewerAssignment::where('paper_id', $paperId)
            ->where('user_id', $reviewerId)
            ->whereIn('status', [ReviewerAssignment::STATUS_PENDING, ReviewerAssignment::STATUS_ACCEPTED])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phân công'
            ], 404);
        }

        // Không cho phép xóa nếu đã có review
        if ($assignment->status === ReviewerAssignment::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy phân công đã hoàn thành'
            ], 400);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy phân công reviewer'
        ]);
    }

    /**
     * Tự động phân công cho toàn bộ hội thảo
     * POST /chair/conferences/{id}/auto-assign
     */
    public function autoAssignConference(Request $request, $conferenceId)
    {
        $conference = HoiThao::findOrFail($conferenceId);

        // Kiểm tra quyền CHAIR
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $conferenceId)) {
            abort(403);
        }

        $paperIds = $request->input('paper_ids'); // Optional: specific papers

        $result = $this->autoAssignService->autoAssignForConference(
            $conferenceId,
            $paperIds,
            auth()->id()
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => "Đã tự động phân công thành công",
                'statistics' => $result
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình phân công',
                'errors' => $result['errors']
            ], 500);
        }
    }

    /**
     * Lấy thống kê phân công
     * GET /chair/conferences/{id}/assignment-stats
     */
    public function getAssignmentStats($conferenceId)
    {
        // Kiểm tra quyền CHAIR
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $conferenceId)) {
            abort(403);
        }

        $stats = $this->autoAssignService->getAssignmentStatistics($conferenceId);

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
}
