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
        $assignments = DB::table('PhanCongPhanBien as pc')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
            ->where('pc.reviewer_id', $userId)
            ->select(
                'pc.assignment_id',
                'pc.paper_id',
                'pc.status_code',
                'pc.assigned_at',
                'pc.deadline',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'ht.title as conference_name',
                'ht.conference_id',
                'pb.review_id',
                'pb.submitted_at',
                'pb.recommendation_code'
            )
            ->orderBy('pc.assigned_at', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('status_code', 'INVITED')->count(),
            'accepted' => $assignments->where('status_code', 'ACCEPTED')->count(),
            'completed' => $assignments->where('status_code', 'COMPLETED')->count(),
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
        $assignment = DB::table('phancongphanbien')
            ->where('assignment_id', $id)
            ->where('reviewer_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment not found or you do not have permission.');
        }
        
        // Check if already accepted or completed
        if (in_array($assignment->status_code, ['ACCEPTED', 'COMPLETED'])) {
            return redirect()->route('reviewer.assignments')
                ->with('warning', 'This assignment has already been accepted or completed.');
        }
        
        // Update status to ACCEPTED
        DB::table('phancongphanbien')
            ->where('assignment_id', $id)
            ->update([
                'status_code' => 'ACCEPTED',
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
        $assignment = DB::table('phancongphanbien')
            ->where('assignment_id', $id)
            ->where('reviewer_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment not found or you do not have permission.');
        }
        
        // Check if already completed
        if ($assignment->status_code === 'COMPLETED') {
            return redirect()->route('reviewer.assignments')
                ->with('warning', 'Cannot decline a completed review.');
        }
        
        // Update status to DECLINED
        DB::table('phancongphanbien')
            ->where('assignment_id', $id)
            ->update([
                'status_code' => 'DECLINED',
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
        $reviews = DB::table('PhanBien as pb')
            ->join('PhanCongPhanBien as pc', 'pb.assignment_id', '=', 'pc.assignment_id')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pc.reviewer_id', $userId)
            ->select(
                'pb.review_id',
                'pb.assignment_id',
                'pc.paper_id',
                'pb.recommendation_code',
                'pb.score',
                'pb.submitted_at',
                'bb.title as paper_title',
                'bb.status_code as paper_status',
                'ht.title as conference_name',
                'pc.deadline'
            )
            ->orderBy('pb.submitted_at', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => $reviews->count(),
            'average_score' => $reviews->count() > 0 ? round($reviews->avg('score'), 1) : 0,
            'accept' => $reviews->where('recommendation_code', 'ACCEPT')->count(),
            'reject' => $reviews->where('recommendation_code', 'REJECT')->count(),
        ];
        
        return view('reviewer.reviews.index', compact('reviews', 'stats'));
    }
    
    /**
     * Show form to create a review
     */
    public function createReview($assignmentId)
    {
        $userId = Auth::id();
        
        // Get assignment details
        $assignment = DB::table('PhanCongPhanBien as pc')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pc.assignment_id', $assignmentId)
            ->where('pc.reviewer_id', $userId)
            ->select(
                'pc.assignment_id',
                'pc.paper_id',
                'pc.status_code',
                'pc.deadline',
                'bb.title',
                'bb.abstract',
                'bb.keywords',
                'bb.file_path',
                'ht.title as conference_name',
                'ht.conference_id'
            )
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment not found or you do not have permission.');
        }
        
        // Check if assignment is accepted
        if ($assignment->status_code !== 'ACCEPTED') {
            return redirect()->route('reviewer.assignments')
                ->with('warning', 'You must accept the assignment before submitting a review.');
        }
        
        // Check if review already exists
        $existingReview = DB::table('phanbien')
            ->where('assignment_id', $assignmentId)
            ->first();
        
        if ($existingReview) {
            return redirect()->route('reviewer.reviews.edit', $existingReview->review_id)
                ->with('info', 'You have already started this review. Continue editing.');
        }
        
        // Get paper authors (excluding submitter to show only co-authors)
        $authors = DB::table('TacGiaBaiBao as tg')
            ->join('NguoiDung as nd', 'tg.user_id', '=', 'nd.user_id')
            ->where('tg.paper_id', $assignment->paper_id)
            ->select('nd.full_name', 'nd.organization', 'tg.author_order', 'tg.is_contact')
            ->orderBy('tg.author_order')
            ->get();
        
        return view('reviewer.reviews.create', compact('assignment', 'authors'));
    }
    
    /**
     * Store a new review
     */
    public function storeReview(Request $request)
    {
        $userId = Auth::id();
        
        $validated = $request->validate([
            'assignment_id' => 'required|exists:phancongphanbien,assignment_id',
            'score' => 'required|integer|min:1|max:10',
            'recommendation_code' => 'required|in:ACCEPT,MINOR_REVISION,MAJOR_REVISION,REJECT',
            'comment_author' => 'required|string|min:50',
            'comment_chair' => 'nullable|string',
        ]);
        
        // Verify assignment belongs to this reviewer
        $assignment = DB::table('phancongphanbien')
            ->where('assignment_id', $validated['assignment_id'])
            ->where('reviewer_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Invalid assignment.');
        }
        
        // Check if assignment is accepted
        if ($assignment->status_code !== 'ACCEPTED') {
            return redirect()->route('reviewer.assignments')
                ->with('error', 'Assignment must be accepted before submitting review.');
        }
        
        // Check if review already exists
        $existingReview = DB::table('phanbien')
            ->where('assignment_id', $validated['assignment_id'])
            ->first();
        
        if ($existingReview) {
            return redirect()->route('reviewer.reviews.edit', $existingReview->review_id)
                ->with('warning', 'Review already exists. Please edit instead.');
        }
        
        DB::beginTransaction();
        try {
            // Insert review
            $reviewId = DB::table('phanbien')->insertGetId([
                'assignment_id' => $validated['assignment_id'],
                'recommendation_code' => $validated['recommendation_code'],
                'score' => $validated['score'],
                'comment_author' => $validated['comment_author'],
                'comment_chair' => $validated['comment_chair'],
                'submitted_at' => now(),
            ]);
            
            // Update assignment status to COMPLETED
            DB::table('phancongphanbien')
                ->where('assignment_id', $validated['assignment_id'])
                ->update([
                    'status_code' => 'COMPLETED',
                ]);
            
            DB::commit();
            
            return redirect()->route('reviewer.reviews.show', $reviewId)
                ->with('success', 'Review submitted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }
    
    /**
     * Show a specific review
     */
    public function showReview($id)
    {
        $userId = Auth::id();
        
        // Get review details
        $review = DB::table('PhanBien as pb')
            ->join('PhanCongPhanBien as pc', 'pb.assignment_id', '=', 'pc.assignment_id')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pb.review_id', $id)
            ->where('pc.reviewer_id', $userId)
            ->select(
                'pb.*',
                'pc.paper_id',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.file_path',
                'ht.title as conference_name',
                'pc.deadline',
                'pc.assigned_at'
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
        $review = DB::table('PhanBien as pb')
            ->join('PhanCongPhanBien as pc', 'pb.assignment_id', '=', 'pc.assignment_id')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->where('pb.review_id', $id)
            ->where('pc.reviewer_id', $userId)
            ->select(
                'pb.*',
                'pc.assignment_id',
                'pc.paper_id',
                'pc.deadline',
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
        $authors = DB::table('TacGiaBaiBao as tg')
            ->join('NguoiDung as nd', 'tg.user_id', '=', 'nd.user_id')
            ->where('tg.paper_id', $review->paper_id)
            ->select('nd.full_name', 'nd.organization', 'tg.author_order', 'tg.is_contact')
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
        $review = DB::table('PhanBien as pb')
            ->join('PhanCongPhanBien as pc', 'pb.assignment_id', '=', 'pc.assignment_id')
            ->where('pb.review_id', $id)
            ->where('pc.reviewer_id', $userId)
            ->select('pb.*', 'pc.reviewer_id')
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
    public function downloadPaper($assignmentId)
    {
        $userId = Auth::id();
        
        // Get assignment and paper details
        $assignment = DB::table('PhanCongPhanBien as pc')
            ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
            ->where('pc.assignment_id', $assignmentId)
            ->where('pc.reviewer_id', $userId)
            ->select('bb.file_path', 'bb.title')
            ->first();
        
        if (!$assignment) {
            abort(404, 'Paper not found or you do not have permission.');
        }
        
        if (!$assignment->file_path || !Storage::exists($assignment->file_path)) {
            abort(404, 'Paper file not found.');
        }
        
        return Storage::download($assignment->file_path, $assignment->title . '.pdf');
    }
}




