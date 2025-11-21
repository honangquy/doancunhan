<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReviewerInvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:CHAIR']);
    }

    /**
     * Hiển thị trang mời reviewer
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            
            \Log::info('ReviewerInvitationController@index called', ['user_id' => $userId]);
            
            // Lấy danh sách hội thảo mà chair quản lý - kiểm tra và sửa lại query
            $conferences = DB::table('hoithao as ht')
                ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
                ->where('vt.user_id', $userId)
                ->where('vt.role_code', 'CHAIR')
                ->where('ht.status', 'ACTIVE') // Chỉ lấy conferences đang active
                ->select('ht.*')
                ->orderBy('ht.start_date', 'desc') // Sửa từ created_at thành start_date
                ->get();

            \Log::info('Conferences found for chair', [
                'user_id' => $userId,
                'conferences_count' => $conferences->count(),
                'conferences' => $conferences->toArray()
            ]);

            return view('chair.reviewers.invite', compact('conferences'));
            
        } catch (\Exception $e) {
            \Log::error('Error in ReviewerInvitationController@index: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback với empty conferences nhưng với thông báo lỗi
            $conferences = collect();
            return view('chair.reviewers.invite', compact('conferences'))
                ->with('error', 'Có lỗi khi tải danh sách hội thảo. Vui lòng thử lại.');
        }
    }

    /**
     * Gửi lời mời reviewer
     */
    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'conference_id' => 'required|exists:hoithao,conference_id',
        ]);

        $email = $request->email;
        $conferenceId = $request->conference_id;
        $userId = Auth::id();

        // Kiểm tra xem chair có quyền quản lý hội thảo này không
        $isChairOfConference = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChairOfConference) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền mời reviewer cho hội thảo này.'
            ], 403);
        }

        // Lấy thông tin hội thảo
        $conference = DB::table('hoithao')->where('conference_id', $conferenceId)->first();
        
        // Kiểm tra email đã tồn tại trong hệ thống
        $existingUser = DB::table('nguoidung')->where('email', $email)->first();

        if ($existingUser) {
            // 1. Không thể mời chính mình
            if ($existingUser->user_id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => "Bạn không thể mời chính mình làm reviewer."
                ], 400);
            }

            // 2. Kiểm tra xem đã là CHAIR của hội thảo này chưa (Đồng chủ tịch)
            $isCoChair = DB::table('vaitronguoidung')
                ->where('user_id', $existingUser->user_id)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'CHAIR')
                ->exists();

            if ($isCoChair) {
                return response()->json([
                    'success' => false,
                    'message' => "Người dùng này đã là Đồng chủ tịch (Chair) của hội thảo."
                ], 400);
            }

            // 3. Kiểm tra xem đã là REVIEWER của hội thảo này chưa
            $isReviewer = DB::table('vaitronguoidung')
                ->where('user_id', $existingUser->user_id)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'REVIEWER')
                ->exists();

            if ($isReviewer) {
                return response()->json([
                    'success' => false,
                    'message' => "Người dùng này đã là Phản biện viên (Reviewer) của hội thảo."
                ], 400);
            }

            // Nếu là các role khác (AUTHOR, USER, hoặc role ở hội thảo khác) -> Cho phép mời
            // Logic tiếp tục bên dưới để tạo lời mời
        }

        // Kiểm tra xem đã gửi lời mời cho email này chưa
        $existingInvitation = DB::table('reviewer_invitations')
            ->where('email', $email)
            ->where('conference_id', $conferenceId)
            ->where('status', 'PENDING')
            ->first();

        if ($existingInvitation) {
            return response()->json([
                'success' => false,
                'message' => 'Lời mời đã được gửi cho email này.'
            ], 400);
        }

        // Tạo token cho lời mời
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addDays(7); // Lời mời có hiệu lực 7 ngày

        // Lưu lời mời vào database
        $invitationId = DB::table('reviewer_invitations')->insertGetId([
            'email' => $email,
            'conference_id' => $conferenceId,
            'invited_by' => $userId,
            'token' => $token,
            'status' => 'PENDING',
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Gửi email mời
        try {
            $this->sendInvitationEmail($email, $conference, $token);
            
            \Log::info("Invitation email sent to {$email} with token {$token}");
            
            return response()->json([
                'success' => true,
                'message' => 'Lời mời đã được gửi thành công qua email!'
            ]);
        } catch (\Exception $e) {
            // Xóa lời mời nếu gửi email thất bại
            DB::table('reviewer_invitations')->where('id', $invitationId)->delete();
            
            \Log::error('Error sending invitation email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi email mời reviewer
     */
    private function sendInvitationEmail($email, $conference, $token)
    {
        $inviteUrl = route('reviewer.invitation.accept', ['token' => $token]);
        
        $mailData = [
            'name' => 'Reviewer', // Tên chung cho reviewer
            'email' => $email,
            'conference' => $conference,
            'invitation_url' => $inviteUrl, // Sửa tên biến cho đúng template
            'chair_name' => Auth::user()->full_name // Sửa tên biến cho đúng template
        ];

        Mail::send('emails.reviewer-invitation', $mailData, function ($message) use ($email, $conference) {
            $message->to($email)
                    ->subject("Lời mời làm phản biện viên - {$conference->title}");
        });
    }

    /**
     * Danh sách lời mời đã gửi
     */
    public function sentInvitations(Request $request)
    {
        $userId = Auth::id();
        $conferenceId = $request->get('conference_id');

        $query = DB::table('reviewer_invitations as ri')
            ->join('hoithao as ht', 'ri.conference_id', '=', 'ht.conference_id')
            ->leftJoin('nguoidung as nd', 'ri.email', '=', 'nd.email')
            ->where('ri.invited_by', $userId)
            ->select(
                'ri.*',
                'ht.title as conference_title',
                'nd.full_name'
            );

        if ($conferenceId) {
            $query->where('ri.conference_id', $conferenceId);
        }

        $invitations = $query->orderBy('ri.created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'invitations' => $invitations
        ]);
    }

    /**
     * Gửi lại lời mời (vô hiệu hóa link cũ, tạo link mới)
     */
    public function resendInvitation(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Kiểm tra lời mời có tồn tại và thuộc về chair hiện tại không
        $invitation = DB::table('reviewer_invitations')
            ->where('id', $id)
            ->where('invited_by', $userId)
            ->first();

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Lời mời không tồn tại hoặc bạn không có quyền thao tác.'
            ], 404);
        }

        // Chỉ cho phép gửi lại lời mời có trạng thái PENDING
        if ($invitation->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể gửi lại lời mời đang chờ phản hồi.'
            ], 400);
        }

        // Tạo token mới
        $newToken = Str::random(64);
        $newExpiresAt = Carbon::now()->addDays(7);

        try {
            // Cập nhật lời mời với token mới
            DB::table('reviewer_invitations')
                ->where('id', $id)
                ->update([
                    'token' => $newToken,
                    'expires_at' => $newExpiresAt,
                    'updated_at' => now()
                ]);

            // Lấy thông tin hội thảo để gửi email
            $conference = DB::table('hoithao')->where('conference_id', $invitation->conference_id)->first();
            
            // Gửi email với token mới
            $this->sendInvitationEmail($invitation->email, $conference, $newToken);

            \Log::info("Invitation resent to {$invitation->email} with new token {$newToken}");

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi lại lời mời thành công! Link cũ đã được vô hiệu hóa.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error resending invitation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi lại lời mời: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thu hồi lời mời (vô hiệu hóa link)
     */
    public function revokeInvitation(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Kiểm tra lời mời có tồn tại và thuộc về chair hiện tại không
        $invitation = DB::table('reviewer_invitations')
            ->where('id', $id)
            ->where('invited_by', $userId)
            ->first();

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Lời mời không tồn tại hoặc bạn không có quyền thao tác.'
            ], 404);
        }

        // Chỉ cho phép thu hồi lời mời có trạng thái PENDING
        if ($invitation->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể thu hồi lời mời đang chờ phản hồi.'
            ], 400);
        }

        try {
            // Cập nhật trạng thái lời mời thành REVOKED và xóa token
            DB::table('reviewer_invitations')
                ->where('id', $id)
                ->update([
                    'status' => 'REVOKED',
                    'token' => null, // Vô hiệu hóa token
                    'updated_at' => now()
                ]);

            \Log::info("Invitation revoked for {$invitation->email}, invitation ID: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'Đã thu hồi lời mời thành công! Link không còn hiệu lực.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error revoking invitation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thu hồi lời mời: ' . $e->getMessage()
            ], 500);
        }
    }
}