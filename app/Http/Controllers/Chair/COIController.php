<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class COIController extends Controller
{
    /**
     * Display list of all COI cases for the conference
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get chair's conferences
        $conferences = DB::table('vaitronguoidung as vt')
            ->join('hoithao as ht', 'vt.conference_id', '=', 'ht.conference_id')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('vt.user_id', $userId)
            ->where('lvt.role_code', 'CHAIR')
            ->select('ht.conference_id', 'ht.title as code', 'ht.title')
            ->get();

        if ($conferences->isEmpty()) {
            return redirect()->route('chair.dashboard')
                ->with('error', 'Bạn không phải là Chair của bất kỳ hội thảo nào');
        }

        // Default to first conference
        $conferenceId = request('conference_id', $conferences->first()->conference_id);
        
        // Get all COI cases for this conference
        $coiCases = DB::table('COI as c')
            ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
            ->join('nguoidung as reviewer', 'c.reviewer_id', '=', 'reviewer.user_id')
            ->join('loaicoi as lc', 'c.coi_code', '=', 'lc.coi_code')
            ->leftJoin('nguoidung as author', 'bb.submitter_id', '=', 'author.user_id')
            ->leftJoin('xulycoi as xc', 'c.coi_id', '=', 'xc.coi_id')
            ->where('bb.conference_id', $conferenceId)
            ->select(
                'c.coi_id',
                'c.paper_id',
                'c.reviewer_id',
                'c.coi_code',
                'c.source_type',
                'c.evidence',
                'c.created_at',
                'bb.title as paper_title',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'author.full_name as author_name',
                'lc.coi_name',
                'xc.decision_id',
                'xc.decision',
                'xc.decided_at'
            )
            ->orderBy('c.created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $coiCases->count(),
            'unresolved' => $coiCases->whereNull('decision_id')->count(),
            'resolved' => $coiCases->whereNotNull('decision_id')->count(),
            'declared' => $coiCases->where('source_type', 'DECLARED')->count(),
            'detected' => $coiCases->where('source_type', 'DETECTED')->count(),
        ];

        return view('chair.coi.index', compact('coiCases', 'conferences', 'conferenceId', 'stats'));
    }

    /**
     * Display detailed information about a specific COI case
     */
    public function show($coiId)
    {
        $userId = Auth::id();
        
        // Get COI details with all related information
        $coi = DB::table('COI as c')
            ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as reviewer', 'c.reviewer_id', '=', 'reviewer.user_id')
            ->join('loaicoi as lc', 'c.coi_code', '=', 'lc.coi_code')
            ->leftJoin('nguoidung as author', 'bb.submitter_id', '=', 'author.user_id')
            ->leftJoin('xulycoi as xc', 'c.coi_id', '=', 'xc.coi_id')
            ->leftJoin('nguoidung as resolver', 'xc.chair_id', '=', 'resolver.user_id')
            ->where('c.coi_id', $coiId)
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.status_code as paper_status',
                'ht.conference_id',
                'ht.title as conference_code',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'reviewer.organization as reviewer_org',
                'author.full_name as author_name',
                'author.email as author_email',
                'lc.coi_name',
                'xc.decision_id',
                'xc.decision',
                'xc.decided_at',
                'xc.note as resolution_note',
                'resolver.full_name as resolved_by_name'
            )
            ->first();

        if (!$coi) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Không tìm thấy COI case');
        }

        // Check if user is chair of this conference
        $isChair = DB::table('vaitronguoidung as vt')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('vt.user_id', $userId)
            ->where('vt.conference_id', $coi->conference_id)
            ->where('lvt.role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Bạn không có quyền xem COI case này');
        }

        // Get assignment history if exists
        $assignment = DB::table('phancongphanbien as pc')
            ->where('pc.paper_id', $coi->paper_id)
            ->where('pc.reviewer_id', $coi->reviewer_id)
            ->select('pc.*')
            ->first();

        // Get all co-authors
        $coAuthors = DB::table('tacgiabaibao as tg')
            ->join('nguoidung as nd', 'tg.user_id', '=', 'nd.user_id')
            ->where('tg.paper_id', $coi->paper_id)
            ->select('nd.full_name', 'nd.email', 'nd.organization', 'tg.author_order')
            ->orderBy('tg.author_order')
            ->get();

        return view('chair.coi.show', compact('coi', 'assignment', 'coAuthors'));
    }

    /**
     * Display form to resolve a COI case
     */
    public function resolveForm($coiId)
    {
        $userId = Auth::id();
        
        // Get COI details
        $coi = DB::table('COI as c')
            ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
            ->join('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as reviewer', 'c.reviewer_id', '=', 'reviewer.user_id')
            ->join('loaicoi as lc', 'c.coi_code', '=', 'lc.coi_code')
            ->leftJoin('xulycoi as xc', 'c.coi_id', '=', 'xc.coi_id')
            ->where('c.coi_id', $coiId)
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.conference_id',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'lc.coi_name',
                'xc.decision_id'
            )
            ->first();

        if (!$coi) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Không tìm thấy COI case');
        }

        // Check if already resolved
        if ($coi->decision_id) {
            return redirect()->route('chair.coi.show', $coiId)
                ->with('error', 'COI case này đã được giải quyết');
        }

        // Check if user is chair
        $isChair = DB::table('vaitronguoidung as vt')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('vt.user_id', $userId)
            ->where('vt.conference_id', $coi->conference_id)
            ->where('lvt.role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Bạn không có quyền giải quyết COI case này');
        }

        // Get resolution types (hardcoded since no Loaixulycoi table)
        $resolutionTypes = collect([
            (object)['decision' => 'CONFIRMED', 'decision_name' => 'Xác nhận COI', 'description' => 'Xác nhận xung đột lợi ích và xóa phân công reviewer'],
            (object)['decision' => 'REJECTED', 'decision_name' => 'Từ chối COI', 'description' => 'Từ chối khai báo COI, cho phép reviewer tiếp tục review']
        ]);

        return view('chair.coi.resolve', compact('coi', 'resolutionTypes'));
    }

    /**
     * Store COI resolution
     */
    public function resolve(Request $request, $coiId)
    {
        $userId = Auth::id();

        $request->validate([
            'decision' => 'required|in:CONFIRMED,REJECTED',
            'note' => 'nullable|string|max:500',
        ]);

        // Get COI details
        $coi = DB::table('COI as c')
            ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
            ->where('c.coi_id', $coiId)
            ->select('c.*', 'bb.conference_id')
            ->first();

        if (!$coi) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Không tìm thấy COI case');
        }

        // Check if user is chair
        $isChair = DB::table('vaitronguoidung as vt')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('vt.user_id', $userId)
            ->where('vt.conference_id', $coi->conference_id)
            ->where('lvt.role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.coi.index')
                ->with('error', 'Bạn không có quyền giải quyết COI case này');
        }

        // Check if already resolved
        $existingResolution = DB::table('xulycoi')
            ->where('coi_id', $coiId)
            ->first();

        if ($existingResolution) {
            return redirect()->route('chair.coi.show', $coiId)
                ->with('error', 'COI case này đã được giải quyết');
        }

        DB::beginTransaction();
        try {
            // Insert resolution
            DB::table('xulycoi')->insert([
                'coi_id' => $coiId,
                'chair_id' => $userId,
                'decision' => $request->decision,
                'note' => $request->note,
                'decided_at' => now(),
            ]);

            // If decision is CONFIRMED, remove the assignment
            if ($request->decision === 'CONFIRMED') {
                DB::table('phancongphanbien')
                    ->where('paper_id', $coi->paper_id)
                    ->where('reviewer_id', $coi->reviewer_id)
                    ->delete();
            }

            DB::commit();

            return redirect()->route('chair.coi.show', $coiId)
                ->with('success', 'Đã giải quyết COI case thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Lỗi khi giải quyết COI: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get COI statistics for conference
     */
    public function statistics($conferenceId)
    {
        $userId = Auth::id();

        // Check if user is chair
        $isChair = DB::table('vaitronguoidung as vt')
            ->join('loaivaitro as lvt', 'vt.role_code', '=', 'lvt.role_code')
            ->where('vt.user_id', $userId)
            ->where('vt.conference_id', $conferenceId)
            ->where('lvt.role_code', 'CHAIR')
            ->exists();

        if (!$isChair) {
            return redirect()->route('chair.dashboard')
                ->with('error', 'Bạn không có quyền xem thống kê này');
        }

        // Get detailed statistics
        $stats = [
            'total_coi' => DB::table('COI as c')
                ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $conferenceId)
                ->count(),
            
            'by_type' => DB::table('COI as c')
                ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->join('loaicoi as lc', 'c.coi_code', '=', 'lc.coi_code')
                ->where('bb.conference_id', $conferenceId)
                ->select('lc.coi_name', DB::raw('COUNT(*) as count'))
                ->groupBy('lc.coi_name')
                ->get(),
            
            'by_source' => DB::table('COI as c')
                ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->where('bb.conference_id', $conferenceId)
                ->select('c.source_type', DB::raw('COUNT(*) as count'))
                ->groupBy('c.source_type')
                ->get(),
            
            'by_resolution' => DB::table('COI as c')
                ->join('baibao as bb', 'c.paper_id', '=', 'bb.paper_id')
                ->leftJoin('xulycoi as xc', 'c.coi_id', '=', 'xc.coi_id')
                ->where('bb.conference_id', $conferenceId)
                ->select(
                    DB::raw('COALESCE(xc.decision, "Chưa giải quyết") as status'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('xc.decision')
                ->get(),
        ];

        return view('chair.coi.statistics', compact('stats', 'conferenceId'));
    }
}




