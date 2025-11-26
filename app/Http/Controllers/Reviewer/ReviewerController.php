<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewerController extends Controller
{
    /**
     * Display list of review assignments
     */
    public function assignments()
    {
        $userId = Auth::id();
        
        // Get all assignments for this reviewer
        $assignments = DB::table('reviewer_assignments as ra')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->leftJoin('phanbien as pb', 'ra.id', '=', 'pb.assignment_id')
            ->where('ra.user_id', $userId)
            ->select(
                'ra.id as assignment_id',
                'ra.paper_id',
                'ra.status',
                'ra.assigned_at',
                'ra.responded_at',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'ht.title as conference_name',
                'ht.conference_id',
                'pb.review_id',
                'pb.submitted_at',
                'pb.recommendation_code'
            )
            ->orderBy('ra.assigned_at', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('status', 'PENDING')->count(),
            'accepted' => $assignments->where('status', 'ACCEPTED')->count(),
            'completed' => $assignments->where('status', 'COMPLETED')->count(),
        ];
        
        return view('reviewer.assignments', compact('assignments', 'stats'));
    }
    
    /**
     * Accept a review assignment
     */
    public function acceptAssignment($id)
    {
        $userId = Auth::id();
        
        // Check assignment exists and belongs to this reviewer
        $assignment = DB::table('reviewer_assignments')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment not found or you do not have permission.');
        }
        
        // Check if already accepted or completed
        if (in_array($assignment->status, ['ACCEPTED', 'COMPLETED'])) {
            return redirect()->route('reviewer.assignments')
                ->with('warning', 'This assignment has already been accepted or completed.');
        }
        
        // Update status to ACCEPTED
        DB::table('reviewer_assignments')
            ->where('id', $id)
            ->update([
                'status' => 'ACCEPTED',
                'responded_at' => now(),
            ]);
        
        return redirect()->route('reviewer.assignments')
            ->with('success', 'Assignment accepted successfully. You can now start your review.');
    }
    
    /**
     * Decline a review assignment
     */
    public function declineAssignment(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Check assignment exists and belongs to this reviewer
        $assignment = DB::table('reviewer_assignments')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment not found or you do not have permission.');
        }
        
        // Check if already completed
        if ($assignment->status === 'COMPLETED') {
            return redirect()->route('reviewer.assignments')
                ->with('warning', 'Cannot decline a completed review.');
        }
        
        // Update status to DECLINED
        DB::table('reviewer_assignments')
            ->where('id', $id)
            ->update([
                'status' => 'DECLINED',
                'responded_at' => now(),
                'decline_reason' => $request->input('reason'),
            ]);
        
        return redirect()->route('reviewer.assignments')
            ->with('success', 'Assignment declined. The chair will be notified.');
    }
    
    /**
     * Display list of reviews
     */
    public function reviews()
    {
        $userId = Auth::id();
        
        // Get all reviews by this reviewer
        $reviews = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('ra.user_id', $userId)
            ->whereNotNull('pb.submitted_at')
            ->select(
                'pb.review_id',
                'pb.assignment_id',
                'ra.paper_id',
                'pb.recommendation_code',
                'pb.total_score',
                'pb.submitted_at',
                'bb.title as paper_title',
                'bb.status_code as paper_status',
                'ht.title as conference_name',
                'ra.assigned_at'
            )
            ->orderBy('pb.submitted_at', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $reviews->count(),
            'average_score' => $reviews->count() > 0 ? round($reviews->avg('total_score'), 1) : 0,
            'accept' => $reviews->whereIn('recommendation_code', ['ACCEPT', 'STRONG_ACCEPT', 'WEAK_ACCEPT'])->count(),
            'reject' => $reviews->whereIn('recommendation_code', ['REJECT', 'STRONG_REJECT', 'WEAK_REJECT'])->count(),
        ];
        
        return view('reviewer.reviews.index', compact('reviews', 'stats'));
    }
    
    /**
     * Show form to create a review
     */
    public function createReview($assignmentId)
    {
        $userId = Auth::id();
        
        // Get assignment and paper details
        $assignment = DB::table('reviewer_assignments as ra')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->leftJoin('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->leftJoin('tieuban as tb', 'bb.track_id', '=', 'tb.track_id')
            ->leftJoin('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
            ->where('ra.id', $assignmentId)
            ->where('ra.user_id', $userId)
            ->select(
                'ra.id',
                'ra.paper_id', 
                'ra.status',
                'ra.assigned_at',
                'bb.title',
                'bb.abstract', 
                'bb.keywords',
                'bb.file_path',
                'bb.created_at',
                'nd.full_name as author_name',
                'nd.full_name as author_names', // For backward compatibility
                'ht.title as conference_name',
                'ht.conference_id',
                'tb.title as track_name'
            )
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments.index')
                ->with('error', 'Không tìm thấy phân công hoặc bạn không có quyền truy cập.');
        }
        
        // Check if assignment is accepted
        if ($assignment->status !== 'ACCEPTED') {
            return redirect()->route('reviewer.assignments.show', $assignmentId)
                ->with('warning', 'Bạn cần chấp nhận phân công trước khi có thể phản biện.');
        }
        
        // Create paper object with proper field mapping
        $paper = (object) [
            'paper_id' => $assignment->paper_id,
            'title' => $assignment->title,
            'abstract' => $assignment->abstract,
            'keywords' => $assignment->keywords,
            'file_path' => $assignment->file_path,
            'author_name' => $assignment->author_name ?? 'Chưa xác định',
            'author_names' => $assignment->author_names ?? 'Chưa xác định',
            'created_at' => $assignment->created_at,
            'conference_name' => $assignment->conference_name,
            'track_name' => $assignment->track_name ?? 'Chưa xác định',
            'field' => $assignment->track_name ?? 'Chưa xác định'
        ];

        // Load existing review (draft or final) if exists
        $existingReview = DB::table('phanbien')
            ->where('assignment_id', $assignmentId)
            ->first();
        
        return view('reviewer.reviews.create', compact('assignment', 'paper', 'existingReview'));
    }
    
    /**
     * Store a new review or update existing draft
     */
    public function storeReview(Request $request, $assignmentId)
    {
        $userId = Auth::id();
        
        // Debug logging
        \Log::info('Review submission started', [
            'assignment_id' => $assignmentId,
            'user_id' => $userId,
            'has_file' => $request->hasFile('review_file'),
            'files' => $request->file() ? array_keys($request->file()) : 'no files',
            'all_input' => $request->except(['_token'])
        ]);
        
        // Convert string '1'/'0' to boolean
        $isDraft = $request->input('is_draft') === '1';
        
        $validated = $request->validate([
            'score_novelty' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_relevance' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10', 
            'score_technical_quality' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_presentation' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'score_references' => $isDraft ? 'nullable|integer|min:1|max:10' : 'required|integer|min:1|max:10',
            'detailed_comments' => $isDraft ? 'nullable|string' : 'required|string|min:50',
            'recommendation_code' => $isDraft ? 'nullable|in:ACCEPT,REJECT,STRONG_ACCEPT,WEAK_ACCEPT,STRONG_REJECT,WEAK_REJECT,BORDERLINE' : 'required|in:ACCEPT,REJECT,STRONG_ACCEPT,WEAK_ACCEPT,STRONG_REJECT,WEAK_REJECT,BORDERLINE',
            'is_draft' => 'required',
            'review_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240' // 10MB max
        ]);
        
        // Verify assignment belongs to this reviewer
        $assignment = DB::table('reviewer_assignments')
            ->where('id', $assignmentId)
            ->where('user_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments.index')
                ->with('error', 'Không tìm thấy phân công hoặc không có quyền truy cập.');
        }
        
        // Check if assignment is accepted
        if ($assignment->status !== 'ACCEPTED') {
            return redirect()->route('reviewer.assignments.show', $assignmentId)
                ->with('error', 'Cần chấp nhận phân công trước khi phản biện.');
        }
        
        DB::beginTransaction();
        try {
            // Calculate total score
            $totalScore = null;
            if ($validated['score_novelty'] && $validated['score_relevance'] && 
                $validated['score_technical_quality'] && $validated['score_presentation'] && 
                $validated['score_references']) {
                $totalScore = ($validated['score_novelty'] + $validated['score_relevance'] + 
                              $validated['score_technical_quality'] + $validated['score_presentation'] + 
                              $validated['score_references']) / 5;
            }
            
            // Handle file upload if present
            $reviewFilePath = null;
            if ($request->hasFile('review_file')) {
                $file = $request->file('review_file');
                \Log::info('File upload detected', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType()
                ]);
                $reviewFilePath = $file->store('reviews', 'public');
                \Log::info('File stored at: ' . $reviewFilePath);
            } else {
                \Log::info('No file uploaded in request');
            }
            
            // Check if review already exists
            $existingReview = DB::table('phanbien')
                ->where('assignment_id', $assignmentId)
                ->first();
            
            $reviewData = [
                'assignment_id' => $assignmentId,
                'score_novelty' => $validated['score_novelty'],
                'score_relevance' => $validated['score_relevance'],
                'score_technical_quality' => $validated['score_technical_quality'],
                'score_presentation' => $validated['score_presentation'],
                'score_references' => $validated['score_references'],
                'total_score' => $totalScore,
                'detailed_comments' => $validated['detailed_comments'],
                'recommendation_code' => $validated['recommendation_code'],
                'is_draft' => $isDraft
            ];
            
            if ($reviewFilePath) {
                $reviewData['review_file_path'] = $reviewFilePath;
                \Log::info('Adding file path to review data: ' . $reviewFilePath);
            } else {
                \Log::info('No file path to add to review data');
            }
            
            if ($existingReview) {
                // Update existing review
                if (!$isDraft) {
                    $reviewData['submitted_at'] = now();
                } else {
                    // Keep submitted_at as null for drafts
                    $reviewData['submitted_at'] = null;
                }
                
                DB::table('phanbien')
                    ->where('review_id', $existingReview->review_id)
                    ->update($reviewData);
                $reviewId = $existingReview->review_id;
            } else {
                // Create new review
                if (!$isDraft) {
                    $reviewData['submitted_at'] = now();
                } else {
                    // For drafts, explicitly set submitted_at to null to override default
                    $reviewData['submitted_at'] = null;
                }
                $reviewId = DB::table('phanbien')->insertGetId($reviewData);
            }
            
            // Update assignment status if final submission (not draft)
            if (!$isDraft) {
                DB::table('reviewer_assignments')
                    ->where('id', $assignmentId)
                    ->update([
                        'status' => 'COMPLETED',
                        'review_submitted_at' => now()
                    ]);
            }
            
            DB::commit();
            
            $message = $isDraft ? 'Đã lưu bản nháp thành công!' : 'Đã gửi phản biện thành công!';
            
            if ($isDraft) {
                // For drafts, redirect back to create form to continue editing
                return redirect()->route('reviewer.reviews.create', $assignmentId)
                    ->with('success', $message);
            } else {
                // For final submission, go to assignment details
                return redirect()->route('reviewer.assignments.show', $assignmentId)
                    ->with('success', $message);
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Get existing review data for an assignment
     */
    public function getReviewData($assignmentId)
    {
        $userId = Auth::id();
        
        // Verify assignment belongs to this reviewer
        $assignment = DB::table('reviewer_assignments')
            ->where('id', $assignmentId)
            ->where('user_id', $userId)
            ->first();
        
        if (!$assignment) {
            return response()->json(['review' => null], 404);
        }
        
        $review = DB::table('phanbien')
            ->where('assignment_id', $assignmentId)
            ->first();
        
        return response()->json(['review' => $review]);
    }
    
    /**
     * Show a specific review
     */
    public function showReview($id)
    {
        $userId = Auth::id();
        
        // Get review details
        $review = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pb.review_id', $id)
            ->where('ra.user_id', $userId)
            ->select(
                'pb.*',
                'ra.id as assignment_id',
                'ra.paper_id',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.file_path',
                'ht.title as conference_name',
                'ra.assigned_at'
            )
            ->first();
        
        if (!$review) {
            return redirect()->route('reviewer.reviews')
                ->with('error', 'Review not found or you do not have permission.');
        }
        
        return view('reviewer.reviews.show', compact('review'));
    }
    
    /**
     * Show form to edit a review (if not yet submitted)
     */
    public function editReview($id)
    {
        $userId = Auth::id();
        
        // Get review details
        $review = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pb.review_id', $id)
            ->where('ra.user_id', $userId)
            ->select(
                'pb.*',
                'ra.id as assignment_id',
                'ra.paper_id',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.file_path',
                'ht.title as conference_name'
            )
            ->first();
        
        if (!$review) {
            return redirect()->route('reviewer.reviews')
                ->with('error', 'Review not found or you do not have permission.');
        }
        
        // Get paper authors
        $authors = DB::table('tacgiabaibao as tg')
            ->join('nguoidung as nd', 'tg.user_id', '=', 'nd.user_id')
            ->where('tg.paper_id', $review->paper_id)
            ->select(
                'nd.full_name', 
                'tg.organization', // Use organization from tacgiabaibao table
                'tg.author_order', 
                'tg.is_contact'
            )
            ->orderBy('tg.author_order')
            ->get();
        
        return view('reviewer.reviews.edit', compact('review', 'authors'));
    }
    
    /**
     * Update a review
     */
    public function updateReview(Request $request, $id)
    {
        $userId = Auth::id();
        
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'recommendation_code' => 'required|in:ACCEPT,MINOR_REVISION,MAJOR_REVISION,REJECT',
            'comment_author' => 'required|string|min:50',
            'comment_chair' => 'nullable|string',
        ]);
        
        // Verify review belongs to this reviewer
        $review = DB::table('phanbien as pb')
            ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
            ->where('pb.review_id', $id)
            ->where('ra.user_id', $userId)
            ->select('pb.*', 'ra.user_id')
            ->first();
        
        if (!$review) {
            return redirect()->route('reviewer.reviews')
                ->with('error', 'Review not found or you do not have permission.');
        }
        
        DB::beginTransaction();
        try {
            // Update review
            DB::table('phanbien')
                ->where('review_id', $id)
                ->update([
                    'recommendation_code' => $validated['recommendation_code'],
                    'score' => $validated['score'],
                    'comment_author' => $validated['comment_author'],
                    'comment_chair' => $validated['comment_chair'],
                    'submitted_at' => now(),
                ]);
            
            DB::commit();
            
            return redirect()->route('reviewer.reviews.show', $id)
                ->with('success', 'Review updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update review: ' . $e->getMessage());
        }
    }
    
    /**
     * Download paper file
     */
    public function downloadPaper(Request $request, $paperId)
    {
        $userId = Auth::id();
        $versionNo = $request->query('version');
        
        \Log::info("Download attempt - User: $userId, Paper: $paperId, Version: $versionNo");
        
        // Verify reviewer has permission to access this paper  
        $assignment = DB::table('reviewer_assignments as ra')
            ->where('ra.paper_id', $paperId)
            ->where('ra.user_id', $userId)
            ->first();
        
        if (!$assignment) {
            \Log::warning("No assignment found for user $userId to paper $paperId");
            abort(404, 'Bạn không có quyền truy cập file này.');
        }
        
        // Get file path based on version
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
                ->select('file_path')
                ->first();
            
            if (!$paper || !$paper->file_path) {
                abort(404, 'Không tìm thấy file bài báo.');
            }
            
            $filePath = $paper->file_path;
        }
        
        if (!\Storage::exists($filePath)) {
            abort(404, 'File bài báo không tồn tại trên server.');
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
}




