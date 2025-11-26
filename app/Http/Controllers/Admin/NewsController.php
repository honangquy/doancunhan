<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\HoiThao;
use App\Models\User;
use App\Notifications\NewsApprovalRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of news
     */
    public function index(Request $request)
    {
        $query = News::with(['conference', 'createdBy'])
                     ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by conference
        if ($request->filled('conference_id')) {
            $query->where('conference_id', $request->conference_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('summary', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $news = $query->paginate(15);
        $conferences = HoiThao::select('conference_id', 'title')->get();

        return view('admin.news.index', compact('news', 'conferences'));
    }

    /**
     * Show the form for creating a new news
     */
    public function create()
    {
        $conferences = HoiThao::select('conference_id', 'title')
                              ->orderBy('title')
                              ->get();

        return view('admin.news.create', compact('conferences'));
    }

    /**
     * Store a newly created news in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|in:NEWS,ANNOUNCEMENT,EVENT,GUIDE',
            'conference_id' => 'nullable|exists:hoithao,conference_id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:DRAFT,PENDING,PUBLISHED,ARCHIVED',
            'published_at' => 'nullable|date',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('news/covers', $filename, 'public');
            $validated['cover_image'] = $path;
        }

        // Handle attachment upload
        if ($request->hasFile('attachment_path')) {
            $file = $request->file('attachment_path');
            $filename = time() . '_' . Str::slug($request->title) . '_attachment.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('news/attachments', $filename, 'public');
            $validated['attachment_path'] = $path;
        }

        // Handle gallery images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . Str::slug($request->title) . '_gallery_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('news/gallery', $filename, 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        // Set created_by
        $validated['created_by'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $news = News::create($validated);

        // Send notification to admins if status is PENDING
        if ($news->status === 'PENDING') {
            $admins = User::whereHas('roles', function($q) {
                $q->where('role_code', 'ADMIN');
            })->get();

            if ($admins->count() > 0) {
                Notification::send($admins, new NewsApprovalRequested($news));
            }
        }

        return redirect()->route('admin.news.index')
                        ->with('success', 'Tin tức đã được tạo thành công!');
    }

    /**
     * Display the specified news
     */
    public function show(News $news)
    {
        $news->load(['conference', 'createdBy', 'updatedBy']);
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news
     */
    public function edit(News $news)
    {
        $conferences = HoiThao::select('conference_id', 'title')
                              ->orderBy('title')
                              ->get();

        return view('admin.news.edit', compact('news', 'conferences'));
    }

    /**
     * Update the specified news in storage
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->news_id . ',news_id',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|in:NEWS,ANNOUNCEMENT,EVENT,GUIDE',
            'conference_id' => 'nullable|exists:hoithao,conference_id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachment_path' => 'nullable|file|mimes:pdf|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:DRAFT,PENDING,PUBLISHED,ARCHIVED',
            'published_at' => 'nullable|date',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($news->cover_image && Storage::disk('public')->exists($news->cover_image)) {
                Storage::disk('public')->delete($news->cover_image);
            }

            $file = $request->file('cover_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('news/covers', $filename, 'public');
            $validated['cover_image'] = $path;
        }

        // Handle attachment upload
        if ($request->hasFile('attachment_path')) {
            // Delete old attachment
            if ($news->attachment_path && Storage::disk('public')->exists($news->attachment_path)) {
                Storage::disk('public')->delete($news->attachment_path);
            }

            $file = $request->file('attachment_path');
            $filename = time() . '_' . Str::slug($request->title) . '_attachment.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('news/attachments', $filename, 'public');
            $validated['attachment_path'] = $path;
        }

        // Handle gallery images upload (Append to existing)
        if ($request->hasFile('images')) {
            $imagePaths = $news->images ?? [];
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . Str::slug($request->title) . '_gallery_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('news/gallery', $filename, 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        // Handle removing specific images
        if ($request->has('remove_images')) {
            $currentImages = $validated['images'] ?? ($news->images ?? []);
            $imagesToRemove = $request->remove_images;
            $remainingImages = [];

            foreach ($currentImages as $image) {
                if (in_array($image, $imagesToRemove)) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                } else {
                    $remainingImages[] = $image;
                }
            }
            $validated['images'] = $remainingImages;
        }

        $validated['updated_by'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        $news->update($validated);

        return redirect()->route('admin.news.index')
                        ->with('success', 'Tin tức đã được cập nhật thành công!');
    }

    /**
     * Remove the specified news from storage
     */
    public function destroy(News $news)
    {
        // Delete cover image if exists
        if ($news->cover_image && Storage::disk('public')->exists($news->cover_image)) {
            Storage::disk('public')->delete($news->cover_image);
        }

        // Delete attachment if exists
        if ($news->attachment_path && Storage::disk('public')->exists($news->attachment_path)) {
            Storage::disk('public')->delete($news->attachment_path);
        }

        // Delete gallery images if exist
        if (!empty($news->images)) {
            foreach ($news->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $news->delete();

        return redirect()->route('admin.news.index')
                        ->with('success', 'Tin tức đã được xóa thành công!');
    }

    /**
     * Approve news (change status from PENDING to PUBLISHED)
     */
    public function approve($id)
    {
        $news = News::where('news_id', $id)->firstOrFail();

        if ($news->status !== 'PENDING') {
            return redirect()->back()
                           ->with('error', 'Chỉ có thể duyệt tin tức đang ở trạng thái chờ duyệt.');
        }

        $news->update([
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'updated_by' => auth()->id()
        ]);

        // Notify the creator (Chair)
        if ($news->createdBy) {
            DB::table('notifications')->insert([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $news->createdBy->user_id,
                'user_id' => $news->createdBy->user_id,
                'type' => 'App\Notifications\NewsApproved',
                'title' => 'Tin tức đã được duyệt',
                'message' => sprintf(
                    'Bài viết "%s" đã được Admin phê duyệt và xuất bản.',
                    $news->title
                ),
                'data' => json_encode([
                    'url' => route('chair.news.show', $news->news_id),
                    'level' => 'success',
                    'type' => 'news_approved',
                    'approved_by' => auth()->user()->full_name ?? 'Admin',
                    'news_id' => $news->news_id
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Tin tức đã được duyệt và xuất bản thành công!');
    }

    /**
     * Reject news (change status from PENDING to DRAFT with reason)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $news = News::where('news_id', $id)->firstOrFail();

        if ($news->status !== 'PENDING') {
            return redirect()->back()
                           ->with('error', 'Chỉ có thể từ chối tin tức đang ở trạng thái chờ duyệt.');
        }

        $news->update([
            'status' => 'DRAFT',
            'updated_by' => auth()->id()
        ]);

        // Notify the creator (Chair) with rejection reason
        if ($news->createdBy) {
            DB::table('notifications')->insert([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $news->createdBy->user_id,
                'user_id' => $news->createdBy->user_id,
                'type' => 'App\Notifications\NewsRejected',
                'title' => 'Tin tức bị từ chối',
                'message' => sprintf(
                    'Bài viết "%s" đã bị từ chối. Lý do: %s',
                    $news->title,
                    $request->rejection_reason
                ),
                'data' => json_encode([
                    'url' => route('chair.news.edit', $news->news_id),
                    'level' => 'error',
                    'type' => 'news_rejected',
                    'rejected_by' => auth()->user()->full_name ?? 'Admin',
                    'rejection_reason' => $request->rejection_reason,
                    'news_id' => $news->news_id
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Tin tức đã được từ chối và trả về trạng thái nháp.');
    }
}
