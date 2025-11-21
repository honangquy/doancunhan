<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\ReviewerAssignment;
use App\Models\ReviewerBidding;
use App\Models\ReviewerPreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard tổng quan báo cáo cho Chair với bộ lọc và thống kê chi tiết
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Lấy danh sách hội thảo mà user là Chair
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->where('role_code', 'CHAIR');
        })->get();

        // Nếu có conference_id được chọn, load dashboard chi tiết
        $conferenceId = $request->input('conference_id');

        if ($conferenceId) {
            // Kiểm tra user có phải Chair của conference này không
            $hasRole = HoiThao::where('conference_id', $conferenceId)
                ->whereHas('vaiTroNguoiDungs', function ($q) use ($user) {
                    $q->where('user_id', $user->user_id)
                      ->where('role_code', 'CHAIR');
                })->exists();

            if ($hasRole) {
                return $this->detailedDashboard($request, $conferenceId);
            }

            abort(403, 'Bạn không có quyền xem báo cáo của hội thảo này');
        }

        return view('chair.reports.index', compact('conferences'));
    }

    /**
     * Dashboard chi tiết với bộ lọc và thống kê tổng hợp
     */
    private function detailedDashboard(Request $request, $conferenceId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $conference = HoiThao::findOrFail($conferenceId);

        // Lấy danh sách hội thảo mà user là Chair (cho dropdown chọn conference)
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->where('role_code', 'CHAIR');
        })->get();

        // Bộ lọc
        $filters = [
            'status' => $request->input('status'),
            'track_id' => $request->input('track_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // Danh sách tracks cho bộ lọc
        $tracks = DB::table('tieuban')
            ->where('conference_id', $conferenceId)
            ->get();

        // Danh sách status
        $statuses = DB::table('trangthaibaibao')->get();

        // ========== CARDS THỐNG KÊ TỔNG QUAN ==========
        $query = BaiBao::where('conference_id', $conferenceId);

        // Apply filters
        if ($filters['status']) {
            $query->where('status_code', $filters['status']);
        }
        if ($filters['track_id']) {
            $query->where('track_id', $filters['track_id']);
        }
        if ($filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Card 1: Tổng số bài nộp
        $totalPapers = (clone $query)->count();

        // Card 2-5: Số bài theo trạng thái
        $underReviewCount = BaiBao::where('conference_id', $conferenceId)
            ->where('status_code', 'UNDER_REVIEW')->count();
        $acceptedCount = BaiBao::where('conference_id', $conferenceId)
            ->where('decision', 'ACCEPT')->count();
        $rejectedCount = BaiBao::where('conference_id', $conferenceId)
            ->where('decision', 'REJECT')->count();
        $revisionCount = BaiBao::where('conference_id', $conferenceId)
            ->where('decision', 'REVISION')->count();

        // Card 6: Tổng số Reviewer
        $totalReviewers = User::whereHas('roles', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('role_code', 'REVIEWER');
        })->count();

        // Card 7: Số bài chưa đủ reviewer
        $requiredReviewers = $conference->reviewers_per_paper ?? 3;
        $papersWithoutEnoughReviewers = BaiBao::where('conference_id', $conferenceId)
            ->withCount('reviewerAssignments')
            ->having('reviewer_assignments_count', '<', $requiredReviewers)
            ->count();

        // Card 8: Tổng số COI
        $totalCOI = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('coi', true)
            ->count();

        // ========== BÁO CÁO CHI TIẾT ==========

        // Block 1: Bảng bài báo
        $papers = $this->getPapersReport($conferenceId, $filters);

        // Block 2: Bảng reviewers
        $reviewers = $this->getReviewersReport($conferenceId);

        // Block 3: Bidding & COI
        $biddingStats = $this->getBiddingStats($conferenceId);

        // Block 4: Timeline
        $timeline = $this->getTimelineData($conferenceId, $filters);

        return view('chair.reports.dashboard', compact(
            'conference',
            'conferences',
            'tracks',
            'statuses',
            'filters',
            'totalPapers',
            'underReviewCount',
            'acceptedCount',
            'rejectedCount',
            'revisionCount',
            'totalReviewers',
            'papersWithoutEnoughReviewers',
            'totalCOI',
            'papers',
            'reviewers',
            'biddingStats',
            'timeline'
        ));
    }

    /**
     * Báo cáo chi tiết bài báo
     */
    private function getPapersReport($conferenceId, $filters)
    {
        $query = BaiBao::where('conference_id', $conferenceId)
            ->with(['submitter', 'tieuBan'])
            ->withCount([
                'reviewerAssignments',
                'reviewerBiddings as coi_count' => function ($q) {
                    $q->where('coi', true);
                }
            ]);

        // Apply filters
        if ($filters['status']) {
            $query->where('status_code', $filters['status']);
        }
        if ($filters['track_id']) {
            $query->where('track_id', $filters['track_id']);
        }
        if ($filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Báo cáo chi tiết reviewers
     */
    private function getReviewersReport($conferenceId)
    {
        return User::whereHas('roles', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('role_code', 'REVIEWER');
        })
        ->withCount([
            'reviewerAssignments as total_assigned' => function ($q) use ($conferenceId) {
                $q->where('conference_id', $conferenceId);
            },
            'reviewerAssignments as completed_count' => function ($q) use ($conferenceId) {
                $q->where('conference_id', $conferenceId)
                  ->where('status', 'COMPLETED');
            },
            'reviewerAssignments as pending_count' => function ($q) use ($conferenceId) {
                $q->where('conference_id', $conferenceId)
                  ->whereIn('status', ['PENDING', 'ACCEPTED']);
            },
            'reviewerBiddings as coi_declared' => function ($q) use ($conferenceId) {
                $q->where('conference_id', $conferenceId)
                  ->where('coi', true);
            }
        ])
        ->with(['reviewerPreferences' => function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId);
        }])
        ->get()
        ->map(function ($reviewer) {
            $preference = $reviewer->reviewerPreferences->first();
            $reviewer->max_papers = $preference ? $preference->max_papers_wanted : 3;
            $reviewer->workload_percent = $reviewer->max_papers > 0
                ? round(($reviewer->total_assigned / $reviewer->max_papers) * 100, 1)
                : 0;
            return $reviewer;
        });
    }

    /**
     * Thống kê Bidding & COI
     */
    private function getBiddingStats($conferenceId)
    {
        $stats = [];

        // Tổng số bidding
        $stats['total'] = ReviewerBidding::where('conference_id', $conferenceId)->count();

        // Bidding theo giá trị (interested = true)
        $stats['interested'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', true)->count();
        $stats['not_interested'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', false)->count();

        // COI
        $stats['total_coi'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('coi', true)->count();

        // Số bài có COI
        $stats['papers_with_coi'] = BaiBao::where('conference_id', $conferenceId)
            ->whereHas('reviewerBiddings', function ($q) {
                $q->where('coi', true);
            })->count();

        // Số reviewers có COI
        $stats['reviewers_with_coi'] = User::whereHas('roles', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('role_code', 'REVIEWER');
        })
        ->whereHas('reviewerBiddings', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('coi', true);
        })->count();

        // Bài có nhiều COI nhất (Top 10)
        $stats['top_papers_with_coi'] = BaiBao::where('conference_id', $conferenceId)
            ->withCount(['reviewerBiddings as coi_count' => function ($q) {
                $q->where('coi', true);
            }])
            ->having('coi_count', '>', 0)
            ->orderBy('coi_count', 'desc')
            ->limit(10)
            ->get();

        // Reviewer có nhiều COI nhất (Top 10)
        $stats['top_reviewers_with_coi'] = User::whereHas('roles', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('role_code', 'REVIEWER');
        })
        ->withCount(['reviewerBiddings as coi_count' => function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId)
              ->where('coi', true);
        }])
        ->having('coi_count', '>', 0)
        ->orderBy('coi_count', 'desc')
        ->limit(10)
        ->get();

        return $stats;
    }

    /**
     * Dữ liệu timeline theo ngày
     */
    private function getTimelineData($conferenceId, $filters)
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30)->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? now()->format('Y-m-d');

        // Số bài nộp theo ngày
        $submissions = DB::table('baibao')
            ->where('conference_id', $conferenceId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Số bài review xong theo ngày
        $completions = DB::table('reviewer_assignments')
            ->where('conference_id', $conferenceId)
            ->where('status', 'COMPLETED')
            ->whereNotNull('review_submitted_at')
            ->whereBetween('review_submitted_at', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(review_submitted_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Merge data cho chart
        $timeline = [];
        $current = new \DateTime($dateFrom);
        $end = new \DateTime($dateTo);

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $timeline[] = [
                'date' => $dateStr,
                'submissions' => $submissions[$dateStr]->count ?? 0,
                'completions' => $completions[$dateStr]->count ?? 0,
            ];
            $current->modify('+1 day');
        }

        return $timeline;
    }

    /**
     * Báo cáo tổng quan về bài báo theo hội thảo
     */
    public function paperStatistics($conferenceId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Kiểm tra quyền Chair
        if (!$user->hasRoleForConference($conferenceId, 2)) {
            abort(403, 'Bạn không có quyền xem báo cáo này');
        }

        $conference = HoiThao::findOrFail($conferenceId);

        // Thống kê bài báo theo trạng thái
        $papersByStatus = BaiBao::where('conference_id', $conferenceId)
            ->select('status_code', DB::raw('count(*) as total'))
            ->groupBy('status_code')
            ->get()
            ->keyBy('status_code');

        // Thống kê bài báo theo quyết định
        $papersByDecision = BaiBao::where('conference_id', $conferenceId)
            ->whereNotNull('decision')
            ->select('decision', DB::raw('count(*) as total'))
            ->groupBy('decision')
            ->get()
            ->keyBy('decision');

        // Thống kê bài báo theo tiểu ban
        $papersByTrack = BaiBao::where('conference_id', $conferenceId)
            ->leftJoin('tieuban', 'baibao.track_id', '=', 'tieuban.track_id')
            ->select('tieuban.title as track_name', DB::raw('count(baibao.paper_id) as total'))
            ->groupBy('tieuban.track_id', 'tieuban.title')
            ->get();

        // Tổng số bài báo
        $totalPapers = BaiBao::where('conference_id', $conferenceId)->count();

        // Bài báo đã được phân công reviewer
        $assignedPapers = BaiBao::where('conference_id', $conferenceId)
            ->whereHas('reviewerAssignments')
            ->count();

        // Bài báo chưa được phân công
        $unassignedPapers = $totalPapers - $assignedPapers;

        // Bài báo đã có đủ số lượng reviewer (ví dụ: >= 3)
        $fullyAssignedPapers = BaiBao::where('conference_id', $conferenceId)
            ->withCount('reviewerAssignments')
            ->having('reviewer_assignments_count', '>=', 3)
            ->count();

        return view('chair.reports.paper-statistics', compact(
            'conference',
            'papersByStatus',
            'papersByDecision',
            'papersByTrack',
            'totalPapers',
            'assignedPapers',
            'unassignedPapers',
            'fullyAssignedPapers'
        ));
    }

    /**
     * Báo cáo về reviewer assignments
     */
    public function reviewerStatistics($conferenceId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->hasRoleForConference($conferenceId, 2)) {
            abort(403, 'Bạn không có quyền xem báo cáo này');
        }

        $conference = HoiThao::findOrFail($conferenceId);

        // Danh sách reviewers và workload của họ
        $reviewers = User::whereHas('roles', function ($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId)
                  ->where('role_code', 'REVIEWER');
        })
        ->withCount([
            'reviewerAssignments as total_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId);
            },
            'reviewerAssignments as pending_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId)
                      ->where('status', 'PENDING');
            },
            'reviewerAssignments as accepted_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId)
                      ->where('status', 'ACCEPTED');
            },
            'reviewerAssignments as completed_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId)
                      ->where('status', 'COMPLETED');
            },
            'reviewerAssignments as declined_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId)
                      ->where('status', 'DECLINED');
            }
        ])
        ->with(['reviewerPreferences' => function ($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }])
        ->get()
        ->map(function ($reviewer) use ($conferenceId) {
            $preference = $reviewer->reviewerPreferences->first();
            $reviewer->max_papers_wanted = $preference ? $preference->max_papers_wanted : config('conference.default_max_papers', 3);
            $reviewer->remaining_slots = $reviewer->max_papers_wanted - $reviewer->total_assignments;
            return $reviewer;
        });

        // Thống kê tổng hợp
        $totalReviewers = $reviewers->count();
        $totalAssignments = ReviewerAssignment::where('conference_id', $conferenceId)->count();
        $averageWorkload = $totalReviewers > 0 ? round($totalAssignments / $totalReviewers, 2) : 0;

        // Reviewer có workload cao nhất/thấp nhất
        $maxWorkload = $reviewers->max('total_assignments');
        $minWorkload = $reviewers->min('total_assignments');

        return view('chair.reports.reviewer-statistics', compact(
            'conference',
            'reviewers',
            'totalReviewers',
            'totalAssignments',
            'averageWorkload',
            'maxWorkload',
            'minWorkload'
        ));
    }

    /**
     * Báo cáo về bidding process
     */
    public function biddingStatistics($conferenceId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->hasRoleForConference($conferenceId, 2)) {
            abort(403, 'Bạn không có quyền xem báo cáo này');
        }

        $conference = HoiThao::findOrFail($conferenceId);

        // Tổng số bidding records
        $totalBiddings = ReviewerBidding::where('conference_id', $conferenceId)->count();

        // Biddings đã locked (submitted)
        $lockedBiddings = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('is_locked', true)
            ->count();

        // Biddings chưa locked (draft)
        $draftBiddings = $totalBiddings - $lockedBiddings;

        // Bidding theo giá trị (interested/not interested)
        $interestedBiddings = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', true)
            ->count();

        $notInterestedBiddings = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', false)
            ->count();

        // Biddings có COI
        $coiBiddings = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('coi', true)
            ->count();

        // Số lượng reviewers đã submit bidding (locked)
        $reviewersSubmitted = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('is_locked', true)
            ->distinct('user_id')
            ->count('user_id');

        // Số lượng reviewers có preference
        $reviewersWithPreference = ReviewerPreference::where('conference_id', $conferenceId)
            ->count();

        // Danh sách reviewers chưa submit bidding
        $totalReviewers = User::whereHas('roles', function ($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId)
                  ->where('role_code', 'REVIEWER');
        })->count();

        $reviewersNotSubmitted = $totalReviewers - $reviewersSubmitted;

        // Chi tiết bidding theo từng bài báo
        $paperBiddingStats = BaiBao::where('conference_id', $conferenceId)
            ->withCount([
                'reviewerBiddings as total_biddings',
                'reviewerBiddings as interested_biddings' => function ($query) {
                    $query->where('bidding_value', true);
                },
                'reviewerBiddings as locked_biddings' => function ($query) {
                    $query->where('is_locked', true);
                }
            ])
            ->orderBy('interested_biddings', 'desc')
            ->limit(20)
            ->get();

        return view('chair.reports.bidding-statistics', compact(
            'conference',
            'totalBiddings',
            'lockedBiddings',
            'draftBiddings',
            'interestedBiddings',
            'notInterestedBiddings',
            'coiBiddings',
            'reviewersSubmitted',
            'reviewersWithPreference',
            'reviewersNotSubmitted',
            'totalReviewers',
            'paperBiddingStats'
        ));
    }

    /**
     * Export báo cáo theo loại
     */
    public function exportReport(Request $request, $conferenceId)
    {
        $type = $request->get('type', 'papers'); // papers | reviewers
        $format = $request->get('format', 'csv');

        if ($type === 'reviewers') {
            return $this->exportReviewersReport($conferenceId, $format);
        } elseif ($type === 'bidding') {
            return $this->exportBiddingReport($conferenceId, $format);
        } else {
            return $this->exportPapersReport($conferenceId, $format);
        }
    }

    /**
     * Export papers report as CSV
     */
    private function exportPapersReport($conferenceId, $format)
    {
        $papers = BaiBao::where('conference_id', $conferenceId)
            ->with(['submitter', 'tieuBan', 'trangThai'])
            ->withCount([
                'reviewerAssignments',
                'reviewerBiddings as coi_count' => function ($q) {
                    $q->where('coi', true);
                }
            ])
            ->get();

        $csv = "Paper ID,Title,Author,Track,Submission Date,Status,Reviewers Assigned,COI Count,Decision\n";

        foreach ($papers as $paper) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",%s,%s,%d,%d,%s\n",
                $paper->paper_id,
                str_replace('"', '""', $paper->title),
                str_replace('"', '""', $paper->submitter->full_name ?? 'N/A'),
                str_replace('"', '""', $paper->tieuBan->title ?? 'N/A'),
                $paper->created_at ? \Carbon\Carbon::parse($paper->created_at)->format('Y-m-d') : 'N/A',
                $paper->status_code,
                $paper->reviewer_assignments_count,
                $paper->coi_count,
                $paper->decision ?? 'N/A'
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="papers_report_' . $conferenceId . '.csv"',
        ]);
    }

    /**
     * Export báo cáo reviewers
     */
    private function exportReviewersReport($conferenceId, $format)
    {
        $reviewers = User::whereHas('roles', function ($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId)
                  ->where('role_code', 'REVIEWER');
        })
        ->withCount([
            'reviewerAssignments as total_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId);
            },
            'reviewerAssignments as completed_assignments' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId)
                      ->where('status', 'COMPLETED');
            }
        ])
        ->get();

        $csv = "Reviewer ID,Full Name,Email,Total Assignments,Completed,Pending\n";

        foreach ($reviewers as $reviewer) {
            $csv .= sprintf(
                "%d,\"%s\",%s,%d,%d,%d\n",
                $reviewer->user_id,
                str_replace('"', '""', $reviewer->full_name),
                $reviewer->email,
                $reviewer->total_assignments,
                $reviewer->completed_assignments,
                $reviewer->total_assignments - $reviewer->completed_assignments
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviewers_report_' . $conferenceId . '.csv"',
        ]);
    }

    /**
     * Export báo cáo bidding
     */
    private function exportBiddingReport($conferenceId, $format)
    {
        $biddings = ReviewerBidding::where('conference_id', $conferenceId)
            ->with(['user', 'paper'])
            ->get();

        $csv = "Reviewer,Paper Title,Bidding Value,COI,Locked,Locked At,Note\n";

        foreach ($biddings as $bidding) {
            $csv .= sprintf(
                "\"%s\",\"%s\",%s,%s,%s,%s,\"%s\"\n",
                str_replace('"', '""', $bidding->user->full_name ?? 'N/A'),
                str_replace('"', '""', $bidding->paper->title ?? 'N/A'),
                $bidding->bidding_value ? 'Interested' : 'Not Interested',
                $bidding->coi ? 'Yes' : 'No',
                $bidding->is_locked ? 'Yes' : 'No',
                $bidding->locked_at ? $bidding->locked_at->format('Y-m-d H:i:s') : 'N/A',
                str_replace('"', '""', $bidding->note ?? '')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bidding_report_' . $conferenceId . '.csv"',
        ]);
    }
}
