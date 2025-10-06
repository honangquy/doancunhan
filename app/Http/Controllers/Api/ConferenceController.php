<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoiThao;
use App\Models\TieuBan;
use App\Models\BaiBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ConferenceController extends Controller
{
    /**
     * Display a listing of conferences
     * GET /api/conferences
     */
    public function index(Request $request)
    {
        try {
            $query = HoiThao::with(['khoa', 'parent']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by level
            if ($request->has('level_code')) {
                $query->where('level_code', $request->level_code);
            }

            // Filter by year
            if ($request->has('year')) {
                $query->where('year', $request->year);
            }

            // Filter by faculty
            if ($request->has('faculty_id')) {
                $query->where('faculty_id', $request->faculty_id);
            }

            // Search by title
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            // Only parent conferences (not sub-conferences)
            if ($request->has('parent_only') && $request->parent_only == 'true') {
                $query->whereNull('parent_id');
            }

            // Sort
            $sortBy = $request->get('sort_by', 'start_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $conferences = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $conferences,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created conference
     * POST /api/conferences
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'year' => 'required|integer|min:2000|max:2100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'deadline_submission' => 'required|date|before:start_date',
                'deadline_review' => 'required|date|after:deadline_submission|before:start_date',
                'deadline_camera_ready' => 'required|date|after:deadline_review|before:start_date',
                'level_code' => 'required|exists:CapHoiThao,level_code',
                'faculty_id' => 'required|exists:Khoa,faculty_id',
                'parent_id' => 'nullable|exists:HoiThao,conference_id',
                'status' => 'nullable|in:OPEN,CLOSED,CANCELLED',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if user has permission (must be ADMIN or CHAIR)
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or chair can create conferences.',
                ], 403);
            }

            $conference = HoiThao::create([
                'parent_id' => $request->parent_id,
                'level_code' => $request->level_code,
                'faculty_id' => $request->faculty_id,
                'title' => $request->title,
                'year' => $request->year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'deadline_submission' => $request->deadline_submission,
                'deadline_review' => $request->deadline_review,
                'deadline_camera_ready' => $request->deadline_camera_ready,
                'status' => $request->get('status', 'OPEN'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conference created successfully',
                'data' => $conference->load(['khoa', 'parent']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified conference
     * GET /api/conferences/{id}
     */
    public function show($id)
    {
        try {
            $conference = HoiThao::with([
                'khoa',
                'parent',
                'children',
                'tieuBans',
                'yeuCauHoiThao'
            ])->findOrFail($id);

            // Get statistics
            $stats = [
                'total_tracks' => $conference->tieuBans()->count(),
                'total_papers' => $conference->baiBaos()->count(),
                'submitted_papers' => $conference->baiBaos()->where('trang_thai_code', 'SUBMITTED')->count(),
                'accepted_papers' => $conference->baiBaos()->where('trang_thai_code', 'ACCEPTED')->count(),
                'rejected_papers' => $conference->baiBaos()->where('trang_thai_code', 'REJECTED')->count(),
                'sub_conferences' => $conference->children()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $conference,
                'statistics' => $stats,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conference not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified conference
     * PUT /api/conferences/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $conference = HoiThao::findOrFail($id);

            // Check if user has permission
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or chair can update conferences.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'year' => 'sometimes|required|integer|min:2000|max:2100',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after_or_equal:start_date',
                'deadline_submission' => 'sometimes|required|date',
                'deadline_review' => 'sometimes|required|date',
                'deadline_camera_ready' => 'sometimes|required|date',
                'level_code' => 'sometimes|required|exists:CapHoiThao,level_code',
                'faculty_id' => 'sometimes|required|exists:Khoa,faculty_id',
                'parent_id' => 'nullable|exists:HoiThao,conference_id',
                'status' => 'sometimes|in:OPEN,CLOSED,CANCELLED',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $conference->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Conference updated successfully',
                'data' => $conference->load(['khoa', 'parent']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update conference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified conference
     * DELETE /api/conferences/{id}
     */
    public function destroy($id)
    {
        try {
            $conference = HoiThao::findOrFail($id);

            // Check if user has permission
            $user = auth()->user();
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin can delete conferences.',
                ], 403);
            }

            // Check if conference has papers
            if ($conference->baiBaos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete conference with existing papers. Please delete papers first.',
                ], 400);
            }

            // Check if conference has sub-conferences
            if ($conference->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete conference with sub-conferences. Please delete sub-conferences first.',
                ], 400);
            }

            $conference->delete();

            return response()->json([
                'success' => true,
                'message' => 'Conference deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get conference statistics
     * GET /api/conferences/{id}/statistics
     */
    public function statistics($id)
    {
        try {
            $conference = HoiThao::findOrFail($id);

            $stats = [
                'conference_info' => [
                    'title' => $conference->title,
                    'year' => $conference->year,
                    'status' => $conference->status,
                    'is_submission_open' => $conference->isSubmissionOpen(),
                    'is_review_open' => $conference->isReviewOpen(),
                ],
                'tracks' => [
                    'total' => $conference->tieuBans()->count(),
                    'list' => $conference->tieuBans()->select('track_id', 'track_name')->get(),
                ],
                'papers' => [
                    'total' => $conference->baiBaos()->count(),
                    'by_status' => $conference->baiBaos()
                        ->select('trang_thai_code', DB::raw('count(*) as count'))
                        ->groupBy('trang_thai_code')
                        ->get(),
                    'by_track' => $conference->tieuBans()
                        ->withCount('baiBaos')
                        ->select('track_id', 'track_name')
                        ->get(),
                ],
                'users' => [
                    'total_authors' => $conference->baiBaos()
                        ->distinct('user_id')
                        ->count('user_id'),
                    'total_reviewers' => DB::table('PhanCongReview')
                        ->join('BaiBao', 'PhanCongReview.paper_id', '=', 'BaiBao.paper_id')
                        ->where('BaiBao.conference_id', $id)
                        ->distinct('PhanCongReview.reviewer_id')
                        ->count('PhanCongReview.reviewer_id'),
                ],
                'reviews' => [
                    'total_assignments' => DB::table('PhanCongReview')
                        ->join('BaiBao', 'PhanCongReview.paper_id', '=', 'BaiBao.paper_id')
                        ->where('BaiBao.conference_id', $id)
                        ->count(),
                    'completed_reviews' => DB::table('Review')
                        ->join('BaiBao', 'Review.paper_id', '=', 'BaiBao.paper_id')
                        ->where('BaiBao.conference_id', $id)
                        ->count(),
                ],
                'deadlines' => [
                    'submission' => $conference->deadline_submission,
                    'review' => $conference->deadline_review,
                    'camera_ready' => $conference->deadline_camera_ready,
                    'days_until_submission' => now()->diffInDays($conference->deadline_submission, false),
                    'days_until_review' => now()->diffInDays($conference->deadline_review, false),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get conferences by user role
     * GET /api/my-conferences
     */
    public function myConferences(Request $request)
    {
        try {
            $user = auth()->user();
            $role = $request->get('role', 'all'); // all, chair, reviewer, author

            $conferences = collect();

            if ($role === 'all' || $role === 'author') {
                // Conferences where user is author
                $authorConferences = HoiThao::whereHas('baiBaos', function ($query) use ($user) {
                    $query->where('user_id', $user->user_id);
                })->with('khoa')->get();
                $conferences = $conferences->merge($authorConferences);
            }

            if ($role === 'all' || $role === 'reviewer') {
                // Conferences where user is reviewer
                $reviewerConferences = HoiThao::whereHas('baiBaos.phanCongReviews', function ($query) use ($user) {
                    $query->where('reviewer_id', $user->user_id);
                })->with('khoa')->get();
                $conferences = $conferences->merge($reviewerConferences);
            }

            if ($role === 'all' || $role === 'chair') {
                // Conferences in user's faculty (if user is chair)
                if ($user->isChair()) {
                    $chairConferences = HoiThao::where('faculty_id', $user->faculty_id)
                        ->with('khoa')
                        ->get();
                    $conferences = $conferences->merge($chairConferences);
                }
            }

            // Remove duplicates
            $conferences = $conferences->unique('conference_id')->values();

            return response()->json([
                'success' => true,
                'data' => $conferences,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
