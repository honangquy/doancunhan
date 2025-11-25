@extends('layouts.chair')

@section('title', 'Kỷ yếu - ' . $conference->title)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kỷ yếu Hội thảo</h1>
                <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
                <p class="text-sm text-gray-500">
                    Ngày tổ chức: {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                </p>
            </div>
            
            <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002 2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Quản lý kỷ yếu
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold">{{ count($publishedPapers) }}</div>
                <div class="text-blue-100">Bài báo đã xuất bản</div>
            </div>
            <div class="text-center">
                @php
                    $totalPages = $publishedPapers->filter(function($paper) {
                        return $paper->page_start && $paper->page_end;
                    })->sum(function($paper) {
                        return $paper->page_end - $paper->page_start + 1;
                    });
                @endphp
                <div class="text-3xl font-bold">{{ $totalPages }}</div>
                <div class="text-blue-100">Tổng số trang</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold">{{ $publishedPapers->flatMap->authors->unique('full_name')->count() }}</div>
                <div class="text-blue-100">Tác giả tham gia</div>
            </div>
        </div>
    </div>

    <!-- Conference Info -->
    @if($conference->description || $conference->location)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hội thảo</h3>
            
            @if($conference->description)
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700">Mô tả:</h4>
                    <p class="text-gray-600 mt-1">{{ $conference->description }}</p>
                </div>
            @endif
            
            @if($conference->location)
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700">Địa điểm:</h4>
                    <p class="text-gray-600 mt-1">{{ $conference->location }}</p>
                </div>
            @endif
            
            @if($conference->contact_email)
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700">Liên hệ:</h4>
                    <p class="text-gray-600 mt-1">{{ $conference->contact_email }}</p>
                </div>
            @endif
        </div>
    @endif

    @if($publishedPapers->count() > 0)
        <!-- Table of Contents -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Mục lục
            </h3>
            
            <div class="space-y-4">
                @foreach($publishedPapers as $index => $paper)
                    <div class="flex items-start justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-start">
                                <span class="text-sm font-medium text-gray-500 mr-4 mt-1">{{ $index + 1 }}.</span>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 mb-2">{{ $paper->title }}</h4>
                                    
                                    <div class="text-sm text-gray-600 mb-2">
                                        <span class="font-medium">Tác giả:</span>
                                        {{ $paper->authors->pluck('full_name')->join(', ') }}
                                    </div>
                                    
                                    @if($paper->abstract)
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">Tóm tắt:</span>
                                            {{ Str::limit($paper->abstract, 200) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="ml-6 text-right">
                            @if($paper->page_start && $paper->page_end)
                                <div class="text-sm font-medium text-gray-900">
                                    Trang {{ $paper->page_start }}-{{ $paper->page_end }}
                                </div>
                            @endif
                            
                            @if($paper->latest_version_path)
                                <a href="{{ route('chair.proceedings.download', [$conference->conference_id, $paper->paper_id]) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Tải PDF
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Download All -->
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tải xuống toàn bộ kỷ yếu</h3>
                <p class="text-gray-600 mb-6">
                    Tải xuống tất cả {{ count($publishedPapers) }} bài báo đã được xuất bản
                </p>
                
                <div class="flex justify-center space-x-4">
                    <button class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium flex items-center"
                            onclick="downloadAllPapers()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 7h16"></path>
                        </svg>
                        Tải tất cả (ZIP)
                    </button>
                    
                    <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium flex items-center"
                            onclick="generateCombinedPDF()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Kết hợp PDF
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- No Papers Published -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có bài báo nào được xuất bản</h3>
            <p class="mt-2 text-gray-500">Kỷ yếu sẽ được hiển thị khi có bài báo được xuất bản.</p>
            
            <div class="mt-6">
                <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Bắt đầu xuất bản kỷ yếu
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function downloadAllPapers() {
    // Implement ZIP download functionality
    alert('Tính năng tải xuống ZIP đang được phát triển');
}

function generateCombinedPDF() {
    // Implement combined PDF generation
    alert('Tính năng kết hợp PDF đang được phát triển');
}
</script>
@endsection