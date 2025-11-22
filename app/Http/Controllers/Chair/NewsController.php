<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\HoiThao;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use App\Notifications\NewsApprovalRequested;
use App\Traits\ChairRoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    use ChairRoleHelper;

    /**
     * Display a listing of news for this chair's conference
     */
    public function index(Request $request)
    {
        $conferenceId = $this->getChairConferenceId();

        $query = News::with(['conference', 'createdBy'])
                     ->where('conference_id', $conferenceId)
                     ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
        $conference = HoiThao::find($conferenceId);

        return view('chair.news.index', compact('news', 'conference'));
    }

    /**
     * Show the form for creating a new news
     */
    public function create()
    {
        $conferenceId = $this->getChairConferenceId();
        $conference = HoiThao::find($conferenceId);

        return view('chair.news.create', compact('conference', 'conferenceId'));
    }

    /**
     * Store a newly created news in storage
     */
    public function store(Request $request)
    {
        $conferenceId = $this->getChairConferenceId();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|in:NEWS,ANNOUNCEMENT,EVENT,GUIDE',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:DRAFT,PENDING',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('news/covers', $filename, 'public');
            $validated['cover_image'] = $path;
        }

        // Auto-assign conference_id and created_by
        $validated['conference_id'] = $conferenceId;
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
                foreach ($admins as $admin) {
                    DB::table('notifications')->insert([
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $admin->user_id,
                        'user_id' => $admin->user_id,
                        'type' => 'App\Notifications\NewsApprovalRequested',
                        'title' => 'Yêu cầu duyệt tin tức mới',
                        'message' => sprintf(
                            'Bài: "%s" đang chờ phê duyệt. Người tạo: %s',
                            $news->title,
                            auth()->user()->full_name ?? 'N/A'
                        ),
                        'data' => json_encode([
                            'url' => route('admin.news.show', $news->news_id),
                            'level' => 'warning',
                            'type' => 'news_approval',
                            'created_by' => auth()->user()->full_name ?? 'Hệ thống',
                            'news_id' => $news->news_id
                        ]),
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return redirect()->route('chair.news.index')
                        ->with('success', 'Tin tức đã được tạo thành công!');
    }

    /**
     * Display the specified news
     */
    public function show($id)
    {
        $conferenceId = $this->getChairConferenceId();

        $news = News::where('news_id', $id)
                    ->where('conference_id', $conferenceId)
                    ->firstOrFail();

        $news->load(['conference', 'createdBy', 'updatedBy']);

        return view('chair.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news
     */
    public function edit($id)
    {
        $conferenceId = $this->getChairConferenceId();

        $news = News::where('news_id', $id)
                    ->where('conference_id', $conferenceId)
                    ->firstOrFail();

        $conference = HoiThao::find($conferenceId);

        return view('chair.news.edit', compact('news', 'conference', 'conferenceId'));
    }

    /**
     * Update the specified news in storage
     */
    public function update(Request $request, $id)
    {
        $conferenceId = $this->getChairConferenceId();

        $news = News::where('news_id', $id)
                    ->where('conference_id', $conferenceId)
                    ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->news_id . ',news_id',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|in:NEWS,ANNOUNCEMENT,EVENT,GUIDE',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:DRAFT,PENDING',
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

        $validated['updated_by'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Check if status changed to PENDING
        $oldStatus = $news->status;
        $news->update($validated);

        // Send notification to admins if status changed to PENDING
        if ($news->status === 'PENDING' && $oldStatus !== 'PENDING') {
            $admins = User::whereHas('roles', function($q) {
                $q->where('role_code', 'ADMIN');
            })->get();

            if ($admins->count() > 0) {
                foreach ($admins as $admin) {
                    DB::table('notifications')->insert([
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $admin->user_id,
                        'user_id' => $admin->user_id,
                        'type' => 'App\Notifications\NewsApprovalRequested',
                        'title' => 'Yêu cầu duyệt tin tức mới',
                        'message' => sprintf(
                            'Bài: "%s" đang chờ phê duyệt. Người tạo: %s',
                            $news->title,
                            auth()->user()->full_name ?? 'N/A'
                        ),
                        'data' => json_encode([
                            'url' => route('admin.news.show', $news->news_id),
                            'level' => 'warning',
                            'type' => 'news_approval',
                            'created_by' => auth()->user()->full_name ?? 'Hệ thống',
                            'news_id' => $news->news_id
                        ]),
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return redirect()->route('chair.news.index')
                        ->with('success', 'Tin tức đã được cập nhật thành công!');
    }

    /**
     * Remove the specified news from storage
     */
    public function destroy($id)
    {
        $conferenceId = $this->getChairConferenceId();

        $news = News::where('news_id', $id)
                    ->where('conference_id', $conferenceId)
                    ->firstOrFail();

        // Delete cover image if exists
        if ($news->cover_image && Storage::disk('public')->exists($news->cover_image)) {
            Storage::disk('public')->delete($news->cover_image);
        }

        $news->delete();

        return redirect()->route('chair.news.index')
                        ->with('success', 'Tin tức đã được xóa thành công!');
    }
}
