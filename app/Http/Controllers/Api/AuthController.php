<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\VaiTroNguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:NguoiDung,email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:200',
            'is_student' => 'boolean',
            'faculty_id' => 'nullable|exists:Khoa,faculty_id',
            'organization' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user
            $user = NguoiDung::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'is_student' => $request->is_student ?? false,
                'faculty_id' => $request->faculty_id,
                'organization' => $request->organization,
                'locked' => false,
            ]);

            // Assign default AUTHOR role
            VaiTroNguoiDung::create([
                'user_id' => $user->user_id,
                'role_code' => 'AUTHOR',
                'conference_id' => null,
            ]);

            // Generate token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => [
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'is_student' => $user->is_student,
                        'faculty_id' => $user->faculty_id,
                        'organization' => $user->organization,
                    ],
                    'token' => $token,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email không tồn tại'
            ], 401);
        }

        // Check if locked
        if ($user->locked) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đã bị khóa'
            ], 403);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu không đúng'
            ], 401);
        }

        try {
            // Generate token
            $token = JWTAuth::fromUser($user);

            // Get roles
            $roles = $user->vaiTros()->with('loaiVaiTro')->get()->map(function ($vaiTro) {
                return [
                    'role_code' => $vaiTro->role_code,
                    'role_name' => $vaiTro->loaiVaiTro->role_name ?? null,
                    'conference_id' => $vaiTro->conference_id,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'user' => [
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'is_student' => $user->is_student,
                        'faculty_id' => $user->faculty_id,
                        'organization' => $user->organization,
                        'roles' => $roles,
                    ],
                    'token' => $token,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }

            // Get roles
            $roles = $user->vaiTros()->with('loaiVaiTro')->get()->map(function ($vaiTro) {
                return [
                    'role_code' => $vaiTro->role_code,
                    'role_name' => $vaiTro->loaiVaiTro->role_name ?? null,
                    'conference_id' => $vaiTro->conference_id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'is_student' => $user->is_student,
                    'faculty_id' => $user->faculty_id,
                    'organization' => $user->organization,
                    'created_at' => $user->created_at,
                    'roles' => $roles,
                    'khoa' => $user->khoa,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required|string|max:200',
            'faculty_id' => 'nullable|exists:Khoa,faculty_id',
            'organization' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();

            $user->update($request->only([
                'full_name',
                'faculty_id',
                'organization',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng'
                ], 401);
            }

            // Update password
            $user->update([
                'password_hash' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đổi mật khẩu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng xuất thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'token' => $newToken
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể làm mới token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}




