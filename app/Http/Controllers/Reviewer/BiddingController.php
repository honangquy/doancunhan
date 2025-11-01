<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\VaiTroNguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BiddingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:REVIEWER']);
    }

    /**
     * Show bidding interface
     */
    public function index()
    {
        return view('reviewer.bidding', [
            'title' => 'Bidding & COI'
        ]);
    }

    /**
     * Get conferences where user is a reviewer
     */
    public function getConferences()
    {
        try {
            $userId = Auth::id();

            $conferences = DB::table('hoithao as h')
                ->join('vaitronguoidung as vt', 'h.conference_id', '=', 'vt.conference_id')
                ->where('vt.user_id', $userId)
                ->where('vt.role_code', 'REVIEWER')
                ->where('h.status', 'ACTIVE')
                ->select('h.*')
                ->orderBy('h.start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'conferences' => $conferences
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting reviewer conferences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách hội thảo'
            ], 500);
        }
    }

    /**
     * Get papers for a specific conference with existing bids
     */
    public function getConferencePapers($conferenceId)
    {
        try {
            $userId = Auth::id();

            // Verify reviewer access to this conference
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $conferenceId)
                ->where('role_code', 'REVIEWER')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập hội thảo này'
                ], 403);
            }

            // Get papers with existing bidding data
            $papers = DB::table('baibao as b')
                ->leftJoin('reviewer_bidding as rb', function($join) use ($userId) {
                    $join->on('b.paper_id', '=', 'rb.paper_id')
                         ->where('rb.user_id', '=', $userId);
                })
                ->leftJoin('nguoidung as n', 'b.submitter_id', '=', 'n.user_id')
                ->where('b.conference_id', $conferenceId)
                ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW']) // Show submitted papers for immediate bidding
                ->select(
                    'b.*',
                    'n.full_name as submitted_by_name',
                    'rb.bidding_value',
                    'rb.coi',
                    'rb.coi_reason',
                    'rb.note',
                    DB::raw('COALESCE(rb.bidding_value, 0) as bidding_value'),
                    DB::raw('COALESCE(rb.coi, false) as coi')
                )
                ->orderBy('b.title')
                ->get();

            return response()->json([
                'success' => true,
                'papers' => $papers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting conference papers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách bài báo'
            ], 500);
        }
    }

    /**
     * Submit single bidding
     */
    public function submitBidding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paper_id' => 'required|exists:baibao,paper_id',
            'conference_id' => 'required|exists:hoithao,conference_id',
            'bidding_value' => 'required|integer|between:0,3',
            'coi' => 'boolean',
            'coi_reason' => 'required_if:coi,true|nullable|string|max:1000',
            'note' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $data = $request->all();

            // Verify reviewer access
            $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                ->where('conference_id', $data['conference_id'])
                ->where('role_code', 'REVIEWER')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền phản biện hội thảo này'
                ], 403);
            }

            // Check if paper belongs to conference
            $paper = BaiBao::where('paper_id', $data['paper_id'])
                ->where('conference_id', $data['conference_id'])
                ->first();

            if (!$paper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài báo không tồn tại trong hội thảo này'
                ], 404);
            }

            // Check for existing assignment (can't bid on assigned papers)
            $existingAssignment = ReviewerAssignment::where('user_id', $userId)
                ->where('paper_id', $data['paper_id'])
                ->exists();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã được phân công bài này, không thể thay đổi bidding'
                ], 400);
            }

            // Upsert bidding
            ReviewerBidding::updateOrCreate(
                [
                    'user_id' => $userId,
                    'paper_id' => $data['paper_id']
                ],
                [
                    'conference_id' => $data['conference_id'],
                    'bidding_value' => $data['bidding_value'],
                    'coi' => $data['coi'] ?? false,
                    'coi_reason' => $data['coi_reason'],
                    'note' => $data['note']
                ]
            );

            // Log COI if declared
            if ($data['coi'] ?? false) {
                \Log::info("COI declared by reviewer {$userId} for paper {$data['paper_id']}: " . $data['coi_reason']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu bidding thành công!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error submitting bidding: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu bidding'
            ], 500);
        }
    }

    /**
     * Submit multiple biddings at once
     */
    public function submitBulkBidding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'biddings' => 'required|array|min:1',
            'biddings.*.paper_id' => 'required|exists:baibao,paper_id',
            'biddings.*.conference_id' => 'required|exists:hoithao,conference_id',
            'biddings.*.bidding_value' => 'required|integer|between:0,3',
            'biddings.*.coi' => 'boolean',
            'biddings.*.coi_reason' => 'required_if:biddings.*.coi,true|nullable|string|max:1000',
            'biddings.*.note' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $biddings = $request->input('biddings');

            DB::beginTransaction();

            foreach ($biddings as $biddingData) {
                // Verify access for each conference
                $hasAccess = VaiTroNguoiDung::where('user_id', $userId)
                    ->where('conference_id', $biddingData['conference_id'])
                    ->where('role_code', 'REVIEWER')
                    ->exists();

                if (!$hasAccess) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền truy cập một trong các hội thảo'
                    ], 403);
                }

                // Check for existing assignment
                $existingAssignment = ReviewerAssignment::where('user_id', $userId)
                    ->where('paper_id', $biddingData['paper_id'])
                    ->exists();

                if ($existingAssignment) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Bạn đã được phân công bài {$biddingData['paper_id']}, không thể thay đổi bidding"
                    ], 400);
                }

                // Upsert bidding
                ReviewerBidding::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'paper_id' => $biddingData['paper_id']
                    ],
                    [
                        'conference_id' => $biddingData['conference_id'],
                        'bidding_value' => $biddingData['bidding_value'],
                        'coi' => $biddingData['coi'] ?? false,
                        'coi_reason' => $biddingData['coi_reason'],
                        'note' => $biddingData['note']
                    ]
                );

                // Log COI if declared
                if ($biddingData['coi'] ?? false) {
                    \Log::info("COI declared by reviewer {$userId} for paper {$biddingData['paper_id']}: " . $biddingData['coi_reason']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu tất cả bidding thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting bulk bidding: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu bidding'
            ], 500);
        }
    }

    /**
     * Get reviewer assignments
     */
    public function getAssignments()
    {
        try {
            $userId = Auth::id();

            $assignments = DB::table('reviewer_assignments as ra')
                ->join('baibao as b', 'ra.paper_id', '=', 'b.paper_id')
                ->join('hoithao as h', 'ra.conference_id', '=', 'h.conference_id')
                ->leftJoin('nguoidung as assigner', 'ra.assigned_by', '=', 'assigner.user_id')
                ->where('ra.user_id', $userId)
                ->select(
                    'ra.*',
                    'b.title as paper_title',
                    'b.keywords as paper_keywords',
                    'h.title as conference_title',
                    'assigner.full_name as assigned_by_name'
                )
                ->orderBy('ra.assigned_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting assignments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách phân công'
            ], 500);
        }
    }

    /**
     * Respond to assignment (accept/decline)
     */
    public function respondToAssignment(Request $request, $assignmentId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:ACCEPTED,DECLINED',
            'decline_reason' => 'required_if:status,DECLINED|nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();

            $assignment = ReviewerAssignment::where('id', $assignmentId)
                ->where('user_id', $userId)
                ->where('status', 'PENDING')
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phân công không tồn tại hoặc đã được xử lý'
                ], 404);
            }

            $assignment->update([
                'status' => $request->input('status'),
                'responded_at' => now(),
                'decline_reason' => $request->input('decline_reason')
            ]);

            $message = $request->input('status') === 'ACCEPTED' 
                ? 'Đã chấp nhận phân công thành công!'
                : 'Đã từ chối phân công thành công!';

            // Log the response
            \Log::info("Reviewer {$userId} responded to assignment {$assignmentId}: {$request->input('status')}");

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Error responding to assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi phản hồi phân công'
            ], 500);
        }
    }

    /**
     * Get bidding statistics for reviewer
     */
    public function getBiddingStatistics($conferenceId = null)
    {
        try {
            $userId = Auth::id();

            $query = ReviewerBidding::where('user_id', $userId);
            
            if ($conferenceId) {
                $query->where('conference_id', $conferenceId);
            }

            $stats = [
                'total_bids' => $query->count(),
                'bid_breakdown' => $query->groupBy('bidding_value')
                    ->selectRaw('bidding_value, count(*) as count')
                    ->get()
                    ->pluck('count', 'bidding_value'),
                'coi_count' => $query->where('coi', true)->count(),
                'assignments_count' => ReviewerAssignment::where('user_id', $userId)
                    ->when($conferenceId, function($q) use ($conferenceId) {
                        return $q->where('conference_id', $conferenceId);
                    })
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting bidding statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thống kê'
            ], 500);
        }
    }
}




