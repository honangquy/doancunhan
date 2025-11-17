<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\HoiThao;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of news for public view
     */
    public function index(Request $request)
    {
        $query = News::with(['conference', 'creator'])
                     ->published()
                     ->orderBy('published_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Filter by conference
        if ($request->filled('conference_id')) {
            $query->conference($request->conference_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('summary', 'LIKE', "%{$search}%");
            });
        }

        $news = $query->paginate(12);
        
        // Get featured news for sidebar
        $featuredNews = News::published()
                            ->featured()
                            ->orderBy('published_at', 'desc')
                            ->limit(5)
                            ->get();
        
        // Get conferences for filter
        $conferences = HoiThao::select('conference_id', 'title')
                              ->orderBy('title')
                              ->get();

        return view('news.index', compact('news', 'featuredNews', 'conferences'));
    }

    /**
     * Display the specified news by slug
     */
    public function show($slug)
    {
        $news = News::with(['conference', 'creator'])
                    ->where('slug', $slug)
                    ->published()
                    ->firstOrFail();
        
        // Get related news (same category or conference)
        $relatedNews = News::published()
                           ->where('news_id', '!=', $news->news_id)
                           ->where(function($q) use ($news) {
                               $q->where('category', $news->category)
                                 ->orWhere('conference_id', $news->conference_id);
                           })
                           ->orderBy('published_at', 'desc')
                           ->limit(6)
                           ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }
}
