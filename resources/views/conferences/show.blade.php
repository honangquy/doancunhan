@extends('layouts.app')

@section('title', ($conference->title ?? 'Chi tiết hội thảo'))

@section('content')
@php
    $conferenceId = $conference->conference_id ?? request()->route('id') ?? $conference->id;
@endphp
<div x-data="conferenceDetail()">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Trang chủ</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('conferences.index') }}" class="hover:text-blue-600">Hội thảo</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-900">{{ Str::limit($conference->title, 60) }}</span>
    </nav>

    <!-- Header Section -->
    <!-- Cover image (if provided) -->
    @if(isset($conference->cover_url) && $conference->cover_url)
        <div class="rounded-lg overflow-hidden shadow-md mb-6">
            <img src="{{ $conference->cover_url }}" alt="{{ $conference->title }} cover" class="w-full h-56 object-cover">
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                    @php
                        // Logic kiểm tra trạng thái dựa trên ngày
                        $now = \Carbon\Carbon::now();
                        $submissionDeadline = isset($conference->deadline_submission) ? \Carbon\Carbon::parse($conference->deadline_submission) : null;
                        $conferenceEndDate = isset($conference->end_date) ? \Carbon\Carbon::parse($conference->end_date) : null;

                        $statusClass = 'px-3 py-1 text-xs font-medium rounded-full ';
                        $statusText = '';

                        // Kiểm tra trạng thái thực tế dựa trên ngày
                        if ($submissionDeadline && $now->lt($submissionDeadline)) {
                            // Còn hạn nộp bài
                            $statusClass .= 'bg-green-100 text-green-800';
                            $statusText = 'Đang mở';
                        } elseif ($submissionDeadline && $now->gte($submissionDeadline) && (!$conferenceEndDate || $now->lt($conferenceEndDate))) {
                            // Hết hạn nộp bài nhưng chưa kết thúc hội thảo
                            $statusClass .= 'bg-orange-100 text-orange-800';
                            $statusText = 'Đã đóng nộp bài';
                        } elseif ($conferenceEndDate && $now->gte($conferenceEndDate)) {
                            // Hội thảo đã kết thúc
                            $statusClass .= 'bg-gray-100 text-gray-800';
                            $statusText = 'Đã kết thúc';
                        } else {
                            // Trường hợp khác (chưa có thông tin ngày)
                            $statusClass .= 'bg-blue-100 text-blue-800';
                            $statusText = 'Sắp diễn ra';
                        }
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                    <span class="text-sm text-gray-500">{{ $conference->code ?? 'CONF-2025' }}</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $conference->title ?? 'Tiêu đề hội thảo' }}</h1>
                <p class="text-gray-600 mb-4">{{ $conference->description ?? 'Mô tả ngắn về hội thảo' }}</p>

                <!-- Quick Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="flex items-center space-x-2">
                        <!-- Calendar Icon (larger) -->
                        <svg class="w-7 h-7 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.5"></rect>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.5"></path>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Ngày tổ chức</div>
                            <div class="font-medium">
                                @if(isset($conference->start_date))
                                    {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                                @else
                                    TBA
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Deadline Icon (larger) -->
                        <svg class="w-7 h-7 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="9" stroke-width="1.5"></circle>
                            <path d="M12 7v6l4 2" stroke-width="1.5"></path>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Hạn nộp bài</div>
                            <div class="font-medium text-orange-600">
                                @if(isset($conference->deadline_submission))
                                    {{ \Carbon\Carbon::parse($conference->deadline_submission)->format('d/m/Y') }}
                                @else
                                    TBA
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Location Icon (larger) -->
                        <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z" stroke-width="1.5"></path>
                            <circle cx="12" cy="11" r="2" stroke-width="1.5"></circle>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Địa điểm</div>
                            <div class="font-medium">{{ $conference->location ?? 'Online' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 lg:mt-0 lg:ml-6 flex flex-col space-y-3">
                <!-- Join Request Button -->
                @auth
                    @php
                        // Kiểm tra xem có thể tham gia không dựa trên deadline submission
                        $canJoin = $submissionDeadline && $now->lt($submissionDeadline);
                        // Kiểm tra xem user có phải là Chair của hội thảo này không
                        $isChair = Auth::user()->isChair($conference->conference_id);
                    @endphp

                    @if($isChair)
                        <div class="px-6 py-3 bg-purple-100 text-purple-800 font-medium rounded-lg text-center border border-purple-200">
                            Bạn là Chủ tịch của hội thảo này
                        </div>
                    @elseif($canJoin)
                        <button @click="openJoinModal = true; joinRole = 'AUTHOR'"
                                class="px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Đăng ký tham gia - Tác giả</span>
                        </button>
                    @else
                        <div class="px-6 py-3 bg-gray-100 text-gray-500 font-medium rounded-lg text-center">
                            @if($submissionDeadline && $now->gte($submissionDeadline))
                                Đã hết hạn nộp bài
                            @else
                                Hội thảo chưa mở
                            @endif
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2 text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Đăng nhập để tham gia</span>
                    </a>
                @endauth

                <!-- CFP Download -->
                @php
                    $hasCFP = false;
                    $cfpUrl = null;

                    if(isset($conference->cfp_url) && $conference->cfp_url) {
                        $hasCFP = true;
                        $cfpUrl = $conference->cfp_url;
                    } elseif(isset($conference->cfp_file_path) && $conference->cfp_file_path) {
                        $fullPath = storage_path('app/public/' . $conference->cfp_file_path);
                        if(file_exists($fullPath)) {
                            $hasCFP = true;
                            $cfpUrl = asset('storage/' . $conference->cfp_file_path);
                        }
                    }
                @endphp

                @if($hasCFP && $cfpUrl)
                    <a href="{{ $cfpUrl }}" target="_blank"
                       class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Tải Call for Papers</span>
                    </a>
                @endif

                <!-- Social Share -->
                <div class="flex space-x-2">
                    <button @click="shareConference('copy')" title="Copy link"
                            class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button @click="shareConference('facebook')" title="Share on Facebook"
                            class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                        </svg>
                    </button>
                    <button @click="shareConference('linkedin')" title="Share on LinkedIn"
                            class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['papers'] ?? '0' }}</div>
                    <div class="text-sm text-gray-500">Bài báo đã nhận</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['reviewers'] ?? '0' }}</div>
                    <div class="text-sm text-gray-500">Phản biện viên</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['authors'] ?? '0' }}</div>
                    <div class="text-sm text-gray-500">Tác giả</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900" x-text="timeRemaining">{{ $timeRemaining ?? '--' }}</div>
                    <div class="text-sm text-gray-500">Ngày còn lại</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Tổng quan
                </button>
                <button @click="activeTab = 'cfp'"
                        :class="activeTab === 'cfp' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Call for Papers
                </button>
                <button @click="activeTab = 'dates'"
                        :class="activeTab === 'dates' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Lịch trình
                </button>
                <button @click="activeTab = 'submissions'"
                        :class="activeTab === 'submissions' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Nộp bài
                </button>
                <button @click="activeTab = 'organizers'"
                        :class="activeTab === 'organizers' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Ban tổ chức
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả hội thảo</h3>
                        <div class="prose text-gray-600">
                            @if(isset($conference->detailed_description) && $conference->detailed_description)
                                {!! $conference->detailed_description !!}
                            @elseif(isset($conference->description) && $conference->description)
                                {!! $conference->description !!}
                            @else
                                Thông tin chi tiết sẽ được cập nhật sớm.
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Chủ đề & Từ khóa</h3>
                        @if(isset($conference->keywords) && $conference->keywords)
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $conference->keywords) as $keyword)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">Chưa có thông tin về chủ đề</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- CFP Tab -->
            <div x-show="activeTab === 'cfp'">
                @php
                    $hasCFPFile = false;
                    $hasCFPUrl = false;
                    $cfpFileUrl = null;
                    $cfpExternalUrl = null;

                    // Kiểm tra CFP file upload
                    if(isset($conference->cfp_file_path) && $conference->cfp_file_path) {
                        $fullPath = storage_path('app/public/' . $conference->cfp_file_path);
                        if(file_exists($fullPath)) {
                            $hasCFPFile = true;
                            $cfpFileUrl = route('conferences.cfp', $conferenceId);
                        }
                    }

                    // Kiểm tra CFP external URL
                    if(isset($conference->cfp_url) && $conference->cfp_url) {
                        $hasCFPUrl = true;
                        $cfpExternalUrl = $conference->cfp_url;
                    }
                @endphp

                @if($hasCFPFile || $hasCFPUrl)
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-red-400 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Call for Papers</h3>
                        <p class="text-gray-600 mb-6">Tài liệu hướng dẫn nộp bài chi tiết</p>

                        @if($hasCFPFile)
                            <!-- Embedded PDF Viewer for uploaded file -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                                <!-- PDF Viewer Header -->
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-900">Call for Papers PDF</h4>
                                        <div class="flex space-x-2">
                                            <button onclick="toggleFullscreen()"
                                                    class="text-gray-600 hover:text-gray-900 p-1 rounded"
                                                    title="Toàn màn hình">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- PDF Viewer -->
                                <div class="relative" id="pdf-container">
                                    <div id="pdf-loading" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-10">
                                        <div class="text-center">
                                            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="text-sm text-gray-600">Đang tải PDF...</p>
                                        </div>
                                    </div>

                                    <!-- Simple PDF Preview Options -->
                                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-8 text-center">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-xl font-bold text-gray-900 mb-2">Call for Papers PDF</h4>
                                        <p class="text-gray-600 mb-6">Chọn cách xem tài liệu phù hợp với thiết bị của bạn</p>

                                        <!-- Viewing Options -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-2xl mx-auto">
                                            <!-- View in New Tab -->
                                            <a href="{{ $cfpFileUrl }}" target="_blank"
                                               class="group bg-white rounded-lg p-6 shadow-sm hover:shadow-md border-2 border-transparent hover:border-blue-200 transition-all duration-200 text-center">
                                                <svg class="w-8 h-8 text-blue-600 mx-auto mb-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                <h5 class="font-semibold text-gray-900 mb-1">Xem trực tiếp</h5>
                                                <p class="text-sm text-gray-600">Mở trong tab mới</p>
                                            </a>

                                            <!-- Show Embedded -->
                                            <button onclick="showEmbeddedViewer()"
                                                    class="group bg-white rounded-lg p-6 shadow-sm hover:shadow-md border-2 border-transparent hover:border-green-200 transition-all duration-200 text-center">
                                                <svg class="w-8 h-8 text-green-600 mx-auto mb-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0V9a2 2 0 012 2h2a2 2 0 002 2V7a2 2 0 00-2-2H9a2 2 0 00-2 2z"></path>
                                                </svg>
                                                <h5 class="font-semibold text-gray-900 mb-1">Xem nhúng</h5>
                                                <p class="text-sm text-gray-600">Hiển thị tại đây</p>
                                            </button>

                                            <!-- Download -->
                                            <a href="{{ asset('storage/' . $conference->cfp_file_path) }}" download
                                               class="group bg-white rounded-lg p-6 shadow-sm hover:shadow-md border-2 border-transparent hover:border-purple-200 transition-all duration-200 text-center">
                                                <svg class="w-8 h-8 text-purple-600 mx-auto mb-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <h5 class="font-semibold text-gray-900 mb-1">Tải xuống</h5>
                                                <p class="text-sm text-gray-600">Lưu về máy</p>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Embedded Viewer (hidden by default) -->
                                    <div id="embedded-viewer" class="hidden mt-6 bg-white rounded-lg shadow-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                            <h4 class="font-medium text-gray-900">PDF Viewer</h4>
                                            <button onclick="hideEmbeddedViewer()" class="text-gray-500 hover:text-gray-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <iframe src="{{ $cfpFileUrl }}#view=FitH" width="100%" height="600" class="border-0"></iframe>
                                    </div>
                                </div>
                            </div>


                        @endif

                        @if($hasCFPUrl)
                            <!-- External URL link -->
                            <a href="{{ $cfpExternalUrl }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Xem Call for Papers
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Call for Papers</h3>
                        <p class="text-gray-500">Call for Papers sẽ được cập nhật sớm</p>
                    </div>
                @endif

                @if(isset($conference->submission_guidelines) && $conference->submission_guidelines)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hướng dẫn nộp bài</h3>
                        <div class="prose text-gray-600">
                            {!! $conference->submission_guidelines !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Important Dates Tab -->
            <div x-show="activeTab === 'dates'">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Hạn nộp bài báo</span>
                        </div>
                        <span class="text-orange-600 font-medium">
                            @if(isset($conference->deadline_submission))
                                {{ \Carbon\Carbon::parse($conference->deadline_submission)->format('d/m/Y') }}
                            @else
                                TBA
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="font-medium">Ngày bắt đầu hội thảo</span>
                        </div>
                        <span class="text-blue-600 font-medium">
                            @if(isset($conference->start_date))
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                            @else
                                TBA
                            @endif
                        </span>
                    </div>

                    @if(isset($conference->end_date) && $conference->end_date)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Ngày kết thúc</span>
                        </div>
                        <span class="text-green-600 font-medium">{{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}</span>
                    </div>
                    @endif

                    <!-- Add to Calendar -->
                    <div class="mt-6 pt-6 border-t">
                        <button @click="addToCalendar()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Thêm vào lịch
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submissions Tab -->
            <div x-show="activeTab === 'submissions'">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Danh sách bài nộp</h3>
                    <p class="text-gray-500 mb-4">Chỉ hiển thị sau khi hội thảo kết thúc</p>
                </div>
            </div>

            <!-- Organizers Tab -->
            <div x-show="activeTab === 'organizers'">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ban tổ chức</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3 p-4 border rounded-lg">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium">
                                        @if(isset($conference->chair) && $conference->chair && $conference->chair->full_name)
                                            {{ $conference->chair->full_name }}
                                        @elseif(isset($conference->chair_name) && $conference->chair_name)
                                            {{ $conference->chair_name }}
                                        @elseif(isset($conference->conferenceRequest) && $conference->conferenceRequest && $conference->conferenceRequest->chair_name)
                                            {{ $conference->conferenceRequest->chair_name }}
                                        @elseif(isset($conference->contact_name) && $conference->contact_name)
                                            {{ $conference->contact_name }}
                                        @else
                                            Chưa cập nhật
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">Chủ tịch hội thảo</div>
                                    @php
                                        $chairEmail = null;
                                        if(isset($conference->chair) && $conference->chair && $conference->chair->email) {
                                            $chairEmail = $conference->chair->email;
                                        } elseif(isset($conference->chair_email) && $conference->chair_email) {
                                            $chairEmail = $conference->chair_email;
                                        } elseif(isset($conference->conferenceRequest) && $conference->conferenceRequest && $conference->conferenceRequest->chair_email) {
                                            $chairEmail = $conference->conferenceRequest->chair_email;
                                        } elseif(isset($conference->contact_email) && $conference->contact_email) {
                                            $chairEmail = $conference->contact_email;
                                        }
                                    @endphp
                                    @if($chairEmail)
                                        <div class="text-sm text-blue-600">{{ $chairEmail }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin liên hệ</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-2">
                                @if(isset($conference->contact_email) && $conference->contact_email)
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $conference->contact_email }}</span>
                                    </div>
                                @endif
                                @if(isset($conference->contact_phone) && $conference->contact_phone)
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span>{{ $conference->contact_phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Request Modal -->
    <div x-show="openJoinModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
             @click.away="openJoinModal = false">

            <!-- Role Selection Removed - Direct to Author Registration -->

            <!-- Step 2: Author Form -->
            <div x-show="joinRole === 'AUTHOR'" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Đăng ký tham gia - Tác giả</h3>
                    <button @click="openJoinModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitJoinRequest()">
                    <!-- Thông báo thông tin tài khoản -->
                    @auth
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm">
                                <p class="font-medium text-blue-800">Thông tin cá nhân</p>
                                <p class="text-blue-700 mt-1">Họ tên và email được lấy từ hồ sơ tài khoản của bạn.
                                Để thay đổi thông tin này, vui lòng liên hệ quản trị viên hệ thống.</p>
                            </div>
                        </div>
                    </div>
                    @endauth

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Họ và tên -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.full_name" required readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600 cursor-not-allowed"
                                   title="Thông tin này được lấy từ hồ sơ của bạn và không thể chỉnh sửa">
                            <p class="text-xs text-gray-500 mt-1">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Thông tin lấy từ hồ sơ tài khoản
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" x-model="formData.email_contact" required readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600 cursor-not-allowed"
                                   title="Thông tin này được lấy từ hồ sơ của bạn và không thể chỉnh sửa">
                            <p class="text-xs text-gray-500 mt-1">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Thông tin lấy từ hồ sơ tài khoản
                            </p>
                        </div>

                        <!-- Quốc gia -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quốc gia <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.country" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Đơn vị công tác -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị công tác <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.organization" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Khoa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoa <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.department" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Lĩnh vực -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lĩnh vực <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.field_of_study" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Chức danh/Học vị -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Học hàm/Học vị <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.academic_title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Số điện thoại -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" x-model="formData.phone" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Ghi chú -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                            <textarea x-model="formData.notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                      placeholder="Ghi chú thêm (nếu có)..."></textarea>
                        </div>

                        <!-- Cam kết -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="formData.commitment_confirmed" required class="mr-2">
                                <span class="text-sm text-gray-700">Tôi cam kết thông tin trên là chính xác và tuân thủ quy định của hội thảo <span class="text-red-500">*</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" @click="openJoinModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" :disabled="!formData.commitment_confirmed || isSubmitting"
                                :class="(!formData.commitment_confirmed || isSubmitting) ? 'bg-gray-300 cursor-not-allowed' : 'bg-orange-600 hover:bg-orange-700'"
                                class="flex-1 px-4 py-2 text-white rounded-md transition-colors">
                            <span x-show="!isSubmitting">Gửi yêu cầu</span>
                            <span x-show="isSubmitting">Đang gửi...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Reviewer Form -->
            <div x-show="joinRole === 'REVIEWER'" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Đăng ký tham gia - Phản biện viên</h3>
                    <button @click="openJoinModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitJoinRequest()">
                    <div class="space-y-4">
                        <!-- Email được mời -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email được mời <span class="text-red-500">*</span></label>
                            <input type="email" x-model="formData.email_contact" required
                                   :readonly="invitationData && invitationData.invited"
                                   :class="invitationData && invitationData.invited ?
                                           'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed' :
                                           'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500'">
                            <template x-if="invitationData && invitationData.invited">
                                <p class="mt-1 text-xs text-gray-500">Email này đã được mời tham gia</p>
                            </template>
                        </div>

                        <!-- Họ và tên -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.full_name" required
                                   :readonly="invitationData && invitationData.invited"
                                   :class="invitationData && invitationData.invited ?
                                           'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed' :
                                           'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500'">
                            <template x-if="invitationData && invitationData.invited">
                                <p class="mt-1 text-xs text-gray-500">Thông tin từ tài khoản cá nhân</p>
                            </template>
                        </div>

                        <!-- Đơn vị công tác -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị công tác <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.organization" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <!-- Từ khóa chuyên môn -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Từ khóa chuyên môn <span class="text-red-500">*</span></label>
                            <textarea x-model="formData.expertise_keywords" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                      placeholder="Ví dụ: Machine Learning, Computer Vision, Natural Language Processing..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Liệt kê các lĩnh vực chuyên môn của bạn, cách nhau bằng dấu phẩy</p>
                        </div>

                        <!-- Số bài tối đa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số bài tối đa có thể nhận <span class="text-red-500">*</span></label>
                            <input type="number" x-model="formData.max_papers" min="1" max="50" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <p class="text-xs text-gray-500 mt-1">Số lượng bài báo tối đa bạn có thể phản biện trong hội thảo này</p>
                        </div>

                        <!-- Cam kết -->
                        <div>
                            <label class="flex items-start">
                                <input type="checkbox" x-model="formData.commitment_confirmed" required class="mr-2 mt-1">
                                <span class="text-sm text-gray-700">Tôi cam kết thực hiện đúng quy trình phản biện, đảm bảo tính khách quan và hoàn thành đúng thời hạn <span class="text-red-500">*</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" @click="openJoinModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" :disabled="!formData.commitment_confirmed || isSubmitting"
                                :class="(!formData.commitment_confirmed || isSubmitting) ? 'bg-gray-300 cursor-not-allowed' : 'bg-orange-600 hover:bg-orange-700'"
                                class="flex-1 px-4 py-2 text-white rounded-md transition-colors">
                            <span x-show="!isSubmitting">Gửi yêu cầu</span>
                            <span x-show="isSubmitting">Đang gửi...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function conferenceDetail() {
        return {
            activeTab: 'overview',
            openJoinModal: @if(session('invitation_data'))true @else false @endif, // Auto-open if invited
            joinRole: @if(session('invitation_data'))'REVIEWER'@else 'AUTHOR'@endif, // Default to AUTHOR, REVIEWER only if invited
            isSubmitting: false,
            invitationData: @json(session('invitation_data', null)),
            formData: {
                // Common fields - get from logged in user profile
                full_name: @if(Auth::check())'{{ Auth::user()->full_name ?? Auth::user()->name ?? "" }}'@elseif(session('invitation_data'))'{{ session('invitation_data.full_name', '') }}'@else ''@endif,
                email_contact: @if(Auth::check())'{{ Auth::user()->email ?? "" }}'@elseif(session('invitation_data'))'{{ session('invitation_data.email', '') }}'@else ''@endif,
                commitment_confirmed: false,

                // Author specific fields
                country: '',
                organization: '',
                department: '',
                field_of_study: '',
                academic_title: '',
                phone: '',
                notes: '',

                // Reviewer specific fields
                expertise_keywords: '',
                max_papers: 1
            },
            deadline: '@if(isset($conference->deadline_submission)){{ \Carbon\Carbon::parse($conference->deadline_submission)->format("d/m/Y") }}@else{{ "TBA" }}@endif',
            timeRemaining: '{{ $timeRemaining ?? "--" }}',

            resetForm() {
                this.formData = {
                    full_name: '',
                    email_contact: '',
                    commitment_confirmed: false,
                    country: '',
                    organization: '',
                    department: '',
                    field_of_study: '',
                    academic_title: '',
                    phone: '',
                    notes: '',
                    expertise_keywords: '',
                    max_papers: 1
                };
            },

            submitJoinRequest() {
                if (!this.joinRole || !this.formData.commitment_confirmed) return;

                // Validate email for invited reviewer
                if (this.invitationData && this.invitationData.invited && this.joinRole === 'REVIEWER') {
                    if (this.formData.email_contact !== this.invitationData.email) {
                        alert('Email không trùng với email được mời. Vui lòng sử dụng email: ' + this.invitationData.email);
                        return;
                    }
                }

                this.isSubmitting = true;
                const conferenceId = '{{ $conference->conference_id ?? 1 }}';

                // Prepare data based on role
                let submitData = {
                    role: this.joinRole,
                    full_name: this.formData.full_name,
                    email_contact: this.formData.email_contact,
                    commitment_confirmed: this.formData.commitment_confirmed ? 1 : 0
                };

                // Add invitation token if exists
                if (this.invitationData && this.invitationData.token) {
                    submitData.invitation_token = this.invitationData.token;
                }

                if (this.joinRole === 'AUTHOR') {
                    submitData = {
                        ...submitData,
                        country: this.formData.country,
                        organization: this.formData.organization,
                        department: this.formData.department,
                        field_of_study: this.formData.field_of_study,
                        academic_title: this.formData.academic_title,
                        phone: this.formData.phone,
                        notes: this.formData.notes
                    };
                } else if (this.joinRole === 'REVIEWER') {
                    submitData = {
                        ...submitData,
                        organization: this.formData.organization,
                        expertise_keywords: this.formData.expertise_keywords,
                        max_papers: this.formData.max_papers
                    };
                }

                // Submit form data
                console.log('Submitting data:', submitData);

                @guest
                alert('Bạn cần đăng nhập để gửi yêu cầu tham gia');
                window.location.href = '/login';
                return;
                @endguest

                fetch(`{{ route('conferences.join-request', ['id' => '__ID__']) }}`.replace('__ID__', conferenceId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(submitData)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    this.isSubmitting = false;
                    if (data.success) {
                        alert(data.message);
                        this.openJoinModal = false;
                        this.joinRole = '';
                        this.resetForm();

                        // Clear invitation data if this was an invited user
                        if (data.data && data.data.is_invited) {
                            this.invitationData = null;
                        }
                    } else {
                        if (data.errors) {
                            // Display validation errors
                            let errorMessages = [];
                            for (const field in data.errors) {
                                errorMessages.push(...data.errors[field]);
                            }
                            alert('Lỗi nhập liệu:\n' + errorMessages.join('\n'));
                        } else {
                            alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu');
                });
            },

            shareConference(platform) {
                const url = window.location.href;
                const title = document.title;

                switch (platform) {
                    case 'copy':
                        navigator.clipboard.writeText(url).then(() => {
                            alert('Đã sao chép link!');
                        });
                        break;
                    case 'facebook':
                        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                        break;
                    case 'linkedin':
                        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
                        break;
                }
            },

            addToCalendar() {
                const startDate = '@if(isset($conference->start_date)){{ \Carbon\Carbon::parse($conference->start_date)->format("Ymd") }}@endif';
                const endDate = '@if(isset($conference->end_date)){{ \Carbon\Carbon::parse($conference->end_date)->format("Ymd") }}@endif';
                const title = '{{ $conference->title ?? "Hội thảo" }}';
                const description = '{{ $conference->description ?? "" }}';
                const location = '{{ $conference->location ?? "Online" }}';

                if (!startDate) {
                    alert('Thông tin lịch chưa đầy đủ');
                    return;
                }

                const icsContent = [
                    'BEGIN:VCALENDAR',
                    'VERSION:2.0',
                    'PRODID:-//HUIT Conferences//EN',
                    'BEGIN:VEVENT',
                    'UID:conf-{{ $conference->conference_id ?? 1 }}@huit.edu.vn',
                    `DTSTART:${startDate}T090000Z`,
                    `DTEND:${endDate || startDate}T180000Z`,
                    `SUMMARY:${title}`,
                    `DESCRIPTION:${description}`,
                    `LOCATION:${location}`,
                    'END:VEVENT',
                    'END:VCALENDAR'
                ].join('\r\n');

                const blob = new Blob([icsContent], { type: 'text/calendar' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `${title.replace(/[^a-zA-Z0-9]/g, '_')}.ics`;
                link.click();
                window.URL.revokeObjectURL(url);
            }
        };
    }

    // PDF Viewer Functions
    function showEmbeddedViewer() {
        const embeddedViewer = document.getElementById('embedded-viewer');
        if (embeddedViewer) {
            embeddedViewer.classList.remove('hidden');
            embeddedViewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function hideEmbeddedViewer() {
        const embeddedViewer = document.getElementById('embedded-viewer');
        if (embeddedViewer) {
            embeddedViewer.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
