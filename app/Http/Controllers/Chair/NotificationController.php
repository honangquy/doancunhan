<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get unread notifications (API endpoint for Chair)
     */
    public function getUnread()
    {
        $user = Auth::user();

        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function($notification) {
                $data = json_decode($notification->data, true) ?? [];
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'url' => $data['url'] ?? '#',
                    'level' => $data['level'] ?? 'info',
                    'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
                    'is_read' => $notification->read_at !== null,
                ];
            }),
            'unread_count' => DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    /**
     * Mark notification as read (API endpoint)
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Thông báo không tồn tại'
            ], 404);
        }

        if (!$notification->read_at) {
            DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đã đọc'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc'
        ]);
    }

    /**
     * Mark notification as read and redirect to its URL
     */
    public function markAsReadAndRedirect($id)
    {
        $user = Auth::user();

        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$notification) {
            return redirect()->route('chair.dashboard')
                ->with('error', 'Thông báo không tồn tại');
        }

        // Mark as read
        if (!$notification->read_at) {
            DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        // Get redirect URL from notification data
        $data = json_decode($notification->data, true) ?? [];
        $url = $data['url'] ?? route('chair.dashboard');

        return redirect($url);
    }
}
