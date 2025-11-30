<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChairController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:CHAIR');
    }

    /**
     * Display chair dashboard with conference overview
     */
    public function dashboard()
    {
        $userId = Auth::id();

        // Get chair's conferences through vaitronguoidung OR hoithao.chair_id
        $conferences = DB::table('hoithao as ht')
            ->leftJoin('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->where(function($query) use ($userId) {
                $query->where('vt.user_id', $userId)
                      ->orWhere('ht.chair_id', $userId);
            })
            ->select('ht.*')
            ->distinct()
            ->get();

        // Add papers count to conferences
        foreach ($conferences as $conf) {
            $conf->papers_count = DB::table('baibao')
                ->where('conference_id', $conf->conference_id)
                ->count();
        }

        // Get conference requests
        $conferenceRequests = DB::table('yeucauhoithao')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics for all chair's conferences
        $conferenceIds = $conferences->pluck('conference_id');

        // Overall statistics
        $stats = [
            'total_conferences' => $conferences->count(),
            'approved_conferences' => $conferences->where('status', 'ACTIVE')->count(),
            'total_papers' => 0,  // For view compatibility
            'total_submissions' => 0,
            'papers_under_review' => 0,
            'under_review' => 0,  // For view compatibility
            'papers_reviewed' => 0,
            'accepted' => 0,  // For view compatibility
            'needs_reviewers' => 0,  // For view compatibility
            'pending_decisions' => 0,
            'decisions_made' => 0
        ];

        if ($conferenceIds->isNotEmpty()) {
            // Total submissions / Total papers (same thing)
            $totalPapers = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->count();
            $stats['total_submissions'] = $totalPapers;
            $stats['total_papers'] = $totalPapers;

            // Papers under review (has assignments but not all completed)
            $underReview = DB::table('baibao as bb')
                ->whereIn('bb.conference_id', $conferenceIds)
                ->where('bb.status_code', 'UNDER_REVIEW')
                ->count();
            $stats['papers_under_review'] = $underReview;
            $stats['under_review'] = $underReview;

            // Papers fully reviewed (all reviews completed)
            $stats['papers_reviewed'] = DB::table('baibao as bb')
                ->whereIn('bb.conference_id', $conferenceIds)
                ->where('bb.status_code', 'PENDING_CHAIR_REVIEW')
                ->count();

            // Accepted papers
            $stats['accepted'] = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->where('status_code', 'ACCEPTED')
                ->count();

            // Papers needing reviewers (no assignments yet)
            $needsReviewersCount = DB::table('baibao as bb')
                ->leftJoin('reviewer_assignments as ra', 'bb.paper_id', '=', 'ra.paper_id')
                ->whereIn('bb.conference_id', $conferenceIds)
                ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                ->select('bb.paper_id')
                ->groupBy('bb.paper_id')
                ->havingRaw('COUNT(ra.id) = 0')
                ->get()
                ->count();
            $stats['needs_reviewers'] = $needsReviewersCount;

            // Pending decisions (papers with REVIEWED status ready for decision)
            $stats['pending_decisions'] = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->where('status_code', 'PENDING_CHAIR_REVIEW')
                ->count();

            // Decisions made (ACCEPTED or REJECTED status)
            $stats['decisions_made'] = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->whereIn('status_code', ['ACCEPTED', 'REJECTED'])
                ->count();

            // Papers by decision status
            $stats['accepted'] = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->where('decision', 'ACCEPT')
                ->count();

            $stats['published'] = DB::table('baibao')
                ->whereIn('conference_id', $conferenceIds)
                ->where('decision', 'PUBLISHED')
                ->count();
        }

        // Recent papers (last 10)
        $recentPapers = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
            ->whereIn('bb.conference_id', $conferenceIds)
            ->select(
                'bb.paper_id',
                'bb.title',
                'bb.created_at',
                'bb.status_code',
                'ht.title as conference_name',
                'nd.full_name as author_name',
                'ttbb.status_name'
            )
            ->orderBy('bb.created_at', 'desc')
            ->limit(10)
            ->get();

        // Add review counts to recent papers
        foreach ($recentPapers as $paper) {
            $reviewCounts = DB::table('reviewer_assignments as ra')
                ->leftJoin('phanbien as pb', 'ra.id', '=', 'pb.assignment_id')
                ->where('ra.paper_id', $paper->paper_id)
                ->selectRaw('
                    COUNT(ra.id) as total_assigned,
                    COUNT(pb.review_id) as completed
                ')
                ->first();

            $paper->reviews_total = $reviewCounts->total_assigned ?? 0;
            $paper->reviews_completed = $reviewCounts->completed ?? 0;
        }

        // Pending actions (papers needing attention)
        $pendingActions = [];

        // Papers with no reviewers assigned
        $needsReviewers = DB::table('baibao as bb')
            ->leftJoin('reviewer_assignments as ra', 'bb.paper_id', '=', 'ra.paper_id')
            ->whereIn('bb.conference_id', $conferenceIds)
            ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
            ->groupBy('bb.paper_id', 'bb.title')
            ->havingRaw('COUNT(ra.id) = 0')
            ->select('bb.paper_id', 'bb.title')
            ->get();

        foreach ($needsReviewers as $paper) {
            $pendingActions[] = [
                'type' => 'assign_reviewers',
                'paper_id' => $paper->paper_id,
                'message' => "Paper '{$paper->title}' needs reviewers assigned",
                'priority' => 'high'
            ];
        }

        // Papers with all reviews completed but no decision (PENDING_CHAIR_REVIEW status)
        $needsDecision = DB::table('baibao as bb')
            ->whereIn('bb.conference_id', $conferenceIds)
            ->where('bb.status_code', 'PENDING_CHAIR_REVIEW')
            ->select('bb.paper_id', 'bb.title')
            ->get();

        foreach ($needsDecision as $paper) {
            $pendingActions[] = [
                'type' => 'make_decision',
                'paper_id' => $paper->paper_id,
                'message' => "Paper '{$paper->title}' is ready for decision",
                'priority' => 'medium'
            ];
        }

        return view('chair.dashboard', [
            'conferences' => $conferences,
            'stats' => $stats,
            'recentPapers' => $recentPapers,
            'pendingActions' => $pendingActions,
            'conferenceRequests' => $conferenceRequests
        ]);
    }

    /**
     * List all papers in chair's conferences
     */
    public function papers(Request $request)
    {
        $userId = Auth::id();

        // Get chair's conferences through vaitronguoidung
        $conferences = DB::table('hoithao as ht')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->select('ht.*')
            ->get();

        $conferenceIds = $conferences->pluck('conference_id');

        // Build query
        $query = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
            ->whereIn('bb.conference_id', $conferenceIds);

        // Apply filters
        if ($request->filled('conference')) {
            $query->where('bb.conference_id', $request->conference);
        }

        if ($request->filled('status')) {
            $status = $request->status;

            // Check if filtering by decision
            if (str_starts_with($status, 'decision:')) {
                $decisionValue = substr($status, 9); // Remove 'decision:' prefix
                $query->where('bb.decision', $decisionValue);
            } else {
                // Filter by status_code
                $query->where('bb.status_code', $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bb.title', 'LIKE', "%{$search}%")
                  ->orWhere('nd.full_name', 'LIKE', "%{$search}%");
            });
        }

        // Get papers with pagination
        $papers = $query->select(
                'bb.paper_id',
                'bb.title',
                'bb.keywords',
                'bb.created_at',
                'bb.status_code',
                'bb.decision',
                'ht.title as conference_name',
                'ht.conference_id',
                'nd.full_name as author_name',
                'ttbb.status_name'
            )
            ->orderBy('bb.created_at', 'desc')
            ->paginate(20);

        // Add review stats to each paper
        foreach ($papers as $paper) {
            // Get reviewer assignments with status counts
            $assignments = DB::table('reviewer_assignments as ra')
                ->leftJoin('phanbien as pb', 'ra.id', '=', 'pb.assignment_id')
                ->where('ra.paper_id', $paper->paper_id)
                ->selectRaw('
                    COUNT(ra.id) as total_assigned,
                    SUM(CASE WHEN ra.status = "ACCEPTED" THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN ra.status = "DECLINED" THEN 1 ELSE 0 END) as declined,
                    SUM(CASE WHEN ra.status = "PENDING" THEN 1 ELSE 0 END) as pending,
                    COUNT(pb.review_id) as reviews_completed,
                    AVG(pb.score) as avg_score
                ')
                ->first();

            $paper->reviewers_assigned = $assignments->total_assigned ?? 0;
            $paper->reviewers_accepted = $assignments->accepted ?? 0;
            $paper->reviewers_declined = $assignments->declined ?? 0;
            $paper->reviewers_pending = $assignments->pending ?? 0;
            $paper->reviews_completed = $assignments->reviews_completed ?? 0;
            $paper->avg_score = $assignments->avg_score ? round($assignments->avg_score, 1) : null;

            // Get list of reviewers
            $paper->reviewers = DB::table('reviewer_assignments as ra')
                ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
                ->where('ra.paper_id', $paper->paper_id)
                ->select('nd.full_name', 'ra.status')
                ->get();
        }

        // Statistics for dashboard cards
        $statusCounts = DB::table('baibao')
            ->whereIn('conference_id', $conferenceIds)
            ->select('status_code', DB::raw('COUNT(*) as count'))
            ->groupBy('status_code')
            ->pluck('count', 'status_code')
            ->all();

        $pendingCount = $statusCounts['SUBMITTED'] ?? 0;
        $acceptedCount = $statusCounts['ACCEPTED'] ?? 0;
        $rejectedCount = $statusCounts['REJECTED'] ?? 0;

        // Count papers by decision status
        $decisionCounts = DB::table('baibao')
            ->whereIn('conference_id', $conferenceIds)
            ->select('decision', DB::raw('COUNT(*) as count'))
            ->groupBy('decision')
            ->pluck('count', 'decision')
            ->all();

        $publishedCount = $decisionCounts['PUBLISHED'] ?? 0;

        return view('chair.papers.index', [
            'papers' => $papers,
            'conferences' => $conferences,
            'statusCounts' => $statusCounts,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'publishedCount' => $publishedCount,
            'filters' => $request->only(['conference', 'status', 'search'])
        ]);
    }

    /**
     * Show detailed information about a paper
     */
    public function showPaper($paperId)
    {
        $userId = Auth::id();

        // Get paper details with all information
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
            ->leftJoin('tieuban as tb', 'bb.track_id', '=', 'tb.track_id')
            ->where('bb.paper_id', $paperId)
            ->select(
                'bb.*',
                'ht.title as conference_name',
                'ht.conference_id',
                'ht.acronym as conference_acronym',
                'ht.year as conference_year',
                'nd.full_name as author_name',
                'nd.email as author_email',
                'nd.organization as author_organization',
                'ttbb.status_name',
                'tb.title as track_name'
            )
            ->first();

        if (!$paper) {
            abort(404, 'Paper not found');
        }

        // Authorization: Check if user has CHAIR role (simplified for now)
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            abort(403, 'Unauthorized access - CHAIR role required');
        }

        // Get all authors (join with nguoidung for author details)
        $authors = DB::table('tacgiabaibao as ta')
            ->join('nguoidung as nd', 'ta.user_id', '=', 'nd.user_id')
            ->where('ta.paper_id', $paperId)
            ->select('ta.author_order', 'ta.is_contact', 'ta.organization', 'nd.full_name', 'nd.email')
            ->orderBy('ta.author_order')
            ->get();

        // Get review assignments with reviewer info
        $assignments = DB::table('reviewer_assignments as ra')
            ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
            ->join('hoithao as ht', 'ra.conference_id', '=', 'ht.conference_id')
            ->leftJoin('phanbien as pb', function($join) {
                $join->on('ra.id', '=', 'pb.assignment_id')
                     ->where('pb.is_draft', '=', 0);
            })
            ->where('ra.paper_id', $paperId)
            ->select(
                'ra.id as assignment_id',
                'ra.user_id as reviewer_id',
                'ra.assigned_at',
                'ra.status',
                'ra.review_submitted_at',
                'ht.deadline_review as deadline',
                'nd.full_name as reviewer_name',
                'nd.email as reviewer_email',
                'nd.organization as reviewer_org',
                'pb.review_id',
                'pb.total_score',
                'pb.recommendation_code',
                'pb.submitted_at'
            )
            ->orderBy('ra.assigned_at', 'desc')
            ->get();

        // Get completed reviews with full details
        $reviews = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
            ->where('ra.paper_id', $paperId)
            ->whereNotNull('pb.submitted_at')
            ->where('pb.is_draft', 0)
            ->select(
                'pb.*',
                'nd.full_name as reviewer_name',
                'nd.email as reviewer_email',
                'ra.assigned_at',
                'ra.id as assignment_id'
            )
            ->orderBy('pb.submitted_at', 'desc')
            ->get();

        // Calculate review statistics
        $completedReviews = $assignments->whereNotNull('review_submitted_at');

        $reviewStats = [
            'total' => $assignments->count(),
            'completed' => $completedReviews->count(),
            'pending' => $assignments->whereNull('review_submitted_at')->count(),
            'accepted' => $assignments->where('status', 'ACCEPTED')->count(),
            'declined' => $assignments->where('status', 'DECLINED')->count()
        ];

        // Calculate average scores from phanbien table
        $averageScores = null;
        if ($reviews->count() > 0) {
            $averageScores = [
                'novelty' => round($reviews->avg('score_novelty') ?: 0, 1),
                'relevance' => round($reviews->avg('score_relevance') ?: 0, 1),
                'technical_quality' => round($reviews->avg('score_technical_quality') ?: 0, 1),
                'presentation' => round($reviews->avg('score_presentation') ?: 0, 1),
                'references' => round($reviews->avg('score_references') ?: 0, 1),
                'total' => round($reviews->avg('total_score') ?: 0, 1)
            ];
        }

        // Get all paper versions
        $versions = DB::table('phienbanbaibao')
            ->where('paper_id', $paperId)
            ->orderBy('version_no', 'desc')
            ->get();

        return view('chair.papers.show', [
            'paper' => $paper,
            'authors' => $authors,
            'assignments' => $assignments,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'averageScores' => $averageScores,
            'completedReviews' => $completedReviews,
            'versions' => $versions,
            'paperId' => $paperId
        ]);
    }

    /**
     * Download paper file (supports versions)
     */
    public function downloadPaper(Request $request, $paperId)
    {
        $versionNo = $request->query('version');

        if ($versionNo) {
            // Download specific version
            $version = DB::table('phienbanbaibao')
                ->where('paper_id', $paperId)
                ->where('version_no', $versionNo)
                ->first();

            if (!$version) {
                abort(404, 'Version not found');
            }

            $filePath = $version->file_path;

            // If version file doesn't exist, try to fallback
            if (!\Storage::exists($filePath)) {
                \Log::warning("Version {$versionNo} file missing for paper {$paperId}: {$filePath}");

                // Try to use latest version file or baibao file as fallback
                $fallbackFile = $this->findFallbackFile($paperId);
                if ($fallbackFile) {
                    \Log::info("Using fallback file: {$fallbackFile}");
                    $filePath = $fallbackFile;
                } else {
                    abort(404, "File không tồn tại cho version {$versionNo}");
                }
            }
        } else {
            // Download latest version from baibao table
            $paper = DB::table('baibao')
                ->where('paper_id', $paperId)
                ->first();

            if (!$paper || !$paper->file_path) {
                abort(404, 'Paper file not found');
            }

            $filePath = $paper->file_path;
        }

        // Final check if file exists
        if (!\Storage::exists($filePath)) {
            abort(404, 'File không tồn tại trên server.');
        }

        // Use original filename
        $originalFileName = basename($filePath);
        return \Storage::download($filePath, $originalFileName);
    }

    private function findFallbackFile($paperId)
    {
        // Try baibao table first
        $paper = DB::table('baibao')->where('paper_id', $paperId)->first();
        if ($paper && $paper->file_path && \Storage::exists($paper->file_path)) {
            return $paper->file_path;
        }

        // Try latest version with existing file
        $versions = DB::table('phienbanbaibao')
            ->where('paper_id', $paperId)
            ->orderBy('version_no', 'desc')
            ->get();

        foreach ($versions as $version) {
            if (\Storage::exists($version->file_path)) {
                return $version->file_path;
            }
        }

        return null;
    }

    /**
     * AJAX endpoint for paper details (for embedding in other views)
     */
    public function showPaperAjax($paperId)
    {
        $userId = Auth::id();

        // Get paper details
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
            ->where('bb.paper_id', $paperId)
            ->select(
                'bb.*',
                'ht.title as conference_name',
                'ht.conference_id',
                'nd.full_name as author_name',
                'nd.email as author_email',
                'ttbb.status_name'
            )
            ->first();

        if (!$paper) {
            return response()->json(['error' => 'Paper not found'], 404);
        }

        // Authorization: Check if user has CHAIR role
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get authors
        $authors = DB::table('tacgiabaibao as tg')
            ->join('nguoidung as nd', 'tg.user_id', '=', 'nd.user_id')
            ->where('tg.paper_id', $paperId)
            ->select('nd.full_name', 'nd.email', 'tg.author_order', 'tg.is_corresponding')
            ->orderBy('tg.author_order')
            ->get();

        // Get review assignments and reviews
        $assignments = DB::table('reviewer_assignments as ra')
            ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
            ->where('ra.paper_id', $paperId)
            ->select(
                'ra.*',
                'nd.full_name as reviewer_name',
                'nd.email as reviewer_email'
            )
            ->get();

        $reviews = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
            ->where('ra.paper_id', $paperId)
            ->select('pb.*', 'nd.full_name as reviewer_name')
            ->orderBy('pb.submitted_at', 'desc')
            ->get();

        // Calculate review statistics
        $reviewStats = [
            'total_assigned' => $assignments->count(),
            'completed' => $reviews->count(),
            'pending' => $assignments->where('status', 'PENDING')->count(),
            'accepted' => $assignments->where('status', 'ACCEPTED')->count(),
            'declined' => $assignments->where('status', 'DECLINED')->count(),
            'avg_score' => $reviews->avg('score'),
            'recommendations' => $reviews->pluck('recommendation_code')->countBy()->all()
        ];

        return view('chair.papers.show-ajax', [
            'paper' => $paper,
            'authors' => $authors,
            'assignments' => $assignments,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats
        ]);
    }

    /**
     * Show reviewer assignment form for a paper
     */
    public function assignReviewers($paperId)
    {
        $userId = Auth::id();

        // Get paper with conference info, verify chair access
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->where('bb.paper_id', $paperId)
            ->select('bb.*', 'ht.title as conference_name', 'ht.deadline_review')
            ->first();

        if (!$paper) {
            return redirect()->route('chair.papers')->with('error', 'Không có quyền truy cập bài báo này');
        }

        // Get paper authors
        $authors = DB::table('tacgiabaibao')
            ->join('nguoidung as nd', 'tacgiabaibao.user_id', '=', 'nd.user_id')
            ->where('tacgiabaibao.paper_id', $paperId)
            ->select('nd.user_id', 'nd.full_name', 'nd.email', 'tacgiabaibao.is_contact')
            ->orderBy('tacgiabaibao.author_order')
            ->get();

        // Get current assignments
        $currentAssignments = DB::table('phancongphanbien as pc')
            ->join('nguoidung as nd', 'pc.reviewer_id', '=', 'nd.user_id')
            ->leftJoin('phanbien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
            ->where('pc.paper_id', $paperId)
            ->select(
                'pc.assignment_id',
                'pc.reviewer_id',
                'nd.full_name as reviewer_name',
                'nd.email as reviewer_email',
                'nd.organization as reviewer_org',
                'pc.status_code',
                'pc.assigned_at',
                'pc.deadline',
                'pb.review_id',
                'pb.submitted_at'
            )
            ->orderBy('pc.assigned_at', 'desc')
            ->get();

        // Get available reviewers (exclude authors and already assigned)
        // Note: Reviewers can review papers from any conference, so we don't filter by conference_id
        $authorIds = $authors->pluck('user_id')->toArray();
        $assignedIds = $currentAssignments->pluck('reviewer_id')->toArray();
        $excludeIds = array_merge($authorIds, $assignedIds);

        $availableReviewers = DB::table('vaitronguoidung as vt')
            ->join('nguoidung as nd', 'vt.user_id', '=', 'nd.user_id')
            ->where('vt.role_code', 'REVIEWER')
            ->whereNotIn('vt.user_id', $excludeIds)
            ->select(
                'nd.user_id',
                'nd.full_name',
                'nd.email',
                'nd.organization'
            )
            ->distinct()
            ->get();

        // Calculate reviewer workload (current assignments)
        $workload = DB::table('phancongphanbien')
            ->select('reviewer_id', DB::raw('COUNT(*) as assignment_count'))
            ->whereIn('status_code', ['INVITED', 'ACCEPTED'])
            ->groupBy('reviewer_id')
            ->pluck('assignment_count', 'reviewer_id');

        // Add workload to reviewers
        foreach ($availableReviewers as $reviewer) {
            $reviewer->workload = $workload[$reviewer->user_id] ?? 0;
        }

        // Check COI for all available reviewers
        $coiList = DB::table('coi')
            ->where('paper_id', $paperId)
            ->whereIn('reviewer_id', $availableReviewers->pluck('user_id'))
            ->select('reviewer_id', 'coi_code', 'source_type', 'evidence')
            ->get()
            ->keyBy('reviewer_id');

        // Add COI info to reviewers
        foreach ($availableReviewers as $reviewer) {
            $reviewer->has_coi = isset($coiList[$reviewer->user_id]);
            $reviewer->coi_info = $coiList[$reviewer->user_id] ?? null;
        }

        return view('chair.papers.assign', [
            'paper' => $paper,
            'authors' => $authors,
            'currentAssignments' => $currentAssignments,
            'availableReviewers' => $availableReviewers
        ]);
    }

    /**
     * Store a new reviewer assignment
     */
    public function storeAssignment(Request $request, $paperId)
    {
        $userId = Auth::id();

        // Validate input
        $request->validate([
            'reviewer_id' => 'required|integer|exists:nguoidung,user_id',
            'deadline' => 'required|date|after:today'
        ]);

        $reviewerId = $request->input('reviewer_id');
        $deadline = $request->input('deadline');

        try {
            // Verify chair access to paper
            $hasAccess = DB::table('baibao as bb')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('bb.paper_id', $paperId)
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Không có quyền phân công cho bài báo này'], 403);
            }

            // Check if reviewer is an author (prevent self-review)
            $isAuthor = DB::table('tacgiabaibao')
                ->where('paper_id', $paperId)
                ->where('user_id', $reviewerId)
                ->exists();

            if ($isAuthor) {
                return response()->json(['success' => false, 'message' => 'Không thể phân công tác giả phản biện bài báo của chính họ'], 400);
            }

            // Check for existing assignment (UNIQUE constraint will also catch this)
            $existingAssignment = DB::table('phancongphanbien')
                ->where('paper_id', $paperId)
                ->where('reviewer_id', $reviewerId)
                ->first();

            if ($existingAssignment) {
                return response()->json(['success' => false, 'message' => 'Reviewer này đã được phân công cho bài báo này'], 400);
            }

            // Check for COI
            $coi = DB::table('coi')
                ->where('paper_id', $paperId)
                ->where('reviewer_id', $reviewerId)
                ->first();

            if ($coi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviewer có xung đột lợi ích (COI) với bài báo này',
                    'coi_info' => $coi
                ], 400);
            }

            // Generate unique token
            $token = \Illuminate\Support\Str::uuid()->toString();

            // Insert assignment
            $assignmentId = DB::table('phancongphanbien')->insertGetId([
                'paper_id' => $paperId,
                'reviewer_id' => $reviewerId,
                'chair_id' => $userId,
                'status_code' => 'INVITED',
                'token' => $token,
                'assigned_at' => now(),
                'deadline' => $deadline
            ]);

            // Get reviewer info for response
            $reviewer = DB::table('nguoidung')
                ->where('user_id', $reviewerId)
                ->select('full_name', 'email')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Đã phân công reviewer thành công',
                'assignment' => [
                    'assignment_id' => $assignmentId,
                    'reviewer_name' => $reviewer->full_name,
                    'reviewer_email' => $reviewer->email,
                    'deadline' => $deadline
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi phân công reviewer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a reviewer assignment
     */
    public function removeAssignment($assignmentId)
    {
        $userId = Auth::id();

        try {
            // Get assignment info and verify chair access
            $assignment = DB::table('phancongphanbien as pc')
                ->join('baibao as bb', 'pc.paper_id', '=', 'bb.paper_id')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('pc.assignment_id', $assignmentId)
                ->select('pc.*')
                ->first();

            if (!$assignment) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy phân công hoặc không có quyền xóa'], 404);
            }

            // Check if review has been submitted
            $reviewSubmitted = DB::table('phanbien')
                ->where('assignment_id', $assignmentId)
                ->whereNotNull('submitted_at')
                ->exists();

            if ($reviewSubmitted) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa phân công đã có bài phản biện'], 400);
            }

            // Delete the assignment
            DB::table('phancongphanbien')->where('assignment_id', $assignmentId)->delete();

            return response()->json(['success' => true, 'message' => 'Đã xóa phân công thành công']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa phân công: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for conflicts of interest between paper and reviewer
     */
    public function checkCOI($paperId, $reviewerId)
    {
        $userId = Auth::id();

        // Verify chair access
        $hasAccess = DB::table('baibao as bb')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('bb.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->where('bb.paper_id', $paperId)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
        }

        // Check if reviewer is an author
        $isAuthor = DB::table('tacgiabaibao')
            ->where('paper_id', $paperId)
            ->where('user_id', $reviewerId)
            ->exists();

        if ($isAuthor) {
            return response()->json([
                'has_coi' => true,
                'coi_type' => 'AUTHOR',
                'message' => 'Reviewer là tác giả của bài báo này'
            ]);
        }

        // Check COI table
        $coi = DB::table('coi')
            ->join('loaicoi as lc', 'coi.coi_code', '=', 'lc.coi_code')
            ->where('coi.paper_id', $paperId)
            ->where('coi.reviewer_id', $reviewerId)
            ->select('coi.*', 'lc.coi_name', 'lc.description')
            ->first();

        if ($coi) {
            return response()->json([
                'has_coi' => true,
                'coi_info' => $coi,
                'message' => 'Có xung đột lợi ích: ' . $coi->coi_name
            ]);
        }

        return response()->json([
            'has_coi' => false,
            'message' => 'Không có xung đột lợi ích'
        ]);
    }

    /**
     * Suggest reviewers for a paper based on expertise (future enhancement)
     */
    public function suggestReviewers($paperId)
    {
        $userId = Auth::id();

        // Verify chair access
        $hasAccess = DB::table('baibao as bb')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('bb.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->where('bb.paper_id', $paperId)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
        }

        // TODO: Implement expertise matching in Phase 8.8
        // For now, return available reviewers sorted by workload

        $paper = DB::table('baibao')->where('paper_id', $paperId)->first();

        // Get authors to exclude
        $authorIds = DB::table('tacgiabaibao')
            ->where('paper_id', $paperId)
            ->pluck('user_id')
            ->toArray();

        // Get already assigned reviewers to exclude
        $assignedIds = DB::table('phancongphanbien')
            ->where('paper_id', $paperId)
            ->pluck('reviewer_id')
            ->toArray();

        $excludeIds = array_merge($authorIds, $assignedIds);

        // Get available reviewers with workload
        $suggestions = DB::table('vaitronguoidung as vt')
            ->join('nguoidung as nd', 'vt.user_id', '=', 'nd.user_id')
            ->leftJoin(DB::raw('(SELECT reviewer_id, COUNT(*) as workload
                                FROM phancongphanbien
                                WHERE status_code IN ("INVITED", "ACCEPTED")
                                GROUP BY reviewer_id) as w'),
                      'vt.user_id', '=', 'w.reviewer_id')
            ->where('vt.role_code', 'REVIEWER')
            ->whereNotIn('vt.user_id', $excludeIds)
            ->select(
                'nd.user_id',
                'nd.full_name',
                'nd.email',
                'nd.organization',
                DB::raw('COALESCE(w.workload, 0) as workload')
            )
            ->orderBy('workload', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
            'message' => 'Gợi ý dựa trên khối lượng công việc (sắp xếp từ ít đến nhiều)'
        ]);
    }

    /**
     * Phase 8.8: View all reviews for a paper
     * GET /chair/papers/{id}/reviews
     */
    public function reviews($paperId)
    {
        $userId = Auth::id();

        // Get paper with authorization check
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->leftJoin('trangthaibaibao as tt', 'bb.status_code', '=', 'tt.status_code')
            ->where('bb.paper_id', $paperId)
            ->select(
                'bb.*',
                'ht.title as conference_title',
                'ht.conference_id',
                'nd.full_name as author_name',
                'tt.status_name as status_name'
            )
            ->first();

        if (!$paper) {
            abort(403, 'Unauthorized access to this paper');
        }

        // Get all assignments with reviews
        $assignments = DB::table('phancongphanbien as pc')
            ->join('nguoidung as nd', 'pc.reviewer_id', '=', 'nd.user_id')
            ->leftJoin('phanbien as pn', 'pc.assignment_id', '=', 'pn.assignment_id')
            ->where('pc.paper_id', $paperId)
            ->select(
                'pc.assignment_id',
                'pc.assigned_at',
                'pc.deadline',
                'pc.status_code as assignment_status',
                'nd.user_id as reviewer_id',
                'nd.full_name as reviewer_name',
                'nd.email as reviewer_email',
                'nd.organization as reviewer_org',
                'pn.review_id',
                'pn.originality_score',
                'pn.quality_score',
                'pn.clarity_score',
                'pn.relevance_score',
                'pn.score',
                'pn\.recommendation_code',
                'pn.summary_comments',
                'pn.strengths',
                'pn.weaknesses',
                'pn.suggestions',
                'pn.confidential_comments',
                'pn.submitted_at'
            )
            ->orderBy('pn.submitted_at', 'desc')
            ->orderBy('pc.assigned_at', 'desc')
            ->get();

        // Separate completed and pending reviews
        $completedReviews = $assignments->filter(function($item) {
            return !is_null($item->review_id);
        });

        $pendingReviews = $assignments->filter(function($item) {
            return is_null($item->review_id);
        });

        // Calculate statistics
        $totalAssignments = $assignments->count();
        $completedCount = $completedReviews->count();
        $pendingCount = $pendingReviews->count();

        // Check for overdue reviews
        $overdueCount = $pendingReviews->filter(function($item) {
            return strtotime($item->deadline) < time();
        })->count();

        // Calculate average score and recommendation distribution
        $avgScore = 0;
        $acceptCount = 0;
        $rejectCount = 0;
        $reviseCount = 0;

        if ($completedCount > 0) {
            $avgScore = $completedReviews->avg('score');
            $acceptCount = $completedReviews->where('recommendation_code', 'ACCEPT')->count();
            $rejectCount = $completedReviews->where('recommendation_code', 'REJECT')->count();
            $reviseCount = $completedReviews->where('recommendation_code', 'REVISE')->count();
        }

        // Calculate consensus indicator
        $consensus = 'unknown';
        if ($completedCount >= 2) {
            $acceptRatio = $acceptCount / $completedCount;
            $rejectRatio = $rejectCount / $completedCount;

            if ($acceptRatio >= 0.8) {
                $consensus = 'strong_accept';
            } elseif ($rejectRatio >= 0.8) {
                $consensus = 'strong_reject';
            } elseif ($avgScore >= 8.0 && $acceptRatio >= 0.6) {
                $consensus = 'accept';
            } elseif ($avgScore <= 5.0 && $rejectRatio >= 0.6) {
                $consensus = 'reject';
            } else {
                $consensus = 'mixed';
            }
        }

        $stats = [
            'total' => $totalAssignments,
            'completed' => $completedCount,
            'pending' => $pendingCount,
            'overdue' => $overdueCount,
            'avg_score' => round($avgScore, 2),
            'accept_count' => $acceptCount,
            'reject_count' => $rejectCount,
            'revise_count' => $reviseCount,
            'consensus' => $consensus
        ];

        return view('chair.papers.reviews', compact(
            'paper',
            'completedReviews',
            'pendingReviews',
            'stats'
        ));
    }

    /**
     * Phase 8.8: Export reviews to PDF or Excel
     * GET /chair/papers/{id}/reviews/export?format=pdf
     */
    public function exportReviews($paperId, Request $request)
    {
        $format = $request->query('format', 'pdf'); // pdf or excel

        $userId = Auth::id();

        // Get paper with authorization check
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->where('bb.paper_id', $paperId)
            ->select(
                'bb.*',
                'ht.title as conference_title',
                'nd.full_name as author_name'
            )
            ->first();

        if (!$paper) {
            abort(403, 'Unauthorized access to this paper');
        }

        // Get all completed reviews
        $reviews = DB::table('phancongphanbien as pc')
            ->join('nguoidung as nd', 'pc.reviewer_id', '=', 'nd.user_id')
            ->join('phanbien as pn', 'pc.assignment_id', '=', 'pn.assignment_id')
            ->where('pc.paper_id', $paperId)
            ->select(
                'nd.full_name as reviewer_name',
                'pn.*'
            )
            ->orderBy('pn.submitted_at', 'desc')
            ->get();

        if ($format === 'pdf') {
            // For now, return a simple response
            // TODO: Implement PDF generation using DomPDF
            return response()->json([
                'message' => 'PDF export will be implemented with DomPDF package',
                'paper' => $paper->title,
                'reviews_count' => $reviews->count()
            ]);
        } else {
            // For now, return a simple response
            // TODO: Implement Excel export using Maatwebsite/Excel
            return response()->json([
                'message' => 'Excel export will be implemented with Maatwebsite/Excel package',
                'paper' => $paper->title,
                'reviews_count' => $reviews->count()
            ]);
        }
    }

    /**
     * Phase 8.9: Show decision form for paper
     * GET /chair/papers/{id}/decision
     */
    public function makeDecision($paperId)
    {
        $userId = Auth::id();

        // Get paper with authorization check
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->leftJoin('trangthaibaibao as tt', 'bb.status_code', '=', 'tt.status_code')
            ->where('bb.paper_id', $paperId)
            ->select(
                'bb.*',
                'ht.title as conference_title',
                'ht.conference_id',
                'nd.full_name as author_name',
                'nd.email as author_email',
                'tt.status_name as status_name'
            )
            ->first();

        if (!$paper) {
            abort(403, 'Unauthorized access to this paper');
        }

        // Check if all reviews are completed (chỉ check khi không phải PENDING_CHAIR_REVIEW)
        if ($paper->status_code !== 'PENDING_CHAIR_REVIEW') {
            $pendingReviews = DB::table('reviewer_assignments as ra')
                ->where('ra.paper_id', $paperId)
                ->where('ra.status', '!=', 'COMPLETED')
                ->whereNull('ra.review_submitted_at')
                ->count();

            if ($pendingReviews > 0) {
                return redirect()->route('chair.papers.show', $paperId)
                    ->with('error', "Không thể đưa ra quyết định. Còn {$pendingReviews} nhận xét chưa hoàn thành.");
            }
        }

        // Get reviews summary (nếu có - cho bài revision sẽ dựa vào review cũ)
        $reviewsData = DB::table('reviewer_assignments as ra')
            ->join('phanbien as pn', 'ra.id', '=', 'pn.assignment_id')
            ->join('nguoidung as nd', 'ra.user_id', '=', 'nd.user_id')
            ->where('ra.paper_id', $paperId)
            ->where('ra.status', 'COMPLETED')
            ->whereNotNull('ra.review_submitted_at')
            ->select(
                'nd.full_name as reviewer_name',
                'pn.total_score as score',
                'pn.recommendation_code',
                'pn.detailed_comments as summary_comments'
            )
            ->get();

        // Calculate statistics
        $totalReviews = $reviewsData->count();
        $avgScore = $reviewsData->whereNotNull('score')->avg('score') ?: 0;
        $acceptCount = $reviewsData->where('recommendation_code', 'ACCEPT')->count();
        $rejectCount = $reviewsData->where('recommendation_code', 'REJECT')->count();
        $reviseCount = $reviewsData->where('recommendation_code', 'REVISE')->count();

        // Đánh dấu là revision submission
        $isRevision = $paper->status_code === 'PENDING_CHAIR_REVIEW';

        // Calculate consensus
        $consensus = 'mixed';
        if ($totalReviews > 0) {
            $acceptRatio = $acceptCount / $totalReviews;
            $rejectRatio = $rejectCount / $totalReviews;

            if ($acceptRatio >= 0.8) {
                $consensus = 'strong_accept';
            } elseif ($rejectRatio >= 0.8) {
                $consensus = 'strong_reject';
            } elseif ($avgScore >= 8.0 && $acceptRatio >= 0.6) {
                $consensus = 'accept';
            } elseif ($avgScore <= 5.0 && $rejectRatio >= 0.6) {
                $consensus = 'reject';
            }
        }

        $stats = [
            'total' => $totalReviews,
            'avg_score' => round($avgScore, 2),
            'accept_count' => $acceptCount,
            'reject_count' => $rejectCount,
            'revise_count' => $reviseCount,
            'consensus' => $consensus
        ];

        // Check if decision already exists
        $existingDecision = DB::table('baibao')
            ->where('paper_id', $paperId)
            ->whereNotNull('decision_date')
            ->first();

        return view('chair.papers.decision', compact(
            'paper',
            'reviewsData',
            'stats',
            'existingDecision',
            'isRevision'
        ))->with([
            'totalReviews' => $totalReviews,
            'avgScore' => $avgScore,
            'acceptCount' => $acceptCount,
            'rejectCount' => $rejectCount,
            'reviseCount' => $reviseCount
        ]);
    }

    /**
     * Phase 8.9: Store final decision for paper
     * POST /chair/papers/{id}/decision
     */
    public function storeDecision(Request $request, $paperId)
    {
        $userId = Auth::id();

        // Validate input
        $validated = $request->validate([
            'decision' => 'required|in:ACCEPT,REJECT,REVISE',
            'comments' => 'required|min:50|max:5000',
            'deadline_revision' => 'nullable|date|after:today'
        ]);

        // Check authorization
        $paper = DB::table('baibao as bb')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('vaitronguoidung as vt', function($join) use ($userId) {
                $join->on('ht.conference_id', '=', 'vt.conference_id')
                     ->where('vt.user_id', '=', $userId)
                     ->where('vt.role_code', '=', 'CHAIR');
            })
            ->where('bb.paper_id', $paperId)
            ->select('bb.*', 'ht.title as conference_title')
            ->first();

        if (!$paper) {
            abort(403, 'Unauthorized');
        }

        // Validate revision deadline if REVISE
        if ($validated['decision'] === 'REVISE' && empty($validated['deadline_revision'])) {
            return back()->withErrors(['deadline_revision' => 'Deadline is required for revision decision'])->withInput();
        }

        // Get status IDs (you may need to adjust these based on your trangthaibaibao table)
        $statusMap = [
            'ACCEPT' => 'ACCEPTED',
            'REJECT' => 'REJECTED',
            'REVISE' => 'REVISION_REQUIRED'
        ];

        $newStatusCode = $statusMap[$validated['decision']];

        // Get status_id from trangthaibaibao table
        $status = DB::table('trangthaibaibao')
            ->where('status_code', $newStatusCode)
            ->first();

        if (!$status) {
            // Fallback: try to find by name
            $statusNames = [
                'ACCEPT' => 'Đã chấp nhận',
                'REJECT' => 'Đã từ chối',
                'REVISE' => 'Cần sửa lại'
            ];
            $status = DB::table('trangthaibaibao')
                ->where('status_name', $statusNames[$validated['decision']])
                ->first();
        }

        try {
            DB::beginTransaction();

            // Update paper with decision
            DB::table('baibao')
                ->where('paper_id', $paperId)
                ->update([
                    'status_code' => $newStatusCode,
                    'decision' => $validated['decision'],
                    'decision_comments' => $validated['comments'],
                    'decision_date' => now(),
                    'decision_by' => $userId,
                    'revision_deadline' => $validated['decision'] === 'REVISE' ? $validated['deadline_revision'] : null,
                ]);

            // TODO: Send email notification to author
            // Mail::to($paper->author_email)->send(new DecisionNotification($paper, $validated));

            // Log action
            DB::table('activity_logs')->insert([
                'user_id' => $userId,
                'log_type' => 'DECISION',
                'action' => 'DECISION_MADE',
                'model_type' => 'Paper',
                'model_id' => $paperId,
                'description' => "Made decision: {$validated['decision']} for paper #{$paperId}",
                'severity' => 'low',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('chair.papers.show', $paperId)
                ->with('success', 'Quyết định đã được lưu thành công! Tác giả sẽ nhận được thông báo qua email.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    // ============================================================
    // PHASE 8.10: REVIEWERS MANAGEMENT
    // ============================================================

    /**
     * List all reviewers with statistics
     */
    public function listReviewers(Request $request)
    {
        // Authorization check
        $userId = Auth::id();
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.dashboard')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Get conference ID (assuming current user is chair of a conference)
        $conferenceId = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->value('conference_id');

        // Build query for reviewers
        $query = DB::table('nguoidung as nd')
            ->join('vaitronguoidung as vt', 'nd.user_id', '=', 'vt.user_id')
            ->where('vt.role_code', 'REVIEWER')
            ->where('vt.conference_id', $conferenceId)
            ->select('nd.*');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nd.full_name', 'like', "%{$search}%")
                  ->orWhere('nd.email', 'like', "%{$search}%")
                  ->orWhere('nd.organization', 'like', "%{$search}%");
            });
        }

        // Expertise filter
        if ($request->has('expertise') && $request->expertise) {
            $expertise = $request->expertise;
            $query->where('nd.expertise', 'like', "%{$expertise}%");
        }

        // Workload filter
        $workloadFilter = $request->get('workload', 'all');

        $reviewers = $query->get();

        // Calculate statistics for each reviewer
        foreach ($reviewers as $reviewer) {
            // Get assignment statistics
            $assignments = DB::table('phancongphanbien as pc')
                ->join('baibao as bb', 'pc.paper_id', '=', 'bb.paper_id')
                ->where('pc.reviewer_id', $reviewer->user_id)
                ->where('bb.conference_id', $conferenceId)
                ->select('pc.*', 'bb.created_at')
                ->get();

            $reviewer->total_assignments = $assignments->count();

            // Get completed reviews
            $completedReviews = DB::table('phanbien as pb')
                ->join('phancongphanbien as pc', 'pb.assignment_id', '=', 'pc.assignment_id')
                ->join('baibao as bb', 'pc.paper_id', '=', 'bb.paper_id')
                ->where('pc.reviewer_id', $reviewer->user_id)
                ->where('bb.conference_id', $conferenceId)
                ->select('pb.*', 'pc.assigned_at')
                ->get();

            $reviewer->completed_reviews = $completedReviews->count();
            $reviewer->pending_reviews = $reviewer->total_assignments - $reviewer->completed_reviews;

            // Calculate completion rate
            $reviewer->completion_rate = $reviewer->total_assignments > 0
                ? round(($reviewer->completed_reviews / $reviewer->total_assignments) * 100, 1)
                : 0;

            // Calculate average response time (in days)
            $totalResponseTime = 0;
            $responseCount = 0;
            foreach ($assignments as $assignment) {
                $review = $completedReviews->firstWhere('assignment_id', $assignment->assignment_id);
                if ($review) {
                    $assignedDate = \Carbon\Carbon::parse($assignment->assigned_at);
                    $reviewDate = \Carbon\Carbon::parse($review->submitted_at);
                    $totalResponseTime += $assignedDate->diffInDays($reviewDate);
                    $responseCount++;
                }
            }
            $reviewer->avg_response_days = $responseCount > 0
                ? round($totalResponseTime / $responseCount, 1)
                : 0;

            // Calculate average score given
            $reviewer->avg_score = $completedReviews->count() > 0
                ? round($completedReviews->avg('score'), 1)
                : 0;

            // Calculate recommendation distribution
            $reviewer->accept_count = $completedReviews->where('recommendation_code', 'ACCEPT')->count();
            $reviewer->revise_count = $completedReviews->where('recommendation_code', 'REVISE')->count();
            $reviewer->reject_count = $completedReviews->where('recommendation_code', 'REJECT')->count();

            // Workload status
            if ($reviewer->pending_reviews == 0) {
                $reviewer->workload_status = 'free';
            } elseif ($reviewer->pending_reviews <= 2) {
                $reviewer->workload_status = 'light';
            } elseif ($reviewer->pending_reviews <= 4) {
                $reviewer->workload_status = 'moderate';
            } else {
                $reviewer->workload_status = 'heavy';
            }

            // Get expertise from ChuyenMonReviewer
            $expertiseData = DB::table('ChuyenMonReviewer as cm')
                ->join('TieuBan as tb', 'cm.track_id', '=', 'tb.track_id')
                ->where('cm.user_id', $reviewer->user_id)
                ->where('tb.conference_id', $conferenceId)
                ->pluck('tb.title')
                ->toArray();

            $reviewer->expertise = !empty($expertiseData) ? implode(', ', $expertiseData) : null;
        }

        // Apply workload filter
        if ($workloadFilter !== 'all') {
            $reviewers = $reviewers->filter(function($reviewer) use ($workloadFilter) {
                return $reviewer->workload_status === $workloadFilter;
            });
        }

        // Sort reviewers
        $sortBy = $request->get('sort', 'name');
        switch ($sortBy) {
            case 'assignments':
                $reviewers = $reviewers->sortByDesc('total_assignments');
                break;
            case 'completion':
                $reviewers = $reviewers->sortByDesc('completion_rate');
                break;
            case 'workload':
                $reviewers = $reviewers->sortByDesc('pending_reviews');
                break;
            case 'score':
                $reviewers = $reviewers->sortByDesc('avg_score');
                break;
            default: // name
                $reviewers = $reviewers->sortBy('full_name');
        }

        // Calculate overall statistics
        $stats = [
            'total_reviewers' => $reviewers->count(),
            'avg_assignments' => $reviewers->count() > 0 ? round($reviewers->avg('total_assignments'), 1) : 0,
            'avg_completion_rate' => $reviewers->count() > 0 ? round($reviewers->avg('completion_rate'), 1) : 0,
            'free_reviewers' => $reviewers->where('workload_status', 'free')->count(),
            'busy_reviewers' => $reviewers->whereIn('workload_status', ['moderate', 'heavy'])->count(),
        ];

        return view('chair.reviewers.index', compact('reviewers', 'stats', 'request'));
    }

    /**
     * Show reviewer profile with detailed statistics
     */
    public function showReviewer($reviewerId)
    {
        // Authorization check
        $userId = Auth::id();
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.dashboard')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Get reviewer info
        $reviewer = DB::table('nguoidung')->where('user_id', $reviewerId)->first();

        if (!$reviewer) {
            return redirect()->route('chair.reviewers.index')
                ->with('error', 'Không tìm thấy reviewer.');
        }

        // Get conference ID
        $conferenceId = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->value('conference_id');

        // Get all assignments for this reviewer
        $assignments = DB::table('phancongphanbien as pc')
            ->join('baibao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->leftJoin('phanbien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
            ->leftJoin('trangthaibaibao as tt', 'bb.status_code', '=', 'tt.status_code')
            ->where('pc.reviewer_id', $reviewerId)
            ->where('bb.conference_id', $conferenceId)
            ->select(
                'pc.*',
                'bb.paper_id',
                'bb.title as paper_title',
                'bb.created_at as paper_created_at',
                'tt.status_name',
                'pb.review_id',
                'pb.score',
                'pb.recommendation_code',
                'pb.submitted_at as review_date'
            )
            ->orderBy('pc.assigned_at', 'desc')
            ->get();

        // Separate completed and pending
        $completedAssignments = $assignments->whereNotNull('review_id');
        $pendingAssignments = $assignments->whereNull('review_id');

        // Calculate detailed statistics
        $stats = [
            'total_assignments' => $assignments->count(),
            'completed' => $completedAssignments->count(),
            'pending' => $pendingAssignments->count(),
            'completion_rate' => $assignments->count() > 0
                ? round(($completedAssignments->count() / $assignments->count()) * 100, 1)
                : 0,
            'avg_score' => $completedAssignments->count() > 0
                ? round($completedAssignments->avg('score'), 1)
                : 0,
            'accept_count' => $completedAssignments->where('recommendation_code', 'ACCEPT')->count(),
            'revise_count' => $completedAssignments->where('recommendation_code', 'REVISE')->count(),
            'reject_count' => $completedAssignments->where('recommendation_code', 'REJECT')->count(),
        ];

        // Calculate average response time
        $totalResponseTime = 0;
        $responseCount = 0;
        foreach ($completedAssignments as $assignment) {
            if ($assignment->assigned_at && $assignment->review_date) {
                $assignedDate = \Carbon\Carbon::parse($assignment->assigned_at);
                $reviewDate = \Carbon\Carbon::parse($assignment->review_date);
                $totalResponseTime += $assignedDate->diffInDays($reviewDate);
                $responseCount++;
            }
        }
        $stats['avg_response_days'] = $responseCount > 0
            ? round($totalResponseTime / $responseCount, 1)
            : 0;

        // Check for overdue reviews
        $overdueCount = 0;
        foreach ($pendingAssignments as $assignment) {
            if ($assignment->deadline && \Carbon\Carbon::parse($assignment->deadline)->isPast()) {
                $overdueCount++;
            }
        }
        $stats['overdue'] = $overdueCount;

        // Get expertise as array
        $reviewer->expertise_array = $reviewer->expertise
            ? explode(',', $reviewer->expertise)
            : [];

        return view('chair.reviewers.show', compact('reviewer', 'stats', 'completedAssignments', 'pendingAssignments'));
    }
    /**
     * Get review details for modal display
     */
    public function getReviewDetails($reviewId)
    {
        $review = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
            ->where('pb.review_id', $reviewId)
            ->select([
                'pb.*',
                'u.full_name as reviewer_name',
                'u.email as reviewer_email',
                'u.organization as reviewer_organization'
            ])
            ->first();

        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        return response()->json($review);
    }
}










