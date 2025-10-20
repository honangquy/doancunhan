<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class DashboardController extends Controller
{
    use LogsActivity;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->getPrimaryRole());

        // Log dashboard access
        $this->logActivity(
            'Truy cập Dashboard',
            "Người dùng truy cập dashboard với vai trò: {$role}",
            ['role' => $role, 'user_id' => $user->user_id]
        );

        // Route to appropriate dashboard based on role
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'chair':
                return redirect()->route('chair.dashboard');
            case 'reviewer':
                return redirect()->route('reviewer.dashboard');
            case 'author':
            default:
                return redirect()->route('author.dashboard');
        }
    }

    public function authorDashboard()
    {
        // Use authenticated user ID
        $userId = Auth::id();
        
        // Get user's papers with related data
        $papers = DB::table('BaiBao')
            ->where('submitter_id', $userId)
            ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('NguoiDung', 'BaiBao.submitter_id', '=', 'NguoiDung.user_id')
            ->select(
                'BaiBao.paper_id',
                'BaiBao.title',
                'BaiBao.status_code',
                'BaiBao.created_at',
                'TrangThaiBaiBao.status_name',
                'HoiThao.title as conference_name',
                'HoiThao.conference_id',
                'NguoiDung.full_name as author_name'
            )
            ->orderBy('BaiBao.created_at', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $papers->count(),
            'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
            'accepted' => $papers->where('status_code', 'ACCEPTED')->count(),
            'rejected' => $papers->where('status_code', 'REJECTED')->count(),
        ];
        
        return view('author.dashboard', [
            'title' => 'Author Dashboard',
            'papers' => $papers,
            'stats' => $stats
        ]);
    }

    public function reviewerDashboard()
    {
        // Use authenticated user ID
        $userId = Auth::id();
        
        // Get reviewer's assignments with paper and review data
        $assignments = DB::table('PhanCongPhanBien')
            ->where('reviewer_id', $userId)
            ->join('BaiBao', 'PhanCongPhanBien.paper_id', '=', 'BaiBao.paper_id')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('NguoiDung as Submitter', 'BaiBao.submitter_id', '=', 'Submitter.user_id')
            ->leftJoin('PhanBien', 'PhanCongPhanBien.assignment_id', '=', 'PhanBien.assignment_id')
            ->leftJoin('LoaiKhuyenNghi', 'PhanBien.recommendation_code', '=', 'LoaiKhuyenNghi.recommendation_code')
            ->select(
                'PhanCongPhanBien.assignment_id',
                'PhanCongPhanBien.status_code as assignment_status',
                'PhanCongPhanBien.deadline',
                'BaiBao.paper_id',
                'BaiBao.title as paper_title',
                'HoiThao.title as conference_name',
                'Submitter.full_name as author_name',
                'PhanBien.review_id',
                'PhanBien.recommendation_code',
                'LoaiKhuyenNghi.recommendation_name',
                'PhanBien.score'
            )
            ->orderBy('PhanCongPhanBien.deadline', 'asc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('assignment_status', 'INVITED')->count(),
            'in_progress' => $assignments->where('assignment_status', 'ACCEPTED')->count(),
            'completed' => $assignments->whereNotNull('review_id')->count(),
        ];
        
        return view('reviewer.dashboard', [
            'title' => 'Reviewer Dashboard',
            'assignments' => $assignments,
            'stats' => $stats
        ]);
    }

    public function chairDashboard()
    {
        // Use authenticated user ID  
        $userId = Auth::id();
        
        // Get chair's conference (first active conference for now)
        // TODO: In production, filter by conferences where user is chair
        $conference = DB::table('HoiThao')
            ->where('status', 'ACTIVE')
            ->first();
        
        if (!$conference) {
            $conference = DB::table('HoiThao')->first();
        }
        
        $papers = collect();
        $stats = [
            'total_papers' => 0,
            'accepted' => 0,
            'under_review' => 0,
            'rejected' => 0,
            'needs_reviewers' => 0
        ];
        
        if ($conference) {
            // Get papers for this conference
            $papers = DB::table('BaiBao')
                ->where('conference_id', $conference->conference_id)
                ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
                ->join('NguoiDung', 'BaiBao.submitter_id', '=', 'NguoiDung.user_id')
                ->leftJoin(DB::raw('(SELECT paper_id, COUNT(*) as reviewer_count FROM PhanCongPhanBien GROUP BY paper_id) as ReviewerCounts'), 
                    'BaiBao.paper_id', '=', 'ReviewerCounts.paper_id')
                ->select(
                    'BaiBao.paper_id',
                    'BaiBao.title',
                    'BaiBao.status_code',
                    'BaiBao.created_at',
                    'TrangThaiBaiBao.status_name',
                    'NguoiDung.full_name as author_name',
                    DB::raw('COALESCE(ReviewerCounts.reviewer_count, 0) as reviewer_count')
                )
                ->orderBy('BaiBao.created_at', 'desc')
                ->get();
            
            // Calculate statistics
            $stats = [
                'total_papers' => $papers->count(),
                'accepted' => $papers->where('status_code', 'ACCEPTED')->count(),
                'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
                'rejected' => $papers->where('status_code', 'REJECTED')->count(),
                'needs_reviewers' => $papers->where('reviewer_count', '<', 3)->count()
            ];
        }
        
        return view('chair.dashboard', [
            'title' => 'Chair Dashboard',
            'conference' => $conference,
            'papers' => $papers,
            'stats' => $stats
        ]);
    }

    public function adminDashboard()
    {
        // Get system-wide statistics
        $stats = [
            'total_users' => DB::table('nguoidung')->count(),
            'locked_users' => DB::table('nguoidung')->where('locked', 1)->count(),
            'total_conferences' => DB::table('hoithao')->count(),
            'active_conferences' => DB::table('hoithao')->where('status', 'ACTIVE')->count(),
            'total_papers' => DB::table('baibao')->count(),
            'total_reviews' => DB::table('phanbien')->count(),
        ];
        
        // Get recent papers (simplified for now)
        $recentPapers = collect(); // Empty collection for now to avoid table errors
        
        // Get user role distribution from VaiTroNguoiDung table
        $userRoles = DB::table('VaiTroNguoiDung')
            ->select('role_code as role', DB::raw('count(distinct user_id) as count'))
            ->groupBy('role_code')
            ->get();

        // Add count for users with no roles (USER)
        $usersWithoutRoles = DB::table('nguoidung')
            ->leftJoin('VaiTroNguoiDung', 'nguoidung.user_id', '=', 'VaiTroNguoiDung.user_id')
            ->whereNull('VaiTroNguoiDung.user_id')
            ->count();

        if ($usersWithoutRoles > 0) {
            $userRoles->push((object)['role' => 'USER', 'count' => $usersWithoutRoles]);
        }

        // Get join request statistics
        $joinRequestStats = [
            'total' => DB::table('join_requests')->count(),
            'pending' => DB::table('join_requests')->where('status', 'PENDING')->count(),
            'approved' => DB::table('join_requests')->where('status', 'APPROVED')->count(),
            'rejected' => DB::table('join_requests')->where('status', 'REJECTED')->count(),
        ];

        // Get pending join requests for review
        $pendingJoinRequests = DB::table('join_requests')
            ->join('nguoidung', 'join_requests.user_id', '=', 'nguoidung.user_id')
            ->join('hoithao', 'join_requests.conference_id', '=', 'hoithao.conference_id')
            ->where('join_requests.status', 'PENDING')
            ->select(
                'join_requests.id',
                'join_requests.full_name',
                'join_requests.email_contact',
                'join_requests.role',
                'join_requests.created_at',
                'hoithao.title as conference_title',
                'hoithao.conference_id as conference_code'
            )
            ->orderBy('join_requests.created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentPapers' => $recentPapers,
            'userRoles' => $userRoles,
            'joinRequestStats' => $joinRequestStats,
            'pendingJoinRequests' => $pendingJoinRequests
        ]);
    }

    public function adminUsers(Request $request)
    {
        $query = \App\Models\NguoiDung::with('vaiTros')
            ->select('user_id', 'full_name', 'email', 'created_at', 'email_verified_at');

        // Search by name or email
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'USER') {
                // For USER role, find users with no roles assigned
                $query->whereDoesntHave('vaiTros');
            } else {
                // For other roles, find users with specific role
                $query->whereHas('vaiTros', function($q) use ($request) {
                    $q->where('role_code', $request->role);
                });
            }
        }

        // Filter by email verification status
        if ($request->filled('verified')) {
            if ($request->verified == '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', [
            'title' => 'Quản lý người dùng',
            'users' => $users
        ]);
    }

    public function storeUser(Request $request)
    {
        try {
            // Validate input data
            $validated = $request->validate([
                'full_name' => 'required|string|min:3|max:200',
                'email' => 'required|email|max:255|unique:nguoidung,email',
                'password' => 'required|string|min:6|max:100',
                'role' => 'required|in:ADMIN,CHAIR,REVIEWER,AUTHOR,USER'
            ], [
                'full_name.required' => 'Họ tên không được để trống',
                'full_name.min' => 'Họ tên phải có ít nhất 3 ký tự',
                'full_name.max' => 'Họ tên không được vượt quá 200 ký tự',
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không đúng định dạng',
                'email.max' => 'Email không được vượt quá 255 ký tự',
                'email.unique' => 'Email này đã được sử dụng',
                'password.required' => 'Mật khẩu không được để trống',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.max' => 'Mật khẩu không được vượt quá 100 ký tự',
                'role.required' => 'Vai trò không được để trống',
                'role.in' => 'Vai trò không hợp lệ',
            ]);

            // Create user
            $user = \App\Models\NguoiDung::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password_hash' => bcrypt($request->password),
                'email_verified_at' => now(), // Auto verify admin-created users
            ]);

            // Assign role
            \App\Models\VaiTroNguoiDung::create([
                'user_id' => $user->user_id,
                'role_code' => $request->role,
                'conference_id' => null, // Global role
            ]);

            // Log user creation
            $this->logCrudOperation('create', 'Người dùng', $user->user_id, [
                'created_user_email' => $user->email,
                'created_user_name' => $user->full_name,
                'assigned_role' => $request->role
            ]);

            // Check if request expects JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Người dùng đã được tạo thành công!'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Người dùng đã được tạo thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get user data for editing
     */
    public function editUser($id)
    {
        try {
            $user = DB::table('nguoidung')
                ->leftJoin('VaiTroNguoiDung', 'nguoidung.user_id', '=', 'VaiTroNguoiDung.user_id')
                ->select(
                    'nguoidung.user_id',
                    'nguoidung.full_name',
                    'nguoidung.email',
                    'nguoidung.email_verified_at',
                    'VaiTroNguoiDung.role_code'
                )
                ->where('nguoidung.user_id', $id)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng!'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        try {
            // Validate input data
            $validated = $request->validate([
                'full_name' => 'required|string|min:3|max:200',
                'email' => 'required|email|max:255|unique:nguoidung,email,' . $id . ',user_id',
                'password' => 'nullable|string|min:6|max:100',
                'role' => 'required|in:ADMIN,CHAIR,REVIEWER,AUTHOR,USER'
            ], [
                'full_name.required' => 'Họ tên không được để trống',
                'full_name.min' => 'Họ tên phải có ít nhất 3 ký tự',
                'full_name.max' => 'Họ tên không được vượt quá 200 ký tự',
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không đúng định dạng',
                'email.max' => 'Email không được vượt quá 255 ký tự',
                'email.unique' => 'Email này đã được sử dụng',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.max' => 'Mật khẩu không được vượt quá 100 ký tự',
                'role.required' => 'Vai trò không được để trống',
                'role.in' => 'Vai trò không hợp lệ',
            ]);

            DB::beginTransaction();

            // Update user basic info
            DB::table('nguoidung')
                ->where('user_id', $id)
                ->update([
                    'full_name' => $request->full_name,
                    'email' => $request->email
                ]);

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate(['password' => 'string|min:6']);
                DB::table('nguoidung')
                    ->where('user_id', $id)
                    ->update(['password_hash' => bcrypt($request->password)]);
            }

            // Get old roles for logging
            $oldRoles = DB::table('VaiTroNguoiDung')
                ->where('user_id', $id)
                ->pluck('role_code')
                ->toArray();

            // Update role - xóa tất cả vai trò cũ trước khi thêm vai trò mới
            DB::table('VaiTroNguoiDung')
                ->where('user_id', $id)
                ->delete(); // Xóa tất cả vai trò cũ

            // Thêm vai trò mới
            \App\Models\VaiTroNguoiDung::create([
                'user_id' => $id,
                'role_code' => $request->role,
                'conference_id' => null, // Global role
            ]);

            // Log user update
            $roleChanged = !in_array($request->role, $oldRoles);
            $this->logCrudOperation('update', 'Người dùng', $id, [
                'updated_user_email' => $request->email,
                'updated_user_name' => $request->full_name,
                'old_roles' => $oldRoles,
                'new_role' => $request->role,
                'role_changed' => $roleChanged,
                'password_updated' => $request->filled('password')
            ]);

            DB::commit();

            $message = 'Cập nhật người dùng thành công!';
            if ($roleChanged) {
                $oldRoleText = implode(', ', $oldRoles);
                $message .= " Vai trò đã được thay đổi từ [{$oldRoleText}] thành [{$request->role}].";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        try {
            // Check if user exists
            $user = DB::table('nguoidung')->where('user_id', $id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng!'
                ], 404);
            }

            // Don't allow deleting admin users
            $isAdmin = DB::table('VaiTroNguoiDung')
                ->where('user_id', $id)
                ->where('role_code', 'ADMIN')
                ->exists();

            if ($isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa tài khoản quản trị viên!'
                ], 403);
            }

            // Log user deletion before actually deleting
            $this->logCrudOperation('delete', 'Người dùng', $id, [
                'deleted_user_email' => $user->email,
                'deleted_user_name' => $user->full_name
            ]);
            
            // Delete user roles first
            DB::table('VaiTroNguoiDung')->where('user_id', $id)->delete();
            
            // Delete user
            DB::table('nguoidung')->where('user_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa người dùng thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminConferences()
    {
        $conferences = DB::table('hoithao')
            ->select('conference_id', 'title', 'start_date', 'end_date', 'location', 'status')
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return view('admin.conferences', [
            'title' => 'Quản lý hội thảo',
            'conferences' => $conferences
        ]);
    }

    public function adminReports()
    {
        // Statistics for reports
        $stats = [
            'total_users' => DB::table('nguoidung')->count(),
            'total_conferences' => DB::table('hoithao')->count(),
            'total_papers' => DB::table('baibao')->count(),
            'total_reviews' => DB::table('phanbien')->count(),
        ];

        return view('admin.reports', [
            'title' => 'Báo cáo & Thống kê',
            'stats' => $stats
        ]);
    }

    public function adminRoles()
    {
        $roleStats = DB::table('VaiTroNguoiDung')
            ->select('role_code as role', DB::raw('COUNT(DISTINCT user_id) as count'))
            ->groupBy('role_code')
            ->get();

        // Add count for users with no roles (USER)
        $usersWithoutRoles = DB::table('nguoidung')
            ->leftJoin('VaiTroNguoiDung', 'nguoidung.user_id', '=', 'VaiTroNguoiDung.user_id')
            ->whereNull('VaiTroNguoiDung.user_id')
            ->count();

        if ($usersWithoutRoles > 0) {
            $roleStats->push((object)['role' => 'USER', 'count' => $usersWithoutRoles]);
        }

        return view('admin.roles', [
            'title' => 'Phân quyền',
            'roleStats' => $roleStats
        ]);
    }

    public function adminSystem()
    {
        return view('admin.system', [
            'title' => 'Cài đặt hệ thống'
        ]);
    }

    public function adminPermissions()
    {
        $permissions = [
            'manage_users' => 'Quản lý người dùng',
            'manage_conferences' => 'Quản lý hội thảo',
            'manage_papers' => 'Quản lý bài báo',
            'manage_reviews' => 'Quản lý đánh giá',
            'view_reports' => 'Xem báo cáo',
            'system_settings' => 'Cài đặt hệ thống'
        ];

        return view('admin.permissions', [
            'title' => 'Phân quyền',
            'permissions' => $permissions
        ]);
    }

    public function adminSettings()
    {
        return view('admin.settings', [
            'title' => 'Cài đặt hệ thống'
        ]);
    }

    public function adminLogs()
    {
        // Redirect to ActivityLogController
        return redirect()->route('admin.logs.index');
    }

    /**
     * Manually verify user's email
     */
    public function verifyUserEmail($id)
    {
        try {
            $updated = DB::table('nguoidung')
                ->where('user_id', $id)
                ->update(['email_verified_at' => now()]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email đã được xác thực thành công!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Manually unverify user's email
     */
    public function unverifyUserEmail($id)
    {
        try {
            $updated = DB::table('nguoidung')
                ->where('user_id', $id)
                ->update(['email_verified_at' => null]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã hủy xác thực email!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get user details for view modal
     */
    public function getUserDetails($id)
    {
        try {
            $user = DB::table('nguoidung')
                ->leftJoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
                ->leftJoin('loaivaitro', 'vaitronguoidung.role_code', '=', 'loaivaitro.role_code')
                ->where('nguoidung.user_id', $id)
                ->select(
                    'nguoidung.*',
                    DB::raw('GROUP_CONCAT(loaivaitro.role_name) as role_names')
                )
                ->groupBy(
                    'nguoidung.user_id',
                    'nguoidung.email',
                    'nguoidung.email_verified_at',
                    'nguoidung.password_hash',
                    'nguoidung.full_name',
                    'nguoidung.is_student',
                    'nguoidung.faculty_id',
                    'nguoidung.organization',
                    'nguoidung.avatar_url',
                    'nguoidung.locked',
                    'nguoidung.created_at'
                )
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng!'
                ]);
            }

            // Get roles as array
            $roles = [];
            if ($user->role_names) {
                $roleNames = explode(',', $user->role_names);
                foreach ($roleNames as $roleName) {
                    $roles[] = ['TenVaiTro' => trim($roleName)];
                }
            }

            $userData = [
                'user_id' => $user->user_id,
                'name' => $user->full_name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'roles' => $roles
            ];

            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update user role via AJAX
     */
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            // Check if user exists
            $user = DB::table('nguoidung')->where('user_id', $id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng!'
                ]);
            }

            // Get role ID
            $role = DB::table('VaiTro')->where('TenVaiTro', $request->role)->first();
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vai trò không hợp lệ!'
                ]);
            }

            // Remove all existing roles for this user
            DB::table('VaiTroNguoiDung')->where('user_id', $id)->delete();

            // Add new role
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $id,
                'role_id' => $role->role_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật vai trò thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:nguoidung,user_id'
        ]);

        DB::beginTransaction();
        try {
            $userIds = $request->user_ids;
            $currentUserId = Auth::id();

            // Don't allow deleting current user
            if (in_array($currentUserId, $userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa tài khoản của chính mình!'
                ]);
            }

            // Delete related records first
            DB::table('VaiTroNguoiDung')->whereIn('user_id', $userIds)->delete();
            
            // Delete users
            $deleted = DB::table('nguoidung')->whereIn('user_id', $userIds)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deleted} người dùng thành công!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get conference details for view modal
     */
    public function getConferenceDetails($id)
    {
        try {
            $conference = DB::table('HoiThao')
                ->where('conference_id', $id)
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hội thảo!'
                ]);
            }

            $conferenceData = [
                'conference_id' => $conference->conference_id,
                'title' => $conference->title,
                'description' => $conference->description,
                'start_date' => $conference->start_date,
                'end_date' => $conference->end_date,
                'location' => $conference->location,
                'status' => $conference->status
            ];

            return response()->json([
                'success' => true,
                'conference' => $conferenceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk delete conferences
     */
    public function bulkDeleteConferences(Request $request)
    {
        $request->validate([
            'conference_ids' => 'required|array',
            'conference_ids.*' => 'integer|exists:HoiThao,conference_id'
        ]);

        DB::beginTransaction();
        try {
            $conferenceIds = $request->conference_ids;

            // Delete related records first (papers, assignments, etc.)
            DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->delete();
            DB::table('PhanCong')->whereIn('conference_id', $conferenceIds)->delete();
            
            // Delete conferences
            $deleted = DB::table('HoiThao')->whereIn('conference_id', $conferenceIds)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deleted} hội thảo thành công!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}

