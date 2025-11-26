<?php

namespace App\Services;

use App\Models\BaiBao;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;
use App\Models\User;
use App\Models\TacGiaBaiBao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAssignService
{
    protected $maxReviewersPerPaper;
    protected $maxPapersPerReviewer;
    protected $minBiddingValue;
    protected $weights;

    public function __construct()
    {
        $this->maxReviewersPerPaper = config('assignment.max_reviewers_per_paper', 3);
        $this->maxPapersPerReviewer = config('assignment.max_papers_per_reviewer', 5);
        $this->minBiddingValue = config('assignment.min_bidding_value_for_auto_assign', 1);
        $this->weights = config('assignment.auto_assign_weights', [
            'bidding_value' => 0.5,
            'workload' => 0.3,
            'keyword_match' => 0.2,
        ]);
    }

    /**
     * Auto assign reviewers for an entire conference or specific papers
     *
     * @param int $conferenceId
     * @param array|null $paperIds Optional array of paper IDs to limit assignment
     * @param int|null $assignedBy User ID của CHAIR thực hiện phân công
     * @return array Result với statistics
     */
    public function autoAssignForConference(int $conferenceId, ?array $paperIds = null, ?int $assignedBy = null)
    {
        $result = [
            'success' => true,
            'total_papers_processed' => 0,
            'total_assignments_created' => 0,
            'papers_fully_assigned' => 0,
            'papers_partially_assigned' => 0,
            'papers_not_assigned' => 0,
            'papers_needing_attention' => [],
            'errors' => []
        ];

        DB::beginTransaction();
        try {
            // Lấy danh sách bài cần phân công
            $papers = $this->getPapersNeedingReviewers($conferenceId, $paperIds);
            $result['total_papers_processed'] = $papers->count();

            foreach ($papers as $paper) {
                $assignmentResult = $this->autoAssignForPaper($paper, $assignedBy);

                $result['total_assignments_created'] += $assignmentResult['assignments_created'];

                if ($assignmentResult['is_fully_assigned']) {
                    $result['papers_fully_assigned']++;
                } elseif ($assignmentResult['assignments_created'] > 0) {
                    $result['papers_partially_assigned']++;
                } else {
                    $result['papers_not_assigned']++;
                }

                if (!$assignmentResult['is_fully_assigned']) {
                    $result['papers_needing_attention'][] = [
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'assigned_count' => $assignmentResult['total_assigned'],
                        'needed_count' => $this->maxReviewersPerPaper,
                        'remaining' => $this->maxReviewersPerPaper - $assignmentResult['total_assigned'],
                        'reason' => $assignmentResult['reason'] ?? 'Không đủ reviewer phù hợp'
                    ];
                }
            }

            DB::commit();

            Log::info("Auto-assignment completed for conference {$conferenceId}", $result);

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
            Log::error("Auto-assignment failed for conference {$conferenceId}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Auto assign reviewers for a single paper
     *
     * @param BaiBao $paper
     * @param int|null $assignedBy
     * @return array
     */
    public function autoAssignForPaper(BaiBao $paper, ?int $assignedBy = null)
    {
        $result = [
            'paper_id' => $paper->paper_id,
            'assignments_created' => 0,
            'total_assigned' => 0,
            'is_fully_assigned' => false,
            'reason' => null
        ];

        // Đếm số reviewer hiện tại
        $currentAssignments = $paper->activeAssignments()->count();
        $result['total_assigned'] = $currentAssignments;

        // Nếu đã đủ reviewer
        if ($currentAssignments >= $this->maxReviewersPerPaper) {
            $result['is_fully_assigned'] = true;
            return $result;
        }

        $neededReviewers = $this->maxReviewersPerPaper - $currentAssignments;

        // Lấy danh sách candidate reviewers
        $candidates = $this->getCandidateReviewers($paper);

        if ($candidates->isEmpty()) {
            $result['reason'] = 'Không có reviewer phù hợp (bidding, COI, hoặc đã full workload)';
            return $result;
        }

        // Rank candidates theo score
        $rankedCandidates = $this->rankCandidates($paper, $candidates);

        // Assign top candidates
        $assigned = 0;
        foreach ($rankedCandidates as $candidate) {
            if ($assigned >= $neededReviewers) {
                break;
            }

            try {
                ReviewerAssignment::create([
                    'user_id' => $candidate['reviewer']->user_id,
                    'paper_id' => $paper->paper_id,
                    'conference_id' => $paper->conference_id,
                    'assigned_by' => $assignedBy ?? auth()->id(),
                    'assignment_method' => ReviewerAssignment::METHOD_AUTO,
                    'status' => ReviewerAssignment::STATUS_PENDING,
                    'assigned_at' => now(),
                    'assignment_metadata' => [
                        'bidding_value' => $candidate['bidding_value'],
                        'auto_assign_score' => $candidate['score'],
                        'workload_at_assignment' => $candidate['workload'],
                    ]
                ]);

                $assigned++;
                $result['assignments_created']++;

            } catch (\Exception $e) {
                Log::warning("Failed to create assignment for paper {$paper->paper_id}, reviewer {$candidate['reviewer']->user_id}: " . $e->getMessage());
            }
        }

        $result['total_assigned'] = $currentAssignments + $assigned;
        $result['is_fully_assigned'] = ($result['total_assigned'] >= $this->maxReviewersPerPaper);

        if ($assigned < $neededReviewers) {
            $result['reason'] = "Chỉ tìm được {$assigned}/{$neededReviewers} reviewer phù hợp";
        }

        return $result;
    }

    /**
     * Lấy danh sách bài cần phân công thêm reviewer
     */
    protected function getPapersNeedingReviewers(int $conferenceId, ?array $paperIds = null)
    {
        $query = BaiBao::where('conference_id', $conferenceId)
            ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])
            ->with(['activeAssignments', 'reviewerBiddings', 'tacGias']);

        if ($paperIds) {
            $query->whereIn('paper_id', $paperIds);
        }

        return $query->get()->filter(function ($paper) {
            return $paper->activeAssignments()->count() < $this->maxReviewersPerPaper;
        });
    }

    /**
     * Lấy danh sách candidate reviewers cho một bài
     */
    protected function getCandidateReviewers(BaiBao $paper)
    {
        // Lấy danh sách reviewer đã bidding cho bài này
        $biddings = ReviewerBidding::where('paper_id', $paper->paper_id)
            ->where('bidding_value', '>=', $this->minBiddingValue)
            ->where('coi', false) // Loại bỏ COI
            ->with('reviewer')
            ->get();

        // Lấy danh sách tác giả của bài
        $authorIds = DB::table('TacGiaBaiBao')
            ->where('paper_id', $paper->paper_id)
            ->pluck('user_id')
            ->toArray();

        // Lấy danh sách reviewer đã được phân công
        $assignedReviewerIds = $paper->activeAssignments()
            ->pluck('user_id')
            ->toArray();

        // Filter candidates
        return $biddings->filter(function ($bidding) use ($authorIds, $assignedReviewerIds) {
            // Loại bỏ tác giả
            if (in_array($bidding->user_id, $authorIds)) {
                return false;
            }

            // Loại bỏ reviewer đã được phân công
            if (in_array($bidding->user_id, $assignedReviewerIds)) {
                return false;
            }

            // Kiểm tra workload
            $reviewer = $bidding->reviewer;
            if (!$reviewer || !$reviewer->canAcceptMorePapers()) {
                return false;
            }

            return true;
        });
    }

    /**
     * Rank candidates dựa trên bidding, workload, keyword match
     */
    protected function rankCandidates(BaiBao $paper, $candidates)
    {
        $ranked = [];

        foreach ($candidates as $bidding) {
            $reviewer = $bidding->reviewer;

            // Tính các thành phần score
            $biddingScore = $this->normalizeBiddingValue($bidding->bidding_value);
            $workloadScore = $this->calculateWorkloadScore($reviewer);
            $keywordScore = $this->calculateKeywordMatch($paper, $reviewer);

            // Tính tổng score có trọng số
            $totalScore =
                ($biddingScore * $this->weights['bidding_value']) +
                ($workloadScore * $this->weights['workload']) +
                ($keywordScore * $this->weights['keyword_match']);

            $ranked[] = [
                'reviewer' => $reviewer,
                'bidding_value' => $bidding->bidding_value,
                'workload' => $reviewer->reviewer_workload,
                'keyword_match' => $keywordScore,
                'score' => $totalScore
            ];
        }

        // Sắp xếp theo score giảm dần
        usort($ranked, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $ranked;
    }

    /**
     * Chuẩn hóa bidding value về scale 0-1
     */
    protected function normalizeBiddingValue($biddingValue)
    {
        // 0 = 0.0, 1 = 0.33, 2 = 0.66, 3 = 1.0
        return $biddingValue / 3.0;
    }

    /**
     * Tính workload score (workload thấp = score cao)
     */
    protected function calculateWorkloadScore($reviewer)
    {
        $currentWorkload = $reviewer->reviewer_workload;
        // Score giảm theo workload: workload 0 = 1.0, workload max = 0.0
        return max(0, 1.0 - ($currentWorkload / $this->maxPapersPerReviewer));
    }

    /**
     * Tính keyword match score (placeholder - có thể mở rộng sau)
     */
    protected function calculateKeywordMatch(BaiBao $paper, $reviewer)
    {
        // TODO: Implement keyword matching nếu có bảng keywords
        // Tạm thời return 0.5 (neutral)
        return 0.5;
    }

    /**
     * Get detailed assignment statistics for a conference
     */
    public function getAssignmentStatistics(int $conferenceId)
    {
        $stats = [
            'total_papers' => 0,
            'papers_fully_assigned' => 0,
            'papers_partially_assigned' => 0,
            'papers_not_assigned' => 0,
            'total_reviewers' => 0,
            'total_assignments' => 0,
            'total_biddings' => 0,
            'total_coi_declarations' => 0,
            'avg_bidding_value' => 0,
            'reviewer_workload' => []
        ];

        // Total papers
        $papers = BaiBao::where('conference_id', $conferenceId)
            ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])
            ->with('activeAssignments')
            ->get();

        $stats['total_papers'] = $papers->count();

        foreach ($papers as $paper) {
            $assignedCount = $paper->activeAssignments->count();
            if ($assignedCount >= $this->maxReviewersPerPaper) {
                $stats['papers_fully_assigned']++;
            } elseif ($assignedCount > 0) {
                $stats['papers_partially_assigned']++;
            } else {
                $stats['papers_not_assigned']++;
            }
        }

        // Reviewer statistics
        $stats['total_reviewers'] = User::whereHas('roles', function ($q) use ($conferenceId) {
            $q->where('role_code', 'REVIEWER')
              ->where('conference_id', $conferenceId);
        })->count();

        $stats['total_assignments'] = ReviewerAssignment::where('conference_id', $conferenceId)
            ->whereIn('status', [ReviewerAssignment::STATUS_PENDING, ReviewerAssignment::STATUS_ACCEPTED])
            ->count();

        $stats['total_biddings'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', '>', 0)
            ->count();

        $stats['total_coi_declarations'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('coi', true)
            ->count();

        $stats['avg_bidding_value'] = ReviewerBidding::where('conference_id', $conferenceId)
            ->where('bidding_value', '>', 0)
            ->avg('bidding_value');

        return $stats;
    }
}
