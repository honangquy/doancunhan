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
                'h.status',
                DB::raw('COUNT(b.paper_id) as paper_count')
            )
            ->groupBy('h.conference_id', 'h.title', 'h.start_date', 'h.end_date', 'h.deadline_submission', 'h.status')
            ->orderBy('h.conference_id', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($conference) {
                // Determine conference status
                $now = now();
                $submissionDeadline = $conference->deadline_submission ? Carbon::parse($conference->deadline_submission) : null;
                $startDate = Carbon::parse($conference->start_date);
                
                if ($submissionDeadline && $now->lt($submissionDeadline)) {
                    $conference->status_text = 'Đang mở';
                    $conference->status_class = 'bg-green-100 text-green-800';
                } elseif ($now->lt($startDate)) {
                    $conference->status_text = 'Hết hạn nộp';
                    $conference->status_class = 'bg-orange-100 text-orange-800';
                } else {
                    $conference->status_text = 'Đã kết thúc';
                    $conference->status_class = 'bg-gray-100 text-gray-800';
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
}