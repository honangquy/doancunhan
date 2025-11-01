<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TieuBan;
use App\Models\HoiThao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TrackController extends Controller
{
    /**
     * Display a listing of tracks for a conference
     * GET /api/conferences/{conference_id}/tracks
     */
    public function index($conferenceId)
    {
        try {
            $conference = HoiThao::findOrFail($conferenceId);
            
            $tracks = $conference->tieuBans()
                ->with(['chair' => function($query) {
                    $query->select('user_id', 'full_name', 'email');
                }])
                ->withCount('baiBaos')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tracks,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tracks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created track
     * POST /api/conferences/{conference_id}/tracks
     */
    public function store(Request $request, $conferenceId)
    {
        try {
            $conference = HoiThao::findOrFail($conferenceId);

            // Check if user has permission
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or chair can create tracks.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'track_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'chair_id' => 'required|exists:nguoidung,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if chair has CHAIR role
            $chair = NguoiDung::findOrFail($request->chair_id);
            if (!$chair->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user must have CHAIR role.',
                ], 400);
            }

            $track = TieuBan::create([
                'conference_id' => $conferenceId,
                'track_name' => $request->track_name,
                'description' => $request->description,
                'chair_id' => $request->chair_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Track created successfully',
                'data' => $track->load('chair'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create track',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified track
     * GET /api/tracks/{id}
     */
    public function show($id)
    {
        try {
            $track = TieuBan::with([
                'hoiThao',
                'chair',
                'baiBaos' => function($query) {
                    $query->with('author:user_id,full_name,email');
                }
            ])->findOrFail($id);

            // Get statistics
            $stats = [
                'total_papers' => $track->baiBaos()->count(),
                'by_status' => $track->baiBaos()
                    ->select('trang_thai_code', DB::raw('count(*) as count'))
                    ->groupBy('trang_thai_code')
                    ->get(),
                'total_reviews' => DB::table('Review')
                    ->join('baibao', 'Review.paper_id', '=', 'baibao.paper_id')
                    ->where('baibao.track_id', $id)
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $track,
                'statistics' => $stats,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Track not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified track
     * PUT /api/tracks/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $track = TieuBan::findOrFail($id);

            // Check if user has permission
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or chair can update tracks.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'track_name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'chair_id' => 'sometimes|required|exists:nguoidung,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if new chair has CHAIR role
            if ($request->has('chair_id')) {
                $chair = NguoiDung::findOrFail($request->chair_id);
                if (!$chair->isChair()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected user must have CHAIR role.',
                    ], 400);
                }
            }

            $track->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Track updated successfully',
                'data' => $track->load('chair'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update track',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified track
     * DELETE /api/tracks/{id}
     */
    public function destroy($id)
    {
        try {
            $track = TieuBan::findOrFail($id);

            // Check if user has permission
            $user = auth()->user();
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin can delete tracks.',
                ], 403);
            }

            // Check if track has papers
            if ($track->baiBaos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete track with existing papers. Please reassign or delete papers first.',
                ], 400);
            }

            $track->delete();

            return response()->json([
                'success' => true,
                'message' => 'Track deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete track',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get papers for a specific track
     * GET /api/tracks/{id}/papers
     */
    public function papers($id, Request $request)
    {
        try {
            $track = TieuBan::findOrFail($id);

            $query = $track->baiBaos()->with([
                'author:user_id,full_name,email',
                'trangThai:trang_thai_code,trang_thai_name',
            ]);

            // Filter by status
            if ($request->has('status')) {
                $query->where('trang_thai_code', $request->status);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'submission_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $papers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $papers,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve papers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tracks managed by current user (as chair)
     * GET /api/my-tracks
     */
    public function myTracks()
    {
        try {
            $user = auth()->user();

            if (!$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only chairs can access their managed tracks.',
                ], 403);
            }

            $tracks = TieuBan::where('chair_id', $user->user_id)
                ->with(['hoiThao:conference_id,title,year,status'])
                ->withCount('baiBaos')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tracks,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tracks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}





