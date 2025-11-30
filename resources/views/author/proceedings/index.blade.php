@extends('layouts.author')

@section('title', 'Kỷ yếu hội thảo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Kỷ yếu hội thảo</h1>
        <p class="text-gray-600 mt-2">Chọn hội thảo để xem kỷ yếu đã xuất bản</p>
    </div>

    @if($conferences->count() > 0)
        <!-- Conference List -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Danh sách hội thảo bạn tham gia</h2>
                
                <div class="space-y-4">
                    @foreach($conferences as $conference)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $conference->title }}
                                    </h3>
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-3">
                                        @if($conference->acronym)
                                            <div>
                                                <span class="font-medium">Mã:</span> {{ $conference->acronym }}
                                            </div>
                                        @endif
                                        @if($conference->start_date)
                                            <div>
                                                <span class="font-medium">Ngày:</span> {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($conference->location)
                                            <div>
                                                <span class="font-medium">Địa điểm:</span> {{ $conference->location }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-medium">Số bài báo của bạn:</span> {{ $conference->my_papers_count }}
                                        </div>
                                    </div>

                                    <!-- Proceedings Status -->
                                    <div class="flex items-center space-x-2">
                                        @if($conference->has_proceedings)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Đã có kỷ yếu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Chưa có kỷ yếu
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="ml-4">
                                    <a href="{{ route('author.proceedings.show', $conference->conference_id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Xem kỷ yếu
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Lưu ý:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Chỉ các hội thảo mà bạn có vai trò tác giả (Author) mới hiển thị ở đây</li>
                        <li>Kỷ yếu sẽ được Chair xuất bản sau khi hội thảo kết thúc</li>
                        <li>Bạn có thể tải xuống file PDF kỷ yếu khi đã được xuất bản</li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có hội thảo nào</h3>
            <p class="text-gray-600 mb-6">Bạn chưa tham gia hội thảo nào với vai trò tác giả.</p>
            <a href="{{ route('author.papers.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nộp bài báo mới
            </a>
        </div>
    @endif
</div>
@endsection
