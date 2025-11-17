<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Announcements",
 *     description="API quản lý thông báo hội thảo"
 * )
 */
class AnnouncementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/announcements",
     *     tags={"Announcements"},
     *     summary="Danh sách thông báo",
     *     description="Lấy danh sách thông báo theo hội thảo (Chair) hoặc thông báo nhận được (User)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conference_id",
     *         in="query",
     *         description="ID hội thảo (Chair filter)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Trạng thái: SENT, SCHEDULED, FAILED",
     *         required=false,
     *         @OA\Schema(type="string", enum={"SENT", "SCHEDULED", "FAILED"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="announcements", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="announcement_id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="content", type="string"),
     *                         @OA\Property(property="audience", type="string"),
     *                         @OA\Property(property="status", type="string"),
     *                         @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *                         @OA\Property(property="sent_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="conference_name", type="string"),
     *                         @OA\Property(property="recipient_count", type="integer")
     *                     )
     *                 ),
     *                 @OA\Property(property="statistics", type="object",
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="sent", type="integer"),
     *                     @OA\Property(property="scheduled", type="integer"),
     *                     @OA\Property(property="failed", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Kiểm tra role: Chair có thể quản lý, User chỉ xem được thông báo nhận
        $isChair = DB::table('hoithao')
            ->where('chair_id', $user->user_id)
            ->exists();
        
        if ($isChair) {
            // Chair: Lấy thông báo của các hội thảo mình quản lý
            return $this->getChairAnnouncements($request);
        } else {
            // User: Lấy thông báo đã nhận
            return $this->getUserAnnouncements($request);
        }
    }
    
    /**
     * Lấy danh sách thông báo cho Chair
     */
    private function getChairAnnouncements(Request $request)
    {
        $user = $request->user();
        $conferenceId = $request->get('conference_id');
        $status = $request->get('status');
        
        $query = DB::table('thongbao as tb')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('ht.chair_id', $user->user_id)
            ->select(
                'tb.announcement_id',
                'tb.title',
                'tb.content',
                'tb.audience',
                'tb.channels',
                'tb.status',
                'tb.scheduled_at',
                'tb.sent_at',
                'tb.created_at',
                'tb.conference_id',
                'ht.title as conference_name'
            );
        
        if ($conferenceId) {
            $query->where('tb.conference_id', $conferenceId);
        }
        
        if ($status) {
            $query->where('tb.status', $status);
        }
        
        $announcements = $query->orderBy('tb.created_at', 'desc')->get();
        
        // Thêm recipient count
        $announcements = $announcements->map(function($item) {
            $item->channels = json_decode($item->channels);
            $item->recipient_count = DB::table('user_notifications')
                ->where('announcement_id', $item->announcement_id)
                ->count();
            return $item;
        });
        
        // Statistics
        $allAnnouncements = DB::table('thongbao as tb')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('ht.chair_id', $user->user_id);
        
        $stats = [
            'total' => (clone $allAnnouncements)->count(),
            'sent' => (clone $allAnnouncements)->where('tb.status', 'SENT')->count(),
            'scheduled' => (clone $allAnnouncements)->where('tb.status', 'SCHEDULED')->count(),
            'failed' => (clone $allAnnouncements)->where('tb.status', 'FAILED')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'announcements' => $announcements,
                'statistics' => $stats
            ]
        ]);
    }
    
    /**
     * Lấy danh sách thông báo đã nhận cho User
     */
    private function getUserAnnouncements(Request $request)
    {
        $user = $request->user();
        $conferenceId = $request->get('conference_id');
        
        $query = DB::table('user_notifications as un')
            ->join('thongbao as tb', 'tb.announcement_id', '=', 'un.announcement_id')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('un.user_id', $user->user_id)
            ->select(
                'tb.announcement_id',
                'tb.title',
                'tb.content',
                'tb.sent_at',
                'tb.conference_id',
                'ht.title as conference_name',
                'un.is_read',
                'un.read_at',
                'un.created_at as received_at'
            );
        
        if ($conferenceId) {
            $query->where('tb.conference_id', $conferenceId);
        }
        
        $announcements = $query->orderBy('un.created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'announcements' => $announcements,
                'unread_count' => DB::table('user_notifications')
                    ->where('user_id', $user->user_id)
                    ->where('is_read', false)
                    ->count()
            ]
        ]);
    }
    
    /**
     * @OA\Post(
     *     path="/api/announcements",
     *     tags={"Announcements"},
     *     summary="Tạo thông báo mới",
     *     description="Chair tạo và lên lịch thông báo",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"conference_id", "title", "content", "audience", "scheduled_at"},
     *             @OA\Property(property="conference_id", type="integer", example=8),
     *             @OA\Property(property="title", type="string", example="Thông báo quan trọng"),
     *             @OA\Property(property="content", type="string", example="Nội dung thông báo..."),
     *             @OA\Property(property="audience", type="string", enum={"ALL", "AUTHORS", "REVIEWERS", "CHAIRS"}, example="ALL"),
     *             @OA\Property(property="channels", type="array", @OA\Items(type="string", enum={"SYSTEM", "EMAIL", "CHATBOT"}), example={"SYSTEM"}),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-11-13 14:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Thông báo đã được tạo và lên lịch"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="announcement_id", type="integer"),
     *                 @OA\Property(property="scheduled_at", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Không có quyền")
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        // Validation
        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|integer|exists:hoithao,conference_id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|in:ALL,AUTHORS,REVIEWERS,CHAIRS',
            'channels' => 'required|array',
            'channels.*' => 'in:SYSTEM,EMAIL,CHATBOT',
            'scheduled_at' => 'required|date|after:now',
        ], [
            'conference_id.required' => 'Vui lòng chọn hội thảo',
            'conference_id.exists' => 'Hội thảo không tồn tại',
            'title.required' => 'Vui lòng nhập tiêu đề',
            'content.required' => 'Vui lòng nhập nội dung',
            'audience.required' => 'Vui lòng chọn đối tượng',
            'audience.in' => 'Đối tượng không hợp lệ',
            'channels.required' => 'Vui lòng chọn kênh gửi',
            'scheduled_at.required' => 'Vui lòng chọn thời gian gửi',
            'scheduled_at.after' => 'Thời gian gửi phải sau hiện tại',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Kiểm tra quyền Chair
        $isChair = DB::table('hoithao')
            ->where('conference_id', $request->input('conference_id'))
            ->where('chair_id', $user->user_id)
            ->exists();
        
        if (!$isChair) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền tạo thông báo cho hội thảo này'
            ], 403);
        }
        
        // Tạo thông báo
        $announcementId = DB::table('thongbao')->insertGetId([
            'conference_id' => $request->input('conference_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'audience' => $request->input('audience'),
            'channels' => json_encode($request->input('channels')),
            'status' => 'SCHEDULED',
            'scheduled_at' => $request->input('scheduled_at'),
            'created_at' => now(),
            'created_by' => $user->user_id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thông báo đã được tạo và lên lịch thành công',
            'data' => [
                'announcement_id' => $announcementId,
                'scheduled_at' => $request->input('scheduled_at')
            ]
        ], 201);
    }
    
    /**
     * @OA\Get(
     *     path="/api/announcements/{id}",
     *     tags={"Announcements"},
     *     summary="Chi tiết thông báo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $announcement = DB::table('thongbao as tb')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('tb.announcement_id', $id)
            ->select(
                'tb.*',
                'ht.title as conference_name',
                'ht.chair_id'
            )
            ->first();
        
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }
        
        // Kiểm tra quyền xem
        $isChair = $announcement->chair_id == $user->user_id;
        $hasReceived = DB::table('user_notifications')
            ->where('announcement_id', $id)
            ->where('user_id', $user->user_id)
            ->exists();
        
        if (!$isChair && !$hasReceived) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thông báo này'
            ], 403);
        }
        
        $announcement->channels = json_decode($announcement->channels);
        
        // Statistics (chỉ cho Chair)
        if ($isChair) {
            $announcement->statistics = [
                'total_recipients' => DB::table('user_notifications')
                    ->where('announcement_id', $id)
                    ->count(),
                'read_count' => DB::table('user_notifications')
                    ->where('announcement_id', $id)
                    ->where('is_read', true)
                    ->count(),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }
    
    /**
     * @OA\Put(
     *     path="/api/announcements/{id}",
     *     tags={"Announcements"},
     *     summary="Cập nhật thông báo",
     *     description="Chỉ cập nhật được thông báo SCHEDULED",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=403, description="Không có quyền hoặc thông báo đã gửi")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        $announcement = DB::table('thongbao as tb')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('tb.announcement_id', $id)
            ->select('tb.*', 'ht.chair_id')
            ->first();
        
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }
        
        // Kiểm tra quyền
        if ($announcement->chair_id != $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền sửa thông báo này'
            ], 403);
        }
        
        // Chỉ sửa được thông báo SCHEDULED
        if ($announcement->status !== 'SCHEDULED') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể sửa thông báo đang lên lịch'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'scheduled_at' => 'sometimes|date|after:now',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $updateData = $request->only(['title', 'content', 'scheduled_at']);
        
        if (!empty($updateData)) {
            DB::table('thongbao')
                ->where('announcement_id', $id)
                ->update($updateData);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thông báo'
        ]);
    }
    
    /**
     * @OA\Delete(
     *     path="/api/announcements/{id}",
     *     tags={"Announcements"},
     *     summary="Xóa thông báo",
     *     description="Chỉ xóa được thông báo SCHEDULED",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Xóa thành công"),
     *     @OA\Response(response=403, description="Không có quyền")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $announcement = DB::table('thongbao as tb')
            ->join('hoithao as ht', 'ht.conference_id', '=', 'tb.conference_id')
            ->where('tb.announcement_id', $id)
            ->select('tb.*', 'ht.chair_id')
            ->first();
        
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }
        
        if ($announcement->chair_id != $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa thông báo này'
            ], 403);
        }
        
        if ($announcement->status !== 'SCHEDULED') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể xóa thông báo đang lên lịch'
            ], 403);
        }
        
        DB::table('thongbao')->where('announcement_id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo'
        ]);
    }
    
    /**
     * @OA\Post(
     *     path="/api/announcements/{id}/mark-read",
     *     tags={"Announcements"},
     *     summary="Đánh dấu đã đọc thông báo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        
        $updated = DB::table('user_notifications')
            ->where('announcement_id', $id)
            ->where('user_id', $user->user_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đã đọc'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thông báo hoặc đã đọc rồi'
        ], 404);
    }
    
    /**
     * @OA\Get(
     *     path="/api/announcements/conferences/list",
     *     tags={"Announcements"},
     *     summary="Danh sách hội thảo để gửi thông báo",
     *     description="Lấy danh sách hội thảo mà Chair quản lý",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function getConferences(Request $request)
    {
        $user = $request->user();
        
        $conferences = DB::table('hoithao')
            ->where('chair_id', $user->user_id)
            ->whereIn('status', ['APPROVED', 'ACTIVE'])
            ->select('conference_id', 'title as conference_name', 'start_date', 'end_date')
            ->orderBy('start_date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $conferences
        ]);
    }
    
    /**
     * @OA\Post(
     *     path="/api/announcements/preview-recipients",
     *     tags={"Announcements"},
     *     summary="Xem trước số lượng người nhận",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"conference_id", "audience"},
     *             @OA\Property(property="conference_id", type="integer"),
     *             @OA\Property(property="audience", type="string", enum={"ALL", "AUTHORS", "REVIEWERS", "CHAIRS"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function previewRecipients(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|integer|exists:hoithao,conference_id',
            'audience' => 'required|in:ALL,AUTHORS,REVIEWERS,CHAIRS',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $conferenceId = $request->input('conference_id');
        $audience = $request->input('audience');
        
        $count = 0;
        
        switch ($audience) {
            case 'ALL':
                $count = DB::table('join_requests')
                    ->where('conference_id', $conferenceId)
                    ->where('status', 'APPROVED')
                    ->distinct('user_id')
                    ->count();
                break;
                
            case 'AUTHORS':
                $count = DB::table('baibao')
                    ->where('conference_id', $conferenceId)
                    ->distinct('submitter_id')
                    ->count();
                break;
                
            case 'REVIEWERS':
                $count = DB::table('join_requests')
                    ->where('conference_id', $conferenceId)
                    ->where('status', 'APPROVED')
                    ->where('role', 'REVIEWER')
                    ->distinct('user_id')
                    ->count();
                break;
                
            case 'CHAIRS':
                $count = DB::table('hoithao')
                    ->where('conference_id', $conferenceId)
                    ->whereNotNull('chair_id')
                    ->count();
                break;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
                'audience' => $audience
            ]
        ]);
    }
}
