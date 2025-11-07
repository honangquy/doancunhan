<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if paper can be edited based on conference deadlines and paper status
     */
    private function canEditPaper($paper)
    {
        $now = Carbon::now();
        $submissionDeadline = Carbon::parse($paper->deadline_submission);
        $cameraReadyDeadline = $paper->deadline_camera_ready ? Carbon::parse($paper->deadline_camera_ready) : null;
        
        // Kiểm tra xem có reviewer nào đã hoàn thành review chưa
        if ($this->hasCompletedReviews($paper->paper_id)) {
            return ['can_edit' => false, 'reason' => 'Không thể chỉnh sửa khi đã có reviewer hoàn thành phản biện.'];
        }
        
        // Giai đoạn 1: TRƯỚC Deadline Nộp bài - cho phép chỉnh sửa
        if ($now->lt($submissionDeadline) && in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return ['can_edit' => true, 'reason' => ''];
        }
        
        // Giai đoạn 2: SAU Deadline Nộp bài hoặc đang review - KHÔNG cho phép
        if ($now->gte($submissionDeadline) && in_array($paper->status_code, ['SUBMITTED', 'UNDER_REVIEW'])) {
            return ['can_edit' => false, 'reason' => 'Đã quá hạn nộp bài hoặc bài đang được phản biện.'];
        }
        
        // Giai đoạn 3: Có kết quả review
        if (in_array($paper->status_code, ['ACCEPTED', 'REJECTED'])) {
            // Nếu bị từ chối - KHÔNG cho phép chỉnh sửa
            if ($paper->status_code === 'REJECTED') {
                return ['can_edit' => false, 'reason' => 'Bài báo đã bị từ chối, không thể chỉnh sửa.'];
            }
            
            // Nếu được chấp nhận - chỉ cho phép trong thời hạn camera-ready
            if ($paper->status_code === 'ACCEPTED') {
                if ($cameraReadyDeadline && $now->lt($cameraReadyDeadline)) {
                    return ['can_edit' => true, 'reason' => ''];
                } else {
                    return ['can_edit' => false, 'reason' => 'Đã quá hạn camera-ready hoặc chưa có deadline camera-ready.'];
                }
            }
        }
        
        // Các trạng thái khác (WITHDRAWN, etc.) - KHÔNG cho phép
        return ['can_edit' => false, 'reason' => 'Trạng thái bài báo không cho phép chỉnh sửa.'];
    }
    
    /**
     * Check if paper can be withdrawn based on conference deadlines and paper status
     */
    private function canWithdrawPaper($paper)
    {
        $now = Carbon::now();
        $submissionDeadline = Carbon::parse($paper->deadline_submission);
        
        // Kiểm tra xem có reviewer nào đã hoàn thành review chưa
        if ($this->hasCompletedReviews($paper->paper_id)) {
            return ['can_withdraw' => false, 'reason' => 'Không thể rút bài khi đã có reviewer hoàn thành phản biện.'];
        }
        
        // Giai đoạn 1: TRƯỚC Deadline Nộp bài - cho phép rút bài
        if ($now->lt($submissionDeadline) && in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return ['can_withdraw' => true, 'reason' => ''];
        }
        
        // Giai đoạn 2: SAU Deadline Nộp bài hoặc đang review - KHÔNG cho phép
        if ($now->gte($submissionDeadline) && in_array($paper->status_code, ['SUBMITTED', 'UNDER_REVIEW'])) {
            return ['can_withdraw' => false, 'reason' => 'Đã quá hạn nộp bài hoặc bài đang được phản biện.'];
        }
        
        // Giai đoạn 3: Có kết quả review - KHÔNG cho phép rút (cả ACCEPTED và REJECTED)
        if (in_array($paper->status_code, ['ACCEPTED', 'REJECTED'])) {
            return ['can_withdraw' => false, 'reason' => 'Không thể rút bài sau khi có kết quả phản biện.'];
        }
        
        // Các trạng thái khác - KHÔNG cho phép
        return ['can_withdraw' => false, 'reason' => 'Trạng thái bài báo không cho phép rút bài.'];
    }
    
    /**
     * Check if any reviewer has completed review for this paper
     */
    private function hasCompletedReviews($paperId)
    {
        // Check if there are completed reviews (submitted and not draft)
        $completedReviews = DB::table('reviewer_assignments as ra')
            ->join('phanbien as p', 'ra.id', '=', 'p.assignment_id')
            ->where('ra.paper_id', $paperId)
            ->where('p.is_draft', 0)
            ->whereNotNull('p.submitted_at')
            ->whereNotNull('ra.review_submitted_at')
            ->count();
            
        return $completedReviews > 0;
    }

    /**
     * Display a listing of the author's papers
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get all papers submitted by this author with conference deadline info for permissions
        $papers = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
            ->where('baibao.submitter_id', $userId)
            ->select(
                'baibao.paper_id',
                'baibao.title',
                'baibao.created_at',
                'baibao.status_code',
                'hoithao.title as conference_title',
                'hoithao.conference_id',
                'hoithao.deadline_submission',
                'hoithao.deadline_camera_ready',
                'trangthaibaibao.status_name'
            )
            ->orderBy('baibao.created_at', 'desc')
            ->paginate(20);
        
        // Add permission checks for each paper
        $papers->getCollection()->transform(function ($paper) {
            $editPermission = $this->canEditPaper($paper);
            $withdrawPermission = $this->canWithdrawPaper($paper);
            
            $paper->can_edit = $editPermission['can_edit'];
            $paper->edit_reason = $editPermission['reason'];
            $paper->can_withdraw = $withdrawPermission['can_withdraw'];
            $paper->withdraw_reason = $withdrawPermission['reason'];
            
            return $paper;
        });
        
        // Get statistics
        $stats = [
            'total' => DB::table('baibao')->where('submitter_id', $userId)->count(),
            'draft' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'DRAFT')->count(),
            'submitted' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'SUBMITTED')->count(),
            'under_review' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'UNDER_REVIEW')->count(),
            'accepted' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'ACCEPTED')->count(),
            'rejected' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'REJECTED')->count(),
        ];
        
        return view('author.papers.index', compact('papers', 'stats'));
    }

    /**
     * Show the form for creating a new paper
     */
    public function create()
    {
        $userId = Auth::id();
        
        // Get active conferences with open submissions where user has APPROVED join request as AUTHOR
        $conferences = DB::table('hoithao')
            ->join('join_requests', 'hoithao.conference_id', '=', 'join_requests.conference_id')
            ->where('hoithao.status', 'ACTIVE')
            ->where('hoithao.deadline_submission', '>', now())
            ->where('join_requests.user_id', $userId)
            ->where('join_requests.role', 'AUTHOR')
            ->where('join_requests.status', 'APPROVED')
            ->select('hoithao.conference_id', 'hoithao.title', 'hoithao.deadline_submission')
            ->orderBy('hoithao.deadline_submission', 'asc')
            ->get();
        
        return view('author.papers.create', compact('conferences'));
    }

    /**
     * Store a newly created paper
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        
        // Validate request
        $validated = $request->validate([
            'conference_id' => 'required|exists:hoithao,conference_id',
            'title' => 'required|string|max:500',
            'abstract' => 'required|string',
            'keywords' => 'required|string|max:500',
            'paper_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'co_authors' => 'nullable|array',
            'co_authors.*.name' => 'nullable|string|max:255',
            'co_authors.*.email' => 'nullable|email|max:255',
            'co_authors.*.organization' => 'nullable|string|max:255',
        ]);
        
        // Check submission deadline
        $conference = DB::table('hoithao')
            ->where('conference_id', $validated['conference_id'])
            ->first();
        
        if (!$conference || $conference->deadline_submission < now()) {
            return back()->withErrors(['conference_id' => 'Deadline nộp bài đã qua.'])->withInput();
        }
        
        // Check if user has approved join request as AUTHOR for this conference
        $joinRequest = DB::table('join_requests')
            ->where('conference_id', $validated['conference_id'])
            ->where('user_id', $userId)
            ->where('role', 'AUTHOR')
            ->where('status', 'APPROVED')
            ->first();
            
        if (!$joinRequest) {
            return back()->withErrors(['conference_id' => 'Bạn chưa được phép tham gia hội thảo này với vai trò tác giả.'])->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Insert paper
            $paperId = DB::table('baibao')->insertGetId([
                'conference_id' => $validated['conference_id'],
                'submitter_id' => $userId,
                'title' => $validated['title'],
                'abstract' => $validated['abstract'],
                'keywords' => $validated['keywords'],
                'status_code' => 'SUBMITTED',
                'created_at' => now(),
            ]);
            
            // Handle file upload
            if ($request->hasFile('paper_file')) {
                $file = $request->file('paper_file');
                $filename = $paperId . '_' . time() . '.pdf';
                $path = $file->storeAs('papers/' . $validated['conference_id'], $filename);
                
                // Update paper with file path
                DB::table('baibao')
                    ->where('paper_id', $paperId)
                    ->update(['file_path' => $path]);
            }
            
            // Add submitter as first author (contact author by default)
            DB::table('tacgiabaibao')->insert([
                'paper_id' => $paperId,
                'user_id' => $userId,
                'author_order' => 1,
                'is_contact' => 1,
            ]);
            
            // Add co-authors
            if (!empty($validated['co_authors'])) {
                $order = 2;
                foreach ($validated['co_authors'] as $coAuthor) {
                    if (!empty($coAuthor['email']) && !empty($coAuthor['name'])) {
                        // Find or create user
                        $coAuthorUser = DB::table('nguoidung')
                            ->where('email', $coAuthor['email'])
                            ->first();
                        
                        if (!$coAuthorUser) {
                            // Create new user for co-author
                            $coAuthorUserId = DB::table('nguoidung')->insertGetId([
                                'email' => $coAuthor['email'],
                                'full_name' => $coAuthor['name'],
                                'organization' => $coAuthor['organization'] ?? null,
                                'password_hash' => bcrypt('temporary_password_' . time()),
                                'created_at' => now(),
                            ]);
                        } else {
                            $coAuthorUserId = $coAuthorUser->user_id;
                        }
                        
                        // Add to TacGiaBaiBao
                        DB::table('tacgiabaibao')->insert([
                            'paper_id' => $paperId,
                            'user_id' => $coAuthorUserId,
                            'author_order' => $order,
                            'is_contact' => 0,
                            'organization' => $coAuthor['organization'] ?? null,
                        ]);
                        $order++;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('author.papers.show', $paperId)
                ->with('success', 'Bài báo đã được nộp thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified paper
     */
    public function show($id)
    {
        $userId = Auth::id();
        
        // Get paper details
        $paper = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
            ->where('baibao.paper_id', $id)
            ->where('baibao.submitter_id', $userId)
            ->select(
                'baibao.*',
                'hoithao.title as conference_title',
                'hoithao.deadline_submission',
                'hoithao.deadline_camera_ready',
                'trangthaibaibao.status_name'
            )
            ->first();
        
        if (!$paper) {
            abort(404, 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập.');
        }
        
        // Check permissions for edit and withdraw
        $editPermission = $this->canEditPaper($paper);
        $withdrawPermission = $this->canWithdrawPaper($paper);
        
        // Get authors
        $authors = DB::table('tacgiabaibao')
            ->join('nguoidung', 'tacgiabaibao.user_id', '=', 'nguoidung.user_id')
            ->where('tacgiabaibao.paper_id', $id)
            ->select(
                'nguoidung.user_id',
                'nguoidung.full_name',
                'nguoidung.email',
                'nguoidung.organization',
                'tacgiabaibao.author_order',
                'tacgiabaibao.is_contact'
            )
            ->orderBy('tacgiabaibao.author_order')
            ->get();
        
        // Get review assignments with proper table names
        $assignments = DB::table('reviewer_assignments as ra')
            ->leftJoin('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
            ->where('ra.paper_id', $id)
            ->select(
                'ra.id as assignment_id',
                'ra.user_id', 
                'ra.status',
                'ra.assigned_at',
                'ra.review_submitted_at',
                'u.full_name as reviewer_name'
            )
            ->orderBy('ra.assigned_at')
            ->get();
        
        // Get completed reviews (only if paper is under review or later)
        $reviews = [];
        if (in_array($paper->status_code, ['UNDER_REVIEW', 'ACCEPTED', 'REJECTED'])) {
            $reviews = DB::table('phanbien as p')
                ->join('reviewer_assignments as ra', 'p.assignment_id', '=', 'ra.id')
                ->leftJoin('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                ->leftJoin('loaikhuyennghi as lkn', 'p.recommendation_code', '=', 'lkn.recommendation_code')
                ->where('ra.paper_id', $id)
                ->where('p.is_draft', 0)
                ->whereNotNull('p.submitted_at')
                ->select(
                    'p.review_id',
                    'p.total_score',
                    'p.recommendation_code',
                    'p.comment_author',
                    'p.submitted_at',
                    'p.score_novelty',
                    'p.score_relevance', 
                    'p.score_technical_quality',
                    'p.score_presentation',
                    'p.score_references',
                    'lkn.recommendation_name',
                    'u.full_name as reviewer_name'
                )
                ->orderBy('p.submitted_at')
                ->get();
        }
        
        return view('author.papers.show', compact(
            'paper', 
            'authors', 
            'assignments', 
            'reviews',
            'editPermission',
            'withdrawPermission'
        ));
    }

    /**
     * Show the form for editing the specified paper
     */
    public function edit($id)
    {
        $userId = Auth::id();
        
        // Get paper with conference deadline info
        $paper = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->where('baibao.paper_id', $id)
            ->where('baibao.submitter_id', $userId)
            ->select(
                'baibao.*', 
                'hoithao.deadline_submission',
                'hoithao.deadline_camera_ready'
            )
            ->first();
        
        if (!$paper) {
            abort(404, 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập.');
        }
        
        // Check if can edit based on new logic
        $editPermission = $this->canEditPaper($paper);
        if (!$editPermission['can_edit']) {
            return redirect()
                ->route('author.papers.show', $id)
                ->withErrors(['error' => $editPermission['reason']]);
        }
        
        // Get active conferences (only if before deadline)
        $conferences = DB::table('hoithao')
            ->where('status', 'ACTIVE')
            ->where('deadline_submission', '>', now())
            ->select('conference_id', 'title', 'deadline_submission')
            ->get();
        
        // Get current authors (excluding submitter)
        $coAuthors = DB::table('tacgiabaibao')
            ->join('nguoidung', 'tacgiabaibao.user_id', '=', 'nguoidung.user_id')
            ->where('tacgiabaibao.paper_id', $id)
            ->where('tacgiabaibao.user_id', '!=', $userId)
            ->select(
                'nguoidung.user_id',
                'nguoidung.full_name',
                'tacgiabaibao.author_order',
                'tacgiabaibao.is_contact'
            )
            ->orderBy('tacgiabaibao.author_order')
            ->get();
        
        return view('author.papers.edit', compact('paper', 'conferences', 'coAuthors'));
    }

    /**
     * Update the specified paper
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Validate request
        $validated = $request->validate([
            'conference_id' => 'required|exists:hoithao,conference_id',
            'title' => 'required|string|max:500',
            'abstract' => 'required|string',
            'keywords' => 'required|string|max:500',
            'paper_file' => 'nullable|file|mimes:pdf|max:10240',
            'co_authors' => 'nullable|array',
        ]);
        
        // Get paper with conference deadline info
        $paper = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->where('baibao.paper_id', $id)
            ->where('baibao.submitter_id', $userId)
            ->select(
                'baibao.*',
                'hoithao.deadline_submission',
                'hoithao.deadline_camera_ready'
            )
            ->first();
        
        if (!$paper) {
            abort(404);
        }
        
        // Check if can edit based on new logic
        $editPermission = $this->canEditPaper($paper);
        if (!$editPermission['can_edit']) {
            return back()->withErrors(['error' => $editPermission['reason']]);
        }
        if (!in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return back()->withErrors(['error' => 'Không thể chỉnh sửa bài báo này.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Update paper
            $updateData = [
                'conference_id' => $validated['conference_id'],
                'title' => $validated['title'],
                'abstract' => $validated['abstract'],
                'keywords' => $validated['keywords'],
            ];
            
            // Handle file upload if new file provided
            if ($request->hasFile('paper_file')) {
                // Delete old file
                if ($paper->file_path) {
                    Storage::delete($paper->file_path);
                }
                
                $file = $request->file('paper_file');
                $filename = $id . '_' . time() . '.pdf';
                $path = $file->storeAs('papers/' . $validated['conference_id'], $filename);
                $updateData['file_path'] = $path;
            }
            
            DB::table('baibao')
                ->where('paper_id', $id)
                ->update($updateData);
            
            // Update co-authors (delete all except submitter, then re-add)
            DB::table('tacgiabaibao')
                ->where('paper_id', $id)
                ->where('user_id', '!=', $userId)
                ->delete();
            
            if (!empty($validated['co_authors'])) {
                $order = 2;
                foreach ($validated['co_authors'] as $coAuthor) {
                    if (!empty($coAuthor['user_id'])) {
                        DB::table('tacgiabaibao')->insert([
                            'paper_id' => $id,
                            'user_id' => $coAuthor['user_id'],
                            'author_order' => $order,
                            'is_contact' => $coAuthor['is_contact'] ?? 0,
                        ]);
                        $order++;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('author.papers.show', $id)
                ->with('success', 'Bài báo đã được cập nhật thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Withdraw the specified paper
     */
    public function withdraw(Request $request, $id)
    {
        $userId = Auth::id();
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);
        
        // Get paper with conference deadline info
        $paper = DB::table('baibao')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->where('baibao.paper_id', $id)
            ->where('baibao.submitter_id', $userId)
            ->select(
                'baibao.*',
                'hoithao.deadline_submission',
                'hoithao.deadline_camera_ready'
            )
            ->first();
        
        if (!$paper) {
            abort(404);
        }
        
        // Check if can withdraw based on new logic
        $withdrawPermission = $this->canWithdrawPaper($paper);
        if (!$withdrawPermission['can_withdraw']) {
            return back()->withErrors(['error' => $withdrawPermission['reason']]);
        }
        
        // Update status
        DB::table('baibao')
            ->where('paper_id', $id)
            ->update([
                'status_code' => 'WITHDRAWN',
                'withdrawal_reason' => $validated['reason'] ?? null,
            ]);
        
        return redirect()
            ->route('author.papers.index')
            ->with('success', 'Bài báo đã được rút thành công.');
    }

    /**
     * Download paper file
     */
    public function download($id)
    {
        $userId = Auth::id();
        
        $paper = DB::table('baibao')
            ->where('paper_id', $id)
            ->where('submitter_id', $userId)
            ->first();
        
        if (!$paper || !$paper->file_path) {
            abort(404);
        }
        
        if (!Storage::exists($paper->file_path)) {
            abort(404, 'File không tồn tại.');
        }
        
        return Storage::download($paper->file_path, $paper->title . '.pdf');
    }
}