<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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

        // Default to author dashboard
        return redirect()->intended('/author/dashboard')
            ->with('success', 'Chào mừng ' . $user->full_name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('status', 'Bạn đã đăng xuất thành công.');
    }
}
