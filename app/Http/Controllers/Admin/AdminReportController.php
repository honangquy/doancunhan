<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NguoiDung;
use App\Models\HoiThao;
use App\Models\BaiBao;
use App\Models\ReviewerAssignment;
use App\Models\TieuBan;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    /**
     * Hiển thị trang Báo cáo & Thống kê
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Lấy filter parameters
        $year = $request->input('year');
        $facultyId = $request->input('faculty');
        $level = $request->input('level');
        $status = $request->input('status');

        // ===== THỐNG KÊ TỔNG QUAN =====
        
        // Tổng số người dùng hệ thống
        $totalUsers = NguoiDung::count();
        
        // Tổng số hội thảo (có thể filter theo năm)
        $totalConferences = HoiThao::when($year, function($query) use ($year) {
            return $query->where('year', $year);
        })->count();
        
        // Tổng số bài báo đã nộp (có thể filter)
        $totalPapers = BaiBao::when($year, function($query) use ($year) {
            return $query->whereHas('hoiThao', function($q) use ($year) {
                $q->where('year', $year);
            });
        })->count();
        
        // Tổng số lượt đánh giá/phản biện
        $totalReviews = ReviewerAssignment::whereIn('status', [
            ReviewerAssignment::STATUS_COMPLETED,
            ReviewerAssignment::STATUS_ACCEPTED
        ])->count();
        
        // Số bài đang trong quá trình phản biện (status = UNDER_REVIEW hoặc tương tự)
        $papersUnderReview = BaiBao::whereIn('status_code', ['UNDER_REVIEW', 'REVIEWING', 'IN_REVIEW'])
            ->count();
        
        // Số bài đã được chấp nhận
        $papersAccepted = BaiBao::whereIn('status_code', ['ACCEPTED', 'APPROVED'])
            ->count();
        
        // Số reviewer đang hoạt động (có ít nhất một assignment)
        $activeReviewers = ReviewerAssignment::distinct('user_id')->count('user_id');
        
        // Số kỷ yếu đã xuất bản (nếu có, giả sử có field published trong hoithao hoặc bảng riêng)
        // Tạm thời đếm hội thảo có status = COMPLETED/PUBLISHED
        $publishedProceedings = HoiThao::whereIn('status', ['COMPLETED', 'PUBLISHED'])
            ->count();

        // ===== DỮ LIỆU CHO BIỂU ĐỒ =====
        
        // 1. Thống kê số hội thảo theo năm (5 năm gần nhất)
        $currentYear = Carbon::now()->year;
        $conferencesByYear = HoiThao::selectRaw('year, COUNT(*) as total')
            ->whereBetween('year', [$currentYear - 4, $currentYear])
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        
        // Chuẩn bị data cho chart
        $yearsData = [];
        $conferenceCountsData = [];
        for ($i = $currentYear - 4; $i <= $currentYear; $i++) {
            $yearsData[] = $i;
            $found = $conferencesByYear->firstWhere('year', $i);
            $conferenceCountsData[] = $found ? $found->total : 0;
        }
        
        // 2. Phân bố bài báo theo track/tiểu ban
        $papersByTrack = BaiBao::join('tieuban', 'baibao.track_id', '=', 'tieuban.track_id')
            ->selectRaw('tieuban.title, COUNT(baibao.paper_id) as total')
            ->groupBy('tieuban.track_id', 'tieuban.title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        $trackNames = $papersByTrack->pluck('title')->toArray();
        $trackCounts = $papersByTrack->pluck('total')->toArray();
        
        // 3. Tiến độ phản biện theo tháng (12 tháng gần nhất)
        $reviewsByMonth = ReviewerAssignment::selectRaw('DATE_FORMAT(review_submitted_at, "%Y-%m") as month, COUNT(*) as total')
            ->where('status', ReviewerAssignment::STATUS_COMPLETED)
            ->whereNotNull('review_submitted_at')
            ->where('review_submitted_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Chuẩn bị data cho 12 tháng
        $monthsData = [];
        $reviewCountsData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $monthsData[] = Carbon::now()->subMonths($i)->format('M Y');
            $found = $reviewsByMonth->firstWhere('month', $month);
            $reviewCountsData[] = $found ? $found->total : 0;
        }
        
        // 4. Phân bố trạng thái bài báo
        $papersByStatus = BaiBao::selectRaw('status_code, COUNT(*) as total')
            ->groupBy('status_code')
            ->get();
        
        $statusLabels = [];
        $statusCounts = [];
        foreach ($papersByStatus as $item) {
            // Map status code sang label tiếng Việt
            $label = $this->getStatusLabel($item->status_code);
            $statusLabels[] = $label;
            $statusCounts[] = $item->total;
        }
        
        // ===== BẢNG CHI TIẾT =====
        
        // Top 10 Reviewer (theo số lượt review hoàn thành)
        $topReviewers = ReviewerAssignment::select('user_id', DB::raw('COUNT(*) as review_count'))
            ->where('status', ReviewerAssignment::STATUS_COMPLETED)
            ->groupBy('user_id')
            ->orderByDesc('review_count')
            ->limit(10)
            ->with('reviewer:user_id,full_name,email')
            ->get();
        
        // Top 10 Tác giả (theo số bài báo đã nộp)
        $topAuthors = BaiBao::select('submitter_id', DB::raw('COUNT(*) as paper_count'))
            ->groupBy('submitter_id')
            ->orderByDesc('paper_count')
            ->limit(10)
            ->with('submitter:user_id,full_name,email')
            ->get();
        
        // Danh sách 10 hội thảo gần đây
        $recentConferences = HoiThao::with('chair:user_id,full_name')
            ->orderByDesc('conference_id')
            ->limit(10)
            ->get();
        
        // Danh sách kỷ yếu gần đây (hội thảo đã hoàn thành)
        $recentProceedings = HoiThao::whereIn('status', ['COMPLETED', 'PUBLISHED'])
            ->with('chair:user_id,full_name')
            ->orderByDesc('end_date')
            ->limit(10)
            ->get();
        
        // Danh sách năm, khoa, level cho filter dropdown
        $years = HoiThao::distinct('year')
            ->orderByDesc('year')
            ->pluck('year');
        
        $faculties = DB::table('khoa')
            ->select('faculty_id', 'faculty_name')
            ->get();
        
        $levels = HoiThao::distinct('level_code')
            ->whereNotNull('level_code')
            ->pluck('level_code');

        return view('admin.reports.index', [
            // Thống kê tổng quan
            'totalUsers' => $totalUsers,
            'totalConferences' => $totalConferences,
            'totalPapers' => $totalPapers,
            'totalReviews' => $totalReviews,
            'papersUnderReview' => $papersUnderReview,
            'papersAccepted' => $papersAccepted,
            'activeReviewers' => $activeReviewers,
            'publishedProceedings' => $publishedProceedings,
            
            // Dữ liệu biểu đồ
            'yearsData' => $yearsData,
            'conferenceCountsData' => $conferenceCountsData,
            'trackNames' => $trackNames,
            'trackCounts' => $trackCounts,
            'monthsData' => $monthsData,
            'reviewCountsData' => $reviewCountsData,
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            
            // Bảng chi tiết
            'topReviewers' => $topReviewers,
            'topAuthors' => $topAuthors,
            'recentConferences' => $recentConferences,
            'recentProceedings' => $recentProceedings,
            
            // Filter options
            'years' => $years,
            'faculties' => $faculties,
            'levels' => $levels,
            
            // Current filters
            'selectedYear' => $year,
            'selectedFaculty' => $facultyId,
            'selectedLevel' => $level,
            'selectedStatus' => $status
        ]);
    }

    /**
     * API endpoint để lấy dữ liệu biểu đồ theo filter (JSON)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $year = $request->input('year');
        $facultyId = $request->input('faculty');
        $level = $request->input('level');
        $status = $request->input('status');
        $chartType = $request->input('chart'); // 'conferences', 'tracks', 'reviews', 'status'

        $data = [];

        switch ($chartType) {
            case 'conferences':
                // Số hội thảo theo năm
                $currentYear = Carbon::now()->year;
                $conferences = HoiThao::selectRaw('year, COUNT(*) as total')
                    ->when($facultyId, function($query) use ($facultyId) {
                        return $query->where('faculty_id', $facultyId);
                    })
                    ->when($level, function($query) use ($level) {
                        return $query->where('level_code', $level);
                    })
                    ->whereBetween('year', [$currentYear - 4, $currentYear])
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get();
                
                $data['labels'] = [];
                $data['values'] = [];
                for ($i = $currentYear - 4; $i <= $currentYear; $i++) {
                    $data['labels'][] = $i;
                    $found = $conferences->firstWhere('year', $i);
                    $data['values'][] = $found ? $found->total : 0;
                }
                break;

            case 'tracks':
                // Phân bố bài báo theo track
                $query = BaiBao::join('tieuban', 'baibao.track_id', '=', 'tieuban.track_id')
                    ->selectRaw('tieuban.title, COUNT(baibao.paper_id) as total')
                    ->when($year, function($q) use ($year) {
                        return $q->whereHas('hoiThao', function($hq) use ($year) {
                            $hq->where('year', $year);
                        });
                    });
                
                $papers = $query->groupBy('tieuban.track_id', 'tieuban.title')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get();
                
                $data['labels'] = $papers->pluck('title')->toArray();
                $data['values'] = $papers->pluck('total')->toArray();
                break;

            case 'reviews':
                // Tiến độ phản biện theo tháng
                $reviews = ReviewerAssignment::selectRaw('DATE_FORMAT(review_submitted_at, "%Y-%m") as month, COUNT(*) as total')
                    ->where('status', ReviewerAssignment::STATUS_COMPLETED)
                    ->whereNotNull('review_submitted_at')
                    ->where('review_submitted_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                
                $data['labels'] = [];
                $data['values'] = [];
                for ($i = 11; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i)->format('Y-m');
                    $data['labels'][] = Carbon::now()->subMonths($i)->format('M Y');
                    $found = $reviews->firstWhere('month', $month);
                    $data['values'][] = $found ? $found->total : 0;
                }
                break;

            case 'status':
                // Phân bố trạng thái bài báo
                $query = BaiBao::selectRaw('status_code, COUNT(*) as total')
                    ->when($year, function($q) use ($year) {
                        return $q->whereHas('hoiThao', function($hq) use ($year) {
                            $hq->where('year', $year);
                        });
                    });
                
                $papers = $query->groupBy('status_code')->get();
                
                $data['labels'] = $papers->map(function($item) {
                    return $this->getStatusLabel($item->status_code);
                })->toArray();
                $data['values'] = $papers->pluck('total')->toArray();
                break;

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }

        return response()->json($data);
    }

    /**
     * Helper: Map status code sang label tiếng Việt
     * 
     * @param string $statusCode
     * @return string
     */
    private function getStatusLabel($statusCode)
    {
        $labels = [
            'DRAFT' => 'Nháp',
            'SUBMITTED' => 'Đã nộp',
            'UNDER_REVIEW' => 'Đang phản biện',
            'REVIEWING' => 'Đang phản biện',
            'IN_REVIEW' => 'Đang phản biện',
            'ACCEPTED' => 'Đã chấp nhận',
            'APPROVED' => 'Đã chấp nhận',
            'REJECTED' => 'Bị từ chối',
            'REVISION_REQUIRED' => 'Cần sửa',
            'CAMERA_READY' => 'Camera ready',
        ];

        return $labels[$statusCode] ?? $statusCode;
    }
}
