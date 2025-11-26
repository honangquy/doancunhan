<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Reviewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="main-content" x-data="{ activeTab: 'assignments' }">
        <!-- Header with Back Button -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <button onclick="window.location.href = '{{ route('chair.reviewers.index') }}'" 
                    class="text-sm text-gray-600 hover:text-gray-900 mb-4 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Quay lại danh sách reviewer
            </button>

            <!-- Reviewer Profile Card -->
            <div class="flex items-start space-x-6">
                <!-- Avatar -->
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-4xl flex-shrink-0">
                    {{ strtoupper(substr($reviewer->full_name, 0, 1)) }}
                </div>

                <!-- Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $reviewer->full_name }}</h1>
                    <div class="space-y-2 text-gray-600">
                        <p class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $reviewer->email }}
                        </p>
                        @if($reviewer->organization)
                        <p class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $reviewer->organization }}
                        </p>
                        @endif
                        @if($reviewer->phone)
                        <p class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $reviewer->phone }}
                        </p>
                        @endif
                    </div>

                    <!-- Expertise Tags -->
                    @if(count($reviewer->expertise_array) > 0)
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($reviewer->expertise_array as $expertise)
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">
                            {{ trim($expertise) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Tổng phân công</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_assignments'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $stats['completed'] }} hoàn thành, {{ $stats['pending'] }} đang chờ
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Tỷ lệ hoàn thành</p>
                        <p class="text-3xl font-bold {{ $stats['completion_rate'] >= 80 ? 'text-green-600' : ($stats['completion_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $stats['completion_rate'] }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $stats['completed'] }}/{{ $stats['total_assignments'] }} nhận xét
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Điểm trung bình</p>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ $stats['avg_score'] > 0 ? number_format($stats['avg_score'], 1) : '--' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Từ {{ $stats['completed'] }} nhận xét
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Thời gian phản hồi</p>
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ $stats['avg_response_days'] > 0 ? $stats['avg_response_days'] : '--' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Trung bình (ngày)
                </p>
            </div>
        </div>

        <!-- Recommendation Distribution -->
        @if($stats['completed'] > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">📊 Phân bố đề xuất</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['accept_count'] }}</p>
                    <p class="text-sm text-green-700 mt-1">Chấp nhận</p>
                    <p class="text-xs text-green-600 mt-1">
                        ({{ $stats['completed'] > 0 ? round(($stats['accept_count'] / $stats['completed']) * 100, 1) : 0 }}%)
                    </p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['revise_count'] }}</p>
                    <p class="text-sm text-yellow-700 mt-1">Sửa lại</p>
                    <p class="text-xs text-yellow-600 mt-1">
                        ({{ $stats['completed'] > 0 ? round(($stats['revise_count'] / $stats['completed']) * 100, 1) : 0 }}%)
                    </p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-3xl font-bold text-red-600">{{ $stats['reject_count'] }}</p>
                    <p class="text-sm text-red-700 mt-1">Từ chối</p>
                    <p class="text-xs text-red-600 mt-1">
                        ({{ $stats['completed'] > 0 ? round(($stats['reject_count'] / $stats['completed']) * 100, 1) : 0 }}%)
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'assignments'" 
                            :class="activeTab === 'assignments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                          Phân công ({{ $stats['total_assignments'] }})
                    </button>
                    <button @click="activeTab = 'completed'" 
                            :class="activeTab === 'completed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        ✅ Đã hoàn thành ({{ $stats['completed'] }})
                    </button>
                    <button @click="activeTab = 'pending'" 
                            :class="activeTab === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        ⏳ Đang chờ ({{ $stats['pending'] }})
                    </button>
                </nav>
            </div>

            <!-- Tab Content: All Assignments -->
            <div x-show="activeTab === 'assignments'" class="p-6">
                @if($completedAssignments->isEmpty() && $pendingAssignments->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">Chưa có phân công nào</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($completedAssignments->merge($pendingAssignments)->sortByDesc('assigned_at') as $assignment)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-bold text-gray-900">
                                        Bài #{{ $assignment->paper_id }}: {{ $assignment->paper_title }}
                                    </h3>
                                    @if($assignment->review_id)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                        Đã hoàn thành
                                    </span>
                                    @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded">
                                        Đang chờ
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-2">
                                    <span>📅 Phân công: {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}</span>
                                    @if($assignment->deadline)
                                    <span class="{{ \Carbon\Carbon::parse($assignment->deadline)->isPast() && !$assignment->review_id ? 'text-red-600 font-medium' : '' }}">
                                        ⏰ Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    @if($assignment->review_date)
                                    <span>✅ Hoàn thành: {{ \Carbon\Carbon::parse($assignment->review_date)->format('d/m/Y') }}</span>
                                    @endif
                                </div>

                                @if($assignment->review_id)
                                <div class="flex items-center space-x-4 mt-3">
                                    <span class="text-sm">
                                        <span class="text-gray-600">Điểm:</span>
                                        <span class="font-bold text-purple-600">{{ number_format($assignment->overall_score, 1) }}/10</span>
                                    </span>
                                    <span class="text-sm">
                                        <span class="text-gray-600">Đề xuất:</span>
                                        @if($assignment->recommendation == 'ACCEPT')
                                        <span class="font-medium text-green-600">Chấp nhận</span>
                                        @elseif($assignment->recommendation == 'REVISE')
                                        <span class="font-medium text-yellow-600">Sửa lại</span>
                                        @else
                                        <span class="font-medium text-red-600">Từ chối</span>
                                        @endif
                                    </span>
                                </div>
                                @elseif($assignment->deadline && \Carbon\Carbon::parse($assignment->deadline)->isPast())
                                <div class="mt-3 text-sm text-red-600 font-medium">
                                    ⚠️ Quá hạn {{ \Carbon\Carbon::parse($assignment->deadline)->diffForHumans() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Tab Content: Completed -->
            <div x-show="activeTab === 'completed'" class="p-6">
                @if($completedAssignments->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">Chưa có nhận xét hoàn thành</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($completedAssignments as $assignment)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-2">
                                    Bài #{{ $assignment->paper_id }}: {{ $assignment->paper_title }}
                                </h3>

                                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-3">
                                    <span>📅 Phân công: {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}</span>
                                    <span>✅ Hoàn thành: {{ \Carbon\Carbon::parse($assignment->review_date)->format('d/m/Y') }}</span>
                                    <span class="text-indigo-600 font-medium">
                                        ⏱️ {{ \Carbon\Carbon::parse($assignment->assigned_at)->diffInDays(\Carbon\Carbon::parse($assignment->review_date)) }} ngày
                                    </span>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">Điểm:</span>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 font-bold rounded">
                                            {{ number_format($assignment->overall_score, 1) }}/10
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">Đề xuất:</span>
                                        @if($assignment->recommendation == 'ACCEPT')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 font-medium rounded">✓ Chấp nhận</span>
                                        @elseif($assignment->recommendation == 'REVISE')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-medium rounded">↻ Sửa lại</span>
                                        @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 font-medium rounded">✗ Từ chối</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Tab Content: Pending -->
            <div x-show="activeTab === 'pending'" class="p-6">
                @if($pendingAssignments->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">Không có phân công đang chờ</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($pendingAssignments as $assignment)
                    <div class="border border-gray-200 rounded-lg p-4 {{ \Carbon\Carbon::parse($assignment->deadline)->isPast() ? 'bg-red-50 border-red-300' : 'hover:bg-gray-50' }} transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-bold text-gray-900">
                                        Bài #{{ $assignment->paper_id }}: {{ $assignment->paper_title }}
                                    </h3>
                                    @if($assignment->deadline && \Carbon\Carbon::parse($assignment->deadline)->isPast())
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">
                                        ⚠️ Quá hạn
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-2">
                                    <span>📅 Phân công: {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}</span>
                                    @if($assignment->deadline)
                                    <span class="{{ \Carbon\Carbon::parse($assignment->deadline)->isPast() ? 'text-red-600 font-bold' : '' }}">
                                        ⏰ Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    <span class="text-gray-500">
                                        ⏳ Đã {{ \Carbon\Carbon::parse($assignment->assigned_at)->diffForHumans() }}
                                    </span>
                                </div>

                                @if($assignment->deadline && \Carbon\Carbon::parse($assignment->deadline)->isPast())
                                <div class="mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-700">
                                    <strong>⚠️ Cảnh báo:</strong> Nhận xét đã quá hạn {{ \Carbon\Carbon::parse($assignment->deadline)->diffForHumans() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
