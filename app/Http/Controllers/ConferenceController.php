<?php

namespace App\Http\Controllers;

use App\Models\HoiThao;
use App\Models\JoinRequest;
use App\Models\VaiTroNguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConferenceController extends Controller
{
    /**
     * Display a listing of conferences.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $level = $request->get('level');
        
        $query = HoiThao::with('khoa')
            ->where('status', 'ACTIVE'); // Chỉ hiển thị hội thảo đã được admin duyệt
            
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }
        
        // Level filter
        if ($level) {
            $query->where('level_code', $level);
        }
        
        // Status filter based on dates
        if ($status !== 'all') {
            $now = now();
            switch ($status) {
                case 'open':
                    $query->where('deadline_submission', '>', $now);
                    break;
                case 'upcoming':
                    $query->where('start_date', '>', $now)
                          ->where('deadline_submission', '<=', $now);
                    break;
                case 'ongoing':
                    $query->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'ended':
                    $query->where('end_date', '<', $now);
                    break;
            }
        }
        
        $conferences = $query->orderBy('year', 'desc')
            ->orderBy('conference_id', 'desc')
            ->paginate(12);
            
        // Get filter options
        $levels = HoiThao::where('status', 'ACTIVE')
            ->distinct()
            ->pluck('level_code')
            ->filter()
            ->sort();
            
        return view('conferences.index', compact('conferences', 'levels', 'search', 'status', 'level'));
    }

    /**
     * Display the specified conference detail page.
     */
    public function show($id)
    {
        $conference = HoiThao::with(['khoa', 'joinRequests'])
            ->where('status', 'ACTIVE') // Chỉ hiển thị hội thảo đã được duyệt
            ->findOrFail($id);
            
        // Get conference statistics
        $stats = $conference->getStats();
        
        // Calculate time remaining until submission deadline
        $timeRemaining = $conference->getDaysUntilSubmission();
        
        return view('conferences.show', compact('conference', 'stats', 'timeRemaining'));
    }

    /**
     * Handle join request submission.
     */
    public function submitJoinRequest(Request $request, $id)
    {
        \Log::info('Join request submission started', [
            'conference_id' => $id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để gửi yêu cầu tham gia'
            ], 401);
        }

        // Get role from request to determine validation rules
        $role = $request->input('role');
        
        // Validate based on role
        if ($role === JoinRequest::ROLE_AUTHOR) {
            $validated = $request->validate(JoinRequest::getAuthorValidationRules());
        } elseif ($role === JoinRequest::ROLE_REVIEWER) {
            $validated = $request->validate(JoinRequest::getReviewerValidationRules());
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vai trò không hợp lệ'
            ], 422);
        }

        try {
            $conference = HoiThao::findOrFail($id);
            
            // Check if conference is still open
            if (!$conference->isOpen()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hội thảo đã đóng, không thể gửi yêu cầu tham gia'
                ]);
            }

            $userId = Auth::id();

            // Check if user already has a join request for this conference and role
            $existingRequest = JoinRequest::where([
                'conference_id' => $id,
                'user_id' => $userId,
                'role' => $validated['role']
            ])->first();

            if ($existingRequest) {
                $statusMessage = match($existingRequest->status) {
                    'PENDING' => 'Bạn đã gửi yêu cầu tham gia với vai trò này và đang chờ xét duyệt',
                    'APPROVED' => 'Yêu cầu tham gia của bạn đã được chấp thuận',
                    'REJECTED' => 'Yêu cầu tham gia của bạn đã bị từ chối trước đó'
                };
                
                return response()->json([
                    'success' => false,
                    'message' => $statusMessage
                ]);
            }

            // Prepare data for creating join request
            $joinRequestData = [
                'conference_id' => $id,
                'user_id' => $userId,
                'role' => $validated['role'],
                'status' => JoinRequest::STATUS_PENDING,
                'full_name' => $validated['full_name'],
                'email_contact' => $validated['email_contact'],
                'commitment_confirmed' => $validated['commitment_confirmed']
            ];

            // Add common optional fields
            if (isset($validated['notes'])) {
                $joinRequestData['notes'] = $validated['notes'];
            }

            // Add role-specific fields
            if ($role === JoinRequest::ROLE_AUTHOR) {
                $joinRequestData = array_merge($joinRequestData, [
                    'country' => $validated['country'],
                    'organization' => $validated['organization'],
                    'department' => $validated['department'],
                    'field_of_study' => $validated['field_of_study'],
                    'academic_title' => $validated['academic_title'],
                    'phone' => $validated['phone']
                ]);
            } elseif ($role === JoinRequest::ROLE_REVIEWER) {
                $joinRequestData = array_merge($joinRequestData, [
                    'organization' => $validated['organization'],
                    'expertise_keywords' => $validated['expertise_keywords'],
                    'max_papers' => $validated['max_papers']
                ]);
            }

            // Create new join request
            $joinRequest = JoinRequest::create($joinRequestData);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu tham gia đã được gửi thành công! Chúng tôi sẽ xem xét và phản hồi sớm nhất.',
                'data' => [
                    'request_id' => $joinRequest->id,
                    'status' => $joinRequest->status
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Join request submission error: ' . $e->getMessage(), [
                'conference_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $validated
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Get user's join requests for a conference.
     */
    public function getUserJoinRequests($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $requests = JoinRequest::where([
            'conference_id' => $id,
            'user_id' => Auth::id()
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Admin method to manage join requests.
     */
    public function manageJoinRequests($id)
    {
        // This would be used in admin panel
        $conference = HoiThao::with(['pendingJoinRequests.user'])->findOrFail($id);
        
        return view('admin.conferences.join-requests', compact('conference'));
    }

    /**
     * Admin method to approve/reject join requests.
     */
    public function processJoinRequest(Request $request, $requestId)
    {
        // Log the incoming request for debugging
        \Log::info('Join request processing started', [
            'request_id' => $requestId,
            'admin_id' => Auth::id(),
            'request_data' => $request->all(),
            'content_type' => $request->header('content-type'),
            'method' => $request->method()
        ]);

        // Check if user is admin
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện hành động này'
            ], 401);
        }

        $user = Auth::user();
        if (!$user->hasRole('ADMIN')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 403);
        }

        // Validate request data
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'admin_notes' => 'nullable|string|max:500'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Join request validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        }

        try {
            // Find the join request
            $joinRequest = JoinRequest::where('id', $requestId)->first();
            
            if (!$joinRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu tham gia'
                ], 404);
            }

            if ($joinRequest->status !== JoinRequest::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xử lý các yêu cầu đang chờ duyệt'
                ], 400);
            }

            // Prepare update data
            $updateData = [
                'status' => $validated['action'] === 'approve' 
                    ? JoinRequest::STATUS_APPROVED 
                    : JoinRequest::STATUS_REJECTED,
                'processed_by' => $user->user_id, // Use user_id explicitly
                'processed_at' => now(),
                'admin_notes' => $validated['admin_notes'] ?? null
            ];

            \Log::info('Updating join request', [
                'request_id' => $requestId,
                'update_data' => $updateData
            ]);

            // Update the join request
            $joinRequest->update($updateData);

            // If approved, assign the requested role to the user
            if ($validated['action'] === 'approve') {
                try {
                    // Get the conference_id from join request (may be null for global roles)
                    $conferenceId = $joinRequest->conference_id;
                    
                    // Check if user already has this role for this conference
                    $existingRole = VaiTroNguoiDung::where('user_id', $joinRequest->user_id)
                        ->where('role_code', $joinRequest->role)
                        ->where(function($query) use ($conferenceId) {
                            if ($conferenceId) {
                                $query->where('conference_id', $conferenceId);
                            } else {
                                $query->whereNull('conference_id');
                            }
                        })
                        ->exists();

                    if (!$existingRole) {
                        // Assign the role
                        VaiTroNguoiDung::create([
                            'user_id' => $joinRequest->user_id,
                            'role_code' => $joinRequest->role,
                            'conference_id' => $conferenceId,
                        ]);

                        \Log::info('Role assigned successfully', [
                            'user_id' => $joinRequest->user_id,
                            'role_code' => $joinRequest->role,
                            'conference_id' => $conferenceId,
                            'request_id' => $requestId
                        ]);
                    } else {
                        \Log::info('User already has this role', [
                            'user_id' => $joinRequest->user_id,
                            'role_code' => $joinRequest->role,
                            'conference_id' => $conferenceId
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to assign role after approval', [
                        'user_id' => $joinRequest->user_id,
                        'role_code' => $joinRequest->role,
                        'conference_id' => $joinRequest->conference_id ?? null,
                        'error' => $e->getMessage(),
                        'request_id' => $requestId
                    ]);
                    // Don't fail the entire request if role assignment fails
                }
            }

            $actionText = $validated['action'] === 'approve' ? 'duyệt' : 'từ chối';
            
            \Log::info('Join request processed successfully', [
                'request_id' => $requestId,
                'action' => $validated['action'],
                'admin_id' => $user->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => "Yêu cầu đã được {$actionText} thành công",
                'data' => [
                    'id' => $joinRequest->id,
                    'status' => $joinRequest->status,
                    'processed_by' => $joinRequest->processed_by,
                    'processed_at' => $joinRequest->processed_at,
                    'admin_notes' => $joinRequest->admin_notes
                ]
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in join request processing', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'request_id' => $requestId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cơ sở dữ liệu. Vui lòng thử lại sau'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Unexpected error in join request processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $requestId,
                'admin_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi không mong muốn xảy ra. Vui lòng thử lại sau'
            ], 500);
        }
    }

    /**
     * Show user's all join requests.
     */
    public function myJoinRequests()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $joinRequests = JoinRequest::with(['conference', 'processedBy'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.join-requests', compact('joinRequests'));
    }

    /**
     * Admin method to view all join requests.
     */
    public function adminJoinRequests(Request $request)
    {
        $query = JoinRequest::with(['conference', 'user', 'processedBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Search by user name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $joinRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => JoinRequest::count(),
            'pending' => JoinRequest::where('status', JoinRequest::STATUS_PENDING)->count(),
            'approved' => JoinRequest::where('status', JoinRequest::STATUS_APPROVED)->count(),
            'rejected' => JoinRequest::where('status', JoinRequest::STATUS_REJECTED)->count(),
        ];

        return view('admin.join-requests.index', compact('joinRequests', 'stats'));
    }
}
