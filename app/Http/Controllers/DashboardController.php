<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'author';

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
            'total_users' => DB::table('NguoiDung')->count(),
            'locked_users' => DB::table('NguoiDung')->where('locked', 1)->count(),
            'total_conferences' => DB::table('HoiThao')->count(),
            'active_conferences' => DB::table('HoiThao')->where('status', 'ACTIVE')->count(),
            'total_papers' => DB::table('BaiBao')->count(),
            'total_reviews' => DB::table('PhanBien')->count(),
        ];
        
        // Get recent papers
        $recentPapers = DB::table('BaiBao')
            ->join('NguoiDung', 'BaiBao.submitter_id', '=', 'NguoiDung.user_id')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
            ->select(
                'BaiBao.paper_id',
                'BaiBao.title',
                'BaiBao.created_at',
                'NguoiDung.full_name as author',
                'HoiThao.title as conference',
                'TrangThaiBaiBao.status_name'
            )
            ->orderBy('BaiBao.created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get user role distribution
        $userRoles = DB::table('VaiTroNguoiDung')
            ->join('LoaiVaiTro', 'VaiTroNguoiDung.role_code', '=', 'LoaiVaiTro.role_code')
            ->select('LoaiVaiTro.role_name', DB::raw('count(*) as count'))
            ->groupBy('LoaiVaiTro.role_name', 'LoaiVaiTro.role_code')
            ->get();
        
        return view('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentPapers' => $recentPapers,
            'userRoles' => $userRoles
        ]);
    }
}

