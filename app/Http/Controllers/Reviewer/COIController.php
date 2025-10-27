<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class COIController extends Controller
{
    /**
     * Display list of declared COI by the reviewer
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Verify reviewer role
        $isReviewer = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'REVIEWER')
            ->exists();
            
        if (!$isReviewer) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }
        
        // Get all COI declared by this reviewer
        $declaredCOI = DB::table('coi')
            ->join('baibao', 'coi.paper_id', '=', 'baibao.paper_id')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('loaicoi', 'coi.coi_code', '=', 'loaicoi.coi_code')
            ->leftJoin('XuLyCOI', 'coi.coi_id', '=', 'XuLycoi.coi_id')
            ->where('coi.reviewer_id', $userId)
            ->where('coi.source_type', 'DECLARED')
            ->select(
                'coi.coi_id',
                'coi.paper_id',
                'baibao.title as paper_title',
                'baibao.status as paper_status',
                'hoithao.title as conference_code',
                'hoithao.title as conference_title',
                'loaicoi.coi_code',
                'loaicoi.coi_name',
                'coi.evidence',
                'coi.note',
                'coi.detected_at',
                'coi.created_at',
                'XuLycoi.decision_id',
                'XuLycoi.decision',
                'XuLycoi.decided_at'
            )
            ->orderBy('coi.created_at', 'desc')
            ->get();
        
        // Get statistics
        $stats = [
            'total' => $declaredCOI->count(),
            'resolved' => $declaredCOI->where('decision_id', '!=', null)->count(),
            'unresolved' => $declaredCOI->where('decision_id', null)->count(),
            'by_type' => DB::table('coi')
                ->join('loaicoi', 'coi.coi_code', '=', 'loaicoi.coi_code')
                ->where('coi.reviewer_id', $userId)
                ->where('coi.source_type', 'DECLARED')
                ->select('loaicoi.coi_name', DB::raw('count(*) as count'))
                ->groupBy('loaicoi.coi_name')
                ->get()
        ];
        
        return view('reviewer.coi.index', compact('declaredCOI', 'stats'));
    }
    
    /**
     * Show form to declare new COI
     */
    public function create()
    {
        $userId = Auth::id();
        
        // Verify reviewer role
        $isReviewer = DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'REVIEWER')
            ->exists();
            
        if (!$isReviewer) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }
        
        // Get COI types
        $coiTypes = DB::table('loaicoi')
            ->orderBy('coi_name')
            ->get();
        
        // Get conferences where reviewer has assignments
        $conferences = DB::table('hoithao')
            ->join('baibao', 'hoithao.conference_id', '=', 'baibao.conference_id')
            ->join('phancong', 'baibao.paper_id', '=', 'phancong.paper_id')
            ->where('phancong.reviewer_id', $userId)
            ->select('hoithao.conference_id', 'hoithao.title as conference_code', 'hoithao.title')
            ->distinct()
            ->get();
        
        return view('reviewer.coi.create', compact('coiTypes', 'conferences'));
    }
    
    /**
     * Store new COI declaration
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        
        // Validate input
        $validated = $request->validate([
            'paper_id' => 'required|exists:baibao,paper_id',
            'coi_code' => 'required|exists:loaicoi,coi_code',
            'evidence' => 'required|string|max:1000',
            'note' => 'nullable|string|max:500'
        ], [
            'paper_id.required' => 'Vui lòng chọn bài báo',
            'paper_id.exists' => 'Bài báo không tồn tại',
            'coi_code.required' => 'Vui lòng chọn loại COI',
            'coi_code.exists' => 'Loại COI không hợp lệ',
            'evidence.required' => 'Vui lòng cung cấp bằng chứng COI',
            'evidence.max' => 'Bằng chứng không được quá 1000 ký tự',
            'note.max' => 'Ghi chú không được quá 500 ký tự'
        ]);
        
        // Check if COI already declared for this paper-reviewer pair
        $existingCOI = DB::table('coi')
            ->where('paper_id', $validated['paper_id'])
            ->where('reviewer_id', $userId)
            ->where('source_type', 'DECLARED')
            ->first();
        
        if ($existingCOI) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn đã khai báo COI cho bài báo này rồi.');
        }
        
        // Check if reviewer is assigned to this paper
        $assignment = DB::table('phancong')
            ->where('paper_id', $validated['paper_id'])
            ->where('reviewer_id', $userId)
            ->first();
        
        if (!$assignment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn không được phân công phản biện bài báo này.');
        }
        
        try {
            DB::beginTransaction();
            
            // Insert COI record
            $coiId = DB::table('coi')->insertGetId([
                'paper_id' => $validated['paper_id'],
                'reviewer_id' => $userId,
                'coi_code' => $validated['coi_code'],
                'source_type' => 'DECLARED',
                'evidence' => $validated['evidence'],
                'created_at' => Carbon::now()
            ]);
            
            DB::commit();
            
            return redirect()->route('reviewer.coi.index')
                ->with('success', 'Khai báo COI thành công. Chair sẽ xem xét và xử lý.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi khai báo COI: ' . $e->getMessage());
        }
    }
    
    /**
     * Show details of a specific COI declaration
     */
    public function show($coiId)
    {
        $userId = Auth::id();
        
        // Get COI details
        $coi = DB::table('coi')
            ->join('baibao', 'coi.paper_id', '=', 'baibao.paper_id')
            ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
            ->join('loaicoi', 'coi.coi_code', '=', 'loaicoi.coi_code')
            ->leftJoin('XuLyCOI', 'coi.coi_id', '=', 'XuLycoi.coi_id')
            ->leftjoin('nguoidung', 'XuLycoi.chair_id', '=', 'nguoidung.user_id')
            ->where('coi.coi_id', $coiId)
            ->where('coi.reviewer_id', $userId)
            ->where('coi.source_type', 'DECLARED')
            ->select(
                'coi.*',
                'baibao.title as paper_title',
                'baibao.abstract as paper_abstract',
                'baibao.keywords as paper_keywords',
                'baibao.status as paper_status',
                'baibao.submitted_at',
                'hoithao.title as conference_code',
                'hoithao.title as conference_title',
                'loaicoi.coi_name',
                'XuLycoi.decision_id',
                'XuLycoi.decision',
                'XuLycoi.note as resolution_note',
                'XuLycoi.decided_at',
                'nguoidung.full_name as resolved_by_name'
            )
            ->first();
        
        if (!$coi) {
            return redirect()->route('reviewer.coi.index')
                ->with('error', 'Không tìm thấy COI hoặc bạn không có quyền truy cập.');
        }
        
        // Get assignment status
        $assignment = DB::table('phancong')
            ->where('paper_id', $coi->paper_id)
            ->where('reviewer_id', $userId)
            ->first();
        
        return view('reviewer.coi.show', compact('coi', 'assignment'));
    }
    
    /**
     * Search papers for COI declaration
     */
    public function searchPapers(Request $request)
    {
        $userId = Auth::id();
        $conferenceId = $request->input('conference_id');
        $search = $request->input('search', '');
        
        // Get papers assigned to this reviewer in the selected conference
        $query = DB::table('baibao')
            ->join('phancong', 'baibao.paper_id', '=', 'phancong.paper_id')
            ->leftjoin('coi', function($join) use ($userId) {
                $join->on('baibao.paper_id', '=', 'coi.paper_id')
                    ->where('coi.reviewer_id', '=', $userId)
                    ->where('coi.source_type', '=', 'DECLARED');
            })
            ->where('phancong.reviewer_id', $userId)
            ->whereNull('coi.coi_id'); // Only papers without declared COI
        
        if ($conferenceId) {
            $query->where('baibao.conference_id', $conferenceId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('baibao.title', 'LIKE', "%{$search}%")
                  ->orWhere('baibao.paper_id', 'LIKE', "%{$search}%");
            });
        }
        
        $papers = $query->select(
                'baibao.paper_id',
                'baibao.title',
                'baibao.status',
                'phancong.assigned_at'
            )
            ->orderBy('phancong.assigned_at', 'desc')
            ->limit(20)
            ->get();
        
        return response()->json($papers);
    }
    
    /**
     * Retract/withdraw a COI declaration (only if not yet resolved)
     */
    public function retract($coiId)
    {
        $userId = Auth::id();
        
        // Check if COI exists and belongs to this reviewer
        $coi = DB::table('coi')
            ->leftJoin('XuLyCOI', 'coi.coi_id', '=', 'XuLycoi.coi_id')
            ->where('coi.coi_id', $coiId)
            ->where('coi.reviewer_id', $userId)
            ->where('coi.source_type', 'DECLARED')
            ->select('coi.*', 'XuLycoi.decision_id')
            ->first();
        
        if (!$coi) {
            return redirect()->route('reviewer.coi.index')
                ->with('error', 'Không tìm thấy COI hoặc bạn không có quyền truy cập.');
        }
        
        if ($coi->decision_id) {
            return redirect()->route('reviewer.coi.index')
                ->with('error', 'Không thể rút lại khai báo COI đã được xử lý.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete COI record
            DB::table('coi')->where('coi_id', $coiId)->delete();
            
            DB::commit();
            
            return redirect()->route('reviewer.coi.index')
                ->with('success', 'Đã rút lại khai báo COI thành công.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi rút lại khai báo COI: ' . $e->getMessage());
        }
    }
}




