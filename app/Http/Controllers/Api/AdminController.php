<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\VaiTroNguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * 1. List All Users
     * GET /api/admin/users
     * 
     * Permission: Admin only
     */
    public function listUsers(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Check admin permission
            if (!$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Build query
            $query = NguoiDung::query();

            // Search by email or name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                      ->orWhere('full_name', 'LIKE', "%{$search}%")
                      ->orWhere('organization', 'LIKE', "%{$search}%");
                });
            }

            // Filter by locked status
            if ($request->has('locked')) {
                $query->where('locked', $request->locked == 'true' ? 1 : 0);
            }

            // Filter by student status
            if ($request->has('is_student')) {
                $query->where('is_student', $request->is_student == 'true' ? 1 : 0);
            }

            // Filter by role
            if ($request->has('role')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('role_code', $request->role);
                });
            }

            // Order by
            $orderBy = $request->get('order_by', 'created_at');
            $orderDir = $request->get('order_dir', 'desc');
            $query->orderBy($orderBy, $orderDir);

            // Paginate
            $perPage = $request->get('per_page', 20);
            $users = $query->paginate($perPage);

            // Add roles to each user
            $users->getCollection()->transform(function($user) {
                $roles = VaiTroNguoiDung::where('user_id', $user->user_id)
                    ->get()
                    ->map(function($role) {
                        return [
                            'role_code' => $role->role_code,
                            'conference_id' => $role->conference_id,
                        ];
                    });
                
                $user->roles = $roles;
                return $user;
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. Update User
     * PUT /api/admin/users/{id}
     * 
     * Permission: Admin only
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = auth()->user();
            
            // Check admin permission
            if (!$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Find user
            $targetUser = NguoiDung::find($id);
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|string|max:200',
                'organization' => 'sometimes|string|max:255',
                'is_student' => 'sometimes|boolean',
                'faculty_id' => 'sometimes|nullable|exists:Khoa,faculty_id',
                'locked' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prevent self-lock
            if ($request->has('locked') && $request->locked && $targetUser->user_id == $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot lock your own account'
                ], 422);
            }

            // Update user
            $updateData = $request->only(['full_name', 'organization', 'is_student', 'faculty_id', 'locked']);
            $targetUser->update($updateData);

            // Load roles
            $roles = VaiTroNguoiDung::where('user_id', $targetUser->user_id)->get();
            $targetUser->roles = $roles;

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $targetUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. Assign/Revoke Role
     * POST /api/admin/users/{id}/roles
     * 
     * Permission: Admin only
     */
    public function manageRoles(Request $request, $id)
    {
        try {
            $user = auth()->user();
            
            // Check admin permission
            if (!$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Find user
            $targetUser = NguoiDung::find($id);
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:assign,revoke',
                'role_code' => 'required|in:ADMIN,CHAIR,REVIEWER',
                'conference_id' => 'nullable|exists:hoithao,conference_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $action = $request->action;
            $roleCode = $request->role_code;
            $conferenceId = $request->conference_id;

            if ($action === 'assign') {
                // Check if role already exists
                $exists = VaiTroNguoiDung::where('user_id', $id)
                    ->where('role_code', $roleCode)
                    ->where('conference_id', $conferenceId)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User already has this role'
                    ], 422);
                }

                // Assign role
                VaiTroNguoiDung::create([
                    'user_id' => $id,
                    'role_code' => $roleCode,
                    'conference_id' => $conferenceId,
                ]);

                $message = "Role {$roleCode} assigned successfully";

            } else { // revoke
                // Prevent self-revoke admin
                if ($roleCode === 'ADMIN' && $targetUser->user_id == $user->user_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot revoke your own admin role'
                    ], 422);
                }

                // Revoke role
                $deleted = VaiTroNguoiDung::where('user_id', $id)
                    ->where('role_code', $roleCode)
                    ->where('conference_id', $conferenceId)
                    ->delete();

                if (!$deleted) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Role not found'
                    ], 404);
                }

                $message = "Role {$roleCode} revoked successfully";
            }

            // Load updated roles
            $roles = VaiTroNguoiDung::where('user_id', $targetUser->user_id)->get();
            $targetUser->roles = $roles;

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'user' => $targetUser,
                    'roles' => $roles
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error managing roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 4. Conference Report
     * GET /api/admin/reports/conference/{id}
     * 
     * Permission: Admin, Chair
     */
    public function conferenceReport($id)
    {
        try {
            $user = auth()->user();
            
            // Check permission (Admin or Chair of conference)
            if (!$this->isAdmin($user) && !$this->isChairOfConference($user, $id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Chair access required.'
                ], 403);
            }

            // Get conference
            $conference = DB::table('hoithao')->where('conference_id', $id)->first();
            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conference not found'
                ], 404);
            }

            // Papers statistics
            $papersStats = DB::table('baibao')
                ->where('conference_id', $id)
                ->select(
                    DB::raw('COUNT(*) as total_papers'),
                    DB::raw('SUM(CASE WHEN status_code = "SUBMITTED" THEN 1 ELSE 0 END) as submitted'),
                    DB::raw('SUM(CASE WHEN status_code = "UNDER_REVIEW" THEN 1 ELSE 0 END) as under_review'),
                    DB::raw('SUM(CASE WHEN status_code = "ACCEPTED" THEN 1 ELSE 0 END) as accepted'),
                    DB::raw('SUM(CASE WHEN status_code = "REJECTED" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status_code = "WITHDRAWN" THEN 1 ELSE 0 END) as withdrawn')
                )
                ->first();

            // Assignments statistics
            $assignmentsStats = DB::table('PhanCongPhanBien as pcp')
                ->join('BaiBao as bb', 'pcp.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $id)
                ->select(
                    DB::raw('COUNT(*) as total_assignments'),
                    DB::raw('SUM(CASE WHEN pcp.status_code = "INVITED" THEN 1 ELSE 0 END) as invited'),
                    DB::raw('SUM(CASE WHEN pcp.status_code = "ACCEPTED" THEN 1 ELSE 0 END) as accepted'),
                    DB::raw('SUM(CASE WHEN pcp.status_code = "DECLINED" THEN 1 ELSE 0 END) as declined'),
                    DB::raw('SUM(CASE WHEN pcp.status_code = "REVIEWED" THEN 1 ELSE 0 END) as reviewed')
                )
                ->first();

            // Reviews statistics
            $reviewsStats = DB::table('PhanBien as pb')
                ->join('PhanCongPhanBien as pcp', 'pb.assignment_id', '=', 'pcp.assignment_id')
                ->join('BaiBao as bb', 'pcp.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $id)
                ->select(
                    DB::raw('COUNT(*) as total_reviews'),
                    DB::raw('AVG(pb.overall_rating) as avg_rating'),
                    DB::raw('AVG(pb.confidence_level) as avg_confidence'),
                    DB::raw('SUM(CASE WHEN pb.recommendation_code = "ACCEPT" THEN 1 ELSE 0 END) as recommend_accept'),
                    DB::raw('SUM(CASE WHEN pb.recommendation_code = "MINOR_REVISION" THEN 1 ELSE 0 END) as recommend_minor'),
                    DB::raw('SUM(CASE WHEN pb.recommendation_code = "MAJOR_REVISION" THEN 1 ELSE 0 END) as recommend_major'),
                    DB::raw('SUM(CASE WHEN pb.recommendation_code = "REJECT" THEN 1 ELSE 0 END) as recommend_reject')
                )
                ->first();

            // COI statistics
            $coiStats = DB::table('COI as c')
                ->join('BaiBao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $id)
                ->select(
                    DB::raw('COUNT(*) as total_cois'),
                    DB::raw('SUM(CASE WHEN c.resolution_status = "PENDING" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN c.resolution_status = "CONFIRMED" THEN 1 ELSE 0 END) as confirmed'),
                    DB::raw('SUM(CASE WHEN c.resolution_status = "REJECTED" THEN 1 ELSE 0 END) as rejected')
                )
                ->first();

            // Bidding statistics
            $biddingStats = DB::table('Bidding as bid')
                ->join('BaiBao as bb', 'bid.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $id)
                ->select(
                    DB::raw('COUNT(*) as total_biddings'),
                    DB::raw('SUM(CASE WHEN bid.bidding_code = "EAGER" THEN 1 ELSE 0 END) as eager'),
                    DB::raw('SUM(CASE WHEN bid.bidding_code = "WILLING" THEN 1 ELSE 0 END) as willing'),
                    DB::raw('SUM(CASE WHEN bid.bidding_code = "NEUTRAL" THEN 1 ELSE 0 END) as neutral'),
                    DB::raw('SUM(CASE WHEN bid.bidding_code = "UNWILLING" THEN 1 ELSE 0 END) as unwilling'),
                    DB::raw('SUM(CASE WHEN bid.bidding_code = "CONFLICT" THEN 1 ELSE 0 END) as conflict')
                )
                ->first();

            // Top reviewers
            $topReviewers = DB::table('PhanBien as pb')
                ->join('PhanCongPhanBien as pcp', 'pb.assignment_id', '=', 'pcp.assignment_id')
                ->join('BaiBao as bb', 'pcp.paper_id', '=', 'bb.paper_id')
                ->join('NguoiDung as u', 'pcp.reviewer_id', '=', 'u.user_id')
                ->where('bb.conference_id', $id)
                ->select(
                    'u.user_id',
                    'u.full_name',
                    'u.email',
                    'u.organization',
                    DB::raw('COUNT(*) as reviews_count'),
                    DB::raw('AVG(pb.overall_rating) as avg_rating')
                )
                ->groupBy('u.user_id', 'u.full_name', 'u.email', 'u.organization')
                ->orderByDesc('reviews_count')
                ->limit(10)
                ->get();

            // Papers needing attention
            $papersNeedingAttention = DB::table('BaiBao as bb')
                ->leftJoin('PhanCongPhanBien as pcp', 'bb.paper_id', '=', 'pcp.paper_id')
                ->where('bb.conference_id', $id)
                ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                ->select(
                    'bb.paper_id',
                    'bb.title',
                    'bb.status_code',
                    'bb.submitted_at',
                    DB::raw('COUNT(pcp.assignment_id) as reviewers_assigned'),
                    DB::raw('SUM(CASE WHEN pcp.status_code = "REVIEWED" THEN 1 ELSE 0 END) as reviews_completed')
                )
                ->groupBy('bb.paper_id', 'bb.title', 'bb.status_code', 'bb.submitted_at')
                ->having('reviewers_assigned', '<', 3)
                ->orderBy('bb.submitted_at')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'conference' => [
                        'conference_id' => $conference->conference_id,
                        'title' => $conference->title,
                        'acronym' => $conference->acronym,
                        'start_date' => $conference->start_date,
                        'end_date' => $conference->end_date,
                        'status_code' => $conference->status_code,
                    ],
                    'papers' => $papersStats,
                    'assignments' => $assignmentsStats,
                    'reviews' => $reviewsStats,
                    'cois' => $coiStats,
                    'biddings' => $biddingStats,
                    'top_reviewers' => $topReviewers,
                    'papers_needing_attention' => $papersNeedingAttention,
                    'completion_rate' => $assignmentsStats->total_assignments > 0 
                        ? round(($assignmentsStats->reviewed / $assignmentsStats->total_assignments) * 100, 2)
                        : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating conference report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. System Overview Report
     * GET /api/admin/reports/overview
     * 
     * Permission: Admin only
     */
    public function systemOverview()
    {
        try {
            $user = auth()->user();
            
            // Check admin permission
            if (!$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Total counts
            $totalUsers = DB::table('nguoidung')->count();
            $totalConferences = DB::table('hoithao')->count();
            $totalPapers = DB::table('baibao')->count();
            $totalReviews = DB::table('phanbien')->count();

            // Active conferences
            $activeConferences = DB::table('hoithao')
                ->where('status_code', 'ACTIVE')
                ->count();

            // Papers by status
            $papersByStatus = DB::table('baibao')
                ->select('status_code', DB::raw('COUNT(*) as count'))
                ->groupBy('status_code')
                ->get();

            // Users by role
            $usersByRole = DB::table('vaitronguoidung')
                ->select('role_code', DB::raw('COUNT(DISTINCT user_id) as count'))
                ->groupBy('role_code')
                ->get();

            // Recent activity (last 30 days)
            $recentActivity = [
                'new_users' => DB::table('nguoidung')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'new_papers' => DB::table('baibao')
                    ->where('submitted_at', '>=', now()->subDays(30))
                    ->count(),
                'new_reviews' => DB::table('phanbien')
                    ->where('submitted_at', '>=', now()->subDays(30))
                    ->count(),
            ];

            // Top conferences by papers
            $topConferences = DB::table('HoiThao as h')
                ->leftJoin('BaiBao as b', 'h.conference_id', '=', 'b.conference_id')
                ->select(
                    'h.conference_id',
                    'h.title',
                    'h.acronym',
                    'h.start_date',
                    'h.status_code',
                    DB::raw('COUNT(b.paper_id) as papers_count')
                )
                ->groupBy('h.conference_id', 'h.title', 'h.acronym', 'h.start_date', 'h.status_code')
                ->orderByDesc('papers_count')
                ->limit(5)
                ->get();

            // Review completion rate
            $totalAssignments = DB::table('phancongphanbien')->count();
            $completedReviews = DB::table('phancongphanbien')
                ->where('status_code', 'REVIEWED')
                ->count();
            
            $reviewCompletionRate = $totalAssignments > 0 
                ? round(($completedReviews / $totalAssignments) * 100, 2)
                : 0;

            // System health indicators
            $systemHealth = [
                'total_users' => $totalUsers,
                'active_conferences' => $activeConferences,
                'papers_under_review' => DB::table('baibao')->where('status_code', 'UNDER_REVIEW')->count(),
                'pending_assignments' => DB::table('phancongphanbien')->where('status_code', 'INVITED')->count(),
                'pending_cois' => DB::table('coi')->where('resolution_status', 'PENDING')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'totals' => [
                        'users' => $totalUsers,
                        'conferences' => $totalConferences,
                        'papers' => $totalPapers,
                        'reviews' => $totalReviews,
                        'active_conferences' => $activeConferences,
                    ],
                    'papers_by_status' => $papersByStatus,
                    'users_by_role' => $usersByRole,
                    'recent_activity' => $recentActivity,
                    'top_conferences' => $topConferences,
                    'review_completion_rate' => $reviewCompletionRate,
                    'system_health' => $systemHealth,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating system overview: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function isAdmin($user)
    {
        return VaiTroNguoiDung::where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();
    }

    private function isChairOfConference($user, $conferenceId)
    {
        return VaiTroNguoiDung::where('user_id', $user->user_id)
            ->where('role_code', 'CHAIR')
            ->where('conference_id', $conferenceId)
            ->exists();
    }
}




