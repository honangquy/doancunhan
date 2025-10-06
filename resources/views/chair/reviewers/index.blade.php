<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Reviewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="main-content">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">👥 Quản lý Reviewer</h1>
                    <p class="text-gray-600">Xem thông tin, thống kê và hiệu suất của các reviewer</p>
                </div>
                <button onclick="window.history.back()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                    ← Quay lại
                </button>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-medium mb-1">Tổng reviewer</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $stats['total_reviewers'] }}</p>
                        </div>
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-medium mb-1">Rảnh việc</p>
                            <p class="text-2xl font-bold text-green-900">{{ $stats['free_reviewers'] }}</p>
                        </div>
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-600 text-sm font-medium mb-1">Đang bận</p>
                            <p class="text-2xl font-bold text-orange-900">{{ $stats['busy_reviewers'] }}</p>
                        </div>
                        <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-600 text-sm font-medium mb-1">TB phân công</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $stats['avg_assignments'] }}</p>
                        </div>
                        <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-600 text-sm font-medium mb-1">Tỷ lệ hoàn thành</p>
                            <p class="text-2xl font-bold text-indigo-900">{{ $stats['avg_completion_rate'] }}%</p>
                        </div>
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" x-data="{ showFilters: false }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">🔍 Tìm kiếm & Lọc</h2>
                <button @click="showFilters = !showFilters" 
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <span x-show="!showFilters">Hiện bộ lọc ↓</span>
                    <span x-show="showFilters">Ẩn bộ lọc ↑</span>
                </button>
            </div>

            <form method="GET" action="{{ route('chair.reviewers.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ $request->search ?? '' }}"
                               placeholder="Tìm theo tên, email, tổ chức..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        Tìm kiếm
                    </button>
                    @if($request->search || $request->expertise || $request->workload != 'all' || $request->sort != 'name')
                    <a href="{{ route('chair.reviewers.index') }}" 
                       class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition">
                        Xóa bộ lọc
                    </a>
                    @endif
                </div>

                <!-- Advanced Filters -->
                <div x-show="showFilters" x-collapse>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chuyên môn</label>
                            <input type="text" 
                                   name="expertise" 
                                   value="{{ $request->expertise ?? '' }}"
                                   placeholder="VD: Machine Learning, AI..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khối lượng công việc</label>
                            <select name="workload" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all" {{ ($request->workload ?? 'all') == 'all' ? 'selected' : '' }}>Tất cả</option>
                                <option value="free" {{ ($request->workload ?? '') == 'free' ? 'selected' : '' }}>Rảnh (0 pending)</option>
                                <option value="light" {{ ($request->workload ?? '') == 'light' ? 'selected' : '' }}>Nhẹ (1-2 pending)</option>
                                <option value="moderate" {{ ($request->workload ?? '') == 'moderate' ? 'selected' : '' }}>Vừa (3-4 pending)</option>
                                <option value="heavy" {{ ($request->workload ?? '') == 'heavy' ? 'selected' : '' }}>Nặng (5+ pending)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                            <select name="sort" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="name" {{ ($request->sort ?? 'name') == 'name' ? 'selected' : '' }}>Tên (A-Z)</option>
                                <option value="assignments" {{ ($request->sort ?? '') == 'assignments' ? 'selected' : '' }}>Số phân công</option>
                                <option value="completion" {{ ($request->sort ?? '') == 'completion' ? 'selected' : '' }}>Tỷ lệ hoàn thành</option>
                                <option value="workload" {{ ($request->sort ?? '') == 'workload' ? 'selected' : '' }}>Công việc đang chờ</option>
                                <option value="score" {{ ($request->sort ?? '') == 'score' ? 'selected' : '' }}>Điểm trung bình</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reviewers List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
                📋 Danh sách Reviewer ({{ $reviewers->count() }})
            </h2>

            @if($reviewers->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 text-lg font-medium">Không tìm thấy reviewer nào</p>
                <p class="text-gray-400 text-sm mt-2">Thử thay đổi bộ lọc hoặc tìm kiếm khác</p>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reviewers as $reviewer)
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition cursor-pointer"
                     onclick="window.location.href = '{{ route('chair.reviewers.show', $reviewer->user_id) }}'">
                    
                    <!-- Reviewer Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($reviewer->full_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 truncate">{{ $reviewer->full_name }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $reviewer->email }}</p>
                            </div>
                        </div>
                        
                        <!-- Workload Badge -->
                        @if($reviewer->workload_status == 'free')
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Rảnh</span>
                        @elseif($reviewer->workload_status == 'light')
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Nhẹ</span>
                        @elseif($reviewer->workload_status == 'moderate')
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Vừa</span>
                        @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Nặng</span>
                        @endif
                    </div>

                    <!-- Organization -->
                    @if($reviewer->organization)
                    <p class="text-sm text-gray-600 mb-3 truncate">
                        🏢 {{ $reviewer->organization }}
                    </p>
                    @endif

                    <!-- Expertise Tags -->
                    @if($reviewer->expertise)
                    <div class="flex flex-wrap gap-1 mb-4">
                        @php
                            $expertiseList = explode(',', $reviewer->expertise);
                            $displayCount = min(count($expertiseList), 3);
                        @endphp
                        @for($i = 0; $i < $displayCount; $i++)
                        <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs rounded">
                            {{ trim($expertiseList[$i]) }}
                        </span>
                        @endfor
                        @if(count($expertiseList) > 3)
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                            +{{ count($expertiseList) - 3 }}
                        </span>
                        @endif
                    </div>
                    @endif

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-lg font-bold text-blue-600">{{ $reviewer->total_assignments }}</p>
                            <p class="text-xs text-gray-500">Phân công</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-green-600">{{ $reviewer->completed_reviews }}</p>
                            <p class="text-xs text-gray-500">Hoàn thành</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-orange-600">{{ $reviewer->pending_reviews }}</p>
                            <p class="text-xs text-gray-500">Đang chờ</p>
                        </div>
                    </div>

                    <!-- Performance Indicators -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Tỷ lệ hoàn thành:</span>
                            <span class="font-medium {{ $reviewer->completion_rate >= 80 ? 'text-green-600' : ($reviewer->completion_rate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $reviewer->completion_rate }}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Điểm TB:</span>
                            <span class="font-medium text-purple-600">
                                {{ $reviewer->avg_score > 0 ? number_format($reviewer->avg_score, 1) : '--' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Thời gian phản hồi:</span>
                            <span class="font-medium text-indigo-600">
                                {{ $reviewer->avg_response_days > 0 ? $reviewer->avg_response_days . ' ngày' : '--' }}
                            </span>
                        </div>
                    </div>

                    <!-- View Details Button -->
                    <button class="w-full mt-4 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium rounded-lg transition text-sm">
                        Xem chi tiết →
                    </button>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</body>
</html>
