<?php

namespace App\Http\Controllers;

use App\Models\HoiThao;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConferenceController extends Controller
{
    /**
     * Display a listing of conferences.
     */
    public function index()
    {
        $conferences = HoiThao::with('khoa')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('conferences.index', compact('conferences'));
    }

    /**
     * Display the specified conference detail page.
     */
    public function show($id)
    {
        $conference = HoiThao::with(['khoa', 'joinRequests'])
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
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để gửi yêu cầu tham gia'
            ], 401);
        }

        // Validate request data
        $validated = $request->validate([
            'role' => 'required|in:AUTHOR,REVIEWER',
            'message' => 'nullable|string|max:1000'
        ]);

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

            // Create new join request
            $joinRequest = JoinRequest::create([
                'conference_id' => $id,
                'user_id' => $userId,
                'role' => $validated['role'],
                'message' => $validated['message'] ?? null,
                'status' => JoinRequest::STATUS_PENDING
            ]);

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
    public function processJoinRequest(Request $request, $conferenceId, $requestId)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        try {
            $joinRequest = JoinRequest::where([
                'id' => $requestId,
                'conference_id' => $conferenceId,
                'status' => JoinRequest::STATUS_PENDING
            ])->firstOrFail();

            $joinRequest->update([
                'status' => $validated['action'] === 'approve' 
                    ? JoinRequest::STATUS_APPROVED 
                    : JoinRequest::STATUS_REJECTED,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'admin_notes' => $validated['admin_notes']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu đã được xử lý thành công'
            ]);

        } catch (\Exception $e) {
            \Log::error('Join request processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý yêu cầu'
            ], 500);
        }
    }
}
