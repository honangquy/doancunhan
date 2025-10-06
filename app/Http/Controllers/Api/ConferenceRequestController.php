<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ConferenceRequestController extends Controller
{
    /**
     * Display a listing of conference requests
     * GET /api/conference-requests
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            
            $query = YeuCauHoiThao::with(['hoiThao', 'requester', 'admin']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Admin sees all, others see only their own requests
            if (!$user->isAdmin()) {
                $query->where('requester_id', $user->user_id);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'request_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $requests = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $requests,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conference requests',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created conference request
     * POST /api/conference-requests
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            // Only CHAIR can create conference requests
            if (!$user->isChair()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only chairs can submit conference requests.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'year' => 'required|integer|min:2000|max:2100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'deadline_submission' => 'required|date|before:start_date',
                'deadline_review' => 'required|date|after:deadline_submission|before:start_date',
                'deadline_camera_ready' => 'required|date|after:deadline_review|before:start_date',
                'level_code' => 'required|exists:CapHoiThao,level_code',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Create conference (status = CLOSED initially)
                $conference = HoiThao::create([
                    'level_code' => $request->level_code,
                    'faculty_id' => $user->faculty_id, // Use requester's faculty
                    'title' => $request->title,
                    'year' => $request->year,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'deadline_submission' => $request->deadline_submission,
                    'deadline_review' => $request->deadline_review,
                    'deadline_camera_ready' => $request->deadline_camera_ready,
                    'status' => 'CLOSED', // Will be OPEN after approval
                ]);

                // Create request
                $conferenceRequest = YeuCauHoiThao::create([
                    'conference_id' => $conference->conference_id,
                    'requester_id' => $user->user_id,
                    'request_date' => now(),
                    'status' => 'PENDING',
                    'notes' => $request->notes,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Conference request submitted successfully',
                    'data' => $conferenceRequest->load(['hoiThao', 'requester']),
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conference request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified conference request
     * GET /api/conference-requests/{id}
     */
    public function show($id)
    {
        try {
            $user = auth()->user();

            $conferenceRequest = YeuCauHoiThao::with([
                'hoiThao',
                'requester',
                'admin'
            ])->findOrFail($id);

            // Check permission
            if (!$user->isAdmin() && $conferenceRequest->requester_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this request.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $conferenceRequest,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conference request not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Approve conference request
     * POST /api/conference-requests/{id}/approve
     */
    public function approve(Request $request, $id)
    {
        try {
            $user = auth()->user();

            // Only ADMIN can approve
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admin can approve conference requests.',
                ], 403);
            }

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            if (!$conferenceRequest->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be approved.',
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update request
                $conferenceRequest->update([
                    'status' => 'APPROVED',
                    'admin_id' => $user->user_id,
                    'approval_date' => now(),
                    'notes' => $request->get('notes', $conferenceRequest->notes),
                ]);

                // Update conference status to OPEN
                $conference = $conferenceRequest->hoiThao;
                $conference->update(['status' => 'OPEN']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Conference request approved successfully',
                    'data' => $conferenceRequest->load(['hoiThao', 'requester', 'admin']),
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve conference request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject conference request
     * POST /api/conference-requests/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        try {
            $user = auth()->user();

            // Only ADMIN can reject
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admin can reject conference requests.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'notes' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejection reason is required',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            if (!$conferenceRequest->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be rejected.',
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update request
                $conferenceRequest->update([
                    'status' => 'REJECTED',
                    'admin_id' => $user->user_id,
                    'approval_date' => now(),
                    'notes' => $request->notes,
                ]);

                // Update conference status to CANCELLED
                $conference = $conferenceRequest->hoiThao;
                $conference->update(['status' => 'CANCELLED']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Conference request rejected',
                    'data' => $conferenceRequest->load(['hoiThao', 'requester', 'admin']),
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject conference request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel own conference request
     * POST /api/conference-requests/{id}/cancel
     */
    public function cancel($id)
    {
        try {
            $user = auth()->user();

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            // Check permission
            if ($conferenceRequest->requester_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only cancel your own requests.',
                ], 403);
            }

            if (!$conferenceRequest->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be cancelled.',
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update request
                $conferenceRequest->update([
                    'status' => 'REJECTED',
                    'notes' => 'Cancelled by requester',
                ]);

                // Update conference status to CANCELLED
                $conference = $conferenceRequest->hoiThao;
                $conference->update(['status' => 'CANCELLED']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Conference request cancelled',
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel conference request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for conference requests
     * GET /api/conference-requests/statistics
     */
    public function statistics()
    {
        try {
            $user = auth()->user();

            // Only ADMIN can see all statistics
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admin can view statistics.',
                ], 403);
            }

            $stats = [
                'total' => YeuCauHoiThao::count(),
                'by_status' => YeuCauHoiThao::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get(),
                'pending' => YeuCauHoiThao::where('status', 'PENDING')->count(),
                'approved' => YeuCauHoiThao::where('status', 'APPROVED')->count(),
                'rejected' => YeuCauHoiThao::where('status', 'REJECTED')->count(),
                'recent_requests' => YeuCauHoiThao::with(['hoiThao:conference_id,title', 'requester:user_id,full_name'])
                    ->orderBy('request_date', 'desc')
                    ->take(10)
                    ->get(),
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
}

