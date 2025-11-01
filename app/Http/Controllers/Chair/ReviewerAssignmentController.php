<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewerAssignmentController extends Controller
{
    /**
     * Hiển thị trang phân công phản biện
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Lấy danh sách hội thảo mà Chair này quản lý
            $conferences = DB::table('hoithao as h')
                ->join('vaitronguoidung as vr', 'h.conference_id', '=', 'vr.conference_id')
                ->where('vr.user_id', $user->user_id)
                ->where('vr.role_code', 'CHAIR')
                ->select('h.*')
                ->get();

            // Lấy danh sách bài báo chưa được phân công đầy đủ
            $unassignedPapers = collect();
            
            foreach ($conferences as $conference) {
                $papers = DB::table('baibao as b')
                    ->leftJoin('phancongphanbien as pc', 'b.paper_id', '=', 'pc.paper_id')
                    ->leftJoin('nguoidung as author', 'b.corresponding_author_id', '=', 'author.user_id')
                    ->where('b.conference_id', $conference->conference_id)
                    ->where('b.status', 'SUBMITTED')
                    ->select(
                        'b.*',
                        'author.full_name as author_name',
                        DB::raw('COUNT(pc.assignment_id) as reviewer_count')
                    )
                    ->groupBy('b.paper_id')
                    ->having('reviewer_count', '<', 3) // Giả sử mỗi bài cần 3 phản biện viên
                    ->get();
                    
                $unassignedPapers = $unassignedPapers->merge($papers);
            }

            return view('chair.assignments.index', compact('conferences', 'unassignedPapers'));
            
        } catch (\Exception $e) {
            Log::error('Error in ReviewerAssignmentController@index: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu.');
        }
    }

    /**
     * Hiển thị form phân công phản biện cho bài báo cụ thể
     */
    public function assign($paperId)
    {
        try {
            // Lấy thông tin bài báo
            $paper = DB::table('baibao as b')
                ->join('nguoidung as author', 'b.corresponding_author_id', '=', 'author.user_id')
                ->join('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
                ->where('b.paper_id', $paperId)
                ->select('b.*', 'author.full_name as author_name', 'h.title as conference_title')
                ->first();

            if (!$paper) {
                return back()->with('error', 'Không tìm thấy bài báo.');
            }

            // Kiểm tra quyền của Chair
            $user = Auth::user();
            $hasPermission = DB::table('vaitronguoidung')
                ->where('user_id', $user->user_id)
                ->where('conference_id', $paper->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasPermission) {
                return back()->with('error', 'Bạn không có quyền phân công cho bài báo này.');
            }

            // Lấy danh sách reviewer có thể phân công
            $availableReviewers = DB::table('nguoidung as n')
                ->join('vaitronguoidung as vr', 'n.user_id', '=', 'vr.user_id')
                ->leftJoin('phancongphanbien as pc', function($join) use ($paperId) {
                    $join->on('n.user_id', '=', 'pc.reviewer_id')
                         ->where('pc.paper_id', '=', $paperId);
                })
                ->leftJoin('coi', function($join) use ($paperId) {
                    $join->on('n.user_id', '=', 'coi.reviewer_id')
                         ->where('coi.paper_id', '=', $paperId)
                         ->where('coi.has_conflict', '=', true);
                })
                ->where('vr.conference_id', $paper->conference_id)
                ->where('vr.role_code', 'REVIEWER')
                ->where('n.user_id', '!=', $paper->corresponding_author_id) // Không cho tác giả tự phản biện
                ->whereNull('pc.assignment_id') // Chưa được phân công
                ->whereNull('coi.coi_id') // Không có xung đột lợi ích
                ->select('n.user_id', 'n.full_name', 'n.email', 'n.expertise')
                ->get();

            // Lấy danh sách reviewer đã được phân công
            $assignedReviewers = DB::table('phancongphanbien as pc')
                ->join('nguoidung as n', 'pc.reviewer_id', '=', 'n.user_id')
                ->where('pc.paper_id', $paperId)
                ->select('pc.*', 'n.full_name', 'n.email')
                ->get();

            return view('chair.assignments.assign', compact(
                'paper', 
                'availableReviewers', 
                'assignedReviewers'
            ));

        } catch (\Exception $e) {
            Log::error('Error in ReviewerAssignmentController@assign: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu.');
        }
    }

    /**
     * Thực hiện phân công reviewer cho bài báo
     */
    public function store(Request $request)
    {
        $request->validate([
            'paper_id' => 'required|integer',
            'reviewer_ids' => 'required|array|min:1|max:3',
            'reviewer_ids.*' => 'integer'
        ]);

        try {
            $paperId = $request->paper_id;
            $reviewerIds = $request->reviewer_ids;

            // Kiểm tra quyền
            $paper = DB::table('baibao')->where('paper_id', $paperId)->first();
            $user = Auth::user();
            
            $hasPermission = DB::table('vaitronguoidung')
                ->where('user_id', $user->user_id)
                ->where('conference_id', $paper->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasPermission) {
                return response()->json(['error' => 'Không có quyền thực hiện'], 403);
            }

            DB::beginTransaction();

            // Xóa phân công cũ (nếu có)
            DB::table('phancongphanbien')->where('paper_id', $paperId)->delete();

            // Tạo phân công mới
            foreach ($reviewerIds as $reviewerId) {
                DB::table('phancongphanbien')->insert([
                    'paper_id' => $paperId,
                    'reviewer_id' => $reviewerId,
                    'assigned_by' => $user->user_id,
                    'assigned_at' => now(),
                    'status' => 'PENDING',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Cập nhật trạng thái bài báo
            DB::table('baibao')
                ->where('paper_id', $paperId)
                ->update([
                    'status' => 'UNDER_REVIEW',
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Phân công phản biện thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in ReviewerAssignmentController@store: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Có lỗi xảy ra khi phân công. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Xóa phân công reviewer
     */
    public function remove(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|integer'
        ]);

        try {
            $assignmentId = $request->assignment_id;
            
            // Lấy thông tin phân công
            $assignment = DB::table('phancongphanbien as pc')
                ->join('baibao as b', 'pc.paper_id', '=', 'b.paper_id')
                ->where('pc.assignment_id', $assignmentId)
                ->select('pc.*', 'b.conference_id')
                ->first();

            if (!$assignment) {
                return response()->json(['error' => 'Không tìm thấy phân công'], 404);
            }

            // Kiểm tra quyền
            $user = Auth::user();
            $hasPermission = DB::table('vaitronguoidung')
                ->where('user_id', $user->user_id)
                ->where('conference_id', $assignment->conference_id)
                ->where('role_code', 'CHAIR')
                ->exists();

            if (!$hasPermission) {
                return response()->json(['error' => 'Không có quyền thực hiện'], 403);
            }

            // Xóa phân công
            DB::table('phancongphanbien')->where('assignment_id', $assignmentId)->delete();

            // Kiểm tra nếu bài báo không còn phân công nào thì đổi trạng thái
            $remainingAssignments = DB::table('phancongphanbien')
                ->where('paper_id', $assignment->paper_id)
                ->count();

            if ($remainingAssignments == 0) {
                DB::table('baibao')
                    ->where('paper_id', $assignment->paper_id)
                    ->update([
                        'status' => 'SUBMITTED',
                        'updated_at' => now()
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa phân công thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ReviewerAssignmentController@remove: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Có lỗi xảy ra khi xóa phân công.'
            ], 500);
        }
    }
}