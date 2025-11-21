<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    /**
     * Display a listing of published news
     */
    public function index(Request $request)
    {
        $query = News::with(['conference', 'createdBy'])
            ->published()
            ->where('type', 'news');

        $newsList = $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('public.news.index', compact('newsList'));
    }

    /**
     * Display a listing of published events
     */
    public function events(Request $request)
    {
        $query = News::with(['conference', 'createdBy'])
            ->published()
            ->where('type', 'event');

        $eventsList = $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('public.news.events', compact('eventsList'));
    }

    /**
     * Display the specified news/event detail
     */
    public function show($slug)
    {
        $news = News::with(['conference', 'createdBy'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Get related news/events (same type, same conference if exists)
        $related = News::published()
            ->where('type', $news->type)
            ->where('id', '!=', $news->id);

        if ($news->conference_id) {
            $related->where('conference_id', $news->conference_id);
        }

        $relatedNews = $related->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.news.show', compact('news', 'relatedNews'));
    }
}
