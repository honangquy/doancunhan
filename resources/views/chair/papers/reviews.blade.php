<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reviews - {{ $paper->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="main-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="{
            filterStatus: 'all',
            sortBy: 'date',
            searchQuery: '',
            expandedReviews: [],
            
            toggleReview(reviewId) {
                if (this.expandedReviews.includes(reviewId)) {
                    this.expandedReviews = this.expandedReviews.filter(id => id !== reviewId);
                } else {
                    this.expandedReviews.push(reviewId);
                }
            },
            
            isExpanded(reviewId) {
                return this.expandedReviews.includes(reviewId);
            },
            
            exportReviews(format) {
                window.location.href = `/qly_hthao/qlyhoithao/public/chair/papers/{{ $paper->paper_id }}/reviews/export?format=${format}`;
            }
         }">
        
        <!-- Back Button -->
        <div class="mb-6">
            <button onclick="if(window.Alpine && Alpine.$data(document.body).viewPaperDetail) { 
                    Alpine.$data(document.body).viewPaperDetail({{ $paper->paper_id }}); 
                } else { 
                    window.location.href = '{{ route('chair.papers.show', $paper->paper_id) }}'; 
                }"
                class="flex items-center text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại chi tiết bài báo
            </button>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                📋 Tất cả nhận xét
            </h1>
            <p class="text-gray-600">Bài báo #{{ $paper->paper_id }}</p>
        </div>

        <!-- Paper Summary Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="text-sm font-medium text-gray-500">#{{ $paper->paper_id }}</span>
                        @if($paper->status_name)
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $paper->status_name === 'Đã nộp' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $paper->status_name === 'Đang phản biện' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $paper->status_name === 'Đã chấp nhận' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $paper->status_name === 'Đã từ chối' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $paper->status_name }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $paper->title }}</h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span>📚 {{ $paper->conference_title }}</span>
                        <span>👤 {{ $paper->author_name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <!-- Total Reviews -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Tổng số</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Hoàn thành</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Chờ xử lý</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                        @if($stats['overdue'] > 0)
                        <p class="text-xs text-red-600 mt-1">{{ $stats['overdue'] }} quá hạn</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Điểm TB</p>
                        @if($stats['completed'] > 0)
                        <p class="text-2xl font-bold {{ $stats['avg_score'] >= 7 ? 'text-green-600' : ($stats['avg_score'] >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($stats['avg_score'], 1) }}/10
                        </p>
                        @else
                        <p class="text-2xl font-bold text-gray-400">--</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Khuyến nghị</p>
                    @if($stats['completed'] > 0)
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-green-600">✓ Chấp nhận:</span>
                            <span class="font-semibold">{{ $stats['accept_count'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-yellow-600">↻ Sửa lại:</span>
                            <span class="font-semibold">{{ $stats['revise_count'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-red-600">✗ Từ chối:</span>
                            <span class="font-semibold">{{ $stats['reject_count'] }}</span>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Chưa có dữ liệu</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Consensus Indicator -->
        @if($stats['completed'] >= 2)
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-700">Mức độ đồng thuận:</span>
                    @if($stats['consensus'] === 'strong_accept')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        🎉 Đồng thuận cao - Nên chấp nhận
                    </span>
                    @elseif($stats['consensus'] === 'accept')
                    <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                        ✓ Nghiêng về chấp nhận
                    </span>
                    @elseif($stats['consensus'] === 'strong_reject')
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                        ⚠️ Đồng thuận cao - Nên từ chối
                    </span>
                    @elseif($stats['consensus'] === 'reject')
                    <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-sm font-medium">
                        ✗ Nghiêng về từ chối
                    </span>
                    @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                        ⚡ Ý kiến trái chiều - Cần xem xét kỹ
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Filters & Export -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <!-- Status Filter -->
                    <select x-model="filterStatus" 
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="completed">Đã hoàn thành</option>
                        <option value="pending">Đang chờ</option>
                        <option value="overdue">Quá hạn</option>
                    </select>

                    <!-- Sort -->
                    <select x-model="sortBy"
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="date">Sắp xếp: Ngày gửi</option>
                        <option value="score">Sắp xếp: Điểm số</option>
                        <option value="reviewer">Sắp xếp: Tên reviewer</option>
                    </select>

                    <!-- Search -->
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Tìm kiếm trong nhận xét..."
                           class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent w-64">
                </div>

                <!-- Export Buttons -->
                <div class="flex items-center space-x-2">
                    <button @click="exportReviews('pdf')"
                            class="flex items-center space-x-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export PDF</span>
                    </button>
                    <button @click="exportReviews('excel')"
                            class="flex items-center space-x-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Completed Reviews List -->
        @if($completedReviews->count() > 0)
        <div class="space-y-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                ✅ Nhận xét đã hoàn thành ({{ $completedReviews->count() }})
            </h3>

            @foreach($completedReviews as $review)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Review Header (Clickable) -->
                <div @click="toggleReview({{ $review->review_id }})" 
                     class="p-4 cursor-pointer hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Expand/Collapse Icon -->
                            <svg x-show="!isExpanded({{ $review->review_id }})" 
                                 class="w-5 h-5 text-gray-400 flex-shrink-0" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <svg x-show="isExpanded({{ $review->review_id }})" 
                                 class="w-5 h-5 text-gray-400 flex-shrink-0" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>

                            <!-- Reviewer Info -->
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $review->reviewer_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $review->reviewer_email }}</p>
                                @if($review->reviewer_org)
                                <p class="text-xs text-gray-500">{{ $review->reviewer_org }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Review Summary -->
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Ngày gửi</p>
                                <p class="text-sm font-medium">{{ date('d/m/Y', strtotime($review->submitted_at)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Điểm</p>
                                <p class="text-xl font-bold {{ $review->overall_score >= 7 ? 'text-green-600' : ($review->overall_score >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($review->overall_score, 1) }}
                                </p>
                            </div>
                            <div>
                                @if($review->recommendation === 'ACCEPT')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    ✓ CHẤP NHẬN
                                </span>
                                @elseif($review->recommendation === 'REJECT')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                    ✗ TỪ CHỐI
                                </span>
                                @elseif($review->recommendation === 'REVISE')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                    ↻ SỬA LẠI
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Details (Expandable) -->
                <div x-show="isExpanded({{ $review->review_id }})" 
                     x-collapse
                     class="border-t border-gray-200 bg-gray-50">
                    <div class="p-6 space-y-6">
                        <!-- Individual Scores -->
                        <div>
                            <h5 class="font-semibold text-gray-900 mb-3">📊 Chi tiết điểm số</h5>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-1">Tính mới</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $review->originality_score }}/10</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-1">Chất lượng</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ $review->quality_score }}/10</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-1">Rõ ràng</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $review->clarity_score }}/10</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-1">Phù hợp</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ $review->relevance_score }}/10</p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Comments -->
                        @if($review->summary_comments)
                        <div>
                            <h5 class="font-semibold text-gray-900 mb-2">💬 Nhận xét tổng quan</h5>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $review->summary_comments }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Strengths, Weaknesses, Suggestions -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($review->strengths)
                            <div>
                                <h5 class="font-semibold text-green-700 mb-2">✅ Điểm mạnh</h5>
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $review->strengths }}</p>
                                </div>
                            </div>
                            @endif

                            @if($review->weaknesses)
                            <div>
                                <h5 class="font-semibold text-red-700 mb-2">⚠️ Điểm yếu</h5>
                                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $review->weaknesses }}</p>
                                </div>
                            </div>
                            @endif

                            @if($review->suggestions)
                            <div>
                                <h5 class="font-semibold text-blue-700 mb-2">💡 Đề xuất</h5>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $review->suggestions }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Confidential Comments (For Chair Only) -->
                        @if($review->confidential_comments)
                        <div>
                            <h5 class="font-semibold text-gray-900 mb-2">🔒 Nhận xét riêng (chỉ chủ tịch xem)</h5>
                            <div class="bg-yellow-50 rounded-lg p-4 border-2 border-yellow-300">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $review->confidential_comments }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Pending Reviews List -->
        @if($pendingReviews->count() > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                ⏳ Nhận xét đang chờ ({{ $pendingReviews->count() }})
            </h3>

            @foreach($pendingReviews as $pending)
            @php
                $isOverdue = strtotime($pending->deadline) < time();
            @endphp
            <div class="bg-white rounded-lg shadow-sm border {{ $isOverdue ? 'border-red-300' : 'border-gray-200' }} p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 {{ $isOverdue ? 'bg-red-100' : 'bg-yellow-100' }} rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $isOverdue ? 'text-red-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $pending->reviewer_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $pending->reviewer_email }}</p>
                            @if($pending->reviewer_org)
                            <p class="text-xs text-gray-500">{{ $pending->reviewer_org }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Deadline</p>
                        <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-900' }}">
                            {{ date('d/m/Y', strtotime($pending->deadline)) }}
                        </p>
                        @if($isOverdue)
                        <span class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">
                            ⚠️ Quá hạn
                        </span>
                        @else
                        <span class="inline-block mt-1 px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">
                            Đang chờ
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Empty State -->
        @if($completedReviews->count() === 0 && $pendingReviews->count() === 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có nhận xét</h3>
            <p class="text-gray-600">Bài báo này chưa được phân công phản biện.</p>
        </div>
        @endif
    </div>
</body>
</html>
