<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of news
     */
    public function index(Request $request)
    {
        $query = News::with(['conference', 'creator'])
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

        // Set created_by
        $validated['created_by'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $news = News::create($validated);

        return redirect()->route('admin.news.index')
                        ->with('success', 'Tin tức đã được tạo thành công!');
    }

    /**
     * Display the specified news
     */
    public function show(News $news)
    {
        $news->load(['conference', 'creator', 'updater']);
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

        $news->delete();

        return redirect()->route('admin.news.index')
                        ->with('success', 'Tin tức đã được xóa thành công!');
    }
}
