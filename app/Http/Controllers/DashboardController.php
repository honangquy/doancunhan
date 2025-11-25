<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use App\Models\ActivityLog;

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

        // Get user's primary role from database
        $roleRecord = DB::table('vaitronguoidung')
            ->where('user_id', $user->user_id)
            ->whereNull('conference_id')
            ->first();

        $role = $roleRecord ? strtolower($roleRecord->role_code) : 'author';

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
        $papers = DB::table('baibao')
            ->where('submitter_id', $userId)
            ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('nguoidung', 'baibao.submitter_id', '=', 'nguoidung.user_id')
            ->select(
                'baibao.paper_id',
                'baibao.title',
                'baibao.status_code',
                'baibao.decision',
                'baibao.created_at',
                'trangthaibaibao.status_name',
                'hoithao.title as conference_name',
                'hoithao.conference_id',
                'nguoidung.full_name as author_name'
            )
            ->orderBy('baibao.created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $papers->count(),
            'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
            'accepted' => $papers->where('decision', 'ACCEPT')->count(),
            'published' => $papers->where('decision', 'PUBLISHED')->count(),
            'rejected' => $papers->where('decision', 'REJECT')->count(),
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
        $assignments = DB::table('reviewer_assignments as ra')
            ->where('ra.user_id', $userId)
            ->join('baibao', 'ra.paper_id', '=', 'baibao.paper_id')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('nguoidung as Submitter', 'baibao.submitter_id', '=', 'Submitter.user_id')
            ->leftjoin('phanbien', 'ra.id', '=', 'phanbien.assignment_id')
            ->leftJoin('loaikhuyennghi as LoaiKhuyenNghi', 'phanbien.recommendation_code', '=', 'LoaiKhuyenNghi.recommendation_code')
            ->select(
                'ra.id as assignment_id',
                'ra.status as assignment_status',
                'ra.assigned_at',
                'hoithao.deadline_review as deadline',
                'baibao.paper_id',
                'baibao.title as paper_title',
                'hoithao.title as conference_name',
                'Submitter.full_name as author_name',
                'phanbien.review_id',
                'phanbien.recommendation_code',
                'LoaiKhuyenNghi.recommendation_name',
                'phanbien.score',
                'ra.review_submitted_at'
            )
            ->orderBy('ra.assigned_at', 'desc')
            ->get();

        // Get papers available for bidding
        $availablePapers = DB::table('baibao as b')
            ->join('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
            ->join('vaitronguoidung as vr', function($join) use ($userId) {
                $join->on('h.conference_id', '=', 'vr.conference_id')
                     ->where('vr.user_id', '=', $userId)
                     ->where('vr.role_code', '=', 'REVIEWER');
            })
            ->leftJoin('reviewer_bidding as rb', function($join) use ($userId) {
                $join->on('b.paper_id', '=', 'rb.paper_id')
                     ->where('rb.user_id', '=', $userId);
            })
            ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
            ->whereNull('rb.paper_id') // Only papers not yet bid on
            ->select('b.*', 'h.title as conference_name')
            ->orderBy('b.created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('assignment_status', 'PENDING')->count(),
            'in_progress' => $assignments->where('assignment_status', 'ACCEPTED')->count(),
            'completed' => $assignments->whereNotNull('review_submitted_at')->count(),
        ];

        return view('reviewer.dashboard', [
            'title' => 'Reviewer Dashboard',
            'assignments' => $assignments,
            'availablePapers' => $availablePapers,
            'stats' => $stats
        ]);
    }

    public function chairDashboard()
    {
        // Use authenticated user ID
        $userId = Auth::id();
        $userEmail = Auth::user()->email;

        // Get chair's conference requests and approved conferences
        $conferenceRequests = DB::table('yeucauhoithao')
            ->where('chair_email', $userEmail)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get approved conferences that have been created from requests
        $approvedConferences = DB::table('hoithao')
            ->join('yeucauhoithao', function($join) use ($userEmail) {
                $join->on('hoithao.title', '=', 'yeucauhoithao.title')
                     ->where('yeucauhoithao.chair_email', '=', $userEmail)
                     ->where('yeucauhoithao.status', '=', 'APPROVED');
            })
            ->select('hoithao.*', 'yeucauhoithao.request_id', 'yeucauhoithao.status as request_status')
            ->get();

        // Get the primary conference for this chair (prioritize conference with papers)
        $conference = null;
        /** @var object $conf */
        foreach ($approvedConferences as $conf) {
            $paperCount = DB::table('baibao')->where('conference_id', $conf->conference_id)->count();
            if ($paperCount > 0) {
                $conference = $conf;
                break;
            }
        }
        // Fallback to first approved conference if no papers found
        if (!$conference) {
            $conference = $approvedConferences->first();
        }

        $papers = collect();
        $stats = [
            'total_papers' => 0,
            'accepted' => 0,
            'under_review' => 0,
            'rejected' => 0,
            'needs_reviewers' => 0,
            'total_requests' => $conferenceRequests->count(),
            'approved_conferences' => $approvedConferences->count(),
            'pending_requests' => $conferenceRequests->where('status', 'PENDING')->count()
        ];

        if ($conference) {
            // Store conference_id in session for other features (reports, etc.)
            session(['current_conference_id' => $conference->conference_id]);

            // Get papers for this conference with reviewer assignment counts
            $papers = DB::table('baibao')
                ->where('conference_id', $conference->conference_id)
                ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
                ->join('nguoidung', 'baibao.submitter_id', '=', 'nguoidung.user_id')
                ->leftJoin(DB::raw('(SELECT paper_id, COUNT(*) as reviewer_count,
                    SUM(CASE WHEN status = "PENDING" THEN 1 ELSE 0 END) as pending_reviewers,
                    SUM(CASE WHEN status = "ACCEPTED" THEN 1 ELSE 0 END) as active_reviewers,
                    SUM(CASE WHEN review_submitted_at IS NOT NULL THEN 1 ELSE 0 END) as completed_reviews
                    FROM reviewer_assignments GROUP BY paper_id) as ReviewerCounts'),
                    'baibao.paper_id', '=', 'ReviewerCounts.paper_id')
                ->select(
                    'baibao.paper_id',
                    'baibao.title',
                    'baibao.status_code',
                    'baibao.created_at',
                    'trangthaibaibao.status_name',
                    'nguoidung.full_name as author_name',
                    DB::raw('COALESCE(ReviewerCounts.reviewer_count, 0) as reviewer_count'),
                    DB::raw('COALESCE(ReviewerCounts.pending_reviewers, 0) as pending_reviewers'),
                    DB::raw('COALESCE(ReviewerCounts.active_reviewers, 0) as active_reviewers'),
                    DB::raw('COALESCE(ReviewerCounts.completed_reviews, 0) as completed_reviews')
                )
                ->orderBy('baibao.created_at', 'desc')
                ->get();

            // Calculate statistics
            $stats = [
                'total_papers' => $papers->count(),
                'accepted' => $papers->where('status_code', 'ACCEPTED')->count(),
                'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
                'rejected' => $papers->where('status_code', 'REJECTED')->count(),
                'needs_reviewers' => $papers->where('reviewer_count', '<', 3)->count(),
                'total_reviewers' => $papers->sum('reviewer_count'),
                'pending_reviewers' => $papers->sum('pending_reviewers'),
                'active_reviewers' => $papers->sum('active_reviewers'),
                'completed_reviews' => $papers->sum('completed_reviews'),
                'total_requests' => $conferenceRequests->count(),
                'approved_conferences' => $approvedConferences->count(),
                'pending_requests' => $conferenceRequests->where('status', 'PENDING')->count()
            ];
        }

        return view('chair.dashboard', [
            'title' => 'Chair Dashboard',
            'conference' => $conference,
            'papers' => $papers,
            'stats' => $stats,
            'conferenceRequests' => $conferenceRequests,
            'approvedConferences' => $approvedConferences
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
            'pending_requests' => DB::table('join_requests')->where('status', 'PENDING')->count(),
        ];

        // Get recent papers
        $recentPapers = DB::table('baibao')
            ->join('nguoidung', 'baibao.submitter_id', '=', 'nguoidung.user_id')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
            ->select(
                'baibao.paper_id',
                'baibao.title',
                'baibao.created_at',
                'nguoidung.full_name as author_name',
                'hoithao.title as conference_title',
                'trangthaibaibao.status_name',
                'trangthaibaibao.status_code'
            )
            ->orderBy('baibao.created_at', 'desc')
            ->limit(5)
            ->get();

        // Get user role distribution from VaiTroNguoiDung table
        $userRoles = DB::table('vaitronguoidung')
            ->select('role_code as role', DB::raw('count(distinct user_id) as count'))
            ->groupBy('role_code')
            ->get();

        // Add count for users with no roles (USER)
        $usersWithoutRoles = DB::table('nguoidung')
            ->leftjoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
            ->whereNull('vaitronguoidung.user_id')
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

        // Get pending join requests for review (yêu cầu tham gia hội thảo)
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

        // Get recent system logs from ActivityLog
        $recentLogs = DB::table('activity_logs')
            ->leftjoin('nguoidung', 'activity_logs.user_id', '=', 'nguoidung.user_id')
            ->select(
                'activity_logs.*',
                'nguoidung.full_name as user_name',
                'nguoidung.email as user_email'
            )
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(8)
            ->get();

        // Get pending conference organization requests (yêu cầu tổ chức hội thảo)
        $pendingConferenceRequests = DB::table('yeucauhoithao')
            ->where('status', 'PENDING')
            ->select('request_id', 'title', 'chair_fullname', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentPapers' => $recentPapers,
            'userRoles' => $userRoles,
            'joinRequestStats' => $joinRequestStats,
            'pendingJoinRequests' => $pendingJoinRequests,
            'recentLogs' => $recentLogs,
            'pendingConferenceRequests' => $pendingConferenceRequests
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
                ->leftjoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
                ->select(
                    'nguoidung.user_id',
                    'nguoidung.full_name',
                    'nguoidung.email',
                    'nguoidung.email_verified_at',
                    'vaitronguoidung.role_code'
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
            $oldRoles = DB::table('vaitronguoidung')
                ->where('user_id', $id)
                ->pluck('role_code')
                ->toArray();

            // Update role - xóa tất cả vai trò cũ trước khi thêm vai trò mới
            DB::table('vaitronguoidung')
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
            $isAdmin = DB::table('vaitronguoidung')
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
            DB::table('vaitronguoidung')->where('user_id', $id)->delete();

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
        $roleStats = DB::table('vaitronguoidung')
            ->select('role_code as role', DB::raw('COUNT(DISTINCT user_id) as count'))
            ->groupBy('role_code')
            ->get();

        // Add count for users with no roles (USER)
        $usersWithoutRoles = DB::table('nguoidung')
            ->leftjoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
            ->whereNull('vaitronguoidung.user_id')
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
                ->leftjoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
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
            DB::table('vaitronguoidung')->where('user_id', $id)->delete();

            // Add new role
            DB::table('vaitronguoidung')->insert([
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
            DB::table('vaitronguoidung')->whereIn('user_id', $userIds)->delete();

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
            $conference = DB::table('hoithao')
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
            'conference_ids.*' => 'integer|exists:hoithao,conference_id'
        ]);

        DB::beginTransaction();
        try {
            $conferenceIds = $request->conference_ids;

            // Delete related records first (papers, assignments, etc.)
            DB::table('baibao')->whereIn('conference_id', $conferenceIds)->delete();
            DB::table('phancong')->whereIn('conference_id', $conferenceIds)->delete();

            // Delete conferences
            $deleted = DB::table('hoithao')->whereIn('conference_id', $conferenceIds)->delete();

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





