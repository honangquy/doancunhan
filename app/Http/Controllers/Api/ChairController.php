<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChairController extends Controller
{
    /**
     * Get dashboard statistics for chair
     * GET /api/chair/dashboard
     */
    public function dashboard()
    {
        try {
            $userId = auth()->id();
            
            // Get chair's conferences
            $conferences = DB::table('hoithao as ht')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('ht.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->select('ht.*')
                ->get();
            
            $conferenceIds = $conferences->pluck('conference_id');
            
            // Overall statistics
            $stats = [
                'total_conferences' => $conferences->count(),
                'total_submissions' => 0,
                'papers_under_review' => 0,
                'papers_reviewed' => 0,
                'accepted_assignments' => 0,
                'needs_reviewers' => 0,
                'pending_decisions' => 0,
                'decisions_made' => 0,
            ];
            
            if ($conferenceIds->isNotEmpty()) {
                // Total submissions
                $stats['total_submissions'] = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->count();
                
                // Papers under review
                $stats['papers_under_review'] = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->where('status_code', 'UNDER_REVIEW')
                    ->count();
                
                // Papers fully reviewed
                $stats['papers_reviewed'] = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->where('status_code', 'REVIEWED')
                    ->count();
                
                // Accepted reviewer assignments
                $stats['accepted_assignments'] = DB::table('reviewer_assignments')
                    ->whereIn('conference_id', $conferenceIds)
                    ->where('status', 'ACCEPTED')
                    ->count();
                
                // Papers needing reviewers (no assignments yet)
                $stats['needs_reviewers'] = DB::table('baibao as bb')
                    ->leftJoin('reviewer_assignments as ra', 'bb.paper_id', '=', 'ra.paper_id')
                    ->whereIn('bb.conference_id', $conferenceIds)
                    ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                    ->select('bb.paper_id')
                    ->groupBy('bb.paper_id')
                    ->havingRaw('COUNT(ra.id) = 0')
                    ->get()
                    ->count();
                
                // Pending decisions (papers with REVIEWED status)
                $stats['pending_decisions'] = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->where('status_code', 'REVIEWED')
                    ->count();
                
                // Decisions made (ACCEPTED or REJECTED)
                $stats['decisions_made'] = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->whereIn('status_code', ['ACCEPTED', 'REJECTED'])
                    ->count();
            }
            
            // Recent papers (last 10)
            $recentPapers = [];
            if ($conferenceIds->isNotEmpty()) {
                $papers = DB::table('baibao as bb')
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
                
                foreach ($papers as $paper) {
                    $reviewCounts = DB::table('reviewer_assignments as ra')
                        ->leftJoin('phanbien as pb', function($join) {
                            $join->on('ra.id', '=', 'pb.assignment_id')
                                 ->where('pb.is_draft', 0)
                                 ->whereNotNull('pb.submitted_at');
                        })
                        ->where('ra.paper_id', $paper->paper_id)
                        ->selectRaw('
                            COUNT(ra.id) as total_assigned,
                            COUNT(pb.review_id) as completed
                        ')
                        ->first();
                    
                    $recentPapers[] = [
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'conference_name' => $paper->conference_name,
                        'author_name' => $paper->author_name,
                        'status_code' => $paper->status_code,
                        'status_name' => $paper->status_name,
                        'created_at' => $paper->created_at,
                        'reviews_total' => $reviewCounts->total_assigned ?? 0,
                        'reviews_completed' => $reviewCounts->completed ?? 0,
                    ];
                }
            }
            
            // Pending actions
            $pendingActions = [];
            
            if ($conferenceIds->isNotEmpty()) {
                // Papers needing reviewers
                $needsReviewers = DB::table('baibao as bb')
                    ->leftJoin('reviewer_assignments as ra', 'bb.paper_id', '=', 'ra.paper_id')
                    ->whereIn('bb.conference_id', $conferenceIds)
                    ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                    ->select('bb.paper_id', 'bb.title')
                    ->groupBy('bb.paper_id', 'bb.title')
                    ->havingRaw('COUNT(ra.id) = 0')
                    ->limit(5)
                    ->get();
                
                foreach ($needsReviewers as $paper) {
                    $pendingActions[] = [
                        'type' => 'assign_reviewers',
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'message' => 'Cần phân công phản biện viên',
                        'priority' => 'high'
                    ];
                }
                
                // Papers ready for decision
                $needsDecision = DB::table('baibao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->where('status_code', 'REVIEWED')
                    ->select('paper_id', 'title')
                    ->limit(5)
                    ->get();
                
                foreach ($needsDecision as $paper) {
                    $pendingActions[] = [
                        'type' => 'make_decision',
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'message' => 'Sẵn sàng để ra quyết định',
                        'priority' => 'medium'
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard statistics',
                'data' => [
                    'conferences' => $conferences,
                    'statistics' => $stats,
                    'recent_papers' => $recentPapers,
                    'pending_actions' => $pendingActions,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy thống kê dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of papers with filters and pagination
     * GET /api/chair/papers
     */
    public function papers(Request $request)
    {
        try {
            $userId = auth()->id();
            
            // Get chair's conferences
            $conferenceIds = DB::table('vaitronguoidung')
                ->where('user_id', $userId)
                ->where('role_code', 'CHAIR')
                ->pluck('conference_id');
            
            if ($conferenceIds->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Không có bài báo',
                    'data' => [
                        'papers' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'total' => 0,
                            'per_page' => 20,
                            'last_page' => 1,
                        ]
                    ]
                ]);
            }
            
            // Build query
            $query = DB::table('baibao as bb')
                ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
                ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
                ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
                ->whereIn('bb.conference_id', $conferenceIds);
            
            // Filters
            if ($request->filled('conference_id')) {
                $query->where('bb.conference_id', $request->conference_id);
            }
            
            if ($request->filled('status')) {
                $query->where('bb.status_code', $request->status);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('bb.title', 'LIKE', "%{$search}%")
                      ->orWhere('nd.full_name', 'LIKE', "%{$search}%")
                      ->orWhere('bb.keywords', 'LIKE', "%{$search}%");
                });
            }
            
            // Count total
            $total = $query->count();
            
            // Pagination
            $perPage = $request->get('per_page', 20);
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            // Get papers
            $papers = $query->select(
                    'bb.paper_id',
                    'bb.title',
                    'bb.keywords',
                    'bb.created_at',
                    'bb.status_code',
                    'ht.title as conference_name',
                    'ht.conference_id',
                    'nd.full_name as author_name',
                    'ttbb.status_name'
                )
                ->orderBy('bb.created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();
            
            // Add reviewer stats
            $papersWithStats = [];
            foreach ($papers as $paper) {
                $assignments = DB::table('reviewer_assignments as ra')
                    ->leftJoin('phanbien as pb', function($join) {
                        $join->on('ra.id', '=', 'pb.assignment_id')
                             ->where('pb.is_draft', 0)
                             ->whereNotNull('pb.submitted_at');
                    })
                    ->where('ra.paper_id', $paper->paper_id)
                    ->selectRaw('
                        COUNT(ra.id) as total_assigned,
                        SUM(CASE WHEN ra.status = "ACCEPTED" THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN ra.status = "DECLINED" THEN 1 ELSE 0 END) as declined,
                        SUM(CASE WHEN ra.status = "PENDING" THEN 1 ELSE 0 END) as pending,
                        COUNT(pb.review_id) as completed,
                        AVG(pb.total_score) as avg_score
                    ')
                    ->first();
                
                // Get reviewers list
                $reviewers = DB::table('reviewer_assignments as ra')
                    ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                    ->where('ra.paper_id', $paper->paper_id)
                    ->select('u.full_name', 'ra.status')
                    ->get();
                
                $papersWithStats[] = [
                    'paper_id' => $paper->paper_id,
                    'title' => $paper->title,
                    'keywords' => $paper->keywords,
                    'conference_name' => $paper->conference_name,
                    'conference_id' => $paper->conference_id,
                    'author_name' => $paper->author_name,
                    'status_code' => $paper->status_code,
                    'status_name' => $paper->status_name,
                    'created_at' => $paper->created_at,
                    'reviewers' => [
                        'assigned' => $assignments->total_assigned ?? 0,
                        'accepted' => $assignments->accepted ?? 0,
                        'declined' => $assignments->declined ?? 0,
                        'pending' => $assignments->pending ?? 0,
                        'completed' => $assignments->completed ?? 0,
                        'avg_score' => $assignments->avg_score ? round($assignments->avg_score, 2) : null,
                        'list' => $reviewers
                    ]
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách bài báo',
                'data' => [
                    'papers' => $papersWithStats,
                    'pagination' => [
                        'current_page' => $currentPage,
                        'total' => $total,
                        'per_page' => $perPage,
                        'last_page' => ceil($total / $perPage),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách bài báo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get paper details with reviews
     * GET /api/chair/papers/{id}
     */
    public function showPaper($paperId)
    {
        try {
            $userId = auth()->id();
            
            // Verify chair has access to this paper's conference
            $paper = DB::table('baibao as bb')
                ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
                ->join('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
                ->join('trangthaibaibao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
                ->leftJoin('tieuban as tb', 'bb.track_id', '=', 'tb.track_id')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('bb.paper_id', $paperId)
                ->select(
                    'bb.*',
                    'ht.title as conference_name',
                    'ht.conference_id',
                    'nd.full_name as author_name',
                    'nd.email as author_email',
                    'ttbb.status_name',
                    'tb.title as track_name'
                )
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập'
                ], 404);
            }
            
            // Get all authors
            $authors = DB::table('tacgiabaibao as tg')
                ->leftJoin('nguoidung as nd', 'tg.user_id', '=', 'nd.user_id')
                ->where('tg.paper_id', $paperId)
                ->select(
                    'nd.full_name as author_name',
                    'nd.email as author_email',
                    'tg.organization as author_organization',
                    'tg.is_contact',
                    'tg.author_order'
                )
                ->orderBy('tg.author_order')
                ->get();
            
            // Get reviewer assignments
            $assignments = DB::table('reviewer_assignments as ra')
                ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                ->leftJoin('phanbien as pb', function($join) {
                    $join->on('ra.id', '=', 'pb.assignment_id')
                         ->where('pb.is_draft', 0)
                         ->whereNotNull('pb.submitted_at');
                })
                ->where('ra.paper_id', $paperId)
                ->select(
                    'ra.id as assignment_id',
                    'ra.user_id',
                    'u.full_name as reviewer_name',
                    'u.email as reviewer_email',
                    'ra.status',
                    'ra.assigned_at',
                    'ra.responded_at',
                    'ra.review_submitted_at',
                    'pb.review_id',
                    'pb.total_score',
                    'pb.recommendation_code'
                )
                ->get();
            
            // Get full review details for completed reviews
            $reviews = [];
            foreach ($assignments as $assignment) {
                if ($assignment->review_id) {
                    $review = DB::table('phanbien')
                        ->where('review_id', $assignment->review_id)
                        ->first();
                    
                    $reviews[] = [
                        'review_id' => $review->review_id,
                        'reviewer_name' => $assignment->reviewer_name,
                        'total_score' => $review->total_score,
                        'recommendation_code' => $review->recommendation_code,
                        'detailed_comments' => $review->detailed_comments,
                        'comments_to_author' => $review->comment_author,
                        'comments_to_chair' => $review->comment_chair,
                        'submitted_at' => $review->submitted_at,
                        'score_novelty' => $review->score_novelty,
                        'score_relevance' => $review->score_relevance,
                        'score_technical_quality' => $review->score_technical_quality,
                        'score_presentation' => $review->score_presentation,
                        'score_references' => $review->score_references,
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Chi tiết bài báo',
                'data' => [
                    'paper' => [
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'abstract' => $paper->abstract,
                        'keywords' => $paper->keywords,
                        'status_code' => $paper->status_code,
                        'status_name' => $paper->status_name,
                        'conference_id' => $paper->conference_id,
                        'conference_name' => $paper->conference_name,
                        'track_name' => $paper->track_name,
                        'file_path' => $paper->file_path,
                        'created_at' => $paper->created_at,
                    ],
                    'authors' => $authors,
                    'assignments' => $assignments,
                    'reviews' => $reviews,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy chi tiết bài báo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available reviewers for a paper
     * GET /api/chair/papers/{id}/available-reviewers
     */
    public function getAvailableReviewers($paperId)
    {
        try {
            $userId = auth()->id();
            
            // Verify chair has access
            $paper = DB::table('baibao as bb')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('bb.paper_id', $paperId)
                ->select('bb.*')
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }
            
            // Get reviewers in conference
            $reviewers = DB::table('vaitronguoidung as vt')
                ->join('nguoidung as u', 'vt.user_id', '=', 'u.user_id')
                ->where('vt.conference_id', $paper->conference_id)
                ->where('vt.role_code', 'REVIEWER')
                ->select('u.user_id', 'u.full_name', 'u.email', 'u.organization')
                ->get();
            
            // Get already assigned reviewers
            $assignedIds = DB::table('reviewer_assignments')
                ->where('paper_id', $paperId)
                ->pluck('user_id');
            
            // Filter out already assigned and paper authors
            $authorIds = DB::table('tacgiabaibao')
                ->where('paper_id', $paperId)
                ->whereNotNull('user_id')
                ->pluck('user_id');
            
            $excludeIds = $assignedIds->merge($authorIds)->merge([$paper->submitter_id]);
            
            $availableReviewers = $reviewers->filter(function($reviewer) use ($excludeIds) {
                return !$excludeIds->contains($reviewer->user_id);
            })->values();
            
            // Get workload for each reviewer
            foreach ($availableReviewers as $reviewer) {
                $workload = DB::table('reviewer_assignments')
                    ->where('user_id', $reviewer->user_id)
                    ->where('conference_id', $paper->conference_id)
                    ->where('status', '!=', 'DECLINED')
                    ->count();
                
                $reviewer->current_assignments = $workload;
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách phản biện viên khả dụng',
                'data' => [
                    'reviewers' => $availableReviewers
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách phản biện viên: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Assign reviewer to paper
     * POST /api/chair/papers/{id}/assign-reviewer
     */
    public function assignReviewer(Request $request, $paperId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reviewer_id' => 'required|integer|exists:nguoidung,user_id',
                'deadline' => 'nullable|date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $userId = auth()->id();
            
            // Verify chair has access
            $paper = DB::table('baibao as bb')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('bb.paper_id', $paperId)
                ->select('bb.*')
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }
            
            // Check if already assigned
            $exists = DB::table('reviewer_assignments')
                ->where('paper_id', $paperId)
                ->where('user_id', $request->reviewer_id)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phản biện viên đã được phân công cho bài báo này'
                ], 422);
            }
            
            // Create assignment
            $assignmentId = DB::table('reviewer_assignments')->insertGetId([
                'paper_id' => $paperId,
                'user_id' => $request->reviewer_id,
                'conference_id' => $paper->conference_id,
                'assigned_by' => $userId,
                'status' => 'PENDING',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update paper status if needed
            if ($paper->status_code === 'SUBMITTED') {
                DB::table('baibao')
                    ->where('paper_id', $paperId)
                    ->update(['status_code' => 'UNDER_REVIEW']);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Phân công phản biện viên thành công',
                'data' => [
                    'assignment_id' => $assignmentId
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi phân công phản biện viên: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove reviewer assignment
     * DELETE /api/chair/assignments/{id}
     */
    public function removeAssignment($assignmentId)
    {
        try {
            $userId = auth()->id();
            
            // Verify chair has access
            $assignment = DB::table('reviewer_assignments as ra')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('ra.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('ra.id', $assignmentId)
                ->select('ra.*')
                ->first();
            
            if (!$assignment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy phân công'
                ], 404);
            }
            
            // Check if review submitted
            $hasReview = DB::table('phanbien')
                ->where('assignment_id', $assignmentId)
                ->where('is_draft', 0)
                ->whereNotNull('submitted_at')
                ->exists();
            
            if ($hasReview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa phân công đã có phản biện hoàn thành'
                ], 422);
            }
            
            // Delete assignment
            DB::table('reviewer_assignments')->where('id', $assignmentId)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa phân công thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi xóa phân công: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Make decision on paper (ACCEPT/REJECT)
     * POST /api/chair/papers/{id}/decision
     */
    public function makeDecision(Request $request, $paperId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'decision' => 'required|in:ACCEPTED,REJECTED',
                'comments' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $userId = auth()->id();
            
            // Verify chair has access and paper is reviewed
            $paper = DB::table('baibao as bb')
                ->join('vaitronguoidung as vt', function($join) use ($userId) {
                    $join->on('bb.conference_id', '=', 'vt.conference_id')
                         ->where('vt.user_id', '=', $userId)
                         ->where('vt.role_code', '=', 'CHAIR');
                })
                ->where('bb.paper_id', $paperId)
                ->where('bb.status_code', 'REVIEWED')
                ->select('bb.*')
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo hoặc bài báo chưa được phản biện đầy đủ'
                ], 404);
            }
            
            // Update paper status
            DB::table('baibao')
                ->where('paper_id', $paperId)
                ->update([
                    'status_code' => $request->decision,
                    'updated_at' => now(),
                ]);
            
            // Log decision (if you have a decisions table, otherwise skip)
            // DB::table('decisions')->insert([...]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Ra quyết định thành công',
                'data' => [
                    'decision' => $request->decision,
                    'paper_id' => $paperId,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi ra quyết định: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get review statistics for conference
     * GET /api/chair/conferences/{id}/review-statistics
     */
    public function reviewStatistics($conferenceId)
    {
        try {
            $userId = auth()->id();
            
            // Verify chair has access
            $hasAccess = DB::table('vaitronguoidung')
                ->where('conference_id', $conferenceId)
                ->where('user_id', $userId)
                ->where('role_code', 'CHAIR')
                ->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền truy cập'
                ], 403);
            }
            
            // Paper statistics by status
            $papersByStatus = DB::table('baibao')
                ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
                ->where('conference_id', $conferenceId)
                ->select('baibao.status_code', 'trangthaibaibao.status_name', DB::raw('COUNT(*) as count'))
                ->groupBy('baibao.status_code', 'trangthaibaibao.status_name')
                ->get();
            
            // Reviewer statistics
            $reviewerStats = DB::table('reviewer_assignments as ra')
                ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                ->leftJoin('phanbien as pb', function($join) {
                    $join->on('ra.id', '=', 'pb.assignment_id')
                         ->where('pb.is_draft', 0)
                         ->whereNotNull('pb.submitted_at');
                })
                ->where('ra.conference_id', $conferenceId)
                ->select(
                    'u.user_id',
                    'u.full_name',
                    DB::raw('COUNT(ra.id) as total_assigned'),
                    DB::raw('SUM(CASE WHEN ra.status = "ACCEPTED" THEN 1 ELSE 0 END) as accepted'),
                    DB::raw('SUM(CASE WHEN ra.status = "DECLINED" THEN 1 ELSE 0 END) as declined'),
                    DB::raw('SUM(CASE WHEN ra.status = "PENDING" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('COUNT(pb.review_id) as completed')
                )
                ->groupBy('u.user_id', 'u.full_name')
                ->get();
            
            // Average scores by recommendation
            $scoresByRecommendation = DB::table('phanbien as pb')
                ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
                ->where('ra.conference_id', $conferenceId)
                ->where('pb.is_draft', 0)
                ->whereNotNull('pb.submitted_at')
                ->select(
                    'pb.recommendation_code',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(pb.total_score) as avg_score')
                )
                ->groupBy('pb.recommendation_code')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Thống kê phản biện',
                'data' => [
                    'papers_by_status' => $papersByStatus,
                    'reviewer_performance' => $reviewerStats,
                    'scores_by_recommendation' => $scoresByRecommendation,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of reviewers with performance metrics
     * GET /api/chair/reviewers
     */
    public function listReviewers(Request $request)
    {
        try {
            $userId = auth()->id();
            
            // Get chair's conferences
            $conferenceIds = DB::table('vaitronguoidung')
                ->where('user_id', $userId)
                ->where('role_code', 'CHAIR')
                ->pluck('conference_id');
            
            if ($conferenceIds->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Không có phản biện viên',
                    'data' => ['reviewers' => []]
                ]);
            }
            
            // Get reviewers with performance stats
            $reviewers = DB::table('vaitronguoidung as vt')
                ->join('nguoidung as u', 'vt.user_id', '=', 'u.user_id')
                ->whereIn('vt.conference_id', $conferenceIds)
                ->where('vt.role_code', 'REVIEWER')
                ->select('u.user_id', 'u.full_name', 'u.email', 'u.organization', 'vt.conference_id')
                ->distinct()
                ->get();
            
            $reviewersWithStats = [];
            foreach ($reviewers as $reviewer) {
                $stats = DB::table('reviewer_assignments as ra')
                    ->leftJoin('phanbien as pb', function($join) {
                        $join->on('ra.id', '=', 'pb.assignment_id')
                             ->where('pb.is_draft', 0)
                             ->whereNotNull('pb.submitted_at');
                    })
                    ->where('ra.user_id', $reviewer->user_id)
                    ->whereIn('ra.conference_id', $conferenceIds)
                    ->selectRaw('
                        COUNT(ra.id) as total_assigned,
                        SUM(CASE WHEN ra.status = "ACCEPTED" THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN ra.status = "DECLINED" THEN 1 ELSE 0 END) as declined,
                        SUM(CASE WHEN ra.status = "PENDING" THEN 1 ELSE 0 END) as pending,
                        COUNT(pb.review_id) as completed,
                        AVG(pb.total_score) as avg_score
                    ')
                    ->first();
                
                $reviewersWithStats[] = [
                    'user_id' => $reviewer->user_id,
                    'full_name' => $reviewer->full_name,
                    'email' => $reviewer->email,
                    'organization' => $reviewer->organization,
                    'statistics' => [
                        'total_assigned' => $stats->total_assigned ?? 0,
                        'accepted' => $stats->accepted ?? 0,
                        'declined' => $stats->declined ?? 0,
                        'pending' => $stats->pending ?? 0,
                        'completed' => $stats->completed ?? 0,
                        'avg_score' => $stats->avg_score ? round($stats->avg_score, 2) : null,
                    ]
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách phản biện viên',
                'data' => [
                    'reviewers' => $reviewersWithStats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách phản biện viên: ' . $e->getMessage()
            ], 500);
        }
    }
}
