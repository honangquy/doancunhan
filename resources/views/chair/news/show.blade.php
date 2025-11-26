@extends('layouts.chair')

@section('title', 'Chi tiết tin tức')

@section('content')
<div class="p-6" x-data="{ activeTab: 'content' }">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('chair.dashboard') }}" class="hover:text-orange-600">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('chair.news.index') }}" class="hover:text-orange-600">Tin tức</a>
                    <span>/</span>
                    <span class="text-gray-900">Chi tiết</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $news->title }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('chair.news.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
                <a href="{{ route('chair.news.edit', $news->news_id) }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition-colors flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Chỉnh sửa
                </a>
                <form action="{{ route('chair.news.destroy', $news->news_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tin tức này?')" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 rounded-lg hover:bg-red-200 font-medium transition-colors flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Column -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50/50">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'content'"
                                :class="{ 'border-orange-500 text-orange-600 bg-white': activeTab === 'content', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'content' }"
                                class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Nội dung bài viết
                        </button>
                        <button @click="activeTab = 'info'"
                                :class="{ 'border-orange-500 text-orange-600 bg-white': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'info' }"
                                class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Thông tin chi tiết
                        </button>
                    </nav>
                </div>

                <!-- Tab Content: Content -->
                <div x-show="activeTab === 'content'" class="p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <!-- Cover Image -->
                    @if($news->cover_image)
                        <div class="mb-6 rounded-lg overflow-hidden border border-gray-100 shadow-sm">
                            <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="w-full h-auto object-cover max-h-[400px]">
                        </div>
                    @endif

                    <!-- Summary -->
                    @if($news->summary)
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6 rounded-r-lg">
                            <h3 class="text-orange-800 font-semibold text-sm mb-1 uppercase tracking-wide">Tóm tắt</h3>
                            <p class="text-gray-800 italic text-lg leading-relaxed">{{ $news->summary }}</p>
                        </div>
                    @endif

                    <!-- Body -->
                    <div class="prose prose-lg max-w-none text-gray-700">
                        {!! nl2br(e($news->content)) !!}
                    </div>
                </div>

                <!-- Tab Content: Info -->
                <div x-show="activeTab === 'info'" class="p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Slug (URL)</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-200 font-mono text-sm text-gray-700 break-all">
                                    {{ $news->slug }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Người tạo</label>
                                <div class="mt-1 flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xs">
                                        {{ substr($news->createdBy->fullname ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="text-gray-900 font-medium">{{ $news->createdBy->fullname ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Ngày tạo</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                    {{ $news->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Cập nhật lần cuối</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                    {{ $news->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($news->conference)
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Thông tin hội thảo
                            </h3>
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                <p class="font-medium text-blue-900 text-lg">{{ $news->conference->title }}</p>
                                <p class="text-blue-700 text-sm mt-1">ID: {{ $news->conference->conference_id }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Trạng thái
                </h3>

                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500 block mb-1">Danh mục</span>
                        @php
                            $categoryColors = [
                                'NEWS' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'ANNOUNCEMENT' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'EVENT' => 'bg-green-100 text-green-800 border-green-200',
                                'GUIDE' => 'bg-orange-100 text-orange-800 border-orange-200'
                            ];
                        @endphp
                        <span class="px-3 py-1.5 inline-flex text-sm font-semibold rounded-lg border {{ $categoryColors[$news->category] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            {{ $news->category_name }}
                        </span>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500 block mb-1">Tình trạng</span>
                        @php
                            $statusColors = [
                                'DRAFT' => 'bg-gray-100 text-gray-800 border-gray-200',
                                'PENDING' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'PUBLISHED' => 'bg-green-100 text-green-800 border-green-200',
                                'ARCHIVED' => 'bg-red-100 text-red-800 border-red-200'
                            ];
                        @endphp
                        <span class="px-3 py-1.5 inline-flex text-sm font-semibold rounded-lg border {{ $statusColors[$news->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            {{ $news->status_name }}
                        </span>
                    </div>

                    @if($news->published_at)
                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500 block mb-1">Ngày xuất bản</span>
                            <div class="flex items-center gap-2 text-gray-900 font-medium">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $news->published_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @endif

                    @if($news->is_featured)
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2 p-3 bg-yellow-50 rounded-lg border border-yellow-100 text-yellow-800">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="font-medium">Tin nổi bật</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
