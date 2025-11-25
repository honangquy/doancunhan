@extends('layouts.chair')

@section('title', 'Chọn Hội thảo - Xuất bản Kỷ yếu')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Xuất bản Kỷ yếu</h1>
        <p class="mt-2 text-gray-600">Chọn hội thảo để quản lý việc xuất bản kỷ yếu</p>
    </div>

    @if($conferences->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($conferences as $conference)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $conference->title }}
                                </h3>
                                
                                @if($conference->acronym)
                                    <p class="text-sm text-gray-500 mb-2">{{ $conference->acronym }}</p>
                                @endif
                                
                                <div class="space-y-1 text-sm text-gray-600 mb-4">
                                    @if($conference->start_date)
                                        <p><span class="font-medium">Ngày:</span> {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}</p>
                                    @endif
                                    @if($conference->location)
                                        <p><span class="font-medium">Địa điểm:</span> {{ $conference->location }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            @if($conference->status === 'ACTIVE')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $conference->status }}
                                </span>
                            @endif
                        </div>

                        <!-- Statistics -->
                        <div class="grid grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-600">{{ $conference->total_papers ?? 0 }}</div>
                                <div class="text-xs text-gray-500">Tổng bài báo</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600">{{ $conference->accepted_papers ?? 0 }}</div>
                                <div class="text-xs text-gray-500">Đã chấp nhận</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600">{{ $conference->published_papers ?? 0 }}</div>
                                <div class="text-xs text-gray-500">Đã xuất bản</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @if($conference->accepted_papers > 0)
                                <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
                                   class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Quản lý
                                </a>
                            @else
                                <div class="flex-1 bg-gray-300 text-gray-500 text-center px-4 py-2 rounded-lg text-sm cursor-not-allowed">
                                    Chưa có bài báo chấp nhận
                                </div>
                            @endif
                            
                            @if($conference->published_papers > 0)
                                <a href="{{ route('chair.conferences.proceedings', $conference->conference_id) }}" 
                                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 text-white">
            <h3 class="text-lg font-semibold mb-4">Tổng quan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $conferences->count() }}</div>
                    <div class="text-blue-100">Hội thảo quản lý</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $conferences->sum('total_papers') }}</div>
                    <div class="text-blue-100">Tổng bài báo</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $conferences->sum('accepted_papers') }}</div>
                    <div class="text-blue-100">Đã chấp nhận</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $conferences->sum('published_papers') }}</div>
                    <div class="text-blue-100">Đã xuất bản</div>
                </div>
            </div>
        </div>
    @else
        <!-- No Conferences -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có hội thảo nào</h3>
            <p class="mt-2 text-gray-500">Bạn chưa là chair của hội thảo nào hoặc chưa có hội thảo nào được tạo.</p>
            
            <div class="mt-6">
                <a href="{{ route('chair.conferences.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Quản lý hội thảo
                </a>
            </div>
        </div>
    @endif
</div>
@endsection