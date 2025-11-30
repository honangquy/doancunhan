@extends('layouts.author')

@section('title', $title)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $conference->title }}</h1>
                <p class="text-gray-600 mt-2">Kỷ yếu hội thảo</p>
            </div>
            <a href="{{ route('author.proceedings.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Conference Info Card -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hội thảo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if($conference->acronym)
                <div>
                    <span class="font-medium text-gray-700">Tên viết tắt:</span>
                    <span class="text-gray-600">{{ $conference->acronym }}</span>
                </div>
                @endif
                
                @if($conference->start_date)
                <div>
                    <span class="font-medium text-gray-700">Ngày tổ chức:</span>
                    <span class="text-gray-600">
                        {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                        @if($conference->end_date)
                            - {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}
                        @endif
                    </span>
                </div>
                @endif
                
                @if($conference->location)
                <div class="md:col-span-2">
                    <span class="font-medium text-gray-700">Địa điểm:</span>
                    <span class="text-gray-600">{{ $conference->location }}</span>
                </div>
                @endif

                @if($conference->year)
                <div>
                    <span class="font-medium text-gray-700">Năm:</span>
                    <span class="text-gray-600">{{ $conference->year }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Proceedings Section -->
    @if($hasProceedings)
        <!-- Kỷ yếu đã được xuất bản -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md border border-blue-200 p-8">
            <div class="flex items-start space-x-6">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        Kỷ yếu đã được xuất bản
                    </h2>
                    
                    @if($conference->proceedings_published_at)
                        <p class="text-gray-700 mb-6">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Ngày xuất bản: <strong>{{ \Carbon\Carbon::parse($conference->proceedings_published_at)->format('d/m/Y H:i') }}</strong>
                        </p>
                    @endif

                    <p class="text-gray-700 mb-6">
                        Kỷ yếu hội thảo đã sẵn sàng để tải xuống. File bao gồm toàn bộ các bài báo đã được chấp nhận và xuất bản tại hội thảo này.
                    </p>

                    <!-- Download Button -->
                    <a href="{{ Storage::url($conference->proceedings_file) }}" 
                       target="_blank"
                       download
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl font-medium text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tải kỷ yếu PDF
                    </a>

                    <p class="text-sm text-gray-600 mt-4">
                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        File sẽ được mở trong tab mới. Bạn có thể lưu file về máy để xem offline.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Kỷ yếu chưa được xuất bản -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
            <svg class="w-16 h-16 text-yellow-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">
                Kỷ yếu chưa được xuất bản
            </h2>
            <p class="text-gray-700 mb-6 max-w-2xl mx-auto">
                Kỷ yếu hội thảo hiện chưa có sẵn. Chair hội thảo sẽ xuất bản kỷ yếu sau khi tổng hợp toàn bộ các bài báo đã được chấp nhận.
            </p>
            <div class="bg-white border border-yellow-300 rounded-lg p-4 max-w-md mx-auto">
                <p class="text-sm text-gray-600">
                    <strong>Lưu ý:</strong> Bạn sẽ nhận được thông báo qua email khi kỷ yếu được xuất bản. Vui lòng quay lại trang này sau.
                </p>
            </div>
        </div>
    @endif

    <!-- Additional Info -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-gray-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-gray-700">
                <p class="font-medium mb-2">Thông tin về kỷ yếu:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-600">
                    <li>Kỷ yếu được ghép từ tất cả bài báo đã được chấp nhận tại hội thảo</li>
                    <li>File PDF có thể có dung lượng lớn (thường từ 5-50MB)</li>
                    <li>Nội dung kỷ yếu bao gồm: bìa, mục lục, và toàn bộ bài báo</li>
                    <li>Bạn có thể trích dẫn kỷ yếu trong các công trình nghiên cứu của mình</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
