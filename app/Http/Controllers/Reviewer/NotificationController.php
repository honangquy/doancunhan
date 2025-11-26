<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ReviewerAssignment;
use App\Models\AssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get all notifications for current reviewer
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Create notifications from reviewer assignments
        $assignments = DB::table('reviewer_assignments as ra')
            ->join('baibao as b', 'ra.paper_id', '=', 'b.paper_id')
            ->join('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
            ->where('ra.user_id', $userId)
            ->select(
                'ra.id',
                'ra.status',
                'ra.assigned_at as created_at',
                'b.title as paper_title',
                'b.paper_id',
                'h.title as conference_name',
                'ra.assigned_at',
                DB::raw("CASE 
                    WHEN ra.status = 'PENDING' THEN 'Bạn được mời phản biện bài báo'
                    WHEN ra.status = 'ACCEPTED' THEN 'Bạn đã chấp nhận phản biện bài báo'
                    WHEN ra.status = 'DECLINED' THEN 'Bạn đã từ chối phản biện bài báo'
                    ELSE 'Cập nhật trạng thái phản biện'
                END as message"),
                DB::raw("NULL as read_at")
            )
            ->orderBy('ra.assigned_at', 'desc')
            ->limit(10)
            ->get();

        // Transform the data to include title and proper format
        $notifications = $assignments->map(function($assignment) {
            return [
                'id' => $assignment->id,
                'title' => 'Phân công phản biện',
                'message' => $assignment->message,
                'paper_title' => $assignment->paper_title,
                'conference_name' => $assignment->conference_name,
                'status' => $assignment->status,
                'created_at' => $assignment->created_at,
                'read_at' => $assignment->read_at
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $userId = Auth::id();

        $notification = AssignmentNotification::join('reviewer_assignments as ra', 'assignment_notifications.assignment_id', '=', 'ra.id')
            ->where('assignment_notifications.id', $notificationId)
            ->where('ra.user_id', $userId)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        AssignmentNotification::where('id', $notificationId)->update([
            'status' => 'READ',
            'read_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();

        // Count pending assignments as unread notifications
        $count = DB::table('reviewer_assignments')
            ->where('user_id', $userId)
            ->where('status', 'PENDING')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();

        AssignmentNotification::join('reviewer_assignments as ra', 'assignment_notifications.assignment_id', '=', 'ra.id')
            ->where('ra.user_id', $userId)
            ->where('assignment_notifications.status', 'PENDING')
            ->update([
                'assignment_notifications.status' => 'READ',
                'assignment_notifications.read_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}