<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\NguoiDung;
use App\Models\ActivityLog;
use App\Rules\StrongPassword;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login', [
            'title' => 'Đăng nhập - HUIT Conference'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Laravel will use password_hash field from NguoiDung model
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Log successful login
            ActivityLog::create([
                'log_type' => 'LOGIN',
                'user_id' => $user->user_id,
                'action' => 'Đăng nhập thành công',
                'description' => 'Người dùng đăng nhập vào hệ thống',
                'properties' => [
                    'email' => $user->email,
                    'user_agent' => $request->userAgent(),
                    'remember' => $request->boolean('remember'),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'low'
            ]);

            // Auto-verify special accounts before checking verification status
            $this->autoVerifySpecialAccounts($user);

            // Refresh user to get updated email_verified_at
            $user->refresh();

            // Check if email is verified (after auto-verification)
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Bạn cần xác thực email trước khi có thể sử dụng tài khoản.');
            }

            // Redirect based on user role
            return $this->redirectToDashboard();
        }

        // Log failed login attempt
        ActivityLog::create([
            'log_type' => 'AUTH',
            'user_id' => null, // No user ID since login failed
            'action' => 'Đăng nhập thất bại',
            'description' => 'Thông tin đăng nhập không chính xác cho email: ' . $credentials['email'],
            'properties' => [
                'email' => $credentials['email'],
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => 'medium'
        ]);

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->only('email'));
    }

    /**
     * Redirect user to appropriate dashboard based on their role.
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        // Auto-verify certain test/admin accounts
        $this->autoVerifySpecialAccounts($user);

        // Get all distinct roles for the user
        $roles = DB::table('vaitronguoidung')
            ->where('user_id', $user->user_id)
            ->distinct()
            ->pluck('role_code');

        // Filter out USER role if other roles exist
        if ($roles->count() > 1 && $roles->contains('USER')) {
            $roles = $roles->reject(function ($value) {
                return $value === 'USER';
            });
        }

        // If user has no role, redirect to home with pending message
        if ($roles->isEmpty()) {
            return redirect()->route('home')
                ->with('warning', 'Tài khoản của bạn đang chờ Admin phê duyệt. Vui lòng quay lại sau.');
        }

        // If user has more than 1 role, redirect to role selection page
        if ($roles->count() > 1) {
            return redirect()->route('role.selection');
        }

        // If user has exactly 1 role, redirect to that role's dashboard
        $role = $roles->first();

        if ($role === 'ADMIN') {
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Chào mừng Admin, ' . $user->full_name . '!');
        }

        if ($role === 'CHAIR') {
            return redirect()->intended('/chair/dashboard')
                ->with('success', 'Chào mừng Chair, ' . $user->full_name . '!');
        }

        if ($role === 'REVIEWER') {
            return redirect()->intended('/reviewer/dashboard')
                ->with('success', 'Chào mừng Reviewer, ' . $user->full_name . '!');
        }

        if ($role === 'AUTHOR') {
            return redirect()->intended('/author/dashboard')
                ->with('success', 'Chào mừng ' . $user->full_name . '!');
        }

        // Fallback: user has role but not recognized
        return redirect()->route('home')
            ->with('info', 'Chào mừng ' . $user->full_name . '!');
    }

    public function showRoleSelection()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Get roles from vaitronguoidung (Reviewer, Chair, Admin, etc.)
        $roles = DB::table('vaitronguoidung')
            ->leftJoin('hoithao', 'vaitronguoidung.conference_id', '=', 'hoithao.conference_id')
            ->where('vaitronguoidung.user_id', $user->user_id)
            ->select(
                'vaitronguoidung.role_code',
                'vaitronguoidung.conference_id',
                'hoithao.title as conference_title'
            )
            ->get();

        // 2. Check for Author role (from baibao table)
        $authorConferences = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->where('baibao.submitter_id', $user->user_id)
            ->select('hoithao.conference_id', 'hoithao.title as conference_title')
            ->distinct()
            ->get();

        foreach ($authorConferences as $conf) {
            // Check if AUTHOR role already exists for this conference (unlikely but safe)
            $exists = $roles->where('role_code', 'AUTHOR')
                           ->where('conference_id', $conf->conference_id)
                           ->isNotEmpty();

            if (!$exists) {
                $roles->push((object)[
                    'role_code' => 'AUTHOR',
                    'conference_id' => $conf->conference_id,
                    'conference_title' => $conf->conference_title
                ]);
            }
        }

        // 3. Check for Chair role (from hoithao table - owner)
        $chairConferences = DB::table('hoithao')
            ->where('chair_id', $user->user_id)
            ->select('conference_id', 'title as conference_title')
            ->get();

        foreach ($chairConferences as $conf) {
            // Check if already exists to avoid duplicates
            $exists = $roles->where('role_code', 'CHAIR')
                           ->where('conference_id', $conf->conference_id)
                           ->isNotEmpty();

            if (!$exists) {
                $roles->push((object)[
                    'role_code' => 'CHAIR',
                    'conference_id' => $conf->conference_id,
                    'conference_title' => $conf->conference_title
                ]);
            }
        }

        // Group by role_code
        $groupedRoles = $roles->groupBy('role_code');

        // If user has other roles besides USER, remove USER role
        if ($groupedRoles->count() > 1 && $groupedRoles->has('USER')) {
            $groupedRoles->forget('USER');
        }

        return view('auth.role-selection', [
            'roles' => $groupedRoles,
            'user' => $user
        ]);
    }

    public function selectRole(Request $request)
    {
        $role = $request->input('role');
        $user = Auth::user();

        $hasRole = false;

        if ($role === 'AUTHOR') {
            // Check if user has submitted any papers OR has explicit role
            $hasRole = DB::table('baibao')->where('submitter_id', $user->user_id)->exists() ||
                       DB::table('vaitronguoidung')
                           ->where('user_id', $user->user_id)
                           ->where('role_code', 'AUTHOR')
                           ->exists();
        } elseif ($role === 'CHAIR') {
             // Check if user owns any conference OR has explicit role
             $hasRole = DB::table('hoithao')->where('chair_id', $user->user_id)->exists() ||
                        DB::table('vaitronguoidung')
                           ->where('user_id', $user->user_id)
                           ->where('role_code', 'CHAIR')
                           ->exists();
        } elseif ($role === 'USER') {
            $hasRole = true; // Basic user role is always available if logged in
        } else {
            // Standard check for other roles
            $hasRole = DB::table('vaitronguoidung')
                ->where('user_id', $user->user_id)
                ->where('role_code', $role)
                ->exists();
        }

        if (!$hasRole) {
            return back()->with('error', 'Bạn không có quyền truy cập vai trò này.');
        }

        // Redirect based on selected role
        switch ($role) {
            case 'ADMIN':
                return redirect()->intended('/admin/dashboard');
            case 'CHAIR':
                return redirect()->intended('/chair/dashboard');
            case 'REVIEWER':
                return redirect()->intended('/reviewer/dashboard');
            case 'AUTHOR':
                return redirect()->intended('/author/dashboard');
            default:
                return redirect()->route('home');
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout before actually logging out
        if ($user) {
            ActivityLog::create([
                'log_type' => 'LOGIN',
                'user_id' => $user->user_id,
                'action' => 'Đăng xuất',
                'description' => 'Người dùng đăng xuất khỏi hệ thống',
                'properties' => [
                    'email' => $user->email,
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'low'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Bạn đã đăng xuất thành công.');
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.register', [
            'title' => 'Đăng ký - HUIT Conference'
        ]);
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:NguoiDung,email',
            'password' => ['required', 'confirmed', new StrongPassword()],
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        try {
            // Check if this is an invited user
            $invitationData = session('invitation_data');
            $isInvitedUser = $invitationData && isset($invitationData['email']) && $invitationData['email'] === $validated['email'];

            // Create new user (without role - admin will assign role later)
            // Email verification is skipped for invited users
            $userId = DB::table('nguoidung')->insertGetId([
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'full_name' => $validated['full_name'],
                'is_student' => false,
                'locked' => false,
                'email_verified_at' => $isInvitedUser ? now() : null, // Auto verify for invited users
                'created_at' => now(),
            ]);

            // Get the user instance
            $user = NguoiDung::find($userId);

            // Log successful registration
            ActivityLog::create([
                'log_type' => 'AUTH',
                'user_id' => $user->user_id,
                'action' => 'Đăng ký tài khoản',
                'description' => 'Người dùng đăng ký tài khoản mới: ' . $user->email . ($isInvitedUser ? ' (qua lời mời)' : ''),
                'properties' => [
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'is_invited' => $isInvitedUser
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'low'
            ]);

            // Login the user
            Auth::login($user);

            if ($isInvitedUser) {
                // Skip email verification for invited users and redirect to conference
                $conferenceId = $invitationData['conference_id'];
                return redirect()->route('conferences.show', $conferenceId)
                    ->with('success', 'Đăng ký thành công! Bạn có thể tham gia làm reviewer ngay bây giờ.');
            } else {
                // Send email verification notification for regular users
                $user->sendEmailVerificationNotification();

                // Redirect to email verification notice
                return redirect()->route('verification.notice')
                    ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản của bạn.');
            }

        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Registration error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Có lỗi xảy ra khi đăng ký: ' . $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Show user profile page
     */
    public function showProfile()
    {
        $user = Auth::user();

        // Get user roles
        $userRoles = DB::table('VaiTroNguoiDung as vt')
            ->join('LoaiVaiTro as lt', 'vt.role_code', '=', 'lt.role_code')
            ->where('vt.user_id', $user->user_id)
            ->select('lt.role_code', 'lt.role_name', 'vt.conference_id')
            ->get();

        // Filter out USER role if other roles exist
        if ($userRoles->count() > 1) {
            $userRoles = $userRoles->reject(function ($role) {
                return $role->role_code === 'USER';
            });
        }

        // Get user statistics
        $stats = [
            'totalPapers' => DB::table('baibao')->where('submitter_id', $user->user_id)->count(),
            'acceptedPapers' => DB::table('baibao')->where('submitter_id', $user->user_id)->where('status_code', 'ACCEPTED')->count(),
            'reviewAssignments' => DB::table('phancongphanbien')->where('reviewer_id', $user->user_id)->count(),
            'completedReviews' => DB::table('phanbien')
                ->join('phancongphanbien', 'phanbien.assignment_id', '=', 'phancongphanbien.assignment_id')
                ->where('phancongphanbien.reviewer_id', $user->user_id)
                ->whereNotNull('phanbien.submitted_at')
                ->count(),
        ];

        return view('auth.profile', [
            'title' => 'Hồ sơ cá nhân',
            'user' => $user,
            'userRoles' => $userRoles,
            'stats' => $stats
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên',
        ]);

        try {
            DB::table('nguoidung')
                ->where('user_id', $user->user_id)
                ->update([
                    'full_name' => $validated['full_name'],
                    'organization' => $validated['organization'] ?? null,
                ]);

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật thông tin.']);
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', new StrongPassword()],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        try {
            DB::table('nguoidung')
                ->where('user_id', $user->user_id)
                ->update([
                    'password_hash' => Hash::make($validated['password']),
                ]);

            return back()->with('success', 'Đổi mật khẩu thành công!');
        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đổi mật khẩu.']);
        }
    }

    /**
     * Update user avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        try {
            $avatarUrl = null;

            // Check if it's a file upload
            if ($request->hasFile('avatar')) {
                $request->validate([
                    'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
                ]);

                $file = $request->file('avatar');
                $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store in public/avatars directory
                $file->move(public_path('avatars'), $filename);
                $avatarUrl = '/avatars/' . $filename;
            }
            // Check if it's a URL
            else if ($request->has('avatar_url')) {
                $request->validate([
                    'avatar_url' => 'required|url',
                ]);

                $avatarUrl = $request->input('avatar_url');
            }

            if ($avatarUrl) {
                // Delete old avatar file if exists and is local
                if ($user->avatar_url && strpos($user->avatar_url, '/avatars/') === 0) {
                    $oldFile = public_path($user->avatar_url);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                DB::table('nguoidung')
                    ->where('user_id', $user->user_id)
                    ->update(['avatar_url' => $avatarUrl]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật ảnh đại diện thành công!',
                    'avatar_url' => $avatarUrl
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ảnh để tải lên'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Avatar update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật ảnh đại diện'
            ], 500);
        }
    }

    /**
     * Show email verification notice
     */
    public function showVerifyEmailForm()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Email của bạn đã được xác thực.');
        }

        return view('auth.verify-email', [
            'title' => 'Xác thực Email - HUIT Conference'
        ]);
    }

    /**
     * Verify email address
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = NguoiDung::findOrFail($id);

        // Simple verification without signature check for now
        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->route('login')->with('error', 'Link xác thực không hợp lệ.');
        }

        // Check if verification link has expired (10 minutes from account creation)
        if (now()->diffInMinutes($user->created_at) > 10) {
            return redirect()->route('login')->with('error', 'Link xác thực đã hết hạn. Vui lòng đăng ký lại tài khoản.');
        }

        if ($user->hasVerifiedEmail()) {
            // Auto login if not logged in
            if (!Auth::check()) {
                Auth::login($user);
            }
            return redirect()->route('home')->with('info', 'Email của bạn đã được xác thực.');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));

            // Auto login the user
            Auth::login($user);

            return redirect()->route('home')->with('success', 'Email của bạn đã được xác thực thành công! Bạn có thể sử dụng tài khoản ngay bây giờ.');
        }

        return redirect()->route('login')->with('error', 'Có lỗi xảy ra khi xác thực email.');
    }

    /**
     * Auto-verify special accounts (admin, test accounts)
     */
    private function autoVerifySpecialAccounts($user)
    {
        // List of emails that should be auto-verified
        $autoVerifyEmails = [
            'admin@huit.edu.vn',
            'chair@huit.edu.vn',
            'reviewer@huit.edu.vn',
            'author@huit.edu.vn',
            'test@huit.edu.vn',
            'nangquy2004@gmail.com', // Your admin email
        ];

        // Check if user has admin role
        $isAdmin = DB::table('vaitronguoidung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();

        // Auto-verify if user email is in the list OR user is admin OR email contains 'test'
        if (in_array($user->email, $autoVerifyEmails) ||
            $isAdmin ||
            strpos($user->email, 'test') !== false ||
            strpos($user->email, 'admin') !== false) {

            if (!$user->hasVerifiedEmail()) {
                DB::table('nguoidung')
                    ->where('user_id', $user->user_id)
                    ->update(['email_verified_at' => now()]);

                Log::info("Auto-verified email for special account: " . $user->email);
            }
        }
    }

    /**
     * Resend email verification notification
     */
    public function resendVerificationEmail(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Email của bạn đã được xác thực.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email xác thực đã được gửi lại!');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password', [
            'title' => 'Quên mật khẩu - HUIT Conference'
        ]);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if user exists
        $user = DB::table('nguoidung')->where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
        }

        // Generate reset token
        $token = \Illuminate\Support\Str::random(60);

        // Store reset token
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send reset email
        try {
            \Mail::send('auth.emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'resetUrl' => route('password.reset', $token)
            ], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Đặt lại mật khẩu - HUIT Conference');
            });

            return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn!');
        } catch (\Exception $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        // Find email associated with this token
        $resets = DB::table('password_resets')->get();
        $email = null;

        foreach ($resets as $reset) {
            if (Hash::check($token, $reset->token)) {
                $email = $reset->email;
                break;
            }
        }

        // Debug: Check if email was found
        \Log::info('Reset password form - Token: ' . $token . ', Email found: ' . ($email ?? 'null'));

        return view('auth.reset-password', [
            'title' => 'Đặt lại mật khẩu - HUIT Conference',
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', new StrongPassword()],
        ]);

        // Check if reset record exists
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        // Check if token is not expired (10 minutes)
        if (now()->diffInMinutes($reset->created_at) > 10) {
            return back()->withErrors(['email' => 'Token đặt lại mật khẩu đã hết hạn.']);
        }

        // Check if user exists
        $user = DB::table('nguoidung')->where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
        }

        try {
            // Update password
            DB::table('nguoidung')
                ->where('email', $request->email)
                ->update([
                    'password_hash' => Hash::make($request->password)
                ]);

            // Delete reset token
            DB::table('password_resets')->where('email', $request->email)->delete();

            return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công! Bạn có thể đăng nhập với mật khẩu mới.');

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đặt lại mật khẩu.']);
        }
    }
}





