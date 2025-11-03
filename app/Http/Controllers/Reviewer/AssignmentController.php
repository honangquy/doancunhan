<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ReviewerAssignment;
use App\Models\BaiBao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display reviewer's assignments
     */
    public function index()
    {
        $userId = Auth::id();
        
        $assignments = ReviewerAssignment::join('baibao as b', 'reviewer_assignments.paper_id', '=', 'b.paper_id')
            // join conference table so we can show conference title/name
            ->leftJoin('hoithao as ht', 'b.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as assigner', 'reviewer_assignments.assigned_by', '=', 'assigner.user_id')
            ->where('reviewer_assignments.user_id', $userId)
            ->select(
                'reviewer_assignments.*',
                'b.title as paper_title',
                'b.abstract as paper_abstract',
                'b.conference_id as conference_id',
                'ht.title as conference_name',
                'assigner.full_name as assigned_by_name'
            )
            ->orderBy('reviewer_assignments.assigned_at', 'desc')
            ->get();

        return view('reviewer.assignments.index', compact('assignments'));
    }

    /**
     * Show specific assignment details
     */
    public function show($assignmentId)
    {
        $userId = Auth::id();
        
        $assignment = ReviewerAssignment::join('baibao as b', 'reviewer_assignments.paper_id', '=', 'b.paper_id')
            // also include conference info
            ->leftJoin('hoithao as ht', 'b.conference_id', '=', 'ht.conference_id')
            ->join('nguoidung as assigner', 'reviewer_assignments.assigned_by', '=', 'assigner.user_id')
            ->where('reviewer_assignments.id', $assignmentId)
            ->where('reviewer_assignments.user_id', $userId)
            ->select(
                'reviewer_assignments.*',
                'b.title as paper_title',
                'b.abstract as paper_abstract',
                'b.file_path as paper_file',
                'b.conference_id as conference_id',
                'ht.title as conference_name',
                'assigner.full_name as assigned_by_name'
            )
            ->firstOrFail();

        return view('reviewer.assignments.show', compact('assignment'));
    }

    /**
     * Accept assignment
     */
    public function accept($assignmentId)
    {
        $userId = Auth::id();
        
        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('user_id', $userId)
            ->where('status', 'PENDING')
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment not found or already processed'], 404);
        }

        $assignment->update([
            'status' => 'ACCEPTED',
            'response_at' => now(),
            'response_note' => 'Assignment accepted by reviewer'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã chấp nhận phân công phản biện',
            'status' => 'ACCEPTED'
        ]);
    }

    /**
     * Decline assignment with reason
     */
    public function decline(Request $request, $assignmentId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'suggest_alternative' => 'nullable|boolean'
        ]);

        $userId = Auth::id();
        
        $assignment = ReviewerAssignment::where('id', $assignmentId)
            ->where('user_id', $userId)
            ->where('status', 'PENDING')
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment not found or already processed'], 404);
        }

        $assignment->update([
            'status' => 'DECLINED',
            'response_at' => now(),
            'response_note' => $request->reason,
            'decline_metadata' => json_encode([
                'suggest_alternative' => $request->suggest_alternative ?? false,
                'declined_at' => now()->toISOString()
            ])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối phân công phản biện',
            'status' => 'DECLINED'
        ]);
    }

    /**
     * Get assignment statistics for reviewer dashboard
     */
    public function getStats()
    {
        $userId = Auth::id();
        
        $stats = [
            'total' => ReviewerAssignment::where('user_id', $userId)->count(),
            'pending' => ReviewerAssignment::where('user_id', $userId)->where('status', 'PENDING')->count(),
            'accepted' => ReviewerAssignment::where('user_id', $userId)->where('status', 'ACCEPTED')->count(),
            'completed' => ReviewerAssignment::where('user_id', $userId)->where('status', 'COMPLETED')->count(),
            'declined' => ReviewerAssignment::where('user_id', $userId)->where('status', 'DECLINED')->count(),
        ];

        return response()->json(['success' => true, 'stats' => $stats]);
    }
}




