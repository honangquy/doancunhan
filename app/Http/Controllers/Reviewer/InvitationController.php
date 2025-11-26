<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvitationController extends Controller
{
    /**
     * Xử lý khi reviewer click vào link mời - chuyển đến form tham gia
     */
    public function acceptInvitation($token)
    {
        // Tìm lời mời theo token
        $invitation = DB::table('reviewer_invitations')
            ->join('hoithao', 'reviewer_invitations.conference_id', '=', 'hoithao.conference_id')
            ->where('reviewer_invitations.token', $token)
            ->where('reviewer_invitations.status', 'PENDING')
            ->where('reviewer_invitations.expires_at', '>', now())
            ->select('reviewer_invitations.*', 'hoithao.title as conference_title', 'hoithao.*')
            ->first();

        if (!$invitation) {
            return redirect()->route('home')->with('error', 'Lời mời không hợp lệ hoặc đã hết hạn.');
        }

        // Lưu thông tin invitation vào session để sử dụng sau khi đăng nhập/đăng ký
        session([
            'invitation_data' => [
                'token' => $token,
                'email' => $invitation->email,
                'conference_id' => $invitation->conference_id,
                'conference_title' => $invitation->conference_title,
                'invited' => true
            ]
        ]);

        // Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            // Chuyển đến trang đăng ký với thông tin email được mời
            return redirect()->route('register')->with([
                'info' => 'Vui lòng đăng ký tài khoản để tham gia làm reviewer cho hội thảo.',
                'pre_filled_email' => $invitation->email
            ]);
        }

        // Nếu đã đăng nhập, redirect đến trang conference
        return redirect()->route('conferences.show', $invitation->conference_id)->with('info', 'Bạn đã được mời tham gia làm reviewer cho hội thảo này.');
    }

    /**
     * Hiển thị form khai báo thông tin reviewer
     */
    public function showJoinForm(Request $request)
    {
        $token = $request->get('token');
        $conferenceId = $request->get('conference_id');

        if (!$token || !$conferenceId) {
            return redirect()->route('home')->with('error', 'Thông tin không hợp lệ.');
        }

        // Verify token và lấy thông tin lời mời
        $invitation = DB::table('reviewer_invitations')
            ->join('hoithao', 'reviewer_invitations.conference_id', '=', 'hoithao.conference_id')
            ->where('reviewer_invitations.token', $token)
            ->where('reviewer_invitations.conference_id', $conferenceId)
            ->where('reviewer_invitations.status', 'PENDING')
            ->where('reviewer_invitations.expires_at', '>', now())
            ->select('reviewer_invitations.*', 'hoithao.title as conference_title')
            ->first();

        if (!$invitation) {
            return redirect()->route('home')->with('error', 'Lời mời không hợp lệ hoặc đã hết hạn.');
        }

        // Kiểm tra user đã đăng nhập và email trùng khớp
        if (!Auth::check() || Auth::user()->email !== $invitation->email) {
            return redirect()->route('home')->with('error', 'Vui lòng đăng nhập bằng email được mời: ' . $invitation->email);
        }

        $user = Auth::user();

        return view('reviewer.join-form', [
            'invitation' => $invitation,
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Xử lý submit form khai báo thông tin reviewer
     */
    public function submitJoinForm(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'organization' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'specialization' => 'required|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'experience_years' => 'required|integer|min:0|max:50'
        ]);

        $token = $request->token;

        // Verify token
        $invitation = DB::table('reviewer_invitations')
            ->where('token', $token)
            ->where('status', 'PENDING')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('home')->with('error', 'Lời mời không hợp lệ hoặc đã hết hạn.');
        }

        $user = Auth::user();

        // Kiểm tra email trùng khớp
        if ($user->email !== $invitation->email) {
            return back()->with('error', 'Email không trùng khớp với lời mời.');
        }

        DB::beginTransaction();
        
        try {
            // Kiểm tra xem đã có join request pending chưa (dùng bảng join_requests có sẵn)
            $existingRequest = DB::table('join_requests')
                ->where('user_id', $user->user_id)
                ->where('conference_id', $invitation->conference_id)
                ->where('role', 'REVIEWER')
                ->where('status', 'PENDING')
                ->first();

            if ($existingRequest) {
                return back()->with('info', 'Yêu cầu vai trò của bạn đã được gửi trước đó và đang chờ admin duyệt.');
            }

            // Tạo join request để admin duyệt (dùng cùng hệ thống như Author)
            DB::table('join_requests')->insert([
                'user_id' => $user->user_id,
                'conference_id' => $invitation->conference_id,
                'role' => 'REVIEWER',
                'status' => 'PENDING',
                'invitation_token' => $token, // Liên kết với invitation
                'full_name' => $user->full_name,
                'email_contact' => $user->email,
                'organization' => $request->organization,
                'expertise_keywords' => $request->specialization,
                'max_papers' => $request->experience_years, // Tạm dùng experience_years
                'notes' => $request->bio,
                'commitment_confirmed' => true,
                'message' => "Đăng ký qua lời mời của Chair. Chuyên môn: {$request->specialization}",
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Cập nhật thông tin cơ bản của user (không phải reviewer-specific info)
            DB::table('nguoidung')
                ->where('user_id', $user->user_id)
                ->update([
                    'organization' => $request->organization,
                    'updated_at' => now()
                ]);

            // Cập nhật trạng thái lời mời thành "RESPONDED" (chưa phải ACCEPTED)
            DB::table('reviewer_invitations')
                ->where('id', $invitation->id)
                ->update([
                    'status' => 'RESPONDED', // Đã phản hồi nhưng chưa được duyệt
                    'responded_at' => now(),
                    'updated_at' => now()
                ]);

            // Tạo thông báo cho admin (sử dụng hệ thống thông báo có sẵn)
            $conference = DB::table('hoithao')->where('conference_id', $invitation->conference_id)->first();
            
            // Tìm admin để gửi thông báo
            $admins = DB::table('nguoidung as nd')
                ->join('vaitronguoidung as vt', 'nd.user_id', '=', 'vt.user_id')
                ->where('vt.role_code', 'ADMIN')
                ->whereNull('vt.conference_id') // Admin toàn hệ thống
                ->select('nd.*')
                ->get();

            foreach ($admins as $admin) {
                DB::table('notifications')->insert([
                    'user_id' => $admin->user_id,
                    'title' => 'Yêu cầu vai trò Reviewer mới (Qua lời mời)',
                    'message' => "Người dùng {$user->full_name} ({$user->email}) đã đăng ký làm Reviewer cho hội thảo \"{$conference->title}\" qua lời mời của Chair. Vui lòng vào mục 'Yêu cầu vai trò' để xem xét và duyệt.",
                    'type' => 'join_request',
                    'data' => json_encode([
                        'user_id' => $user->user_id,
                        'conference_id' => $invitation->conference_id,
                        'role' => 'REVIEWER',
                        'invitation_based' => true,
                        'url' => '/admin/join-requests'
                    ]),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('home')->with('success', 'Cám ơn bạn đã đăng ký! Thông tin của bạn đã được gửi đến admin để xem xét và phê duyệt vai trò Reviewer. Bạn sẽ nhận được thông báo khi yêu cầu được duyệt.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in submitJoinForm: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'invitation_id' => $invitation->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.')->withInput();
        }
    }
}