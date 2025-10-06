<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the author's papers
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get all papers submitted by this author
        $papers = DB::table('BaiBao')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
            ->where('BaiBao.submitter_id', $userId)
            ->select(
                'BaiBao.paper_id',
                'BaiBao.title',
                'BaiBao.created_at',
                'BaiBao.status_code',
                'HoiThao.title as conference_title',
                'HoiThao.conference_id',
                'TrangThaiBaiBao.status_name'
            )
            ->orderBy('BaiBao.created_at', 'desc')
            ->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => DB::table('BaiBao')->where('submitter_id', $userId)->count(),
            'draft' => DB::table('BaiBao')->where('submitter_id', $userId)->where('status_code', 'DRAFT')->count(),
            'submitted' => DB::table('BaiBao')->where('submitter_id', $userId)->where('status_code', 'SUBMITTED')->count(),
            'under_review' => DB::table('BaiBao')->where('submitter_id', $userId)->where('status_code', 'UNDER_REVIEW')->count(),
            'accepted' => DB::table('BaiBao')->where('submitter_id', $userId)->where('status_code', 'ACCEPTED')->count(),
            'rejected' => DB::table('BaiBao')->where('submitter_id', $userId)->where('status_code', 'REJECTED')->count(),
        ];
        
        return view('author.papers.index', compact('papers', 'stats'));
    }

    /**
     * Show the form for creating a new paper
     */
    public function create()
    {
        // Get active conferences with open submissions
        $conferences = DB::table('HoiThao')
            ->where('status', 'ACTIVE')
            ->where('deadline_submission', '>', now())
            ->select('conference_id', 'title', 'deadline_submission')
            ->orderBy('deadline_submission', 'asc')
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
            'conference_id' => 'required|exists:HoiThao,conference_id',
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
        $conference = DB::table('HoiThao')
            ->where('conference_id', $validated['conference_id'])
            ->first();
        
        if (!$conference || $conference->deadline_submission < now()) {
            return back()->withErrors(['conference_id' => 'Deadline nộp bài đã qua.'])->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Insert paper
            $paperId = DB::table('BaiBao')->insertGetId([
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
                DB::table('BaiBao')
                    ->where('paper_id', $paperId)
                    ->update(['file_path' => $path]);
            }
            
            // Add submitter as first author (contact author by default)
            DB::table('TacGiaBaiBao')->insert([
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
                        $coAuthorUser = DB::table('NguoiDung')
                            ->where('email', $coAuthor['email'])
                            ->first();
                        
                        if (!$coAuthorUser) {
                            // Create new user for co-author
                            $coAuthorUserId = DB::table('NguoiDung')->insertGetId([
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
                        DB::table('TacGiaBaiBao')->insert([
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
        $paper = DB::table('BaiBao')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
            ->where('BaiBao.paper_id', $id)
            ->where('BaiBao.submitter_id', $userId)
            ->select(
                'BaiBao.*',
                'HoiThao.title as conference_title',
                'HoiThao.deadline_submission',
                'TrangThaiBaiBao.status_name'
            )
            ->first();
        
        if (!$paper) {
            abort(404, 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập.');
        }
        
        // Get authors
        $authors = DB::table('TacGiaBaiBao')
            ->join('NguoiDung', 'TacGiaBaiBao.user_id', '=', 'NguoiDung.user_id')
            ->where('TacGiaBaiBao.paper_id', $id)
            ->select(
                'NguoiDung.user_id',
                'NguoiDung.full_name',
                'NguoiDung.email',
                'NguoiDung.organization',
                'TacGiaBaiBao.author_order',
                'TacGiaBaiBao.is_contact'
            )
            ->orderBy('TacGiaBaiBao.author_order')
            ->get();
        
        // Get review assignments
        $assignments = DB::table('PhanCongPhanBien')
            ->where('paper_id', $id)
            ->select('assignment_id', 'status_code', 'assigned_at', 'deadline')
            ->get();
        
        // Get reviews (only if paper is under review or later)
        $reviews = [];
        if (in_array($paper->status_code, ['UNDER_REVIEW', 'ACCEPTED', 'REJECTED'])) {
            $reviews = DB::table('PhanBien')
                ->join('PhanCongPhanBien', 'PhanBien.assignment_id', '=', 'PhanCongPhanBien.assignment_id')
                ->where('PhanCongPhanBien.paper_id', $id)
                ->whereNotNull('PhanBien.submitted_at')
                ->select(
                    'PhanBien.review_id',
                    'PhanBien.score',
                    'PhanBien.recommendation',
                    'PhanBien.review_content',
                    'PhanBien.submitted_at'
                )
                ->get();
        }
        
        return view('author.papers.show', compact('paper', 'authors', 'assignments', 'reviews'));
    }

    /**
     * Show the form for editing the specified paper
     */
    public function edit($id)
    {
        $userId = Auth::id();
        
        // Get paper
        $paper = DB::table('BaiBao')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->where('BaiBao.paper_id', $id)
            ->where('BaiBao.submitter_id', $userId)
            ->select('BaiBao.*', 'HoiThao.deadline_submission')
            ->first();
        
        if (!$paper) {
            abort(404, 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập.');
        }
        
        // Check if can edit
        if (!in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return redirect()
                ->route('author.papers.show', $id)
                ->withErrors(['error' => 'Không thể chỉnh sửa bài báo ở trạng thái này.']);
        }
        
        // Check deadline
        if ($paper->deadline_submission < now()) {
            return redirect()
                ->route('author.papers.show', $id)
                ->withErrors(['error' => 'Đã quá hạn nộp bài.']);
        }
        
        // Get active conferences
        $conferences = DB::table('HoiThao')
            ->where('status', 'ACTIVE')
            ->where('deadline_submission', '>', now())
            ->select('conference_id', 'title', 'deadline_submission')
            ->get();
        
        // Get current authors (excluding submitter)
        $coAuthors = DB::table('TacGiaBaiBao')
            ->join('NguoiDung', 'TacGiaBaiBao.user_id', '=', 'NguoiDung.user_id')
            ->where('TacGiaBaiBao.paper_id', $id)
            ->where('TacGiaBaiBao.user_id', '!=', $userId)
            ->select(
                'NguoiDung.user_id',
                'NguoiDung.full_name',
                'TacGiaBaiBao.author_order',
                'TacGiaBaiBao.is_contact'
            )
            ->orderBy('TacGiaBaiBao.author_order')
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
            'conference_id' => 'required|exists:HoiThao,conference_id',
            'title' => 'required|string|max:500',
            'abstract' => 'required|string',
            'keywords' => 'required|string|max:500',
            'paper_file' => 'nullable|file|mimes:pdf|max:10240',
            'co_authors' => 'nullable|array',
        ]);
        
        // Get paper
        $paper = DB::table('BaiBao')
            ->where('paper_id', $id)
            ->where('submitter_id', $userId)
            ->first();
        
        if (!$paper) {
            abort(404);
        }
        
        // Check if can edit
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
            
            DB::table('BaiBao')
                ->where('paper_id', $id)
                ->update($updateData);
            
            // Update co-authors (delete all except submitter, then re-add)
            DB::table('TacGiaBaiBao')
                ->where('paper_id', $id)
                ->where('user_id', '!=', $userId)
                ->delete();
            
            if (!empty($validated['co_authors'])) {
                $order = 2;
                foreach ($validated['co_authors'] as $coAuthor) {
                    if (!empty($coAuthor['user_id'])) {
                        DB::table('TacGiaBaiBao')->insert([
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
        
        // Get paper
        $paper = DB::table('BaiBao')
            ->where('paper_id', $id)
            ->where('submitter_id', $userId)
            ->first();
        
        if (!$paper) {
            abort(404);
        }
        
        // Cannot withdraw if already accepted
        if ($paper->status_code === 'ACCEPTED') {
            return back()->withErrors(['error' => 'Không thể rút bài báo đã được chấp nhận.']);
        }
        
        // Update status
        DB::table('BaiBao')
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
        
        $paper = DB::table('BaiBao')
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

