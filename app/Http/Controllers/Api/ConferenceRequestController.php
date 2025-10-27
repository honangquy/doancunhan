<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use App\Models\ThemVienBoSung;
use App\Models\Notification;
use App\Mail\ConferenceRequestApproved;
use App\Mail\ConferenceRequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

            // User must be verified
            if (!$user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email verification required to submit conference requests.',
                ], 403);
            }

            $rules = [
                'title' => 'required|string|max:255',
                'field' => 'required|string|max:255',
                'level_code' => 'required|in:KHOA,TRUONG',
                'expected_date' => 'required|date|after_or_equal:today',
                'objective' => 'required|string|max:500',
                'affiliation' => 'nullable|string|max:255',
                'chair_fullname' => 'required|string|max:255',
                'chair_email' => 'required|email|max:255',
                'chair_phone' => 'nullable|string|max:20',
                'proposal_file' => 'required|file|mimes:pdf|max:10240', // 10MB
                'co_chairs' => 'nullable|json',
            ];

            // Add faculty_name validation for KHOA level
            if ($request->level_code === 'KHOA') {
                $rules['faculty_name'] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Store proposal file
                $filePath = $request->file('proposal_file')->store('conference-requests', 'public');

                // Create conference request
                $conferenceRequest = YeuCauHoiThao::create([
                    'user_id' => $user->id, // Use standard Laravel user id
                    'title' => $request->title,
                    'field' => $request->field,
                    'level_code' => $request->level_code,
                    'expected_date' => $request->expected_date,
                    'objective' => $request->objective,
                    'proposal_file' => $filePath,
                    'status' => 'PENDING',
                    'faculty_name' => $request->faculty_name, // Faculty name for KHOA level
                    'affiliation' => $request->affiliation,
                    'chair_fullname' => $request->chair_fullname,
                    'chair_email' => $request->chair_email,
                    'chair_phone' => $request->chair_phone,
                    'created_at' => now(),
                ]);

                // Parse and store co-chairs
                if ($request->has('co_chairs') && $request->co_chairs) {
                    $coChairs = json_decode($request->co_chairs, true);
                    if (is_array($coChairs)) {
                        foreach ($coChairs as $coChair) {
                            if (!empty($coChair['fullname']) && !empty($coChair['email'])) {
                                ThemVienBoSung::create([
                                    'request_id' => $conferenceRequest->request_id,
                                    'fullname' => $coChair['fullname'],
                                    'email' => $coChair['email'],
                                    'affiliation' => $coChair['affiliation'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                // Load co-chairs relationship
                $conferenceRequest->load('coChairs');

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu tạo hội thảo đã được gửi thành công!',
                    'request_id' => $conferenceRequest->request_id,
                    'data' => $conferenceRequest,
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo yêu cầu: ' . $e->getMessage(),
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
                    'message' => 'Chỉ admin mới có thể duyệt yêu cầu.',
                ], 403);
            }

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            if ($conferenceRequest->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể duyệt yêu cầu ở trạng thái chờ duyệt.',
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update request status
                $conferenceRequest->update([
                    'status' => 'APPROVED',
                    'approver_id' => $user->user_id,
                    'approved_at' => now(),
                ]);

                // Assign CHAIR role to requester
                \App\Models\VaiTroNguoiDung::firstOrCreate([
                    'user_id' => $conferenceRequest->user_id,
                    'role_code' => 'CHAIR',
                    'conference_id' => null,
                ]);

                // Create in-app notification
                Notification::create([
                    'user_id' => $conferenceRequest->user_id,
                    'type' => 'conference_request_approved',
                    'title' => 'Yêu cầu Tạo Hội thảo Được Duyệt',
                    'message' => "Yêu cầu tạo hội thảo '{$conferenceRequest->title}' đã được duyệt. Vui lòng hoàn thành cấu hình hội thảo để công khai.",
                    'data' => [
                        'request_id' => $conferenceRequest->request_id,
                        'title' => $conferenceRequest->title,
                        'action' => 'configure',
                    ],
                ]);

                // Send email notification
                $configUrl = route('chair.configure-conference', $conferenceRequest->request_id);
                Mail::to($conferenceRequest->requester->email)->send(
                    new ConferenceRequestApproved(
                        $conferenceRequest->requester,
                        $conferenceRequest,
                        $configUrl
                    )
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu đã được duyệt thành công!',
                    'data' => $conferenceRequest->load(['requester', 'approver', 'coChairs']),
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi duyệt yêu cầu: ' . $e->getMessage(),
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
                    'message' => 'Chỉ admin mới có thể từ chối yêu cầu.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            if ($conferenceRequest->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể từ chối yêu cầu ở trạng thái chờ duyệt.',
                ], 400);
            }

            DB::beginTransaction();

            try {
                $rejectionReason = $request->reason ?? 'Bị từ chối bởi quản trị viên';

                // Update request
                $conferenceRequest->update([
                    'status' => 'REJECTED',
                    'approver_id' => $user->user_id,
                    'approved_at' => now(),
                    'approval_note' => $rejectionReason,
                ]);

                // Create in-app notification
                Notification::create([
                    'user_id' => $conferenceRequest->user_id,
                    'type' => 'conference_request_rejected',
                    'title' => 'Yêu cầu Tạo Hội thảo Bị Từ chối',
                    'message' => "Yêu cầu tạo hội thảo '{$conferenceRequest->title}' đã bị từ chối. Lý do: {$rejectionReason}",
                    'data' => [
                        'request_id' => $conferenceRequest->request_id,
                        'title' => $conferenceRequest->title,
                        'reason' => $rejectionReason,
                    ],
                ]);

                // Send email notification
                Mail::to($conferenceRequest->requester->email)->send(
                    new ConferenceRequestRejected(
                        $conferenceRequest->requester,
                        $conferenceRequest,
                        $rejectionReason
                    )
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu đã bị từ chối',
                    'data' => $conferenceRequest->load(['requester', 'approver', 'coChairs']),
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi từ chối yêu cầu: ' . $e->getMessage(),
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
     * Configure approved conference
     * PUT /api/conference-requests/{id}/configure
     */
    public function configure(Request $request, $id)
    {
        try {
            $user = auth()->user();

            $conferenceRequest = YeuCauHoiThao::findOrFail($id);

            // Only requester of approved request can configure
            if ($conferenceRequest->user_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cấu hình yêu cầu này.',
                ], 403);
            }

            if ($conferenceRequest->status !== 'APPROVED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể cấu hình yêu cầu đã được duyệt.',
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'acronym' => 'required|string|max:50',
                'year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 5),
                'conference_name' => 'nullable|string|max:255',
                'description' => 'required|string|max:500',
                'detailed_description' => 'required|string|max:2000',
                'keywords' => 'nullable|string|max:1000',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'deadline_submission' => 'required|date|before:start_date',
                'deadline_review' => 'required|date|after:deadline_submission|before:start_date',
                'deadline_camera_ready' => 'required|date|after:deadline_review|before_or_equal:start_date',
                'result_announcement_deadline' => 'nullable|date|before_or_equal:start_date',
                'reviewers_per_paper' => 'required|integer|min:2|max:5',
                'enable_coi_check' => 'required|boolean',
                'location' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'chair_name' => 'required|string|max:255',
                'chair_email' => 'required|email|max:255',
                'cfp_url' => 'nullable|url|max:500',
                'submission_guidelines' => 'required|string|max:2000',
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
                // Get or create conference for this request
                $conference = $conferenceRequest->hoiThao;

                if (!$conference) {
                    // Create HoiThao record if it doesn't exist
                    $conference = HoiThao::create([
                        'conference_request_id' => $conferenceRequest->request_id,
                        'level_code' => $conferenceRequest->level_code,
                        'faculty_id' => $user->faculty_id ?? null,
                        'title' => $request->title,
                        'acronym' => $request->acronym,
                        'year' => $request->year,
                        'conference_name' => $request->conference_name,
                        'description' => $request->description,
                        'detailed_description' => $request->detailed_description,
                        'keywords' => $request->keywords,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'deadline_submission' => $request->deadline_submission,
                        'deadline_review' => $request->deadline_review,
                        'deadline_camera_ready' => $request->deadline_camera_ready,
                        'result_announcement_deadline' => $request->result_announcement_deadline,
                        'reviewers_per_paper' => $request->reviewers_per_paper,
                        'enable_coi_check' => $request->enable_coi_check,
                        'location' => $request->location,
                        'contact_email' => $request->contact_email,
                        'contact_phone' => $request->contact_phone,
                        'chair_name' => $request->chair_name,
                        'chair_email' => $request->chair_email,
                        'cfp_url' => $request->cfp_url,
                        'submission_guidelines' => $request->submission_guidelines,
                        'status' => 'OPEN',
                        'chair_id' => $user->user_id,
                    ]);
                } else {
                    // Update existing conference with all configuration
                    $conference->update([
                        'title' => $request->title,
                        'acronym' => $request->acronym,
                        'year' => $request->year,
                        'conference_name' => $request->conference_name,
                        'description' => $request->description,
                        'detailed_description' => $request->detailed_description,
                        'keywords' => $request->keywords,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'deadline_submission' => $request->deadline_submission,
                        'deadline_review' => $request->deadline_review,
                        'deadline_camera_ready' => $request->deadline_camera_ready,
                        'result_announcement_deadline' => $request->result_announcement_deadline,
                        'reviewers_per_paper' => $request->reviewers_per_paper,
                        'enable_coi_check' => $request->enable_coi_check,
                        'location' => $request->location,
                        'contact_email' => $request->contact_email,
                        'contact_phone' => $request->contact_phone,
                        'chair_name' => $request->chair_name,
                        'chair_email' => $request->chair_email,
                        'cfp_url' => $request->cfp_url,
                        'submission_guidelines' => $request->submission_guidelines,
                        'status' => 'OPEN',
                        'chair_id' => $user->user_id,
                    ]);
                }

                // Update request status to CONFIGURED
                $conferenceRequest->update([
                    'status' => 'CONFIGURED',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Cấu hình hội thảo thành công!',
                    'data' => [
                        'request' => $conferenceRequest->load('hoiThao'),
                        'conference' => $conference,
                    ],
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cấu hình hội thảo: ' . $e->getMessage(),
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





