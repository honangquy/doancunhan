<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Get statistics for homepage hero section
        $statistics = $this->getStatistics();
        
        // Get recent conferences for featured section  
        $recentConferences = $this->getRecentConferences();
        
        // Get recent papers with authors
        $recentPapers = $this->getRecentPapers();
        
        // Get user-specific data if authenticated
        $userData = Auth::check() ? $this->getUserData() : null;
        
        return view('home', [
            'title' => 'Trang chủ - HUIT Conferences',
            'statistics' => $statistics,
            'recentConferences' => $recentConferences,
            'recentPapers' => $recentPapers,
            'userData' => $userData
        ]);
    }
    
    /**
     * Search conferences via AJAX
     */
    public function searchConferences(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $sortBy = $request->get('sortBy', 'year');
        $sortOrder = $request->get('sortOrder', 'desc');
        
        $query = DB::table('HoiThao as h')
            ->leftJoin('BaiBao as b', 'h.conference_id', '=', 'b.conference_id')
            ->select(
                'h.conference_id',
                'h.title',
                'h.start_date', 
                'h.end_date',
                'h.deadline_submission',
                'h.year',
                'h.status',
                DB::raw('COUNT(b.paper_id) as paper_count')
            )
            ->groupBy('h.conference_id', 'h.title', 'h.start_date', 'h.end_date', 'h.deadline_submission', 'h.year', 'h.status');
            
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('h.title', 'LIKE', "%{$search}%")
                  ->orWhere('h.description', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($status !== 'all') {
            $now = now();
            switch ($status) {
                case 'open':
                    $query->where('h.deadline_submission', '>', $now);
                    break;
                case 'closed':
                    $query->where('h.deadline_submission', '<=', $now)
                          ->where('h.start_date', '>', $now);
                    break;
                case 'ended':
                    $query->where('h.start_date', '<=', $now);
                    break;
            }
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'title':
                $query->orderBy('h.title', $sortOrder);
                break;
            case 'deadline':
                $query->orderBy('h.deadline_submission', $sortOrder);
                break;
            case 'papers':
                $query->orderBy('paper_count', $sortOrder);
                break;
            default:
                $query->orderBy('h.year', $sortOrder)
                      ->orderBy('h.conference_id', 'desc');
        }
        
        $conferences = $query->limit(20)->get()
            ->map(function ($conference) {
                // Determine conference status
                $now = now();
                $submissionDeadline = $conference->deadline_submission ? Carbon::parse($conference->deadline_submission) : null;
                $startDate = $conference->start_date ? Carbon::parse($conference->start_date) : null;
                
                if ($submissionDeadline && $now->lt($submissionDeadline)) {
                    $conference->status_display = 'open';
                    $conference->status_text = 'Đang mở';
                    $conference->status_class = 'bg-green-500 text-white';
                } elseif ($startDate && $now->lt($startDate)) {
                    $conference->status_display = 'closed'; 
                    $conference->status_text = 'Hết hạn nộp';
                    $conference->status_class = 'bg-orange-500 text-white';
                } else {
                    $conference->status_display = 'ended';
                    $conference->status_text = 'Đã kết thúc'; 
                    $conference->status_class = 'bg-gray-500 text-white';
                }
                
                $conference->formatted_dates = $this->formatConferenceDates($conference);
                return $conference;
            });
            
        return response()->json([
            'conferences' => $conferences,
            'total' => $conferences->count()
        ]);
    }
    
    /**
     * Get conference counts by status for filter buttons
     */
    public function getConferenceCounts()
    {
        $now = now();
        
        $counts = [
            'all' => DB::table('HoiThao')->count(),
            'open' => DB::table('HoiThao')->where('deadline_submission', '>', $now)->count(),
            'closed' => DB::table('HoiThao')
                ->where('deadline_submission', '<=', $now)
                ->where('start_date', '>', $now)
                ->count(),
            'ended' => DB::table('HoiThao')->where('start_date', '<=', $now)->count()
        ];
        
        return response()->json($counts);
    }
    
    /**
     * Get system statistics for homepage hero section
     */
    private function getStatistics()
    {
        return [
            'totalConferences' => DB::table('HoiThao')->count(),
            'totalPapers' => DB::table('BaiBao')->count(),
            'totalAuthors' => DB::table('VaiTroNguoiDung')
                ->join('LoaiVaiTro', 'VaiTroNguoiDung.role_code', '=', 'LoaiVaiTro.role_code')
                ->where('LoaiVaiTro.role_code', 'AUTHOR')
                ->distinct('user_id')
                ->count(),
            'totalReviewers' => DB::table('VaiTroNguoiDung')
                ->join('LoaiVaiTro', 'VaiTroNguoiDung.role_code', '=', 'LoaiVaiTro.role_code')
                ->where('LoaiVaiTro.role_code', 'REVIEWER')
                ->distinct('user_id')
                ->count(),
            'totalReviews' => DB::table('PhanBien')->count(),
            'activeConferences' => DB::table('HoiThao')
                ->where('start_date', '>', now())
                ->count()
        ];
    }
    
    /**
     * Get recent conferences for featured section
     */
    private function getRecentConferences()
    {
        return DB::table('HoiThao as h')
            ->leftJoin('BaiBao as b', 'h.conference_id', '=', 'b.conference_id')
            ->select(
                'h.conference_id',
                'h.title',
                'h.start_date',
                'h.end_date',
                'h.deadline_submission',
                'h.year',
                'h.status',
                DB::raw('COUNT(b.paper_id) as paper_count')
            )
            ->groupBy('h.conference_id', 'h.title', 'h.start_date', 'h.end_date', 'h.deadline_submission', 'h.year', 'h.status')
            ->orderBy('h.year', 'desc')
            ->orderBy('h.conference_id', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($conference) {
                // Determine conference status
                $now = now();
                $submissionDeadline = $conference->deadline_submission ? Carbon::parse($conference->deadline_submission) : null;
                $startDate = $conference->start_date ? Carbon::parse($conference->start_date) : null;
                
                if ($submissionDeadline && $now->lt($submissionDeadline)) {
                    $conference->status_display = 'open';
                    $conference->status_text = 'Đang mở';
                    $conference->status_class = 'bg-green-500 text-white';
                } elseif ($startDate && $now->lt($startDate)) {
                    $conference->status_display = 'closed';
                    $conference->status_text = 'Hết hạn nộp';
                    $conference->status_class = 'bg-orange-500 text-white';
                } else {
                    $conference->status_display = 'ended';
                    $conference->status_text = 'Đã kết thúc';
                    $conference->status_class = 'bg-gray-500 text-white';
                }
                
                return $conference;
            });
    }
    
    /**
     * Get recent papers for news section
     */
    private function getRecentPapers()
    {
        return DB::table('BaiBao as b')
            ->join('NguoiDung as u', 'b.submitter_id', '=', 'u.user_id')
            ->join('HoiThao as h', 'b.conference_id', '=', 'h.conference_id')
            ->select(
                'b.paper_id',
                'b.title as paper_title',
                'b.abstract',
                'b.created_at as submitted_at',
                'u.full_name as author_name',
                'h.title as conference_title'
            )
            ->orderBy('b.created_at', 'desc')
            ->limit(3)
            ->get();
    }
    
    /**
     * Get user-specific data when authenticated
     */
    private function getUserData()
    {
        $userId = Auth::id();
        
        // Get user's role information
        $userRoles = DB::table('VaiTroNguoiDung as vt')
            ->join('LoaiVaiTro as lt', 'vt.role_code', '=', 'lt.role_code')
            ->where('vt.user_id', $userId)
            ->select('lt.role_code', 'lt.role_name')
            ->get();
            
        // Get user's papers if author
        $userPapers = DB::table('BaiBao')
            ->where('submitter_id', $userId)
            ->count();
            
        // Get user's assignments if reviewer
        $userAssignments = DB::table('PhanCongPhanBien')
            ->where('reviewer_id', $userId)
            ->count();
            
        return [
            'roles' => $userRoles,
            'paperCount' => $userPapers,
            'assignmentCount' => $userAssignments,
            'dashboardUrl' => $this->getDashboardUrl($userRoles->first()->role_code ?? 'AUTHOR')
        ];
    }
    
    /**
     * Get appropriate dashboard URL based on user role
     */
    private function getDashboardUrl($roleCode)
    {
        $roleMap = [
            'AUTHOR' => route('author.dashboard'),
            'REVIEWER' => route('reviewer.dashboard'),
            'CHAIR' => route('chair.dashboard'),
            'ADMIN' => route('admin.dashboard')
        ];
        
        return $roleMap[$roleCode] ?? route('author.dashboard');
    }

    public function conferences()
    {
        return view('conferences.index', [
            'title' => 'Danh sách Hội thảo'
        ]);
    }

    public function conferenceDetail($id)
    {
        return view('conferences.show', [
            'title' => 'Chi tiết Hội thảo',
            'conferenceId' => $id
        ]);
    }

    public function news()
    {
        // Get user-specific data if authenticated
        $userData = Auth::check() ? $this->getUserData() : null;
        
        return view('news.index', [
            'title' => 'Tin tức & Sự kiện',
            'userData' => $userData
        ]);
    }

    public function process()
    {
        // Get user-specific data if authenticated
        $userData = Auth::check() ? $this->getUserData() : null;
        
        return view('process', [
            'title' => 'Quy trình',
            'userData' => $userData
        ]);
    }

    public function support()
    {
        // Get user-specific data if authenticated
        $userData = Auth::check() ? $this->getUserData() : null;
        
        return view('support', [
            'title' => 'Hỗ trợ',
            'userData' => $userData
        ]);
    }
    
    /**
     * Get user notifications via AJAX
     */
    public function getNotifications(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['notifications' => [], 'unreadCount' => 0]);
        }
        
        $userId = Auth::id();
        $limit = $request->get('limit', 10);
        
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
            
        $unreadCount = DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
            
        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => json_decode($notification->data ?? '{}', true),
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'time_ago' => $this->timeAgo($notification->created_at)
                ];
            }),
            'unreadCount' => $unreadCount
        ]);
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        
        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => $updated > 0]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        
        $updated = DB::table('notifications')
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true, 'marked' => $updated]);
    }
    
    /**
     * Create sample notifications for testing
     */
    public function createSampleNotifications()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        
        $userId = Auth::id();
        $sampleNotifications = [
            [
                'user_id' => $userId,
                'type' => 'paper_submitted',
                'title' => 'Bài báo được nộp thành công',
                'message' => 'Bài báo "Nghiên cứu về AI trong giáo dục" đã được nộp thành công vào hội thảo HUIT 2025.',
                'data' => json_encode(['paper_id' => 1, 'conference_id' => 1]),
                'created_at' => now()->subMinutes(5)
            ],
            [
                'user_id' => $userId,
                'type' => 'review_assigned',
                'title' => 'Được phân công phản biện',
                'message' => 'Bạn đã được phân công phản biện bài báo "Machine Learning trong IoT".',
                'data' => json_encode(['paper_id' => 2, 'assignment_id' => 15]),
                'created_at' => now()->subHours(2)
            ],
            [
                'user_id' => $userId,
                'type' => 'deadline_reminder',
                'title' => 'Sắp hết hạn nộp bài',
                'message' => 'Hội thảo HUIT 2025 sẽ hết hạn nộp bài vào ngày 15/10/2025.',
                'data' => json_encode(['conference_id' => 1, 'deadline' => '2025-10-15']),
                'created_at' => now()->subHours(6)
            ]
        ];
        
        foreach ($sampleNotifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
        
        return response()->json(['success' => true, 'created' => count($sampleNotifications)]);
    }
    
    /**
     * Helper function to format time ago
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'Vừa xong';
        } elseif ($time < 3600) {
            return floor($time/60) . ' phút trước';
        } elseif ($time < 86400) {
            return floor($time/3600) . ' giờ trước';
        } elseif ($time < 2592000) {
            return floor($time/86400) . ' ngày trước';
        } else {
            return date('d/m/Y', strtotime($datetime));
        }
    }
}
