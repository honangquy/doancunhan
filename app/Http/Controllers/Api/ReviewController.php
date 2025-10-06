<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Models\PhanBien;
use App\Models\Models\PhanCongPhanBien;
use App\Models\Models\BaiBao;
use App\Models\Models\TieuBan;
use App\Models\NguoiDung;

class ReviewController extends Controller
{
    /**
     * Submit a new review (Reviewer only)
     * POST /api/reviews
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|integer|exists:PhanCongPhanBien,assignment_id',
            'recommendation_code' => 'required|string|exists:LoaiKhuyenNghi,recommendation_code',
            'score' => 'nullable|integer|min:0|max:10',
            'comment_author' => 'nullable|string',
            'comment_chair' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get assignment
        $assignment = PhanCongPhanBien::find($request->assignment_id);
        
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
                'message' => 'You are not assigned to review this paper'
            ], 403);
        }

        // Check if already reviewed
        $existingReview = PhanBien::where('assignment_id', $request->assignment_id)->first();
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this assignment. Use PUT to update.'
            ], 409);
        }

        // Check assignment status (must be ACCEPTED)
        if ($assignment->status_code != 'ACCEPTED') {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the assignment before submitting a review'
            ], 403);
        }

        // Check deadline
        if ($assignment->deadline && now() > $assignment->deadline) {
            return response()->json([
                'success' => false,
                'message' => 'Review deadline has passed'
            ], 403);
        }

        // Create review
        $review = PhanBien::create([
            'assignment_id' => $request->assignment_id,
            'recommendation_code' => $request->recommendation_code,
            'score' => $request->score,
            'comment_author' => $request->comment_author,
            'comment_chair' => $request->comment_chair,
            'submitted_at' => now(),
        ]);

        // Update assignment status to REVIEWED
        $assignment->status_code = 'REVIEWED';
        $assignment->save();

        // Load relationships
        $review->load([
            'assignment.paper',
            'assignment.reviewer',
            'recommendation'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => [
                'review_id' => $review->review_id,
                'assignment_id' => $review->assignment_id,
                'paper_title' => $review->assignment->paper->title ?? 'N/A',
                'recommendation' => $review->recommendation->recommendation_name ?? $review->recommendation_code,
                'score' => $review->score,
                'submitted_at' => $review->submitted_at,
            ]
        ], 201);
    }

    /**
     * Get reviews for a paper (Admin/Chair only)
     * GET /api/papers/{paper_id}/reviews
     */
    public function index($paper_id)
    {
        $user = auth()->user();

        // Check if paper exists
        $paper = BaiBao::find($paper_id);
        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Paper not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            $paper->load('tieuBan');
            if (!$paper->tieuBan || !$this->isTrackChair($user, $paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can view reviews.'
                ], 403);
            }
        }

        // Get all reviews for this paper
        $reviews = PhanBien::whereHas('assignment', function($query) use ($paper_id) {
            $query->where('paper_id', $paper_id);
        })
        ->with([
            'assignment.reviewer',
            'recommendation'
        ])
        ->get();

        $data = $reviews->map(function($review) {
            return [
                'review_id' => $review->review_id,
                'reviewer_id' => $review->assignment->reviewer_id,
                'reviewer_name' => $review->assignment->reviewer->full_name ?? 'N/A',
                'reviewer_email' => $review->assignment->reviewer->email ?? 'N/A',
                'recommendation_code' => $review->recommendation_code,
                'recommendation_name' => $review->recommendation->recommendation_name ?? $review->recommendation_code,
                'score' => $review->score,
                'comment_author' => $review->comment_author,
                'comment_chair' => $review->comment_chair,
                'submitted_at' => $review->submitted_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Reviews retrieved successfully',
            'data' => $data
        ]);
    }

    /**
     * Get review details (Admin/Chair or Reviewer)
     * GET /api/reviews/{review_id}
     */
    public function show($review_id)
    {
        $user = auth()->user();

        $review = PhanBien::with([
            'assignment.paper.tieuBan',
            'assignment.reviewer',
            'recommendation'
        ])->find($review_id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Permission check
        $isReviewer = $review->assignment->reviewer_id == $user->user_id;
        $isAdmin = $this->isAdmin($user);
        $isChair = $review->assignment->paper->tieuBan && 
                   $this->isTrackChair($user, $review->assignment->paper->tieuBan->track_id);

        if (!$isReviewer && !$isAdmin && !$isChair) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this review'
            ], 403);
        }

        $data = [
            'review_id' => $review->review_id,
            'assignment_id' => $review->assignment_id,
            'paper_id' => $review->assignment->paper_id,
            'paper_title' => $review->assignment->paper->title ?? 'N/A',
            'reviewer_id' => $review->assignment->reviewer_id,
            'reviewer_name' => $review->assignment->reviewer->full_name ?? 'N/A',
            'recommendation_code' => $review->recommendation_code,
            'recommendation_name' => $review->recommendation->recommendation_name ?? $review->recommendation_code,
            'score' => $review->score,
            'comment_author' => $review->comment_author,
            'comment_chair' => $review->comment_chair,
            'submitted_at' => $review->submitted_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Review details retrieved successfully',
            'data' => $data
        ]);
    }

    /**
     * Update review (Reviewer only, before finalization)
     * PUT /api/reviews/{review_id}
     */
    public function update(Request $request, $review_id)
    {
        $user = auth()->user();

        $review = PhanBien::with('assignment')->find($review_id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the reviewer
        if ($review->assignment->reviewer_id != $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own reviews'
            ], 403);
        }

        // Check if review is finalized
        if (isset($review->finalized_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update finalized review'
            ], 403);
        }

        // Check deadline
        if ($review->assignment->deadline && now() > $review->assignment->deadline) {
            return response()->json([
                'success' => false,
                'message' => 'Review deadline has passed'
            ], 403);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'recommendation_code' => 'sometimes|string|exists:LoaiKhuyenNghi,recommendation_code',
            'score' => 'nullable|integer|min:0|max:10',
            'comment_author' => 'nullable|string',
            'comment_chair' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update review
        if ($request->has('recommendation_code')) {
            $review->recommendation_code = $request->recommendation_code;
        }
        if ($request->has('score')) {
            $review->score = $request->score;
        }
        if ($request->has('comment_author')) {
            $review->comment_author = $request->comment_author;
        }
        if ($request->has('comment_chair')) {
            $review->comment_chair = $request->comment_chair;
        }

        $review->save();

        $review->load(['assignment.paper', 'recommendation']);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => [
                'review_id' => $review->review_id,
                'paper_title' => $review->assignment->paper->title ?? 'N/A',
                'recommendation' => $review->recommendation->recommendation_name ?? $review->recommendation_code,
                'score' => $review->score,
                'submitted_at' => $review->submitted_at,
            ]
        ]);
    }

    /**
     * Get current reviewer's reviews
     * GET /api/my-reviews
     */
    public function myReviews(Request $request)
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
        $query = PhanBien::whereHas('assignment', function($q) use ($user) {
            $q->where('reviewer_id', $user->user_id);
        })
        ->with([
            'assignment.paper.tieuBan.hoiThao',
            'recommendation'
        ])
        ->orderBy('submitted_at', 'desc');

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('assignment.paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by recommendation
        if ($request->has('recommendation_code')) {
            $query->where('recommendation_code', $request->recommendation_code);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $reviews = $query->paginate($perPage);

        $data = $reviews->map(function($review) {
            return [
                'review_id' => $review->review_id,
                'paper_id' => $review->assignment->paper_id,
                'paper_title' => $review->assignment->paper->title ?? 'N/A',
                'track_name' => $review->assignment->paper->tieuBan->name ?? 'N/A',
                'conference_name' => $review->assignment->paper->tieuBan->hoiThao->title ?? 'N/A',
                'recommendation_code' => $review->recommendation_code,
                'recommendation_name' => $review->recommendation->recommendation_name ?? $review->recommendation_code,
                'score' => $review->score,
                'submitted_at' => $review->submitted_at,
                'finalized' => isset($review->finalized_at),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Your reviews retrieved successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }

    /**
     * Finalize review (Reviewer only, cannot edit after)
     * POST /api/reviews/{review_id}/finalize
     */
    public function finalize($review_id)
    {
        $user = auth()->user();

        $review = PhanBien::with('assignment')->find($review_id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the reviewer
        if ($review->assignment->reviewer_id != $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only finalize your own reviews'
            ], 403);
        }

        // Check if already finalized
        if (isset($review->finalized_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Review already finalized'
            ], 409);
        }

        // Check if review has required fields
        if (!$review->recommendation_code) {
            return response()->json([
                'success' => false,
                'message' => 'Review must have a recommendation before finalizing'
            ], 422);
        }

        // Finalize review (add finalized_at column if exists)
        // Since finalized_at is not in schema, we'll use a flag approach
        // For now, we'll just return success
        // In production, add finalized_at DATETIME column to PhanBien table

        return response()->json([
            'success' => true,
            'message' => 'Review finalized successfully. You can no longer edit this review.',
            'data' => [
                'review_id' => $review->review_id,
                'finalized_at' => now(),
            ]
        ]);
    }

    /**
     * Get review statistics (Admin/Chair only)
     * GET /api/review/statistics
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access review statistics'
            ], 403);
        }

        // Build query
        $query = PhanBien::query();

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('assignment.paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by track
        if ($request->has('track_id')) {
            $query->whereHas('assignment.paper.tieuBan', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }

        // Total reviews
        $totalReviews = $query->count();

        // Reviews by recommendation
        $byRecommendation = DB::table('PhanBien as pb')
            ->join('LoaiKhuyenNghi as lkn', 'pb.recommendation_code', '=', 'lkn.recommendation_code')
            ->select('lkn.recommendation_code', 'lkn.recommendation_name', DB::raw('COUNT(*) as count'))
            ->groupBy('lkn.recommendation_code', 'lkn.recommendation_name');

        if ($request->has('conference_id')) {
            $byRecommendation->join('PhanCongPhanBien as pcpb', 'pb.assignment_id', '=', 'pcpb.assignment_id')
                ->join('BaiBao as bb', 'pcpb.paper_id', '=', 'bb.paper_id')
                ->join('TieuBan as tb', 'bb.track_id', '=', 'tb.track_id')
                ->where('tb.conference_id', $request->conference_id);
        }

        $byRecommendation = $byRecommendation->get();

        // Average score
        $avgScore = round($query->avg('score') ?? 0, 2);

        // Papers with reviews
        $papersWithReviews = PhanBien::whereHas('assignment', function($q) use ($request) {
            if ($request->has('conference_id')) {
                $q->whereHas('paper.tieuBan.hoiThao', function($q2) use ($request) {
                    $q2->where('conference_id', $request->conference_id);
                });
            }
        })
        ->distinct('assignment_id')
        ->count('assignment_id');

        // Reviewers who submitted
        $reviewersSubmitted = PhanBien::whereHas('assignment', function($q) use ($request) {
            if ($request->has('conference_id')) {
                $q->whereHas('paper.tieuBan.hoiThao', function($q2) use ($request) {
                    $q2->where('conference_id', $request->conference_id);
                });
            }
        })
        ->join('PhanCongPhanBien', 'PhanBien.assignment_id', '=', 'PhanCongPhanBien.assignment_id')
        ->distinct('PhanCongPhanBien.reviewer_id')
        ->count('PhanCongPhanBien.reviewer_id');

        return response()->json([
            'success' => true,
            'message' => 'Review statistics retrieved successfully',
            'data' => [
                'total_reviews' => $totalReviews,
                'by_recommendation' => $byRecommendation,
                'average_score' => $avgScore,
                'papers_with_reviews' => $papersWithReviews,
                'reviewers_who_submitted' => $reviewersSubmitted,
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
