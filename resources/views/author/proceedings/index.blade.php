@extends('layouts.author')

@section('title', 'Kỷ yếu hội thảo')

@push('styles')
<style>
    .card {
        @apply bg-white rounded-xl shadow-lg border border-gray-200;
    }
    .conference-card {
        @apply bg-white rounded-xl shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl hover:scale-105;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kỷ yếu hội thảo</h1>
            <p class="text-gray-600 mt-1">Xem kỷ yếu các hội thảo có bài báo đã xuất bản của bạn</p>
        </div>
        <div class="flex items-center space-x-2 text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
    </div>
</div>

@if($conferences->count() > 0)
<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Tổng hội thảo</p>
                <p class="text-2xl font-bold text-gray-900">{{ $conferences->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Bài báo đã xuất bản</p>
                <p class="text-2xl font-bold text-gray-900">{{ $conferences->sum('published_papers_count') }}</p>
            </div>
        </div>
    </div>
    
    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Năm gần nhất</p>
                <p class="text-2xl font-bold text-gray-900">{{ $conferences->max('year') ?? date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Conferences List -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($conferences as $conference)
    <div class="conference-card">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $conference->title }}</h3>
                    @if($conference->acronym)
                    <p class="text-sm text-blue-600 font-medium mb-2">{{ $conference->acronym }}</p>
                    @endif
                </div>
                <span class="badge bg-purple-100 text-purple-800">{{ $conference->published_papers_count }} bài báo</span>
            </div>
            
            @if($conference->description)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($conference->description, 120) }}</p>
            @endif
            
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h2a2 2 0 012 2v4m-6 4v10a1 1 0 001 1h6a1 1 0 001-1V11a1 1 0 00-1-1H9a1 1 0 00-1 1z"></path>
                    </svg>
                    {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}
                </div>
                
                @if($conference->location)
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $conference->location }}
                </div>
                @endif
            </div>
            
            <div class="pt-4 border-t border-gray-200">
                <a href="{{ route('author.proceedings.show', $conference->conference_id) }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Xem kỷ yếu
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@else
<!-- Empty State -->
<div class="text-center py-16">
    <div class="max-w-md mx-auto">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Chưa có kỷ yếu</h3>
        <p class="text-gray-600 mb-6">Bạn chưa có bài báo nào được xuất bản trong kỷ yếu hội thảo.</p>
        <a href="{{ route('author.papers.create') }}" 
           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nộp bài báo mới
        </a>
    </div>
</div>
@endif
@endsection