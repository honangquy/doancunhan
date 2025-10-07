<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\NguoiDung;

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
            
            // Redirect based on user role
            return $this->redirectToDashboard();
        }

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

        // Check if user has any role assigned
        $hasAnyRole = DB::table('VaiTroNguoiDung')
            ->where('user_id', $user->user_id)
            ->exists();

        // If user has no role, redirect to home with pending message
        if (!$hasAnyRole) {
            return redirect()->route('home')
                ->with('warning', 'Tài khoản của bạn đang chờ Admin phê duyệt. Vui lòng quay lại sau.');
        }

        // Check roles in order of priority: ADMIN > CHAIR > REVIEWER > AUTHOR
        if ($user->hasRole('ADMIN')) {
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Chào mừng Admin, ' . $user->full_name . '!');
        }

        if ($user->hasRole('CHAIR')) {
            return redirect()->intended('/chair/dashboard')
                ->with('success', 'Chào mừng Chair, ' . $user->full_name . '!');
        }

        if ($user->hasRole('REVIEWER')) {
            return redirect()->intended('/reviewer/dashboard')
                ->with('success', 'Chào mừng Reviewer, ' . $user->full_name . '!');
        }

        if ($user->hasRole('AUTHOR')) {
            return redirect()->intended('/author/dashboard')
                ->with('success', 'Chào mừng ' . $user->full_name . '!');
        }

        // Fallback: user has role but not recognized
        return redirect()->route('home')
            ->with('info', 'Chào mừng ' . $user->full_name . '!');
    }

    public function logout(Request $request)
    {
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
            'password' => 'required|min:6|confirmed',
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        try {
            // Create new user (without role - admin will assign role later)
            $userId = DB::table('NguoiDung')->insertGetId([
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'full_name' => $validated['full_name'],
                'is_student' => false,
                'locked' => false,
                'created_at' => now(),
            ]);

            // DO NOT assign role - user must be approved by admin first
            // Admin will assign appropriate role (AUTHOR/REVIEWER/CHAIR) after approval

            // Auto login after registration
            $user = NguoiDung::find($userId);
            Auth::login($user);

            // Redirect to home with success message
            // User will have limited access until admin assigns a role
            return redirect()->route('home')
                ->with('success', 'Đăng ký thành công! Vui lòng đợi Admin phê duyệt tài khoản của bạn.');

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

        // Get user statistics
        $stats = [
            'totalPapers' => DB::table('BaiBao')->where('submitter_id', $user->user_id)->count(),
            'acceptedPapers' => DB::table('BaiBao')->where('submitter_id', $user->user_id)->where('status_code', 'ACCEPTED')->count(),
            'reviewAssignments' => DB::table('PhanCongPhanBien')->where('reviewer_id', $user->user_id)->count(),
            'completedReviews' => DB::table('PhanBien')
                ->join('PhanCongPhanBien', 'PhanBien.assignment_id', '=', 'PhanCongPhanBien.assignment_id')
                ->where('PhanCongPhanBien.reviewer_id', $user->user_id)
                ->whereNotNull('PhanBien.submitted_at')
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
            DB::table('NguoiDung')
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
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        try {
            DB::table('NguoiDung')
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

                DB::table('NguoiDung')
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
}

