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
        $isReviewer = DB::table('VaiTroNguoiDung')
            ->where('user_id', $userId)
            ->where('role_code', 'REVIEWER')
            ->exists();
            
        if (!$isReviewer) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }
        
        // Get all COI declared by this reviewer
        $declaredCOI = DB::table('COI')
            ->join('BaiBao', 'COI.paper_id', '=', 'BaiBao.paper_id')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('LoaiCOI', 'COI.coi_code', '=', 'LoaiCOI.coi_code')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.reviewer_id', $userId)
            ->where('COI.source_type', 'DECLARED')
            ->select(
                'COI.coi_id',
                'COI.paper_id',
                'BaiBao.title as paper_title',
                'BaiBao.status as paper_status',
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_code',
                'LoaiCOI.coi_name',
                'COI.evidence',
                'COI.note',
                'COI.detected_at',
                'COI.created_at',
                'XuLyCOI.decision_id',
                'XuLyCOI.decision',
                'XuLyCOI.decided_at'
            )
            ->orderBy('COI.created_at', 'desc')
            ->get();
        
        // Get statistics
        $stats = [
            'total' => $declaredCOI->count(),
            'resolved' => $declaredCOI->where('decision_id', '!=', null)->count(),
            'unresolved' => $declaredCOI->where('decision_id', null)->count(),
            'by_type' => DB::table('COI')
                ->join('LoaiCOI', 'COI.coi_code', '=', 'LoaiCOI.coi_code')
                ->where('COI.reviewer_id', $userId)
                ->where('COI.source_type', 'DECLARED')
                ->select('LoaiCOI.coi_name', DB::raw('count(*) as count'))
                ->groupBy('LoaiCOI.coi_name')
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
        $isReviewer = DB::table('VaiTroNguoiDung')
            ->where('user_id', $userId)
            ->where('role_code', 'REVIEWER')
            ->exists();
            
        if (!$isReviewer) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }
        
        // Get COI types
        $coiTypes = DB::table('LoaiCOI')
            ->orderBy('coi_name')
            ->get();
        
        // Get conferences where reviewer has assignments
        $conferences = DB::table('HoiThao')
            ->join('BaiBao', 'HoiThao.conference_id', '=', 'BaiBao.conference_id')
            ->join('PhanCong', 'BaiBao.paper_id', '=', 'PhanCong.paper_id')
            ->where('PhanCong.reviewer_id', $userId)
            ->select('HoiThao.conference_id', 'HoiThao.title as conference_code', 'HoiThao.title')
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
            'paper_id' => 'required|exists:BaiBao,paper_id',
            'coi_code' => 'required|exists:LoaiCOI,coi_code',
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
        $existingCOI = DB::table('COI')
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
        $assignment = DB::table('PhanCong')
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
            $coiId = DB::table('COI')->insertGetId([
                'paper_id' => $validated['paper_id'],
                'reviewer_id' => $userId,
                'coi_code' => $validated['coi_code'],
                'source_type' => 'DECLARED',
                'evidence' => $validated['evidence'],
                'note' => $validated['note'],
                'detected_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
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
        $coi = DB::table('COI')
            ->join('BaiBao', 'COI.paper_id', '=', 'BaiBao.paper_id')
            ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
            ->join('LoaiCOI', 'COI.coi_code', '=', 'LoaiCOI.coi_code')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->leftJoin('NguoiDung', 'XuLyCOI.chair_id', '=', 'NguoiDung.user_id')
            ->where('COI.coi_id', $coiId)
            ->where('COI.reviewer_id', $userId)
            ->where('COI.source_type', 'DECLARED')
            ->select(
                'COI.*',
                'BaiBao.title as paper_title',
                'BaiBao.abstract as paper_abstract',
                'BaiBao.keywords as paper_keywords',
                'BaiBao.status as paper_status',
                'BaiBao.submitted_at',
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_name',
                'XuLyCOI.decision_id',
                'XuLyCOI.decision',
                'XuLyCOI.note as resolution_note',
                'XuLyCOI.decided_at',
                'NguoiDung.full_name as resolved_by_name'
            )
            ->first();
        
        if (!$coi) {
            return redirect()->route('reviewer.coi.index')
                ->with('error', 'Không tìm thấy COI hoặc bạn không có quyền truy cập.');
        }
        
        // Get assignment status
        $assignment = DB::table('PhanCong')
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
        $query = DB::table('BaiBao')
            ->join('PhanCong', 'BaiBao.paper_id', '=', 'PhanCong.paper_id')
            ->leftJoin('COI', function($join) use ($userId) {
                $join->on('BaiBao.paper_id', '=', 'COI.paper_id')
                    ->where('COI.reviewer_id', '=', $userId)
                    ->where('COI.source_type', '=', 'DECLARED');
            })
            ->where('PhanCong.reviewer_id', $userId)
            ->whereNull('COI.coi_id'); // Only papers without declared COI
        
        if ($conferenceId) {
            $query->where('BaiBao.conference_id', $conferenceId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('BaiBao.title', 'LIKE', "%{$search}%")
                  ->orWhere('BaiBao.paper_id', 'LIKE', "%{$search}%");
            });
        }
        
        $papers = $query->select(
                'BaiBao.paper_id',
                'BaiBao.title',
                'BaiBao.status',
                'PhanCong.assigned_at'
            )
            ->orderBy('PhanCong.assigned_at', 'desc')
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
        $coi = DB::table('COI')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.coi_id', $coiId)
            ->where('COI.reviewer_id', $userId)
            ->where('COI.source_type', 'DECLARED')
            ->select('COI.*', 'XuLyCOI.decision_id')
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
            DB::table('COI')->where('coi_id', $coiId)->delete();
            
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
