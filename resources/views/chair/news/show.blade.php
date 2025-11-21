@extends('layouts.chair')

@section('title', 'Chi tiết tin tức')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chi tiết tin tức</h1>
            <p class="text-gray-600 mt-1">Xem thông tin chi tiết</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('chair.news.edit', $news->news_id) }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('chair.news.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Cover Image -->
            @if($news->cover_image)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="w-full h-auto">
                </div>
            @endif

            <!-- Title & Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $news->title }}</h2>
                
                @if($news->summary)
                    <div class="bg-gray-50 border-l-4 border-orange-500 p-4 mb-6">
                        <p class="text-gray-700 italic">{{ $news->summary }}</p>
                    </div>
                @endif

                <div class="prose max-w-none">
                    {!! nl2br(e($news->content)) !!}
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin bổ sung</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Slug:</span>
                        <p class="font-medium text-gray-900 mt-1">{{ $news->slug }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Tạo bởi:</span>
                        <p class="font-medium text-gray-900 mt-1">{{ $news->createdBy->fullname ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Ngày tạo:</span>
                        <p class="font-medium text-gray-900 mt-1">{{ $news->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Cập nhật cuối:</span>
                        <p class="font-medium text-gray-900 mt-1">{{ $news->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Trạng thái</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-600">Danh mục:</span>
                        <div class="mt-1">
                            @php
                                $categoryColors = [
                                    'NEWS' => 'bg-blue-100 text-blue-800',
                                    'ANNOUNCEMENT' => 'bg-purple-100 text-purple-800',
                                    'EVENT' => 'bg-green-100 text-green-800',
                                    'GUIDE' => 'bg-orange-100 text-orange-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded {{ $categoryColors[$news->category] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $news->category_name }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-600">Trạng thái:</span>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'DRAFT' => 'bg-gray-100 text-gray-800',
                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                    'PUBLISHED' => 'bg-green-100 text-green-800',
                                    'ARCHIVED' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded {{ $statusColors[$news->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $news->status_name }}
                            </span>
                        </div>
                    </div>

                    @if($news->published_at)
                        <div>
                            <span class="text-sm text-gray-600">Ngày xuất bản:</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $news->published_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    @if($news->is_featured)
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Tin nổi bật
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conference Info -->
            @if($news->conference)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hội thảo</h3>
                    <p class="text-sm text-gray-700">{{ $news->conference->title }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hành động</h3>
                <div class="space-y-2">
                    <a href="{{ route('chair.news.edit', $news->news_id) }}" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Chỉnh sửa
                    </a>
                    <form action="{{ route('chair.news.destroy', $news->news_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tin tức này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Xóa tin tức
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
