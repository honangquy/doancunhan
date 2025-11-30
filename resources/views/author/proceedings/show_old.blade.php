@extends('layouts.author')

@section('title', 'Kỷ yếu - ' . $conference->title)

@push('styles')
<style>
    .card {
        @apply bg-white rounded-xl shadow-lg border border-gray-200;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .paper-row {
        @apply border-b border-gray-100 hover:bg-gray-50 transition;
    }
</style>
@endpush

@section('content')
<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('author.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="text-gray-400">›</span>
        <a href="{{ route('author.proceedings.index') }}" class="text-blue-600 hover:text-blue-800">Kỷ yếu</a>
        <span class="text-gray-400">›</span>
        <span class="text-gray-600">{{ $conference->acronym ?? $conference->title }}</span>
    </nav>
</div>

<!-- Conference Header -->
<div class="card p-8 mb-8">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $conference->title }}</h1>
        @if($conference->acronym)
        <p class="text-xl text-blue-600 font-semibold mb-4">{{ $conference->acronym }}</p>
        @endif

        <div class="flex items-center justify-center space-x-8 text-gray-600 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h2a2 2 0 012 2v4m-6 4v10a1 1 0 001 1h6a1 1 0 001-1V11a1 1 0 00-1-1H9a1 1 0 00-1 1z"></path>
                </svg>
                {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}
            </div>

            @if($conference->location)
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $conference->location }}
            </div>
            @endif
        </div>

        @if($conference->description)
        <p class="text-gray-600 max-w-4xl mx-auto">{{ $conference->description }}</p>
        @endif
    </div>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Tổng bài báo xuất bản</p>
                <p class="text-2xl font-bold text-gray-900">{{ $publishedPapers->count() }}</p>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Bài báo của bạn</p>
                <p class="text-2xl font-bold text-gray-900">{{ $myPublishedPapersCount }}</p>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Tổng trang</p>
                <p class="text-2xl font-bold text-gray-900">{{ $publishedPapers->max('page_end') ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Published Papers -->
<div class="card">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Danh sách bài báo xuất bản</h2>
        <p class="text-gray-600 text-sm mt-1">Kỷ yếu {{ $conference->title }}</p>
    </div>

    @if($publishedPapers->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">STT</th>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Tiêu đề</th>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Tác giả</th>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Tiểu ban</th>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700 text-sm">Trang</th>
                    <th class="text-right py-3 px-6 font-semibold text-gray-700 text-sm">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($publishedPapers as $index => $paper)
                <tr class="paper-row">
                    <td class="py-4 px-6">
                        <span class="font-mono text-sm text-gray-600">{{ $index + 1 }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-900 hover:text-blue-600">
                            {{ $paper->title }}
                        </div>
                        @if($paper->keywords)
                        <div class="text-xs text-gray-500 mt-1">
                            <strong>Từ khóa:</strong> {{ Str::limit($paper->keywords, 60) }}
                        </div>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $paper->author_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $paper->author_email }}</div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @if($paper->track_name)
                            <span class="badge bg-blue-100 text-blue-800 text-xs">{{ $paper->track_name }}</span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        @if(isset($paper->page_start) && isset($paper->page_end) && $paper->page_start && $paper->page_end)
                            <span class="text-sm text-gray-600 font-mono">{{ $paper->page_start }}-{{ $paper->page_end }}</span>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-end space-x-2">
                            @if($paper->file_path)
                                <a href="{{ route('author.proceedings.download', [$conference->conference_id, $paper->paper_id]) }}"
                                   class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition"
                                   title="Tải xuống PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="p-2 text-gray-400 cursor-not-allowed" title="Không có file">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có bài báo xuất bản</h3>
        <p class="text-gray-600">Hội thảo này chưa có bài báo nào được xuất bản trong kỷ yếu.</p>
    </div>
    @endif
</div>
@endsection
