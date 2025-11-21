<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ReviewerPreference;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
     * Hiển thị trang đăng ký số bài muốn nhận
     */
    public function index()
    {
        $userId = Auth::id();

        // Lấy danh sách hội thảo mà user có role REVIEWER
        $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('role_code', 'REVIEWER');
        })->with(['reviewerPreferences' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        return view('reviewer.preferences.index', compact('conferences'));
    }

    /**
     * Lưu hoặc cập nhật preference
     */
    public function store(Request $request)
    {
        $request->validate([
            'conference_id' => 'required|exists:hoithao,conference_id',
            'max_papers_wanted' => 'required|integer|min:1|max:20',
            'expertise' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:500'
        ], [
            'max_papers_wanted.required' => 'Vui lòng nhập số bài muốn nhận',
            'max_papers_wanted.min' => 'Số bài tối thiểu là 1',
            'max_papers_wanted.max' => 'Số bài tối đa là 20',
        ]);

        $userId = Auth::id();

        // Check if user is reviewer for this conference
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('REVIEWER', $request->conference_id)) {
            return back()->with('error', 'Bạn không phải là Reviewer của hội thảo này');
        }

        $preference = ReviewerPreference::updateOrCreate(
            [
                'user_id' => $userId,
                'conference_id' => $request->conference_id
            ],
            [
                'max_papers_wanted' => $request->max_papers_wanted,
                'expertise' => $request->expertise,
                'note' => $request->note
            ]
        );

        return back()->with('success', 'Đã lưu thành công số bài muốn nhận: ' . $request->max_papers_wanted);
    }

    /**
     * API: Get preference for a conference
     */
    public function show($conferenceId)
    {
        $userId = Auth::id();

        $preference = ReviewerPreference::where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$preference) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng ký số bài muốn nhận cho hội thảo này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'preference' => $preference
        ]);
    }

    /**
     * API: Update preference
     */
    public function update(Request $request, $conferenceId)
    {
        $request->validate([
            'max_papers_wanted' => 'required|integer|min:1|max:20',
            'expertise' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:500'
        ]);

        $userId = Auth::id();

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->hasRoleForConference('REVIEWER', $conferenceId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải là Reviewer của hội thảo này'
            ], 403);
        }

        $preference = ReviewerPreference::updateOrCreate(
            [
                'user_id' => $userId,
                'conference_id' => $conferenceId
            ],
            [
                'max_papers_wanted' => $request->max_papers_wanted,
                'expertise' => $request->expertise,
                'note' => $request->note
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thành công',
            'preference' => $preference
        ]);
    }
}
