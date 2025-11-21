<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ReviewerPaperCandidate;
use App\Models\BaiBao;
use App\Models\User;
use App\Models\ReviewerPreference;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CandidateListController extends Controller
{
    /**
     * Hiển thị trang quản lý gửi candidate list
     */
    public function index(Request $request)
    {
        $conferenceId = $request->get('conference_id');

        // Lấy danh sách hội thảo mà user là CHAIR
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) {
            $query->where('user_id', Auth::id())
                  ->where('role_code', 'CHAIR');
        })->get();

        if (!$conferenceId && $conferences->isNotEmpty()) {
            $conferenceId = $conferences->first()->conference_id;
        }

        $reviewers = [];
        $statistics = [];

        if ($conferenceId) {
            // Lấy danh sách reviewers của hội thảo
            $reviewers = User::whereHas('roles', function ($query) use ($conferenceId) {
                $query->where('role_code', 'REVIEWER')
                      ->where('conference_id', $conferenceId);
            })->with(['reviewerPreferences' => function ($query) use ($conferenceId) {
                $query->where('conference_id', $conferenceId);
            }])->get()->map(function ($reviewer) use ($conferenceId) {
                $preference = $reviewer->reviewerPreferences->first();
                $candidatesCount = ReviewerPaperCandidate::where('reviewer_id', $reviewer->user_id)
                    ->where('conference_id', $conferenceId)
                    ->count();

                return [
                    'user_id' => $reviewer->user_id,
                    'full_name' => $reviewer->full_name,
                    'email' => $reviewer->email,
                    'affiliation' => $reviewer->affiliation,
                    'max_papers_wanted' => $preference ? $preference->max_papers_wanted : null,
                    'candidates_sent' => $candidatesCount,
                    'has_preference' => (bool) $preference
                ];
            });

            // Statistics
            $statistics = [
                'total_reviewers' => $reviewers->count(),
                'with_preferences' => $reviewers->where('has_preference', true)->count(),
                'without_preferences' => $reviewers->where('has_preference', false)->count(),
                'total_candidates_sent' => ReviewerPaperCandidate::where('conference_id', $conferenceId)->count()
            ];
        }

        return view('chair.candidates.index', compact('conferences', 'conferenceId', 'reviewers', 'statistics'));
    }

    /**
     * Hiển thị form chọn bài để gửi cho 1 reviewer
     */
    public function create($reviewerId, Request $request)
    {
        $conferenceId = $request->get('conference_id');

        if (!$conferenceId) {
            return back()->with('error', 'Vui lòng chọn hội thảo');
        }

        // Check CHAIR role
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $conferenceId)) {
            abort(403, 'Bạn không có quyền quản lý hội thảo này');
        }

        $reviewer = User::findOrFail($reviewerId);
        $preference = $reviewer->getPreferenceForConference($conferenceId);

        // Lấy danh sách bài phù hợp để gửi
        $papers = BaiBao::where('conference_id', $conferenceId)
            ->whereNotIn('paper_id', function ($query) use ($reviewerId) {
                // Loại bài mà reviewer là tác giả
                $query->select('paper_id')
                    ->from('TacGiaBaiBao')
                    ->where('user_id', $reviewerId);
            })
            ->with(['tacGias', 'tieuBan', 'activeAssignments'])
            ->get()
            ->map(function ($paper) use ($reviewerId, $conferenceId) {
                $alreadySent = ReviewerPaperCandidate::where('paper_id', $paper->paper_id)
                    ->where('reviewer_id', $reviewerId)
                    ->where('conference_id', $conferenceId)
                    ->exists();

                return [
                    'paper_id' => $paper->paper_id,
                    'title' => $paper->title,
                    'authors' => $paper->tacGias->pluck('full_name')->join(', '),
                    'track' => $paper->tieuBan->title ?? 'N/A',
                    'reviewers_count' => $paper->activeAssignments->count(),
                    'already_sent' => $alreadySent
                ];
            });

        return view('chair.candidates.create', compact('reviewer', 'preference', 'papers', 'conferenceId'));
    }

    /**
     * Gửi candidate list cho reviewer
     */
    public function store(Request $request)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:nguoidung,user_id',
            'conference_id' => 'required|exists:hoithao,conference_id',
            'paper_ids' => 'required|array|min:1',
            'paper_ids.*' => 'exists:baibao,paper_id',
            'round_no' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:500'
        ], [
            'paper_ids.required' => 'Vui lòng chọn ít nhất 1 bài báo',
            'paper_ids.min' => 'Vui lòng chọn ít nhất 1 bài báo'
        ]);

        // Check CHAIR role
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $request->conference_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý hội thảo này'
            ], 403);
        }

        $reviewer = User::findOrFail($request->reviewer_id);
        $preference = $reviewer->getPreferenceForConference($request->conference_id);

        // Validate: số bài gửi nên > max_papers_wanted (để reviewer có dư lựa chọn)
        if ($preference && count($request->paper_ids) < $preference->max_papers_wanted) {
            return back()->with('warning',
                "Nên gửi nhiều hơn {$preference->max_papers_wanted} bài để reviewer có dư lựa chọn. " .
                "Hiện tại bạn chỉ chọn " . count($request->paper_ids) . " bài."
            );
        }

        $roundNo = $request->round_no ?? 1;
        $sentBy = Auth::id();
        $created = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($request->paper_ids as $paperId) {
                // Check if reviewer is author
                $isAuthor = DB::table('TacGiaBaiBao')
                    ->where('paper_id', $paperId)
                    ->where('user_id', $request->reviewer_id)
                    ->exists();

                if ($isAuthor) {
                    $skipped++;
                    continue;
                }

                // Check duplicate
                $exists = ReviewerPaperCandidate::where('paper_id', $paperId)
                    ->where('reviewer_id', $request->reviewer_id)
                    ->where('round_no', $roundNo)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                ReviewerPaperCandidate::create([
                    'paper_id' => $paperId,
                    'reviewer_id' => $request->reviewer_id,
                    'conference_id' => $request->conference_id,
                    'sent_by' => $sentBy,
                    'round_no' => $roundNo,
                    'note' => $request->note
                ]);

                $created++;
            }

            DB::commit();

            $message = "Đã gửi {$created} bài cho {$reviewer->full_name}";
            if ($skipped > 0) {
                $message .= " ({$skipped} bài bị bỏ qua do đã gửi hoặc reviewer là tác giả)";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * API: Lấy danh sách candidate đã gửi cho reviewer
     */
    public function getReviewerCandidates($reviewerId, Request $request)
    {
        $conferenceId = $request->get('conference_id');

        $candidates = ReviewerPaperCandidate::where('reviewer_id', $reviewerId)
            ->where('conference_id', $conferenceId)
            ->with(['paper', 'sentBy', 'bidding'])
            ->get()
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'paper_id' => $candidate->paper_id,
                    'paper_title' => $candidate->paper->title,
                    'round_no' => $candidate->round_no,
                    'sent_at' => $candidate->created_at->format('d/m/Y H:i'),
                    'sent_by' => $candidate->sentBy->full_name,
                    'has_bidding' => (bool) $candidate->bidding,
                    'bidding_locked' => $candidate->bidding ? $candidate->bidding->is_locked : false
                ];
            });

        return response()->json([
            'success' => true,
            'candidates' => $candidates
        ]);
    }

    /**
     * Xóa candidate (hủy gửi)
     */
    public function destroy($candidateId)
    {
        $candidate = ReviewerPaperCandidate::findOrFail($candidateId);

        // Check CHAIR role
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('CHAIR', $candidate->conference_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền'
            ], 403);
        }

        // Check if reviewer đã bidding và lock
        $bidding = DB::table('reviewer_bidding')
            ->where('user_id', $candidate->reviewer_id)
            ->where('paper_id', $candidate->paper_id)
            ->where('round_no', $candidate->round_no)
            ->first();

        if ($bidding && $bidding->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vì reviewer đã gửi kết quả bidding'
            ], 400);
        }

        $candidate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bài khỏi danh sách candidate'
        ]);
    }
}
