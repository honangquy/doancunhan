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
            
            // Lấy danh sách hội thảo mà chair quản lý
            $conferences = DB::table('hoithao as ht')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('ht.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->select('ht.*')
                ->get();

            return view('chair.reviewers.invite', compact('conferences'));
            
        } catch (\Exception $e) {
            \Log::error('Error in ReviewerInvitationController@index: ' . $e->getMessage());
            
            // Fallback với empty conferences
            $conferences = collect();
            return view('chair.reviewers.invite', compact('conferences'));
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
        
        // Kiểm tra email đã tồn tại trong hệ thống (ngoại trừ role USER)
        $existingUser = DB::table('nguoidung as nd')
            ->join('vaitronguoidung as vt', 'nd.user_id', '=', 'vt.user_id')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('nd.email', $email)
            ->where('lvt.role_code', '!=', 'USER')
            ->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => "Email đã tồn tại với role {$existingUser->role_name}. Vui lòng nhập email khác."
            ], 400);
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
}