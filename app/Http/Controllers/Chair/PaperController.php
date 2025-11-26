<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chair.papers.index', [
            'title' => 'Quản lý Bài báo'
        ]);
    }

    public function show($id)
    {
        // Lấy thông tin bài báo chi tiết
        $paper = DB::table('baibao as b')
            ->leftJoin('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
            ->leftJoin('tieuban as tb', 'b.track_id', '=', 'tb.track_id')
            ->leftJoin('tacgiabaibao as tg', function($join) {
                $join->on('b.paper_id', '=', 'tg.paper_id')
                     ->where('tg.is_contact', '=', 1);
            })
            ->leftJoin('nguoidung as author', 'tg.user_id', '=', 'author.user_id')
            ->where('b.paper_id', $id)
            ->select([
                'b.*',
                'h.title as conference_name',
                'h.acronym as conference_short_name', 
                'tb.title as track_name',
                'author.full_name as author_name',
                'author.email as author_email',
                'author.affiliation as author_organization'
            ])
            ->first();

        if (!$paper) {
            abort(404, 'Bài báo không tồn tại');
        }

        // Lấy tất cả tác giả
        $authors = DB::table('tacgiabaibao as tg')
            ->join('nguoidung as u', 'tg.user_id', '=', 'u.user_id')
            ->where('tg.paper_id', $id)
            ->select([
                'u.full_name',
                'u.email', 
                'u.affiliation as organization',
                'tg.is_contact'
            ])
            ->get();

        // Lấy assignments với thông tin reviewer
        $assignments = DB::table('reviewer_assignments as ra')
            ->join('nguoidung as reviewer', 'ra.user_id', '=', 'reviewer.user_id')
            ->leftJoin('phanbien as pb', 'ra.id', '=', 'pb.assignment_id')
            ->where('ra.paper_id', $id)
            ->select([
                'ra.id as assignment_id',
                'ra.status',
                'ra.assigned_at',
                'ra.review_submitted_at',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'pb.review_id',
                'pb.is_draft',
                'pb.submitted_at',
                'pb.recommendation_code',
                'pb.score_novelty',
                'pb.score_relevance',
                'pb.score_technical_quality', 
                'pb.score_presentation',
                'pb.score_references',
                'pb.total_score',
                'pb.detailed_comments',
                'pb.comment_author',
                'pb.comment_chair'
            ])
            ->get();

        // Tính toán thống kê reviews
        $reviewStats = [
            'total' => $assignments->count(),
            'completed' => $assignments->whereNotNull('submitted_at')->where('is_draft', 0)->count(),
            'pending' => $assignments->whereNull('submitted_at')->count(),
            'accepted' => $assignments->where('status', 'ACCEPTED')->count(),
            'declined' => $assignments->where('status', 'DECLINED')->count()
        ];

        // Lấy reviews đã hoàn thành và tính điểm trung bình
        $completedReviews = $assignments->whereNotNull('submitted_at')->where('is_draft', 0);
        
        $averageScores = null;
        if ($completedReviews->count() > 0) {
            $averageScores = [
                'novelty' => round($completedReviews->avg('score_novelty'), 1),
                'relevance' => round($completedReviews->avg('score_relevance'), 1),
                'technical_quality' => round($completedReviews->avg('score_technical_quality'), 1),
                'presentation' => round($completedReviews->avg('score_presentation'), 1),
                'references' => round($completedReviews->avg('score_references'), 1),
                'total' => round($completedReviews->avg('total_score'), 1)
            ];
        }

        return view('chair.papers.show', [
            'title' => 'Chi tiết Bài báo',
            'paper' => $paper,
            'authors' => $authors,
            'assignments' => $assignments,
            'reviewStats' => $reviewStats,
            'averageScores' => $averageScores,
            'completedReviews' => $completedReviews
        ]);
    }
}




