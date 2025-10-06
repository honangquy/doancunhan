<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\Bidding;
use App\Models\Models\BaiBao;
use App\Models\Models\PhanCongPhanBien;
use App\Models\Models\COI;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * BiddingController - Phase 5: Review System
 * 
 * Quản lý bidding (reviewer chọn bài muốn phản biện)
 * 
 * APIs:
 * - GET    /api/papers/{paper_id}/biddings         - Danh sách biddings của một bài
 * - POST   /api/papers/{paper_id}/bid              - Reviewer bid bài
 * - GET    /api/my-biddings                        - Biddings của reviewer hiện tại
 * - PUT    /api/biddings/{paper_id}                - Update bid
 * - DELETE /api/biddings/{paper_id}                - Withdraw bid
 * - GET    /api/bidding/statistics                 - Thống kê bidding (Admin/Chair)
 */
class BiddingController extends Controller
{
    /**
     * Get biddings for a specific paper
     * Admin, Track Chair only
     * 
     * GET /api/papers/{paper_id}/biddings
     * 
     * @param int $paper_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($paper_id)
    {
        try {
            $paper = BaiBao::with('tieuBan.hoiThao')->findOrFail($paper_id);
            $user = auth()->user();

            // Check permission: Admin or Track Chair
            if (!$this->isAdmin($user) && !$this->isTrackChair($user, $paper->track_id)) {
                return response()->json([
                    'message' => 'You do not have permission to view biddings for this paper.'
                ], 403);
            }

            $biddings = Bidding::where('paper_id', $paper_id)
                ->with(['reviewer:user_id,full_name,email,organization'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($bid) {
                    return [
                        'user_id' => $bid->user_id,
                        'reviewer' => $bid->reviewer,
                        'bidding_code' => $bid->bidding_code,
                        'bidding_name' => $this->getBiddingName($bid->bidding_code),
                        'note' => $bid->note,
                        'created_at' => $bid->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'message' => 'Biddings retrieved successfully.',
                'data' => [
                    'paper' => [
                        'paper_id' => $paper->paper_id,
                        'title' => $paper->title,
                        'conference' => $paper->tieuBan->hoiThao->title,
                        'track' => $paper->tieuBan->track_name,
                    ],
                    'biddings' => $biddings,
                    'total' => $biddings->count(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Paper not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving biddings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reviewer bids on a paper
     * Reviewer only
     * 
     * POST /api/papers/{paper_id}/bid
     * 
     * Body:
     * {
     *   "bidding_code": "EAGER|WILLING|NEUTRAL|UNWILLING|CONFLICT",
     *   "note": "Optional note"
     * }
     * 
     * @param Request $request
     * @param int $paper_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $paper_id)
    {
        $validator = Validator::make($request->all(), [
            'bidding_code' => 'required|string|in:EAGER,WILLING,NEUTRAL,UNWILLING,CONFLICT',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paper = BaiBao::with('tieuBan.hoiThao', 'submitter', 'authors')->findOrFail($paper_id);
            $user = auth()->user();

            // Check if user is reviewer
            if (!$this->isReviewer($user)) {
                return response()->json([
                    'message' => 'Only reviewers can bid on papers.'
                ], 403);
            }

            // Check if conference is open for bidding
            $conference = $paper->tieuBan->hoiThao;
            if ($conference->status !== 'OPEN') {
                return response()->json([
                    'message' => 'Bidding is not open for this conference.'
                ], 400);
            }

            // Check if reviewer is author or submitter (auto COI)
            if ($this->isAuthorOfPaper($user->user_id, $paper)) {
                return response()->json([
                    'message' => 'You cannot bid on your own paper. This is a conflict of interest.'
                ], 403);
            }

            // Check if bidding already exists
            $existingBid = Bidding::where('user_id', $user->user_id)
                ->where('paper_id', $paper_id)
                ->first();

            if ($existingBid) {
                return response()->json([
                    'message' => 'You have already bid on this paper. Use PUT to update your bid.'
                ], 409);
            }

            // If bidding_code is CONFLICT, auto-create COI record
            if ($request->bidding_code === 'CONFLICT') {
                $this->createCOIFromBidding($user->user_id, $paper_id, $request->note);
            }

            // Create bidding
            $bidding = Bidding::create([
                'user_id' => $user->user_id,
                'paper_id' => $paper_id,
                'bidding_code' => $request->bidding_code,
                'note' => $request->note,
            ]);

            return response()->json([
                'message' => 'Bid submitted successfully.',
                'data' => [
                    'user_id' => $bidding->user_id,
                    'paper_id' => $bidding->paper_id,
                    'bidding_code' => $bidding->bidding_code,
                    'bidding_name' => $this->getBiddingName($bidding->bidding_code),
                    'note' => $bidding->note,
                    'created_at' => $bidding->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Paper not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error submitting bid.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current reviewer's biddings
     * Reviewer only
     * 
     * GET /api/my-biddings
     * Query params: conference_id, bidding_code, per_page
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myBiddings(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$this->isReviewer($user)) {
                return response()->json([
                    'message' => 'Only reviewers can view biddings.'
                ], 403);
            }

            $query = Bidding::where('user_id', $user->user_id)
                ->with(['paper.tieuBan.hoiThao', 'paper.submitter:user_id,full_name,email']);

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->whereHas('paper.tieuBan', function ($q) use ($request) {
                    $q->where('conference_id', $request->conference_id);
                });
            }

            // Filter by bidding_code
            if ($request->has('bidding_code')) {
                $query->where('bidding_code', $request->bidding_code);
            }

            $perPage = $request->get('per_page', 15);
            $biddings = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $data = $biddings->map(function ($bid) {
                return [
                    'paper_id' => $bid->paper_id,
                    'paper' => [
                        'title' => $bid->paper->title,
                        'abstract' => substr($bid->paper->abstract, 0, 150) . '...',
                        'status' => $bid->paper->status,
                        'submitter' => $bid->paper->submitter->full_name,
                    ],
                    'conference' => $bid->paper->tieuBan->hoiThao->title,
                    'track' => $bid->paper->tieuBan->track_name,
                    'bidding_code' => $bid->bidding_code,
                    'bidding_name' => $this->getBiddingName($bid->bidding_code),
                    'note' => $bid->note,
                    'created_at' => $bid->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'message' => 'My biddings retrieved successfully.',
                'data' => $data,
                'pagination' => [
                    'total' => $biddings->total(),
                    'per_page' => $biddings->perPage(),
                    'current_page' => $biddings->currentPage(),
                    'last_page' => $biddings->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving biddings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bidding
     * Reviewer only, can only update own bid
     * 
     * PUT /api/biddings/{paper_id}
     * 
     * Body:
     * {
     *   "bidding_code": "EAGER|WILLING|NEUTRAL|UNWILLING|CONFLICT",
     *   "note": "Updated note"
     * }
     * 
     * @param Request $request
     * @param int $paper_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $paper_id)
    {
        $validator = Validator::make($request->all(), [
            'bidding_code' => 'required|string|in:EAGER,WILLING,NEUTRAL,UNWILLING,CONFLICT',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            
            $bidding = Bidding::where('user_id', $user->user_id)
                ->where('paper_id', $paper_id)
                ->firstOrFail();

            // Check if already assigned (cannot change bid after assignment)
            $assignment = PhanCongPhanBien::where('paper_id', $paper_id)
                ->where('reviewer_id', $user->user_id)
                ->first();

            if ($assignment) {
                return response()->json([
                    'message' => 'Cannot update bid after being assigned as reviewer.'
                ], 403);
            }

            // If changing to CONFLICT, create COI
            if ($request->bidding_code === 'CONFLICT' && $bidding->bidding_code !== 'CONFLICT') {
                $this->createCOIFromBidding($user->user_id, $paper_id, $request->note);
            }

            $bidding->update([
                'bidding_code' => $request->bidding_code,
                'note' => $request->note,
            ]);

            return response()->json([
                'message' => 'Bid updated successfully.',
                'data' => [
                    'user_id' => $bidding->user_id,
                    'paper_id' => $bidding->paper_id,
                    'bidding_code' => $bidding->bidding_code,
                    'bidding_name' => $this->getBiddingName($bidding->bidding_code),
                    'note' => $bidding->note,
                    'updated_at' => $bidding->updated_at->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Bid not found or you do not have permission to update it.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating bid.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw bid
     * Reviewer only, can only withdraw own bid
     * 
     * DELETE /api/biddings/{paper_id}
     * 
     * @param int $paper_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($paper_id)
    {
        try {
            $user = auth()->user();
            
            $bidding = Bidding::where('user_id', $user->user_id)
                ->where('paper_id', $paper_id)
                ->firstOrFail();

            // Check if already assigned
            $assignment = PhanCongPhanBien::where('paper_id', $paper_id)
                ->where('reviewer_id', $user->user_id)
                ->first();

            if ($assignment) {
                return response()->json([
                    'message' => 'Cannot withdraw bid after being assigned as reviewer.'
                ], 403);
            }

            $bidding->delete();

            return response()->json([
                'message' => 'Bid withdrawn successfully.'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Bid not found or you do not have permission to withdraw it.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error withdrawing bid.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bidding statistics
     * Admin or Track Chair only
     * 
     * GET /api/bidding/statistics
     * Query params: conference_id, track_id
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$this->isAdmin($user)) {
                return response()->json([
                    'message' => 'Only administrators can view bidding statistics.'
                ], 403);
            }

            $query = Bidding::query();

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->whereHas('paper.tieuBan', function ($q) use ($request) {
                    $q->where('conference_id', $request->conference_id);
                });
            }

            // Filter by track
            if ($request->has('track_id')) {
                $query->whereHas('paper', function ($q) use ($request) {
                    $q->where('track_id', $request->track_id);
                });
            }

            $total = $query->count();
            
            $byBiddingCode = Bidding::selectRaw('bidding_code, COUNT(*) as count')
                ->when($request->has('conference_id'), function ($q) use ($request) {
                    $q->whereHas('paper.tieuBan', function ($sq) use ($request) {
                        $sq->where('conference_id', $request->conference_id);
                    });
                })
                ->when($request->has('track_id'), function ($q) use ($request) {
                    $q->whereHas('paper', function ($sq) use ($request) {
                        $sq->where('track_id', $request->track_id);
                    });
                })
                ->groupBy('bidding_code')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->bidding_code => [
                        'count' => $item->count,
                        'name' => $this->getBiddingName($item->bidding_code)
                    ]];
                });

            // Papers with bids
            $papersWithBids = BaiBao::whereHas('biddings')
                ->when($request->has('conference_id'), function ($q) use ($request) {
                    $q->whereHas('tieuBan', function ($sq) use ($request) {
                        $sq->where('conference_id', $request->conference_id);
                    });
                })
                ->when($request->has('track_id'), function ($q) use ($request) {
                    $q->where('track_id', $request->track_id);
                })
                ->count();

            // Reviewers who bid
            $reviewersWhoBid = Bidding::distinct('user_id')
                ->when($request->has('conference_id'), function ($q) use ($request) {
                    $q->whereHas('paper.tieuBan', function ($sq) use ($request) {
                        $sq->where('conference_id', $request->conference_id);
                    });
                })
                ->count('user_id');

            return response()->json([
                'message' => 'Bidding statistics retrieved successfully.',
                'data' => [
                    'total_bids' => $total,
                    'by_bidding_code' => $byBiddingCode,
                    'papers_with_bids' => $papersWithBids,
                    'reviewers_who_bid' => $reviewersWhoBid,
                    'average_bids_per_paper' => $papersWithBids > 0 ? round($total / $papersWithBids, 2) : 0,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    private function isAdmin($user)
    {
        return $user->vaiTros()->where('role_code', 'ADMIN')->exists();
    }

    private function isReviewer($user)
    {
        return $user->vaiTros()->where('role_code', 'REVIEWER')->exists();
    }

    private function isTrackChair($user, $trackId)
    {
        return DB::table('TieuBan')
            ->where('track_id', $trackId)
            ->where('chair_id', $user->user_id)
            ->exists();
    }

    private function isAuthorOfPaper($userId, $paper)
    {
        // Check if submitter
        if ($paper->submitter_id === $userId) {
            return true;
        }

        // Check if co-author
        return $paper->authors()->where('user_id', $userId)->exists();
    }

    private function createCOIFromBidding($reviewerId, $paperId, $note)
    {
        // Check if COI already exists
        $existing = COI::where('paper_id', $paperId)
            ->where('reviewer_id', $reviewerId)
            ->first();

        if (!$existing) {
            COI::create([
                'paper_id' => $paperId,
                'reviewer_id' => $reviewerId,
                'coi_code' => 'DECLARED',
                'source_type' => 'DECLARED',
                'evidence' => $note ?? 'Declared through bidding as CONFLICT',
            ]);
        }
    }

    private function getBiddingName($code)
    {
        $names = [
            'EAGER' => 'Very interested (Eager)',
            'WILLING' => 'Interested (Willing)',
            'NEUTRAL' => 'Neutral',
            'UNWILLING' => 'Not interested (Unwilling)',
            'CONFLICT' => 'Conflict of Interest',
        ];

        return $names[$code] ?? $code;
    }
}
