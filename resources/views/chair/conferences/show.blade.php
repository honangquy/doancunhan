@extends('layouts.chair')

@section('title', 'Chi tiết Hội thảo')

@section('page-title', 'Chi tiết Hội thảo')

@section('page-subtitle', 'Thông tin và quản lý hội thảo')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('chair.conferences.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    @if(isset($conference))
        <!-- Conference Header -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($conference->title) ? $conference->title : 'N/A' }}</h1>
                        <div class="mt-2 flex items-center space-x-4">
                            <!-- Status Badge -->
                            @if($conference->status === 'PENDING_ADMIN_APPROVAL')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Chờ admin duyệt
                                </span>
                            @elseif($conference->status === 'ACTIVE')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Đang hoạt động
                                </span>
                            @elseif($conference->status === 'REJECTED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Bị từ chối
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ $conference->status }}
                                </span>
                            @endif
                            
                            <span class="text-sm text-gray-500">
                                ID: {{ isset($conference->conference_id) ? $conference->conference_id : 'N/A' }}</span>
                            </span>
                        </div>
                    </div>
                    
                    @if($conference->status === 'ACTIVE')
                        <div class="flex space-x-3">
                            <a href="{{ route('chair.proceedings.index', isset($conference->conference_id) ? $conference->conference_id : '#') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                                </svg>
                                Quản lý kỷ yếu
                            </a>
                            <a href="{{ route('chair.conferences.edit', isset($conference->conference_id) ? $conference->conference_id : '#') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Chỉnh sửa
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conference Details -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ngày tổ chức</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(isset($conference->start_date) && $conference->start_date)
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                                @if(isset($conference->end_date) && $conference->end_date && $conference->end_date !== $conference->start_date)
                                    - {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}
                                @endif
                            @else
                                Chưa xác định
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Địa điểm</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ isset($conference->location) ? $conference->location : 'Chưa xác định' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Số lượng tham gia tối đa</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ isset($conference->max_participants) ? $conference->max_participants : 'Không giới hạn' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reviewer mỗi bài báo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ isset($conference->reviewers_per_paper) ? $conference->reviewers_per_paper : 3 }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hạn nộp bài</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ isset($conference->deadline_submission) && $conference->deadline_submission ? \Carbon\Carbon::parse($conference->deadline_submission)->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hạn phản biện</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ isset($conference->deadline_review) && $conference->deadline_review ? \Carbon\Carbon::parse($conference->deadline_review)->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                </div>

                @if(isset($conference->description) && $conference->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $conference->description }}</dd>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Thông tin chi tiết</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="divide-y divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Tên viết tắt</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->acronym ?: 'Chưa có' }}
                        </dd>
                    </div>
                    
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Năm tổ chức</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->year ?: 'Chưa xác định' }}
                        </dd>
                    </div>
                    
                    @if($conference->keywords)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Từ khóa</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->keywords }}
                        </dd>
                    </div>
                    @endif
                    
                    @if($conference->detailed_description)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Mô tả chi tiết</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->detailed_description }}
                        </dd>
                    </div>
                    @endif
                    
                    @if($conference->submission_guidelines)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Hướng dẫn nộp bài</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->submission_guidelines }}
                        </dd>
                    </div>
                    @endif
                    
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Hạn camera ready</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ isset($conference->deadline_camera_ready) && $conference->deadline_camera_ready ? \Carbon\Carbon::parse($conference->deadline_camera_ready)->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Hạn công bố kết quả</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ isset($conference->result_announcement_deadline) && $conference->result_announcement_deadline ? \Carbon\Carbon::parse($conference->result_announcement_deadline)->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    
                    @if($conference->cfp_file_path)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Call for Papers (PDF)</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <a href="{{ asset('storage/' . $conference->cfp_file_path) }}" target="_blank" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Tải file CFP
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Thông tin liên hệ</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="divide-y divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Email liên hệ</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conference->contact_email)
                                <a href="mailto:{{ $conference->contact_email }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $conference->contact_email }}
                                </a>
                            @else
                                Chưa có
                            @endif
                        </dd>
                    </div>
                    
                    @if($conference->contact_phone)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Điện thoại</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->contact_phone }}
                        </dd>
                    </div>
                    @endif
                    
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Kiểm tra xung đột lợi ích</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conference->enable_coi_check)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Bật
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Tắt
                                </span>
                            @endif
                        </dd>
                    </div>
                    
                    @if($conference->banner_path)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Banner hội thảo</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <img src="{{ asset('storage/' . $conference->banner_path) }}" 
                                 alt="Banner hội thảo" 
                                 class="max-w-xs h-auto rounded-lg shadow-sm">
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tổng bài báo</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalPapers ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đã chấp nhận</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $acceptedPapers ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đang xét duyệt</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $pendingPapers ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Reviewer</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalReviewers ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committees -->
        @if(isset($committees) && $committees->count() > 0)
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Tiểu ban hội thảo</h2>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($committees as $committee)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ $committee->title }}</h4>
                            @if($committee->description)
                                <p class="text-sm text-gray-500 mt-2">{{ $committee->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Proceedings Section -->
        @if($conference->status === 'ACTIVE')
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Tình trạng kỷ yếu</h2>
                    <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
                       class="text-sm text-blue-600 hover:text-blue-900 font-medium">
                        Quản lý kỷ yếu →
                    </a>
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ isset($acceptedPapersCount) ? $acceptedPapersCount : 0 }}
                        </div>
                        <div class="text-sm text-gray-500">Bài báo chấp nhận</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ isset($publishedPapersCount) ? $publishedPapersCount : 0 }}
                        </div>
                        <div class="text-sm text-gray-500">Đã xuất bản</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ isset($totalProceedingsPages) ? $totalProceedingsPages : 0 }}
                        </div>
                        <div class="text-sm text-gray-500">Tổng số trang</div>
                    </div>
                </div>
                
                @if(isset($publishedPapersCount) && $publishedPapersCount > 0)
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('chair.conferences.proceedings', $conference->conference_id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Xem kỷ yếu công khai
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Conference Details Tabs -->
        <div class="bg-white shadow rounded-lg" x-data="{ activeTab: 'overview' }">
            <!-- Tab Navigation -->
            <div class="px-6 py-4 border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Tổng quan
                    </button>
                    <button @click="activeTab = 'papers'" 
                            :class="activeTab === 'papers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Bài báo gần đây
                        @if($totalPapers > 0)
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ $totalPapers }}</span>
                        @endif
                    </button>
                    <button @click="activeTab = 'proceedings'" 
                            :class="activeTab === 'proceedings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Kỷ yếu
                        @if($publishedPapersCount > 0)
                            <span class="ml-2 bg-purple-100 text-purple-900 py-0.5 px-2.5 rounded-full text-xs">{{ $publishedPapersCount }}</span>
                        @endif
                    </button>
                </nav>
            </div>
            
            <!-- Tab Content -->
            <div class="px-6 py-4">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500">Tổng bài báo</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $totalPapers }}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500">Đã chấp nhận</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $acceptedPapersCount }}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500">Đã xuất bản</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $publishedPapersCount }}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-orange-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <dt class="text-sm font-medium text-gray-500">Phản biện viên</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $totalReviewers }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Papers Tab -->
                <div x-show="activeTab === 'papers'" x-transition>
            <div class="px-6 py-4">
                @if(isset($recentPapers) && $recentPapers->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPapers as $paper)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $paper->title }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">Tác giả: {{ $paper->submitter_name ?: 'Chưa xác định' }}</p>
                                        <div class="mt-2 flex items-center space-x-4">
                                            <span class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i') }}
                                            </span>
                                            @if($paper->status_code === 'ACCEPTED')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Đã chấp nhận
                                                </span>
                                            @elseif($paper->status_code === 'REJECTED')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Bị từ chối
                                                </span>
                                            @elseif($paper->status_code === 'UNDER_REVIEW')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Đang xét duyệt
                                                </span>
                                            @elseif($paper->status_code === 'SUBMITTED')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Đã nộp
                                                </span>
                                            @elseif($paper->status_code === 'REVISION_REQUIRED')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Yêu cầu sửa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $paper->status_code }}
                                                </span>
                                            @endif
                                            
                                            @if($paper->decision)
                                                @if($paper->decision === 'ACCEPT')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Chấp nhận
                                                    </span>
                                                @elseif($paper->decision === 'REJECT')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Từ chối
                                                    </span>
                                                @elseif($paper->decision === 'REVISE')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Yêu cầu sửa
                                                    </span>
                                                @elseif($paper->decision === 'PUBLISHED')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Đã xuất bản
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có bài báo</h3>
                        <p class="mt-1 text-sm text-gray-500">Bài báo được nộp sẽ hiển thị ở đây.</p>
                    </div>
                @endif
                </div>
                
                <!-- Proceedings Tab -->
                <div x-show="activeTab === 'proceedings'" x-transition>
                    @if($publishedPapersCount > 0)
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Kỷ yếu đã xuất bản</h3>
                                    <p class="text-sm text-gray-600">{{ $publishedPapersCount }} bài báo đã được xuất bản trong kỷ yếu</p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                                        Quản lý kỷ yếu
                                    </a>
                                    <a href="{{ route('chair.conferences.proceedings', $conference->conference_id) }}" 
                                       class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm font-medium">
                                        Xem kỷ yếu công khai
                                    </a>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-500">Bài báo chấp nhận</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $acceptedPapersCount }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-500">Đã xuất bản</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $publishedPapersCount }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-500">Tổng trang</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $totalProceedingsPages }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 text-white">
                                <h4 class="text-lg font-semibold mb-2">Trạng thái kỷ yếu</h4>
                                <p class="text-purple-100 mb-4">
                                    Hội thảo đã có {{ $publishedPapersCount }} bài báo được xuất bản trong kỷ yếu với tổng số {{ $totalProceedingsPages }} trang.
                                </p>
                                <div class="flex space-x-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold">{{ number_format(($publishedPapersCount / max($acceptedPapersCount, 1)) * 100, 1) }}%</div>
                                        <div class="text-xs text-purple-200">Tỷ lệ xuất bản</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold">{{ $totalProceedingsPages ? number_format($totalProceedingsPages / max($publishedPapersCount, 1), 1) : 0 }}</div>
                                        <div class="text-xs text-purple-200">Trang/bài báo</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có kỷ yếu</h3>
                            <p class="mt-2 text-gray-500">Chưa có bài báo nào được xuất bản trong kỷ yếu.</p>
                            
                            @if($acceptedPapersCount > 0)
                                <div class="mt-6">
                                    <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}" 
                                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Bắt đầu xuất bản kỷ yếu
                                    </a>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Có {{ $acceptedPapersCount }} bài báo đã chấp nhận sẵn sàng để xuất bản
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy hội thảo</h3>
            <p class="mt-1 text-sm text-gray-500">Hội thảo không tồn tại hoặc bạn không có quyền truy cập.</p>
            <div class="mt-6">
                <a href="{{ route('chair.conferences.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Quay lại danh sách
                </a>
            </div>
        </div>
    @endif

</div>

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
</style>
@endpush
@endsection