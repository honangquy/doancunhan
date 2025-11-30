<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProceedingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display proceedings for author's published papers
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get conferences where author has published papers
        $conferences = DB::table('hoithao as h')
            ->join('baibao as b', 'h.conference_id', '=', 'b.conference_id')
            ->where('b.submitter_id', $userId)
            ->where('b.decision', 'PUBLISHED')
            ->select(
                'h.conference_id',
                'h.title',
                'h.acronym',
                'h.description',
                'h.start_date',
                'h.end_date',
                'h.location',
                'h.year',
                DB::raw('COUNT(b.paper_id) as published_papers_count')
            )
            ->groupBy('h.conference_id', 'h.title', 'h.acronym', 'h.description', 'h.start_date', 'h.end_date', 'h.location', 'h.year')
            ->orderBy('h.start_date', 'desc')
            ->get();

        return view('author.proceedings.index', [
            'title' => 'Kỷ yếu hội thảo',
            'conferences' => $conferences
        ]);
    }

    /**
     * Show proceedings for a specific conference
     */
    public function show($conferenceId)
    {
        $userId = Auth::id();

        // Get conference details
        $conference = DB::table('hoithao')->where('conference_id', $conferenceId)->first();
        
        if (!$conference) {
            abort(404, 'Không tìm thấy hội thảo.');
        }

        // Check if user has published papers in this conference
        $hasPublishedPapers = DB::table('baibao')
            ->where('submitter_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('decision', 'PUBLISHED')
            ->exists();

        if (!$hasPublishedPapers) {
            abort(403, 'Bạn không có bài báo đã xuất bản trong hội thảo này.');
        }

        // Get all published papers in this conference (not just user's papers)
        $publishedPapers = DB::table('baibao as b')
            ->join('nguoidung as n', 'b.submitter_id', '=', 'n.user_id')
            ->leftJoin('tieuban as tb', 'b.track_id', '=', 'tb.track_id')
            ->where('b.conference_id', $conferenceId)
            ->where('b.decision', 'PUBLISHED')
            ->select(
                'b.paper_id',
                'b.title',
                'b.abstract',
                'b.keywords',
                'b.start_page',
                'b.end_page',
                'b.file_path',
                'n.full_name as author_name',
                'n.email as author_email',
                'tb.title as track_name'
            )
            ->orderBy('b.start_page', 'asc')
            ->get();

        // Get user's published papers count in this conference  
        $myPublishedPapersCount = DB::table('baibao')
            ->where('submitter_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('decision', 'PUBLISHED')
            ->count();

        return view('author.proceedings.show', [
            'title' => 'Kỷ yếu - ' . $conference->title,
            'conference' => $conference,
            'publishedPapers' => $publishedPapers,
            'myPublishedPapersCount' => $myPublishedPapersCount
        ]);
    }

    /**
     * Download a published paper from proceedings
     */
    public function downloadPaper($conferenceId, $paperId)
    {
        $userId = Auth::id();

        // Verify the paper exists and is published
        $paper = DB::table('baibao as b')
            ->join('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
            ->where('b.paper_id', $paperId)
            ->where('b.conference_id', $conferenceId)
            ->where('b.decision', 'PUBLISHED')
            ->select('b.*', 'h.title as conference_title')
            ->first();

        if (!$paper) {
            abort(404, 'Không tìm thấy bài báo hoặc bài báo chưa được xuất bản.');
        }

        // Check if user has access (either author of the paper or has published papers in same conference)
        $hasAccess = DB::table('baibao')
            ->where('submitter_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('decision', 'PUBLISHED')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền tải xuống bài báo này.');
        }

        if (!$paper->file_path || !Storage::exists($paper->file_path)) {
            abort(404, 'File bài báo không tồn tại.');
        }

        return Storage::download($paper->file_path, $paper->title . '.pdf');
    }
}