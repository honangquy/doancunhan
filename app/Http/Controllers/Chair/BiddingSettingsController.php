<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ConferenceBiddingSetting;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BiddingSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:CHAIR']);
    }

    /**
     * Show bidding settings page
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get conferences where user is chair (either direct chair_id or via role)
        $conferences = HoiThao::where('chair_id', $userId)
            ->orWhere(function($query) use ($userId) {
                $query->whereExists(function($subQuery) use ($userId) {
                    $subQuery->select(DB::raw(1))
                        ->from('vaitronguoidung')
                        ->whereRaw('vaitronguoidung.conference_id = hoithao.conference_id')
                        ->where('vaitronguoidung.user_id', $userId)
                        ->where('vaitronguoidung.role_code', 'CHAIR');
                });
            })
            ->orderBy('start_date', 'desc')
            ->get();

        return view('chair.bidding-settings.index', [
            'title' => 'Cài đặt Bidding',
            'conferences' => $conferences
        ]);
    }

    /**
     * Get bidding settings for a conference
     */
    public function getSettings($conferenceId)
    {
        try {
            // Verify chair access
            $this->verifyChairAccess($conferenceId);

            $setting = ConferenceBiddingSetting::where('conference_id', $conferenceId)->first();
            
            if (!$setting) {
                // Create default settings if not exists
                $setting = ConferenceBiddingSetting::create([
                    'conference_id' => $conferenceId,
                    'enable_keyword_matching' => false,
                    'keyword_similarity_threshold' => 0.5,
                    'allow_partial_keyword_match' => true,
                    'excluded_keywords' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'settings' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bidding settings
     */
    public function updateSettings(Request $request, $conferenceId)
    {
        $validator = Validator::make($request->all(), [
            'enable_keyword_matching' => 'required|boolean',
            'keyword_similarity_threshold' => 'required|numeric|between:0,1',
            'allow_partial_keyword_match' => 'required|boolean',
            'excluded_keywords' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify chair access
            $this->verifyChairAccess($conferenceId);

            $setting = ConferenceBiddingSetting::updateOrCreate(
                ['conference_id' => $conferenceId],
                [
                    'enable_keyword_matching' => $request->enable_keyword_matching,
                    'keyword_similarity_threshold' => $request->keyword_similarity_threshold,
                    'allow_partial_keyword_match' => $request->allow_partial_keyword_match,
                    'excluded_keywords' => $request->excluded_keywords
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Cài đặt bidding đã được cập nhật thành công',
                'settings' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật cài đặt'
            ], 500);
        }
    }

    /**
     * Get bidding statistics with keyword filtering info
     */
    public function getStatistics($conferenceId)
    {
        try {
            // Verify chair access
            $this->verifyChairAccess($conferenceId);

            $setting = ConferenceBiddingSetting::where('conference_id', $conferenceId)->first();
            
            // Get total papers in conference
            $totalPapers = \DB::table('baibao')
                ->where('conference_id', $conferenceId)
                ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])
                ->count();

            // Get reviewers and their visible papers count
            $reviewerStats = \DB::table('join_requests as jr')
                ->join('nguoidung as n', 'jr.user_id', '=', 'n.user_id')
                ->where('jr.conference_id', $conferenceId)
                ->where('jr.role', 'reviewer')
                ->where('jr.status', 'APPROVED')
                ->select('jr.user_id', 'n.full_name', 'jr.expertise_keywords')
                ->get()
                ->map(function($reviewer) use ($conferenceId, $setting, $totalPapers) {
                    if ($setting && $setting->enable_keyword_matching) {
                        $visiblePapers = $this->countVisiblePapersForReviewer($reviewer->user_id, $conferenceId, $setting);
                    } else {
                        $visiblePapers = $totalPapers;
                    }
                    
                    return [
                        'reviewer_name' => $reviewer->full_name,
                        'expertise_keywords' => $reviewer->expertise_keywords,
                        'visible_papers' => $visiblePapers,
                        'total_papers' => $totalPapers
                    ];
                });

            return response()->json([
                'success' => true,
                'statistics' => [
                    'keyword_matching_enabled' => $setting ? $setting->enable_keyword_matching : false,
                    'total_papers' => $totalPapers,
                    'total_reviewers' => $reviewerStats->count(),
                    'reviewer_stats' => $reviewerStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Count visible papers for a specific reviewer based on keyword matching
     */
    private function countVisiblePapersForReviewer($userId, $conferenceId, $setting)
    {
        $joinRequest = \DB::table('join_requests')
            ->where('user_id', $userId)
            ->where('conference_id', $conferenceId)
            ->where('role', 'reviewer')
            ->where('status', 'APPROVED')
            ->first(['expertise_keywords']);

        if (!$joinRequest || empty($joinRequest->expertise_keywords)) {
            return 0;
        }

        $reviewerKeywords = array_map('trim', explode(',', $joinRequest->expertise_keywords));
        $reviewerKeywords = array_filter($reviewerKeywords);

        if (empty($reviewerKeywords)) {
            return 0;
        }

        $query = \DB::table('baibao')
            ->where('conference_id', $conferenceId)
            ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW']);

        $query->where(function($q) use ($reviewerKeywords, $setting) {
            foreach ($reviewerKeywords as $keyword) {
                $keyword = trim($keyword);
                if (empty($keyword)) continue;
                
                if ($setting->allow_partial_keyword_match) {
                    $q->orWhere('keywords', 'LIKE', "%{$keyword}%");
                } else {
                    $q->orWhereRaw("FIND_IN_SET(?, REPLACE(keywords, ' ', ''))", [$keyword]);
                }
            }
        });

        if (!empty($setting->excluded_keywords)) {
            $excludedKeywords = $setting->excluded_keywords_array;
            $query->where(function($q) use ($excludedKeywords) {
                foreach ($excludedKeywords as $excluded) {
                    $excluded = trim($excluded);
                    if (!empty($excluded)) {
                        $q->where('keywords', 'NOT LIKE', "%{$excluded}%");
                    }
                }
            });
        }

        return $query->count();
    }

    /**
     * Verify chair has access to conference
     */
    private function verifyChairAccess($conferenceId)
    {
        $userId = Auth::id();
        
        $hasAccess = HoiThao::where('conference_id', $conferenceId)
            ->where('chair_id', $userId)
            ->exists();

        if (!$hasAccess) {
            $hasAccess = \DB::table('vaitronguoidung')
                ->where('user_id', $userId)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'CHAIR')
                ->exists();
        }

        if (!$hasAccess) {
            throw new \Exception('Bạn không có quyền truy cập hội thảo này');
        }
    }
}
