<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\HoiThao;
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
     * Hiển thị danh sách hội thảo mà Author tham gia để chọn xem kỷ yếu
     */
    public function index()
    {
        $userId = Auth::id();

        // Lấy danh sách hội thảo mà user có role AUTHOR
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('role_code', 'AUTHOR');
            })
            ->orderBy('start_date', 'desc')
            ->get();

        // Thêm thông tin về kỷ yếu cho mỗi hội thảo
        foreach ($conferences as $conference) {
            $conference->has_proceedings = !empty($conference->proceedings_file);

            // Đếm số bài báo của author trong hội thảo này
            $conference->my_papers_count = DB::table('baibao')
                ->where('submitter_id', $userId)
                ->where('conference_id', $conference->conference_id)
                ->count();
        }

        return view('author.proceedings.index', [
            'title' => 'Kỷ yếu hội thảo',
            'conferences' => $conferences
        ]);
    }

    /**
     * Xem kỷ yếu của một hội thảo cụ thể
     */
    public function show($conferenceId)
    {
        $userId = Auth::id();

        // Lấy thông tin hội thảo
        $conference = HoiThao::where('conference_id', $conferenceId)->first();

        if (!$conference) {
            abort(404, 'Không tìm thấy hội thảo.');
        }

        // Kiểm tra user có role AUTHOR trong hội thảo này không
        $isAuthor = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'AUTHOR')
            ->exists();

        if (!$isAuthor) {
            abort(403, 'Bạn không có quyền truy cập kỷ yếu của hội thảo này. Chỉ tác giả tham gia hội thảo mới được xem.');
        }

        // Kiểm tra kỷ yếu đã được xuất bản chưa
        $hasProceedings = !empty($conference->proceedings_file);

        return view('author.proceedings.show', [
            'title' => 'Kỷ yếu - ' . $conference->title,
            'conference' => $conference,
            'hasProceedings' => $hasProceedings
        ]);
    }

    /**
     * Download a published paper from proceedings (legacy - giữ lại để tương thích)
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
