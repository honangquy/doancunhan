<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * List news (for Chair to see their own news, or public news)
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $query = News::query();

            // Filter by conference if provided
            if ($request->has('conference_id')) {
                $query->where('conference_id', $request->conference_id);
            }

            // Show news created by this user
            $query->where('created_by', $user->user_id);

            $news = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create news (Chair only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|in:NEWS,EVENT,ANNOUNCEMENT,GUIDE',
            'conference_id' => 'nullable|exists:hoithao,conference_id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:2048', // 2MB
            'is_featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['created_by'] = auth()->id();
            $data['status'] = 'PENDING'; // Default status for Chair submission

            // Handle slug
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']) . '-' . time();
            }

            // Handle Image Upload
            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('news/covers', 'public');
                $data['cover_image'] = $path;
            }

            $news = News::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tin tức đã được gửi và đang chờ duyệt.',
                'data' => $news
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show news detail
     */
    public function show($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Update news (Chair can update if status is DRAFT or PENDING?)
     * Usually if PENDING, updating might reset approval or be allowed.
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        // Check permission: Only creator can update
        if ($news->created_by != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền chỉnh sửa tin tức này'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|in:NEWS,EVENT,ANNOUNCEMENT,GUIDE',
            'conference_id' => 'nullable|exists:hoithao,conference_id',
            'summary' => 'nullable|string',
            'content' => 'sometimes|required|string',
            'cover_image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['cover_image', 'status']); // Don't allow status update via API for Chair

            // Handle Image Upload
            if ($request->hasFile('cover_image')) {
                // Delete old image
                if ($news->cover_image) {
                    Storage::disk('public')->delete($news->cover_image);
                }
                $path = $request->file('cover_image')->store('news/covers', 'public');
                $data['cover_image'] = $path;
            }

            $news->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tin tức thành công',
                'data' => $news
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete news
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        if ($news->created_by != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa tin tức này'
            ], 403);
        }

        try {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }

            $news->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tin tức thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
