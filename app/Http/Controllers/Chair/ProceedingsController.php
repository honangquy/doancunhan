<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProceedingsController extends Controller
{
    public function selectConference()
    {
        $userId = Auth::id();

        // Get conferences where user is chair using model
        $conferences = \App\Models\HoiThao::whereHas('vaiTroNguoiDungs', function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('role_code', 'CHAIR');
            })
            ->orderBy('conference_id', 'desc')
            ->get();

        // Add statistics and proceedings info to each conference
        foreach ($conferences as $conference) {
            // Paper statistics
            $stats = DB::table('baibao')
                ->where('conference_id', $conference->conference_id)
                ->selectRaw('
                    COUNT(*) as total_papers,
                    SUM(CASE WHEN decision = "ACCEPT" THEN 1 ELSE 0 END) as accepted_papers,
                    SUM(CASE WHEN decision = "PUBLISHED" THEN 1 ELSE 0 END) as published_papers
                ')
                ->first();

            $conference->total_papers = $stats->total_papers ?? 0;
            $conference->accepted_papers = $stats->accepted_papers ?? 0;
            $conference->published_papers = $stats->published_papers ?? 0;

            // Proceedings info
            $conference->has_proceedings = !empty($conference->proceedings_file);
            $conference->proceedings_status = $conference->has_proceedings
                ? 'Đã xuất bản'
                : 'Chưa có kỷ yếu';
        }

        return view('chair.proceedings.select', compact('conferences'));
    }

    public function index($conferenceId)
    {
        $userId = Auth::id();

        // Verify user is chair of this conference
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Get conference info
        $conference = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Get accepted papers with latest version
        $acceptedPapers = DB::table('baibao as b')
            ->join('nguoidung as submitter', 'b.submitter_id', '=', 'submitter.user_id')
            ->leftJoin('phienbanbaibao as v', function($join) {
                $join->on('b.paper_id', '=', 'v.paper_id')
                     ->whereRaw('v.version_no = (SELECT MAX(version_no) FROM phienbanbaibao WHERE paper_id = b.paper_id)');
            })
            ->where('b.conference_id', $conferenceId)
            ->where(function($query) {
                $query->where('b.decision', 'ACCEPT')
                      ->orWhere('b.decision', '')
                      ->orWhereNull('b.decision');
            })
            ->where('b.decision', '!=', 'PUBLISHED')
            ->where('b.decision', '!=', 'REJECT')
            ->select(
                'b.*',
                'submitter.full_name as submitter_name',
                'v.file_path as latest_version_path',
                'v.version_no as latest_version'
            )
            ->orderBy('b.title')
            ->get();

        // Get authors for each paper
        foreach ($acceptedPapers as $paper) {
            $authors = DB::table('tacgiabaibao as ta')
                ->join('nguoidung as u', 'ta.user_id', '=', 'u.user_id')
                ->where('ta.paper_id', $paper->paper_id)
                ->orderBy('ta.author_order')
                ->select('u.full_name', 'ta.is_contact', 'ta.organization')
                ->get();
            $paper->authors = $authors;
        }

        return view('chair.proceedings.index', compact('conference', 'acceptedPapers'));
    }

    public function updatePagination(Request $request, $conferenceId)
    {
        $userId = Auth::id();

        // Verify user is chair of this conference
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'papers' => 'required|array',
            'papers.*.paper_id' => 'required|integer|exists:baibao,paper_id',
            'papers.*.page_start' => 'required|integer|min:1',
            'papers.*.page_end' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['papers'] as $paperData) {
                // Validate that page_end >= page_start
                if ($paperData['page_end'] < $paperData['page_start']) {
                    throw new \Exception("Trang kết thúc phải lớn hơn hoặc bằng trang bắt đầu cho bài báo ID: {$paperData['paper_id']}");
                }

                DB::table('baibao')
                    ->where('paper_id', $paperData['paper_id'])
                    ->where('conference_id', $conferenceId)
                    ->update([
                        'page_start' => $paperData['page_start'],
                        'page_end' => $paperData['page_end']
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số trang thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function publish(Request $request, $conferenceId)
    {
        $userId = Auth::id();

        // Verify user is chair of this conference
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'paper_ids' => 'required|array',
            'paper_ids.*' => 'integer',
        ]);

        // Debug: Log the input
        \Log::info('Publishing papers', [
            'paper_ids' => $validated['paper_ids'],
            'conference_id' => $conferenceId,
            'user_id' => $userId
        ]);

        DB::beginTransaction();

        try {
            // Check papers exist and are eligible for publishing (ACCEPT status or empty/null decision)
            $eligiblePapers = DB::table('baibao')
                ->whereIn('paper_id', $validated['paper_ids'])
                ->where('conference_id', $conferenceId)
                ->where(function($query) {
                    $query->where('decision', 'ACCEPT')
                          ->orWhere('decision', '')
                          ->orWhereNull('decision');
                })
                ->pluck('paper_id')
                ->toArray();

            \Log::info('Eligible papers for publishing', ['eligible' => $eligiblePapers]);

            if (empty($eligiblePapers)) {
                DB::rollBack();
                return response()->json(['error' => 'Không tìm thấy bài báo đủ điều kiện để xuất bản'], 400);
            }

            // Update papers to published status
            $updatedCount = DB::table('baibao')
                ->whereIn('paper_id', $eligiblePapers)
                ->where('conference_id', $conferenceId)
                ->update([
                    'decision' => 'PUBLISHED',
                    'published_at' => now(),
                ]);

            \Log::info('Papers updated', ['updated_count' => $updatedCount]);

            // Debug: Check if papers were actually updated
            $checkUpdated = DB::table('baibao')
                ->whereIn('paper_id', $eligiblePapers)
                ->whereNotNull('published_at')
                ->count();
            \Log::info('Verification: Papers with published_at set', ['count' => $checkUpdated]);

            // Log activity for successfully updated papers
            foreach ($eligiblePapers as $paperId) {
                DB::table('activity_logs')->insert([
                    'user_id' => $userId,
                    'log_type' => 'proceedings_publish',
                    'action' => 'PAPER_PUBLISHED',
                    'description' => 'Chair đã xuất bản bài báo vào kỷ yếu hội thảo',
                    'model_type' => 'App\\Models\\BaiBao',
                    'model_id' => $paperId,
                    'severity' => 'high',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã xuất bản {$updatedCount} bài báo vào kỷ yếu!",
                'published_count' => $updatedCount,
                'published_papers' => $eligiblePapers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function proceedings($conferenceId)
    {
        // Get conference info
        $conference = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Get published papers with pagination info
        $publishedPapers = DB::table('baibao as b')
            ->join('nguoidung as submitter', 'b.submitter_id', '=', 'submitter.user_id')
            ->leftJoin('phienbanbaibao as v', function($join) {
                $join->on('b.paper_id', '=', 'v.paper_id')
                     ->whereRaw('v.version_no = (SELECT MAX(version_no) FROM phienbanbaibao WHERE paper_id = b.paper_id)');
            })
            ->where('b.conference_id', $conferenceId)
            ->whereNotNull('b.published_at')
            ->select(
                'b.*',
                'submitter.full_name as submitter_name',
                'v.file_path as latest_version_path',
                'v.version_no as latest_version'
            )
            ->orderBy('b.page_start')
            ->get();

        // Get authors for each paper
        foreach ($publishedPapers as $paper) {
            $authors = DB::table('tacgiabaibao as ta')
                ->join('nguoidung as u', 'ta.user_id', '=', 'u.user_id')
                ->where('ta.paper_id', $paper->paper_id)
                ->orderBy('ta.author_order')
                ->select('u.full_name', 'ta.is_contact', 'ta.organization')
                ->get();
            $paper->authors = $authors;
        }

        return view('chair.proceedings.show', compact('conference', 'publishedPapers'));
    }

    public function downloadPaper($conferenceId, $paperId)
    {
        $userId = Auth::id();

        // Verify user is chair of this conference
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Get paper with latest version
        $paper = DB::table('baibao as b')
            ->leftJoin('phienbanbaibao as v', function($join) {
                $join->on('b.paper_id', '=', 'v.paper_id')
                     ->whereRaw('v.version_no = (SELECT MAX(version_no) FROM phienbanbaibao WHERE paper_id = b.paper_id)');
            })
            ->where('b.paper_id', $paperId)
            ->where('b.conference_id', $conferenceId)
            ->whereIn('b.decision', ['ACCEPT', 'PUBLISHED'])
            ->select('b.*', 'v.file_path as latest_version_path')
            ->first();

        if (!$paper || !$paper->latest_version_path) {
            abort(404, 'File không tồn tại');
        }

        $filePath = storage_path('app/' . $paper->latest_version_path);

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại trên server');
        }

        // Generate a clean filename
        $fileName = 'Paper_' . $paperId . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $paper->title) . '.pdf';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Hiển thị form upload kỷ yếu hội thảo
     * Chỉ Chair/Admin của hội thảo mới truy cập được
     */
    public function showUploadForm($conferenceId)
    {
        $userId = Auth::id();

        // Get conference
        $conference = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$conference) {
            abort(404, 'Hội thảo không tồn tại.');
        }

        // Kiểm tra quyền: phải là Chair của hội thảo hoặc Admin
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        $user = Auth::user();
        // Check if user is ADMIN
        $isAdmin = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'ADMIN')
            ->whereNull('conference_id')
            ->exists();

        if (!$isChair && !$isAdmin) {
            abort(403, 'Bạn không có quyền truy cập. Chỉ Chair hoặc Admin mới có quyền upload kỷ yếu.');
        }

        $hasProceedings = !empty($conference->proceedings_file);

        return view('chair.proceedings.upload', compact('conference', 'hasProceedings', 'isAdmin'));
    }

    /**
     * Upload file kỷ yếu PDF
     *
     * Quy trình:
     * - Validate file PDF (max 50MB)
     * - Lưu vào storage/app/public/proceedings/{conference_id}/proceedings.pdf
     * - Cập nhật DB: proceedings_file, proceedings_published_at
     * - Chỉ Admin mới được upload lại nếu đã có kỷ yếu
     */
    public function uploadProceedings(Request $request, $conferenceId)
    {
        $userId = Auth::id();

        // Get conference
        $conference = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$conference) {
            abort(404, 'Hội thảo không tồn tại.');
        }

        // Kiểm tra quyền
        $isChair = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role_code', 'CHAIR')
            ->exists();

        $user = Auth::user();
        // Check if user is ADMIN
        $isAdmin = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'ADMIN')
            ->whereNull('conference_id')
            ->exists();

        if (!$isChair && !$isAdmin) {
            abort(403, 'Bạn không có quyền upload kỷ yếu.');
        }

        // Nếu đã có kỷ yếu và user KHÔNG phải Admin → từ chối
        if (!empty($conference->proceedings_file) && !$isAdmin) {
            abort(403, 'Kỷ yếu đã được xuất bản, không thể upload lại. Chỉ Admin mới có quyền thay thế kỷ yếu.');
        }

        // Validate file upload
        $request->validate([
            'proceedings_file' => 'required|file|mimes:pdf|max:51200', // max 50MB
        ], [
            'proceedings_file.required' => 'Vui lòng chọn file kỷ yếu PDF.',
            'proceedings_file.file' => 'File tải lên không hợp lệ.',
            'proceedings_file.mimes' => 'Chỉ chấp nhận file PDF.',
            'proceedings_file.max' => 'Dung lượng file không được vượt quá 50MB.',
        ]);

        try {
            $file = $request->file('proceedings_file');

            // Xóa file cũ nếu đang thay thế (Admin override)
            if (!empty($conference->proceedings_file) && Storage::disk('public')->exists($conference->proceedings_file)) {
                Storage::disk('public')->delete($conference->proceedings_file);
            }

            // Lưu file mới với tên chuẩn: proceedings.pdf
            $path = $file->storeAs(
                "proceedings/{$conferenceId}",
                'proceedings.pdf',
                'public'
            );

            // Cập nhật database
            DB::table('hoithao')
                ->where('conference_id', $conferenceId)
                ->update([
                    'proceedings_file' => $path,
                    'proceedings_published_at' => now(),
                ]);

            $message = $isAdmin && !empty($conference->proceedings_file)
                ? 'Đã thay thế kỷ yếu thành công.'
                : 'Đã upload kỷ yếu thành công.';

            return redirect()
                ->route('chair.proceedings.upload', $conferenceId)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Upload proceedings error: ' . $e->getMessage(), [
                'conference_id' => $conferenceId,
                'user_id' => $userId,
            ]);

            return back()
                ->with('error', 'Có lỗi xảy ra khi upload kỷ yếu. Vui lòng thử lại.')
                ->withInput();
        }
    }
}
