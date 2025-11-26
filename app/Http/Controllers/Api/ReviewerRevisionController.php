<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\PaperVersion;
use App\Models\ReviewerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller for Reviewer Revision Tracking
 * 
 * Chức năng:
 * - Reviewer xem lịch sử chỉnh sửa (revisions) của bài báo được assign
 * - Reviewer xác nhận kết quả chỉnh sửa (approve/request changes)
 * - Reviewer so sánh các phiên bản
 */
class ReviewerRevisionController extends Controller
{
    /**
     * Lấy danh sách bài báo được assign kèm trạng thái revision
     * 
     * GET /api/reviewer/papers-with-revisions
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "assignment_id": 1,
     *       "paper_id": 5,
     *       "title": "AI in Healthcare",
     *       "current_version": 2,
     *       "total_versions": 2,
     *       "last_revision_date": "2025-11-14 10:30:00",
     *       "revision_status": "pending_review", // pending_review, approved, needs_changes
     *       "author_name": "Nguyễn Văn A",
     *       "submission_date": "2025-11-01"
     *     }
     *   ]
     * }
     */
    public function getPapersWithRevisions(Request $request)
    {
        $userId = Auth::id();

        $papers = ReviewerAssignment::where('user_id', $userId)
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS', 'COMPLETED'])
            ->with([
                'paper:paper_id,title,submitter_id,submission_date,current_version_id,status_code',
                'paper.submitter:user_id,full_name,email',
                'paper.currentVersion:version_id,version_no,uploaded_at'
            ])
            ->get()
            ->map(function ($assignment) {
                $paper = $assignment->paper;
                
                // Đếm số phiên bản
                $totalVersions = PaperVersion::where('paper_id', $paper->paper_id)->count();
                
                // Kiểm tra reviewer đã review phiên bản hiện tại chưa
                $currentVersionReviewed = DB::table('review')
                    ->where('assignment_id', $assignment->assignment_id)
                    ->where('paper_version_id', $paper->current_version_id)
                    ->exists();
                
                // Xác định trạng thái revision
                $revisionStatus = 'pending_review';
                if ($currentVersionReviewed) {
                    $lastReview = DB::table('review')
                        ->where('assignment_id', $assignment->assignment_id)
                        ->where('paper_version_id', $paper->current_version_id)
                        ->orderByDesc('review_id')
                        ->first();
                    
                    if ($lastReview && $lastReview->decision === 'ACCEPT') {
                        $revisionStatus = 'approved';
                    } elseif ($lastReview && in_array($lastReview->decision, ['MINOR_REVISION', 'MAJOR_REVISION'])) {
                        $revisionStatus = 'needs_changes';
                    }
                }
                
                return [
                    'assignment_id' => $assignment->assignment_id,
                    'paper_id' => $paper->paper_id,
                    'title' => $paper->title,
                    'current_version' => $paper->currentVersion->version_no ?? 1,
                    'total_versions' => $totalVersions,
                    'last_revision_date' => $paper->currentVersion->uploaded_at ?? $paper->submission_date,
                    'revision_status' => $revisionStatus,
                    'author_name' => $paper->submitter->full_name ?? 'N/A',
                    'author_email' => $paper->submitter->email ?? '',
                    'submission_date' => $paper->submission_date,
                    'paper_status' => $paper->status_code
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $papers,
            'total' => $papers->count()
        ]);
    }

    /**
     * Lấy chi tiết lịch sử revision của một bài báo
     * 
     * GET /api/reviewer/papers/{paper_id}/revision-history
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "paper": { ... },
     *     "versions": [
     *       {
     *         "version_id": 1,
     *         "version_no": 1,
     *         "file_path": "papers/v1.pdf",
     *         "uploaded_at": "2025-11-01",
     *         "uploaded_by": "Nguyễn Văn A",
     *         "is_current": false,
     *         "review_status": "reviewed",
     *         "reviewer_decision": "MAJOR_REVISION",
     *         "reviewer_comments": "Cần bổ sung phần literature review"
     *       },
     *       {
     *         "version_id": 2,
     *         "version_no": 2,
     *         "file_path": "papers/v2.pdf",
     *         "uploaded_at": "2025-11-10",
     *         "uploaded_by": "Nguyễn Văn A",
     *         "is_current": true,
     *         "review_status": "pending",
     *         "reviewer_decision": null,
     *         "reviewer_comments": null
     *       }
     *     ]
     *   }
     * }
     */
    public function getRevisionHistory(Request $request, $paperId)
    {
        $userId = Auth::id();

        // Kiểm tra reviewer có được assign bài này không
        $assignment = ReviewerAssignment::where('user_id', $userId)
            ->where('paper_id', $paperId)
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS', 'COMPLETED'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem bài báo này'
            ], 403);
        }

        // Lấy thông tin bài báo
        $paper = BaiBao::with('submitter:user_id,full_name,email')
            ->find($paperId);

        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài báo'
            ], 404);
        }

        // Lấy tất cả phiên bản
        $versions = PaperVersion::where('paper_id', $paperId)
            ->orderBy('version_no', 'asc')
            ->get()
            ->map(function ($version) use ($assignment, $paper) {
                // Tìm review của reviewer này cho version này
                $review = DB::table('review')
                    ->where('assignment_id', $assignment->assignment_id)
                    ->where('paper_version_id', $version->version_id)
                    ->orderByDesc('review_id')
                    ->first();

                return [
                    'version_id' => $version->version_id,
                    'version_no' => $version->version_no,
                    'file_path' => $version->file_path,
                    'file_url' => $version->file_path ? asset('storage/' . $version->file_path) : null,
                    'uploaded_at' => $version->uploaded_at,
                    'is_current' => $version->version_id === $paper->current_version_id,
                    'review_status' => $review ? 'reviewed' : 'pending',
                    'reviewer_decision' => $review->decision ?? null,
                    'reviewer_comments' => $review->comments ?? null,
                    'review_date' => $review->created_at ?? null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'paper' => [
                    'paper_id' => $paper->paper_id,
                    'title' => $paper->title,
                    'author_name' => $paper->submitter->full_name ?? 'N/A',
                    'author_email' => $paper->submitter->email ?? '',
                    'submission_date' => $paper->submission_date,
                    'status' => $paper->status_code,
                    'current_version_id' => $paper->current_version_id
                ],
                'versions' => $versions,
                'total_versions' => $versions->count()
            ]
        ]);
    }

    /**
     * Xác nhận kết quả chỉnh sửa (approve hoặc request more changes)
     * 
     * POST /api/reviewer/papers/{paper_id}/confirm-revision
     * 
     * Request:
     * {
     *   "version_id": 2,
     *   "decision": "APPROVE", // APPROVE, REQUEST_CHANGES
     *   "comments": "Tác giả đã chỉnh sửa tốt, bài báo đã đạt yêu cầu"
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "Đã xác nhận kết quả chỉnh sửa",
     *   "data": {
     *     "review_id": 5,
     *     "decision": "APPROVE",
     *     "created_at": "2025-11-14 15:30:00"
     *   }
     * }
     */
    public function confirmRevision(Request $request, $paperId)
    {
        $validator = Validator::make($request->all(), [
            'version_id' => 'required|integer|exists:paperversion,version_id',
            'decision' => 'required|in:APPROVE,REQUEST_CHANGES',
            'comments' => 'required|string|min:10|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();

        // Kiểm tra assignment
        $assignment = ReviewerAssignment::where('user_id', $userId)
            ->where('paper_id', $paperId)
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS', 'COMPLETED'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền review bài báo này'
            ], 403);
        }

        // Kiểm tra version có thuộc paper không
        $version = PaperVersion::where('version_id', $request->version_id)
            ->where('paper_id', $paperId)
            ->first();

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên bản không tồn tại'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Map decision
            $reviewDecision = $request->decision === 'APPROVE' ? 'ACCEPT' : 'MAJOR_REVISION';

            // Tạo review mới
            $reviewId = DB::table('review')->insertGetId([
                'assignment_id' => $assignment->assignment_id,
                'paper_version_id' => $request->version_id,
                'decision' => $reviewDecision,
                'comments' => $request->comments,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Cập nhật assignment status nếu approve
            if ($request->decision === 'APPROVE') {
                $assignment->update([
                    'status' => 'COMPLETED',
                    'review_submitted_at' => now()
                ]);
            }

            // Cập nhật paper status nếu cần
            $paper = BaiBao::find($paperId);
            if ($request->decision === 'APPROVE') {
                // Kiểm tra xem tất cả reviewer đã approve chưa
                $totalReviewers = ReviewerAssignment::where('paper_id', $paperId)
                    ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS', 'COMPLETED'])
                    ->count();

                $approvedReviews = DB::table('review')
                    ->join('reviewer_assignments', 'review.assignment_id', '=', 'reviewer_assignments.assignment_id')
                    ->where('reviewer_assignments.paper_id', $paperId)
                    ->where('review.paper_version_id', $request->version_id)
                    ->where('review.decision', 'ACCEPT')
                    ->count();

                if ($approvedReviews >= $totalReviewers) {
                    $paper->update(['status_code' => 'ACCEPTED']);
                }
            } else {
                $paper->update(['status_code' => 'NEEDS_REVISION']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->decision === 'APPROVE' 
                    ? 'Đã xác nhận phiên bản chỉnh sửa đạt yêu cầu' 
                    : 'Đã yêu cầu tác giả chỉnh sửa thêm',
                'data' => [
                    'review_id' => $reviewId,
                    'decision' => $reviewDecision,
                    'created_at' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * So sánh 2 phiên bản
     * 
     * GET /api/reviewer/papers/{paper_id}/compare-versions?old_version=1&new_version=2
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "old_version": { ... },
     *     "new_version": { ... },
     *     "changes_summary": "Tác giả đã bổ sung 3 trang, sửa 5 lỗi chính tả"
     *   }
     * }
     */
    public function compareVersions(Request $request, $paperId)
    {
        $validator = Validator::make($request->all(), [
            'old_version' => 'required|integer',
            'new_version' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();

        // Kiểm tra quyền
        $assignment = ReviewerAssignment::where('user_id', $userId)
            ->where('paper_id', $paperId)
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS', 'COMPLETED'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem bài báo này'
            ], 403);
        }

        // Lấy 2 phiên bản
        $oldVersion = PaperVersion::where('paper_id', $paperId)
            ->where('version_no', $request->old_version)
            ->first();

        $newVersion = PaperVersion::where('paper_id', $paperId)
            ->where('version_no', $request->new_version)
            ->first();

        if (!$oldVersion || !$newVersion) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiên bản'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'old_version' => [
                    'version_id' => $oldVersion->version_id,
                    'version_no' => $oldVersion->version_no,
                    'file_path' => $oldVersion->file_path,
                    'file_url' => asset('storage/' . $oldVersion->file_path),
                    'uploaded_at' => $oldVersion->uploaded_at
                ],
                'new_version' => [
                    'version_id' => $newVersion->version_id,
                    'version_no' => $newVersion->version_no,
                    'file_path' => $newVersion->file_path,
                    'file_url' => asset('storage/' . $newVersion->file_path),
                    'uploaded_at' => $newVersion->uploaded_at
                ],
                'changes_summary' => sprintf(
                    'Phiên bản %d được tải lên %s (cách phiên bản %d %d ngày)',
                    $newVersion->version_no,
                    $newVersion->uploaded_at->format('d/m/Y H:i'),
                    $oldVersion->version_no,
                    $newVersion->uploaded_at->diffInDays($oldVersion->uploaded_at)
                )
            ]
        ]);
    }
}
