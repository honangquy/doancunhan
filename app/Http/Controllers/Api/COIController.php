<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Models\COI;
use App\Models\Models\XuLyCOI;
use App\Models\Models\BaiBao;
use App\Models\Models\TieuBan;
use App\Models\Models\PhanCongPhanBien;
use App\Models\NguoiDung;

class COIController extends Controller
{
    /**
     * Manually declare a COI (Reviewer declares)
     * POST /api/coi/declare
     */
    public function declare(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'paper_id' => 'required|integer|exists:baibao,paper_id',
            'coi_code' => 'required|string|exists:loaicoi,coi_code',
            'evidence' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if paper exists
        $paper = BaiBao::find($request->paper_id);
        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Paper not found'
            ], 404);
        }

        // Check if already declared COI for this paper
        $existingCOI = COI::where('paper_id', $request->paper_id)
            ->where('reviewer_id', $user->user_id)
            ->first();

        if ($existingCOI) {
            return response()->json([
                'success' => false,
                'message' => 'COI already declared for this paper'
            ], 409);
        }

        // Create COI record
        $coi = COI::create([
            'paper_id' => $request->paper_id,
            'reviewer_id' => $user->user_id,
            'coi_code' => $request->coi_code,
            'source_type' => 'DECLARED',
            'evidence' => $request->evidence,
            'created_at' => now(),
        ]);

        // If there's an existing assignment, we should flag it
        $assignment = PhanCongPhanBien::where('paper_id', $request->paper_id)
            ->where('reviewer_id', $user->user_id)
            ->first();

        if ($assignment) {
            // Update assignment status to indicate COI
            $assignment->status_code = 'COI_DECLARED';
            $assignment->save();
        }

        $coi->load(['paper', 'reviewer', 'coiType']);

        return response()->json([
            'success' => true,
            'message' => 'Conflict of Interest declared successfully',
            'data' => [
                'coi_id' => $coi->coi_id,
                'paper_id' => $coi->paper_id,
                'paper_title' => $coi->paper->title ?? 'N/A',
                'reviewer_name' => $coi->reviewer->full_name ?? 'N/A',
                'coi_code' => $coi->coi_code,
                'coi_name' => $coi->coiType->coi_name ?? $coi->coi_code,
                'source_type' => $coi->source_type,
                'evidence' => $coi->evidence,
                'created_at' => $coi->created_at,
            ]
        ], 201);
    }

    /**
     * Get all COIs for a paper (Admin/Chair only)
     * GET /api/papers/{paper_id}/coi
     */
    public function paperCOIs($paper_id)
    {
        $user = auth()->user();

        // Check if paper exists
        $paper = BaiBao::with('tieuBan')->find($paper_id);
        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'Paper not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            if (!$paper->tieuBan || !$this->isTrackChair($user, $paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can view COIs.'
                ], 403);
            }
        }

        // Get all COIs for this paper
        $cois = COI::where('paper_id', $paper_id)
            ->with(['reviewer', 'coiType', 'decision'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $cois->map(function($coi) {
            return [
                'coi_id' => $coi->coi_id,
                'reviewer_id' => $coi->reviewer_id,
                'reviewer_name' => $coi->reviewer->full_name ?? 'N/A',
                'reviewer_email' => $coi->reviewer->email ?? 'N/A',
                'coi_code' => $coi->coi_code,
                'coi_name' => $coi->coiType->coi_name ?? $coi->coi_code,
                'source_type' => $coi->source_type,
                'evidence' => $coi->evidence,
                'created_at' => $coi->created_at,
                'decision' => $coi->decision ? [
                    'decision_id' => $coi->decision->decision_id,
                    'decision' => $coi->decision->decision,
                    'note' => $coi->decision->note,
                    'decided_by' => $coi->decision->chair->full_name ?? 'N/A',
                    'decided_at' => $coi->decision->decided_at,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'COIs retrieved successfully',
            'data' => $data
        ]);
    }

    /**
     * Get all COIs in the system (Admin only)
     * GET /api/coi
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access all COIs'
            ], 403);
        }

        // Build query
        $query = COI::with(['paper.tieuBan.hoiThao', 'reviewer', 'coiType', 'decision']);

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by track
        if ($request->has('track_id')) {
            $query->whereHas('paper.tieuBan', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }

        // Filter by source type
        if ($request->has('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        // Filter by decision status
        if ($request->has('resolved')) {
            if ($request->resolved == 'true' || $request->resolved == '1') {
                $query->has('decision');
            } else {
                $query->doesntHave('decision');
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $cois = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $cois->map(function($coi) {
            return [
                'coi_id' => $coi->coi_id,
                'paper_id' => $coi->paper_id,
                'paper_title' => $coi->paper->title ?? 'N/A',
                'track_name' => $coi->paper->tieuBan->name ?? 'N/A',
                'conference_name' => $coi->paper->tieuBan->hoiThao->title ?? 'N/A',
                'reviewer_id' => $coi->reviewer_id,
                'reviewer_name' => $coi->reviewer->full_name ?? 'N/A',
                'coi_code' => $coi->coi_code,
                'coi_name' => $coi->coiType->coi_name ?? $coi->coi_code,
                'source_type' => $coi->source_type,
                'evidence' => $coi->evidence,
                'created_at' => $coi->created_at,
                'resolved' => $coi->decision ? true : false,
                'decision' => $coi->decision ? $coi->decision->decision : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'COIs retrieved successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $cois->currentPage(),
                'per_page' => $cois->perPage(),
                'total' => $cois->total(),
                'last_page' => $cois->lastPage(),
            ]
        ]);
    }

    /**
     * Auto-detect COI (System/Admin trigger)
     * POST /api/coi/detect
     */
    public function detect(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can trigger COI detection'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'paper_id' => 'nullable|integer|exists:baibao,paper_id',
            'conference_id' => 'nullable|integer|exists:hoithao,conference_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $detectedCOIs = [];

        // Detection Algorithm
        // 1. Check if reviewer is also an author
        $query = "
            SELECT DISTINCT 
                bb.paper_id,
                pcpb.reviewer_id,
                'AUTHOR_REVIEWER' as coi_code,
                'Reviewer is also an author' as evidence
            FROM PhanCongPhanBien pcpb
            INNER JOIN BaiBao bb ON pcpb.paper_id = bb.paper_id
            INNER JOIN TacGiaBaiBao tgbb ON bb.paper_id = tgbb.paper_id
            WHERE pcpb.reviewer_id = tgbb.user_id
        ";

        // Filter by paper if specified
        if ($request->has('paper_id')) {
            $query .= " AND bb.paper_id = " . intval($request->paper_id);
        }

        // Filter by conference if specified
        if ($request->has('conference_id')) {
            $query .= " 
                AND bb.track_id IN (
                    SELECT track_id FROM TieuBan WHERE conference_id = " . intval($request->conference_id) . "
                )
            ";
        }

        $authorReviewerConflicts = DB::select($query);

        foreach ($authorReviewerConflicts as $conflict) {
            // Check if COI already exists
            $existingCOI = COI::where('paper_id', $conflict->paper_id)
                ->where('reviewer_id', $conflict->reviewer_id)
                ->first();

            if (!$existingCOI) {
                $coi = COI::create([
                    'paper_id' => $conflict->paper_id,
                    'reviewer_id' => $conflict->reviewer_id,
                    'coi_code' => $conflict->coi_code,
                    'source_type' => 'DETECTED',
                    'evidence' => $conflict->evidence,
                    'created_at' => now(),
                ]);

                $detectedCOIs[] = [
                    'coi_id' => $coi->coi_id,
                    'paper_id' => $coi->paper_id,
                    'reviewer_id' => $coi->reviewer_id,
                    'coi_code' => $coi->coi_code,
                ];

                // Update assignment status if exists
                $assignment = PhanCongPhanBien::where('paper_id', $conflict->paper_id)
                    ->where('reviewer_id', $conflict->reviewer_id)
                    ->first();

                if ($assignment) {
                    $assignment->status_code = 'COI_DETECTED';
                    $assignment->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'COI detection completed',
            'data' => [
                'detected_count' => count($detectedCOIs),
                'cois' => $detectedCOIs,
            ]
        ]);
    }

    /**
     * Resolve COI (Chair decision)
     * POST /api/coi/{coi_id}/resolve
     */
    public function resolve(Request $request, $coi_id)
    {
        $user = auth()->user();

        $coi = COI::with('paper.tieuBan')->find($coi_id);

        if (!$coi) {
            return response()->json([
                'success' => false,
                'message' => 'COI not found'
            ], 404);
        }

        // Permission check: Admin or Track Chair
        if (!$this->isAdmin($user)) {
            if (!$coi->paper->tieuBan || !$this->isTrackChair($user, $coi->paper->tieuBan->track_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admin or track chair can resolve COIs.'
                ], 403);
            }
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:CONFIRMED,REJECTED',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if already resolved
        $existingDecision = XuLyCOI::where('coi_id', $coi_id)->first();
        if ($existingDecision) {
            return response()->json([
                'success' => false,
                'message' => 'COI already resolved'
            ], 409);
        }

        // Create decision
        $decision = XuLyCOI::create([
            'coi_id' => $coi_id,
            'chair_id' => $user->user_id,
            'decision' => $request->decision,
            'note' => $request->note,
            'decided_at' => now(),
        ]);

        // If CONFIRMED, remove the assignment if exists
        if ($request->decision == 'CONFIRMED') {
            $assignment = PhanCongPhanBien::where('paper_id', $coi->paper_id)
                ->where('reviewer_id', $coi->reviewer_id)
                ->first();

            if ($assignment) {
                $assignment->delete();
            }
        } elseif ($request->decision == 'REJECTED') {
            // If REJECTED, restore assignment status to normal
            $assignment = PhanCongPhanBien::where('paper_id', $coi->paper_id)
                ->where('reviewer_id', $coi->reviewer_id)
                ->first();

            if ($assignment && in_array($assignment->status_code, ['COI_DECLARED', 'COI_DETECTED'])) {
                $assignment->status_code = 'INVITED';
                $assignment->save();
            }
        }

        $decision->load('chair');

        return response()->json([
            'success' => true,
            'message' => 'COI resolved successfully',
            'data' => [
                'decision_id' => $decision->decision_id,
                'coi_id' => $decision->coi_id,
                'decision' => $decision->decision,
                'note' => $decision->note,
                'decided_by' => $decision->chair->full_name ?? 'N/A',
                'decided_at' => $decision->decided_at,
            ]
        ]);
    }

    /**
     * Get COI statistics (Admin only)
     * GET /api/coi/statistics
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();

        // Admin check
        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access COI statistics'
            ], 403);
        }

        // Build query
        $query = COI::query();

        // Filter by conference
        if ($request->has('conference_id')) {
            $query->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        // Filter by track
        if ($request->has('track_id')) {
            $query->whereHas('paper.tieuBan', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }

        // Total COIs
        $totalCOIs = $query->count();

        // COIs by source type
        $bySourceType = DB::table('coi')
            ->select('source_type', DB::raw('COUNT(*) as count'))
            ->groupBy('source_type')
            ->get();

        // COIs by type
        $byType = DB::table('COI as c')
            ->join('LoaiCOI as lc', 'c.coi_code', '=', 'lc.coi_code')
            ->select('lc.coi_code', 'lc.coi_name', DB::raw('COUNT(*) as count'))
            ->groupBy('lc.coi_code', 'lc.coi_name');

        if ($request->has('conference_id')) {
            $byType->join('BaiBao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->join('TieuBan as tb', 'bb.track_id', '=', 'tb.track_id')
                ->where('tb.conference_id', $request->conference_id);
        }

        $byType = $byType->get();

        // Resolved vs Pending
        $resolved = XuLyCOI::whereIn('coi_id', $query->pluck('coi_id'))->count();
        $pending = $totalCOIs - $resolved;

        // Resolution decisions
        $byDecision = DB::table('XuLyCOI')
            ->select('decision', DB::raw('COUNT(*) as count'))
            ->whereIn('coi_id', $query->pluck('coi_id'))
            ->groupBy('decision')
            ->get();

        // Papers with COIs
        $papersWithCOIs = COI::query();
        if ($request->has('conference_id')) {
            $papersWithCOIs->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }
        $papersWithCOIs = $papersWithCOIs->distinct('paper_id')->count('paper_id');

        // Reviewers with COIs
        $reviewersWithCOIs = COI::query();
        if ($request->has('conference_id')) {
            $reviewersWithCOIs->whereHas('paper.tieuBan.hoiThao', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }
        $reviewersWithCOIs = $reviewersWithCOIs->distinct('reviewer_id')->count('reviewer_id');

        return response()->json([
            'success' => true,
            'message' => 'COI statistics retrieved successfully',
            'data' => [
                'total_cois' => $totalCOIs,
                'by_source_type' => $bySourceType,
                'by_type' => $byType,
                'pending' => $pending,
                'resolved' => $resolved,
                'by_decision' => $byDecision,
                'papers_with_cois' => $papersWithCOIs,
                'reviewers_with_cois' => $reviewersWithCOIs,
            ]
        ]);
    }

    // Helper methods
    private function isAdmin($user)
    {
        return DB::table('vaitronguoidung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();
    }

    private function isTrackChair($user, $trackId)
    {
        $track = TieuBan::find($trackId);
        return $track && $track->chair_id == $user->user_id;
    }
}




