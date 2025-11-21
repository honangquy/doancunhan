@extends('layouts.admin')

@section('title', 'Chi tiết tin tức')

@section('content')
<!-- Hero Header -->
<div class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Chi tiết tin tức
                    </h1>
                    <p class="text-white text-opacity-90 text-sm">Xem thông tin chi tiết bài viết</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.news.index') }}"
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-5 py-2.5 rounded-lg font-semibold backdrop-blur-sm transition-all duration-200 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Quay lại
                    </a>
                    <a href="{{ route('admin.news.edit', $news->news_id) }}"
                       class="bg-white hover:bg-gray-100 text-indigo-600 px-5 py-2.5 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content Column (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Article Content Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                @if($news->cover_image)
                    <div class="relative h-96 overflow-hidden">
                        <img src="{{ asset('storage/' . $news->cover_image) }}"
                             alt="{{ $news->title }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                @endif

                <div class="p-8">
                    <!-- Title -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $news->title }}</h2>

                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-6">
                        @php
                            $statusColors = [
                                'DRAFT' => 'bg-gray-100 text-gray-800',
                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                'PUBLISHED' => 'bg-green-100 text-green-800',
                                'ARCHIVED' => 'bg-red-100 text-red-800'
                            ];
                            $categoryColors = [
                                'NEWS' => 'bg-blue-100 text-blue-800',
                                'ANNOUNCEMENT' => 'bg-purple-100 text-purple-800',
                                'EVENT' => 'bg-green-100 text-green-800',
                                'GUIDE' => 'bg-orange-100 text-orange-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$news->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $news->status_name }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $categoryColors[$news->category] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $news->category_name }}
                        </span>
                        @if($news->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Nổi bật
                            </span>
                        @endif
                    </div>

                    <!-- Summary -->
                    @if($news->summary)
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-1">Tóm tắt</h4>
                                    <p class="text-blue-800">{{ $news->summary }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-lg max-w-none">
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $news->content }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (1/3) -->
        <div class="space-y-6">
            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Thông tin
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Slug (URL)</dt>
                            <dd class="text-sm">
                                <code class="px-2 py-1 bg-gray-100 text-gray-800 rounded font-mono text-xs">{{ $news->slug }}</code>
                            </dd>
                        </div>

                        @if($news->conference)
                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Hội thảo</dt>
                            <dd class="text-sm text-gray-900 font-medium">{{ $news->conference->title }}</dd>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Người tạo</dt>
                            <dd class="text-sm text-gray-900">{{ $news->createdBy->full_name ?? 'N/A' }}</dd>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Ngày tạo</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $news->created_at->format('d/m/Y H:i') }}
                                </div>
                            </dd>
                        </div>

                        @if($news->updated_by)
                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Người cập nhật</dt>
                            <dd class="text-sm text-gray-900">{{ $news->updatedBy->full_name ?? 'N/A' }}</dd>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Cập nhật lần cuối</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $news->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </dd>
                        </div>

                        @if($news->published_at)
                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Ngày xuất bản</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $news->published_at->format('d/m/Y H:i') }}
                                </div>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Public View Card -->
            @if($news->status == 'PUBLISHED')
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg border border-green-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-green-900">Đã xuất bản</h4>
                            <p class="text-xs text-green-700">Bài viết đang hiển thị công khai</p>
                        </div>
                    </div>
                    <a href="{{ route('articles.show', $news->slug) }}"
                       target="_blank"
                       class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Xem trang công khai
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
