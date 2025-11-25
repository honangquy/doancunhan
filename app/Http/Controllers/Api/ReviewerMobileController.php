<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ReviewerAssignment;
use App\Models\BaiBao;

class ReviewerMobileController extends Controller
{
    /**
     * Get all assignments for logged-in reviewer
     * GET /api/mobile/reviewer/assignments
     */
    public function getAssignments(Request $request)
    {
        $userId = Auth::id();

        $query = ReviewerAssignment::join('baibao as b', 'reviewer_assignments.paper_id', '=', 'b.paper_id')
            ->leftJoin('hoithao as ht', 'b.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as assigner', 'reviewer_assignments.assigned_by', '=', 'assigner.user_id')
            ->leftJoin('tacgiabaibao as tg', function($join) {
                $join->on('b.paper_id', '=', 'tg.paper_id')
                     ->where('tg.is_contact', '=', 1);
            })
            ->leftJoin('nguoidung as author', 'tg.user_id', '=', 'author.user_id')
            ->where('reviewer_assignments.user_id', $userId)
            ->select(
                'reviewer_assignments.id',
                'reviewer_assignments.paper_id',
                'reviewer_assignments.status',
                'reviewer_assignments.assigned_at',
                'reviewer_assignments.responded_at',

                'b.title as paper_title',
                'b.abstract as paper_abstract',
                'b.keywords',
                'b.file_path',
                'b.status_code as paper_status',
                'ht.conference_id',
                'ht.title as conference_name',
                'assigner.full_name as assigned_by_name',
                'author.full_name as author_name',
                'author.email as author_email'
            );

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('reviewer_assignments.status', $request->status);
        }

        $assignments = $query->orderBy('reviewer_assignments.assigned_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total' => ReviewerAssignment::where('user_id', $userId)->count(),
            'pending' => ReviewerAssignment::where('user_id', $userId)->where('status', 'PENDING')->count(),
            'accepted' => ReviewerAssignment::where('user_id', $userId)->where('status', 'ACCEPTED')->count(),
            'completed' => ReviewerAssignment::where('user_id', $userId)->where('status', 'COMPLETED')->count(),
            'declined' => ReviewerAssignment::where('user_id', $userId)->where('status', 'DECLINED')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $assignments,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get assignment details
     * GET /api/mobile/reviewer/assignments/{id}
     */
    public function getAssignmentDetail($assignmentId)
    {
        $userId = Auth::id();

        $assignment = ReviewerAssignment::join('baibao as b', 'reviewer_assignments.paper_id', '=', 'b.paper_id')
            ->leftJoin('hoithao as ht', 'b.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as assigner', 'reviewer_assignments.assigned_by', '=', 'assigner.user_id')
            ->leftJoin('tacgiabaibao as tg', function($join) {
                $join->on('b.paper_id', '=', 'tg.paper_id')
                     ->where('tg.is_contact', '=', 1);
            })
            ->leftJoin('nguoidung as author', 'tg.user_id', '=', 'author.user_id')
            ->where('reviewer_assignments.id', $assignmentId)
            ->where('reviewer_assignments.user_id', $userId)
            ->select(
                'reviewer_assignments.*',
                'b.paper_id',
                'b.title as paper_title',
                'b.abstract as paper_abstract',
                'b.keywords',
                'b.file_path as paper_file',
                'b.status_code as paper_status',
                'ht.conference_id',
                'ht.title as conference_name',
                'assigner.full_name as assigned_by_name',
                'author.full_name as author_name',
                'author.email as author_email',
                'author.organization as author_organization'
            )
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or you do not have permission'
            ], 404);
        }

        // Get all paper versions
        $versions = DB::table('phienbanbaibao')
            ->where('paper_id', $assignment->paper_id)
            ->orderBy('version_no', 'desc')
            ->get();

        // Get all authors
        $authors = DB::table('tacgiabaibao as ta')
            ->join('nguoidung as nd', 'ta.user_id', '=', 'nd.user_id')
            ->where('ta.paper_id', $assignment->paper_id)
            ->select('ta.author_order', 'ta.is_contact', 'ta.organization', 'nd.full_name', 'nd.email')
            ->orderBy('ta.author_order')
            ->get();

        // Check if review exists
        $existingReview = DB::table('phanbien')
            ->where('assignment_id', $assignmentId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => $assignment,
                'versions' => $versions,
                'authors' => $authors,
                'existing_review' => $existingReview
            ]
        ]);
    }

    /**
     * Accept assignment
     * POST /api/mobile/reviewer/assignments/{id}/accept
     */
    public function acceptAssignment($assignmentId)
    {
        $userId = Auth::id();

        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('user_id', $userId)
            ->where('status', 'PENDING')
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or already processed'
            ], 404);
        }

        $assignment->update([
            'status' => 'ACCEPTED',
            'responded_at' => now(),
            'response_note' => 'Accepted via mobile app'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment accepted successfully',
            'data' => [
                'assignment_id' => $assignment->id,
                'status' => 'ACCEPTED'
            ]
        ]);
    }

    /**
     * Decline assignment
     * POST /api/mobile/reviewer/assignments/{id}/decline
     */
    public function declineAssignment(Request $request, $assignmentId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();

        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('user_id', $userId)
            ->where('status', 'PENDING')
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or already processed'
            ], 404);
        }

        $assignment->update([
            'status' => 'DECLINED',
            'responded_at' => now(),
            'response_note' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment declined successfully',
            'data' => [
                'assignment_id' => $assignment->id,
                'status' => 'DECLINED'
            ]
        ]);
    }

    /**
     * Get all submitted reviews
     * GET /api/mobile/reviewer/reviews
     */
    public function getReviews()
    {
        $userId = Auth::id();

        $reviews = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('ra.user_id', $userId)
            ->whereNotNull('pb.submitted_at')
            ->select(
                'pb.review_id',
                'pb.assignment_id',
                'ra.paper_id',
                'pb.recommendation_code',
                'pb.total_score',
                'pb.score_novelty',
                'pb.score_relevance',
                'pb.score_technical_quality',
                'pb.score_presentation',
                'pb.score_references',
                'pb.submitted_at',
                'pb.is_draft',
                'bb.title as paper_title',
                'bb.status_code as paper_status',
                'ht.title as conference_name',
                'ra.assigned_at'
            )
            ->orderBy('pb.submitted_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $reviews->count(),
            'average_score' => $reviews->count() > 0 ? round($reviews->avg('total_score'), 2) : 0,
            'accept' => $reviews->whereIn('recommendation_code', ['ACCEPT', 'STRONG_ACCEPT', 'WEAK_ACCEPT'])->count(),
            'reject' => $reviews->whereIn('recommendation_code', ['REJECT', 'STRONG_REJECT', 'WEAK_REJECT'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Submit or update review
     * POST /api/mobile/reviewer/reviews
     */
    public function submitReview(Request $request)
    {
        $userId = Auth::id();

        // Convert string to boolean
        $isDraft = $request->input('is_draft') === true || $request->input('is_draft') === '1';

        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|integer|exists:reviewer_assignments,id',
            'score_novelty' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_relevance' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_technical_quality' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_presentation' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_references' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'detailed_comments' => $isDraft ? 'nullable|string' : 'required|string|min:50',
            'recommendation_code' => $isDraft ? 'nullable|in:ACCEPT,REJECT,STRONG_ACCEPT,WEAK_ACCEPT,STRONG_REJECT,WEAK_REJECT,BORDERLINE' : 'required|in:ACCEPT,REJECT,STRONG_ACCEPT,WEAK_ACCEPT,STRONG_REJECT,WEAK_REJECT,BORDERLINE',
            'is_draft' => 'required|boolean',
            'review_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify assignment
        $assignment = DB::table('reviewer_assignments')
            ->where('id', $request->assignment_id)
            ->where('user_id', $userId)
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or you do not have permission'
            ], 404);
        }

        if ($assignment->status !== 'ACCEPTED') {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the assignment before submitting review'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Calculate total score
            $totalScore = null;
            if ($request->score_novelty && $request->score_relevance &&
                $request->score_technical_quality && $request->score_presentation &&
                $request->score_references) {
                $totalScore = ($request->score_novelty + $request->score_relevance +
                              $request->score_technical_quality + $request->score_presentation +
                              $request->score_references) / 5;
            }

            // Handle file upload
            $reviewFilePath = null;
            if ($request->hasFile('review_file')) {
                $reviewFilePath = $request->file('review_file')->store('reviews', 'public');
            }

            // Check existing review
            $existingReview = DB::table('phanbien')
                ->where('assignment_id', $request->assignment_id)
                ->first();

            $reviewData = [
                'assignment_id' => $request->assignment_id,
                'score_novelty' => $request->score_novelty,
                'score_relevance' => $request->score_relevance,
                'score_technical_quality' => $request->score_technical_quality,
                'score_presentation' => $request->score_presentation,
                'score_references' => $request->score_references,
                'total_score' => $totalScore,
                'detailed_comments' => $request->detailed_comments,
                'recommendation_code' => $request->recommendation_code,
                'is_draft' => $isDraft
            ];

            if ($reviewFilePath) {
                $reviewData['review_file_path'] = $reviewFilePath;
            }

            if (!$isDraft) {
                $reviewData['submitted_at'] = now();
            } else {
                $reviewData['submitted_at'] = null;
            }

            if ($existingReview) {
                DB::table('phanbien')
                    ->where('review_id', $existingReview->review_id)
                    ->update($reviewData);
                $reviewId = $existingReview->review_id;
            } else {
                $reviewId = DB::table('phanbien')->insertGetId($reviewData);
            }

            // Update assignment status if final submission
            if (!$isDraft) {
                DB::table('reviewer_assignments')
                    ->where('id', $request->assignment_id)
                    ->update([
                        'status' => 'COMPLETED',
                        'review_submitted_at' => now()
                    ]);
            }

            DB::commit();

            $message = $isDraft ? 'Draft saved successfully' : 'Review submitted successfully';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'review_id' => $reviewId,
                    'is_draft' => $isDraft,
                    'total_score' => $totalScore
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review detail
     * GET /api/mobile/reviewer/reviews/{id}
     */
    public function getReviewDetail($reviewId)
    {
        $userId = Auth::id();

        $review = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pb.review_id', $reviewId)
            ->where('ra.user_id', $userId)
            ->select(
                'pb.*',
                'ra.paper_id',
                'bb.title as paper_title',
                'bb.abstract as paper_abstract',
                'ht.title as conference_name'
            )
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you do not have permission'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    /**
     * Get dashboard statistics
     * GET /api/mobile/reviewer/dashboard
     */
    public function getDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'assignments' => [
                'total' => ReviewerAssignment::where('user_id', $userId)->count(),
                'pending' => ReviewerAssignment::where('user_id', $userId)->where('status', 'PENDING')->count(),
                'accepted' => ReviewerAssignment::where('user_id', $userId)->where('status', 'ACCEPTED')->count(),
                'completed' => ReviewerAssignment::where('user_id', $userId)->where('status', 'COMPLETED')->count(),
                'declined' => ReviewerAssignment::where('user_id', $userId)->where('status', 'DECLINED')->count(),
            ],
            'reviews' => [
                'total' => DB::table('phanbien as pb')
                    ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
                    ->where('ra.user_id', $userId)
                    ->whereNotNull('pb.submitted_at')
                    ->count(),
                'drafts' => DB::table('phanbien as pb')
                    ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
                    ->where('ra.user_id', $userId)
                    ->where('pb.is_draft', true)
                    ->count(),
                'average_score' => DB::table('phanbien as pb')
                    ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
                    ->where('ra.user_id', $userId)
                    ->whereNotNull('pb.submitted_at')
                    ->avg('pb.total_score') ?? 0
            ]
        ];

        // Recent assignments
        $recentAssignments = ReviewerAssignment::join('baibao as b', 'reviewer_assignments.paper_id', '=', 'b.paper_id')
            ->leftJoin('hoithao as ht', 'b.conference_id', '=', 'ht.conference_id')
            ->where('reviewer_assignments.user_id', $userId)
            ->select(
                'reviewer_assignments.id',
                'reviewer_assignments.status',
                'reviewer_assignments.assigned_at',
                'b.title as paper_title',
                'ht.title as conference_name'
            )
            ->orderBy('reviewer_assignments.assigned_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_assignments' => $recentAssignments
            ]
        ]);
    }

    /**
     * Get all versions of a paper
     * GET /api/mobile/reviewer/papers/{paper_id}/versions
     */
    public function getPaperVersions($paperId)
    {
        $userId = Auth::id();

        // Check if reviewer is assigned to this paper
        $assignment = ReviewerAssignment::where('user_id', $userId)
            ->where('paper_id', $paperId)
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem các phiên bản của bài báo này'
            ], 403);
        }

        // Get paper basic info
        $paper = DB::table('baibao')
            ->leftJoin('hoithao as ht', 'baibao.conference_id', '=', 'ht.conference_id')
            ->where('baibao.paper_id', $paperId)
            ->select(
                'baibao.paper_id',
                'baibao.title',
                'baibao.abstract',
                'baibao.keywords',
                'baibao.file_path as current_file',
                'baibao.status_code',
                'ht.title as conference_name'
            )
            ->first();

        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài báo'
            ], 404);
        }

        // Get all versions
        $versions = DB::table('phienbanbaibao')
            ->where('paper_id', $paperId)
            ->select(
                'version_id',
                'paper_id',
                'version_no',
                'file_path',
                'submitted_at',
                'note'
            )
            ->orderBy('version_no', 'desc')
            ->get();

        // Get authors
        $authors = DB::table('tacgiabaibao as tg')
            ->join('nguoidung as u', 'tg.user_id', '=', 'u.user_id')
            ->where('tg.paper_id', $paperId)
            ->select(
                'tg.author_order',
                'tg.is_contact',
                'tg.organization',
                'u.full_name',
                'u.email'
            )
            ->orderBy('tg.author_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'paper' => $paper,
                'versions' => $versions,
                'authors' => $authors
            ]
        ]);
    }
}
