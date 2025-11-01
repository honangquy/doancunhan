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
            // Thêm role REVIEWER cho user
            DB::table('vaitronguoidung')->insert([
                'user_id' => $user->user_id,
                'conference_id' => $invitation->conference_id,
                'role_code' => 'REVIEWER',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Cập nhật thông tin reviewer trong bảng nguoidung nếu cần
            DB::table('nguoidung')
                ->where('user_id', $user->user_id)
                ->update([
                    'organization' => $request->organization,
                    'position' => $request->position,
                    'specialization' => $request->specialization,
                    'bio' => $request->bio,
                    'experience_years' => $request->experience_years,
                    'updated_at' => now()
                ]);

            // Cập nhật trạng thái lời mời
            DB::table('reviewer_invitations')
                ->where('id', $invitation->id)
                ->update([
                    'status' => 'ACCEPTED',
                    'responded_at' => now(),
                    'updated_at' => now()
                ]);

            DB::commit();

            return redirect()->route('reviewer.dashboard')->with('success', 'Chào mừng bạn đã trở thành phản biện viên! Hồ sơ của bạn đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.')->withInput();
        }
    }
}