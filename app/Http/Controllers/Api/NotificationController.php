<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     * GET /api/notifications
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $perPage = $request->get('per_page', 15);
            $filterRead = $request->get('filter', 'all'); // all, read, unread

            $query = Notification::where('user_id', $user->user_id);

            // Filter by read status
            if ($filterRead === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($filterRead === 'unread') {
                $query->whereNull('read_at');
            }

            // Order by newest first
            $notifications = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::where('user_id', $user->user_id)
                    ->whereNull('read_at')
                    ->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông báo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread notification count
     * GET /api/notifications/unread
     */
    public function unreadCount()
    {
        try {
            $user = auth()->user();
            $count = Notification::where('user_id', $user->user_id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy số thông báo chưa đọc',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show specific notification
     * GET /api/notifications/{id}
     */
    public function show($id)
    {
        try {
            $user = auth()->user();
            $notification = Notification::where('user_id', $user->user_id)
                ->findOrFail($id);

            // Mark as read when viewing
            if (!$notification->isRead()) {
                $notification->markAsRead();
            }

            return response()->json([
                'success' => true,
                'data' => $notification,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thông báo không tồn tại',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Mark notification as read
     * PATCH /api/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        try {
            $user = auth()->user();
            $notification = Notification::where('user_id', $user->user_id)
                ->findOrFail($id);

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được đánh dấu là đã đọc',
                'data' => $notification,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật thông báo',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Mark all notifications as read
     * PATCH /api/notifications/read-all
     */
    public function markAllAsRead()
    {
        try {
            $user = auth()->user();
            
            DB::beginTransaction();

            try {
                Notification::where('user_id', $user->user_id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Tất cả thông báo đã được đánh dấu là đã đọc',
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật thông báo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete notification
     * DELETE /api/notifications/{id}
     */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $notification = Notification::where('user_id', $user->user_id)
                ->findOrFail($id);

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã bị xóa',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa thông báo',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
