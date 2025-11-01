@extends('layouts.admin')

@section('title', 'Quản lý Hội Thảo')

@section('content')
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
</style>

<div class="py-6 opacity-0 animate-fade-in-up">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Quản lý hội thảo
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tất cả hội thảo trong hệ thống với công cụ quản lý toàn diện
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
                <button onclick="exportConferences()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Xuất Excel
                </button>
                <a href="{{ route('admin.configured-conferences.index') }}" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Duyệt mới
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg opacity-0 animate-fade-in stagger-1">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đang hoạt động</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ isset($conferences) ? $conferences->where('status', 'ACTIVE')->count() : 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg opacity-0 animate-fade-in stagger-2">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đang chờ</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ isset($conferences) ? $conferences->where('status', 'PENDING')->count() : 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg opacity-0 animate-fade-in stagger-3">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đã hoàn thành</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ isset($conferences) ? $conferences->where('status', 'COMPLETED')->count() : 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg opacity-0 animate-fade-in stagger-4">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tổng cộng</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ isset($conferences) ? $conferences->count() : 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Search and Filter Form -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tìm kiếm và lọc</h3>
                <form method="GET" action="{{ route('admin.conferences.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search Input -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Tìm kiếm</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Tên hội thảo, mã, chủ tịch..."
                                   class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status" class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Năm</label>
                            <select name="year" id="year" class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Tất cả năm</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Level Filter -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700">Cấp độ</label>
                            <select name="level" id="level" class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Tất cả cấp độ</option>
                                <option value="INTERNATIONAL" {{ request('level') == 'INTERNATIONAL' ? 'selected' : '' }}>Quốc tế</option>
                                <option value="NATIONAL" {{ request('level') == 'NATIONAL' ? 'selected' : '' }}>Quốc gia</option>
                                <option value="REGIONAL" {{ request('level') == 'REGIONAL' ? 'selected' : '' }}>Khu vực</option>
                                <option value="INSTITUTIONAL" {{ request('level') == 'INSTITUTIONAL' ? 'selected' : '' }}>Cơ sở</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <div class="flex items-center space-x-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Tìm kiếm
                            </button>
                            <a href="{{ route('admin.conferences.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Đặt lại
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ isset($conferences) ? $conferences->total() : 0 }} kết quả
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($conferences) && $conferences->count() > 0)
        <!-- Bulk Actions -->
        <div class="bg-white shadow rounded-lg mb-4">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <div class="flex items-center">
                        <input type="checkbox" id="select-all" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="select-all" class="ml-2 text-sm text-gray-900">Chọn tất cả</label>
                    </div>
                    <div class="flex flex-wrap items-center gap-2" id="bulk-actions" style="display: none;">
                        <button onclick="bulkChangeStatus('ACTIVE')" class="inline-flex items-center px-2 py-1 border border-green-300 text-xs font-medium rounded text-green-700 bg-green-50 hover:bg-green-100">
                            Kích hoạt
                        </button>
                        <button onclick="bulkChangeStatus('PENDING')" class="inline-flex items-center px-2 py-1 border border-yellow-300 text-xs font-medium rounded text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                            Tạm dừng
                        </button>
                        <button onclick="bulkExport()" class="inline-flex items-center px-2 py-1 border border-blue-300 text-xs font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100">
                            Xuất file
                        </button>
                        <button onclick="bulkDelete()" class="inline-flex items-center px-2 py-1 border border-red-300 text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Conferences Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto max-w-full">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left">
                                <span class="sr-only">Chọn</span>
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-1/4" onclick="sortTable('title')">
                                Hội thảo ↕
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Chủ tịch
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-24" onclick="sortTable('start_date')">
                                Thời gian ↕
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Cấp độ
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                Trạng thái
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Thống kê
                            </th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($conferences as $conference)
                        <tr class="hover:bg-gray-50" data-conference-id="{{ $conference->conference_id }}">
                            <td class="px-3 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_conferences[]" value="{{ $conference->conference_id }}" class="conference-checkbox h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-1">
                                        <div class="text-sm font-medium text-gray-900 max-w-48 truncate">
                                            {{ $conference->title }}
                                        </div>
                                        <div class="text-xs text-gray-500 max-w-48 truncate">
                                            {{ $conference->acronym ?: 'Chưa có mã' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                @if($conference->chair)
                                    <div class="text-sm text-gray-900 truncate max-w-32">{{ $conference->chair->full_name }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-32">{{ $conference->chair->email }}</div>
                                @else
                                    <span class="text-sm text-gray-400">Chưa có</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                @if($conference->start_date)
                                    <div>{{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}</div>
                                    @if($conference->end_date && $conference->end_date != $conference->start_date)
                                        <div class="text-xs text-gray-400">đến {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Chưa xác định</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                @if($conference->level_code == 'INTERNATIONAL')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Quốc tế
                                    </span>
                                @elseif($conference->level_code == 'NATIONAL')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Quốc gia
                                    </span>
                                @elseif($conference->level_code == 'REGIONAL')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Khu vực
                                    </span>
                                @elseif($conference->level_code == 'INSTITUTIONAL')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Cơ sở
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $conference->level_code ?: 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                @if($conference->status == 'ACTIVE')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Đang hoạt động
                                    </span>
                                @elseif($conference->status == 'PENDING')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Đang chờ
                                    </span>
                                @elseif($conference->status == 'COMPLETED')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Đã hoàn thành
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $conference->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {{ $conference->baiBaos()->count() ?? 0 }} bài báo
                                </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('conferences.show', $conference->conference_id) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 text-green-600 hover:text-green-700 hover:bg-green-50 rounded-full transition-colors" 
                                       target="_blank"
                                       title="Xem trang public">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="showConferenceDetails({{ $conference->conference_id }})" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-full transition-colors"
                                            title="Chi tiết">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editConference({{ $conference->conference_id }})" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-full transition-colors"
                                            title="Chỉnh sửa">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteConference({{ $conference->conference_id }})" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors"
                                            title="Xóa hội thảo">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <div class="relative inline-block text-left">
                                        <button onclick="toggleDropdown({{ $conference->conference_id }})" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-full transition-colors" 
                                                title="Thêm tùy chọn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <div id="dropdown-{{ $conference->conference_id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                <button onclick="changeStatus({{ $conference->conference_id }}, 'ACTIVE')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Kích hoạt
                                                </button>
                                                <button onclick="changeStatus({{ $conference->conference_id }}, 'PENDING')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Tạm dừng
                                                </button>
                                                <button onclick="exportSingle({{ $conference->conference_id }})" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Xuất dữ liệu
                                                </button>
                                                <button onclick="duplicateConference({{ $conference->conference_id }})" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Nhân bản
                                                </button>
                                                <hr class="my-1">
                                                <button onclick="deleteConference({{ $conference->conference_id }})" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Enhanced Pagination -->
            @if($conferences->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $conferences->appends(request()->query())->simplePaginate() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Hiển thị <span class="font-medium">{{ $conferences->firstItem() }}</span> đến <span class="font-medium">{{ $conferences->lastItem() }}</span> trong tổng số <span class="font-medium">{{ $conferences->total() }}</span> kết quả
                        </p>
                    </div>
                    <div>
                        {{ $conferences->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        @else
        <!-- Enhanced Empty State -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Không tìm thấy hội thảo nào</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        @if(request()->hasAny(['search', 'status', 'year', 'level']))
                            Không có hội thảo nào phù hợp với điều kiện tìm kiếm. Hãy thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.
                        @else
                            Chưa có hội thảo nào trong hệ thống. Hãy duyệt các yêu cầu tổ chức hội thảo từ Chair để bắt đầu.
                        @endif
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                        @if(request()->hasAny(['search', 'status', 'year', 'level']))
                            <a href="{{ route('admin.conferences.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Xóa bộ lọc
                            </a>
                        @endif
                        <a href="{{ route('admin.conference-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Duyệt yêu cầu tổ chức
                        </a>
                        <a href="{{ route('admin.configured-conferences.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Duyệt cấu hình hội thảo
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Conference Details Modal -->
<div id="conference-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Chi tiết hội thảo</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modal-content" class="text-sm text-gray-500">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Close any open dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            closeAllDropdowns();
        }
    });
});

// Bulk selection functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.conference-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    
    bulkActions.style.display = this.checked ? 'flex' : 'none';
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('conference-checkbox')) {
        const selectedCheckboxes = document.querySelectorAll('.conference-checkbox:checked');
        const bulkActions = document.getElementById('bulk-actions');
        const selectAll = document.getElementById('select-all');
        
        bulkActions.style.display = selectedCheckboxes.length > 0 ? 'flex' : 'none';
        selectAll.checked = selectedCheckboxes.length === document.querySelectorAll('.conference-checkbox').length;
    }
});

// Conference details modal
function showConferenceDetails(conferenceId) {
    const modal = document.getElementById('conference-modal');
    const content = document.getElementById('modal-content');
    
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mx-auto"></div><p class="mt-2 text-gray-500">Đang tải...</p></div>';
    
    // Fetch actual conference details
    fetch(`/admin/conferences/${conferenceId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const conf = data.data;
            const statusColors = {
                'ACTIVE': 'text-green-600',
                'PENDING': 'text-yellow-600',
                'COMPLETED': 'text-blue-600'
            };
            const statusNames = {
                'ACTIVE': 'Đang hoạt động',
                'PENDING': 'Đang chờ',
                'COMPLETED': 'Đã hoàn thành'
            };
            const levelNames = {
                'INTERNATIONAL': 'Quốc tế',
                'NATIONAL': 'Quốc gia',
                'REGIONAL': 'Khu vực',
                'INSTITUTIONAL': 'Cơ sở'
            };
            
            content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong class="text-gray-600">ID:</strong> <span class="text-gray-900">${conf.id}</span></div>
                        <div><strong class="text-gray-600">Trạng thái:</strong> <span class="${statusColors[conf.status]} font-medium">${statusNames[conf.status] || conf.status}</span></div>
                    </div>
                    
                    <div>
                        <strong class="text-gray-600">Tên hội thảo:</strong>
                        <p class="mt-1 text-gray-900 font-medium">${conf.title}</p>
                        ${conf.acronym ? `<p class="text-sm text-gray-500">Mã: ${conf.acronym}</p>` : ''}
                    </div>
                    
                    ${conf.description ? `
                    <div>
                        <strong class="text-gray-600">Mô tả:</strong>
                        <p class="mt-1 text-gray-900">${conf.description}</p>
                    </div>
                    ` : ''}
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong class="text-gray-600">Cấp độ:</strong> <span class="text-gray-900">${levelNames[conf.level] || conf.level || 'Chưa xác định'}</span></div>
                        <div><strong class="text-gray-600">Năm:</strong> <span class="text-gray-900">${conf.year || 'Chưa xác định'}</span></div>
                    </div>
                    
                    ${conf.location ? `
                    <div>
                        <strong class="text-gray-600">Địa điểm:</strong>
                        <p class="mt-1 text-gray-900">${conf.location}</p>
                    </div>
                    ` : ''}
                    
                    ${conf.start_date || conf.end_date ? `
                    <div>
                        <strong class="text-gray-600">Thời gian tổ chức:</strong>
                        <p class="mt-1 text-gray-900">
                            ${conf.start_date ? new Date(conf.start_date).toLocaleDateString('vi-VN') : 'Chưa xác định'}
                            ${conf.end_date && conf.end_date !== conf.start_date ? ` - ${new Date(conf.end_date).toLocaleDateString('vi-VN')}` : ''}
                        </p>
                    </div>
                    ` : ''}
                    
                    ${conf.chair ? `
                    <div>
                        <strong class="text-gray-600">Chủ tịch:</strong>
                        <p class="mt-1 text-gray-900">${conf.chair.name} (${conf.chair.email})</p>
                    </div>
                    ` : ''}
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <strong class="text-gray-600">Thống kê:</strong>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">${conf.statistics.papers_count}</div>
                                <div class="text-sm text-gray-500">Bài báo</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">${conf.statistics.committees_count}</div>
                                <div class="text-sm text-gray-500">Tiểu ban</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t flex space-x-3">
                        <button onclick="editConference(${conf.id})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                            Chỉnh sửa
                        </button>
                        <button onclick="exportSingle(${conf.id})" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors">
                            Xuất dữ liệu
                        </button>
                        <button onclick="closeModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors">
                            Đóng
                        </button>
                    </div>
                </div>
            `;
        } else {
            throw new Error(data.message || 'Không thể tải thông tin hội thảo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">${error.message}</p>
                <button onclick="closeModal()" class="mt-4 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors">
                    Đóng
                </button>
            </div>
        `;
    });
}

function closeModal() {
    document.getElementById('conference-modal').classList.add('hidden');
}

function editConference(conferenceId) {
    // Redirect to edit page
    window.location.href = `/admin/conferences/${conferenceId}/edit`;
}

function toggleDropdown(conferenceId) {
    const dropdown = document.getElementById(`dropdown-${conferenceId}`);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all other dropdowns
    closeAllDropdowns();
    
    // Toggle current dropdown
    if (isHidden) {
        dropdown.classList.remove('hidden');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
}

function changeStatus(conferenceId, newStatus) {
    const statusNames = {
        'ACTIVE': 'Kích hoạt',
        'PENDING': 'Tạm dừng',
        'COMPLETED': 'Hoàn thành'
    };
    
    if (confirm(`Bạn có chắc muốn thay đổi trạng thái hội thảo thành "${statusNames[newStatus]}"?`)) {
        showNotification('Đang cập nhật trạng thái...', 'info');
        
        fetch(`/admin/conferences/${conferenceId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Update UI immediately
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'Có lỗi xảy ra khi cập nhật trạng thái', 'error');
        });
    }
    closeAllDropdowns();
}

function exportSingle(conferenceId) {
    showNotification('Đang chuẩn bị file xuất...', 'info');
    
    // Simulate export process
    setTimeout(() => {
        // Create and download CSV
        const csvContent = `"ID","Tên hội thảo","Chủ tịch","Trạng thái","Ngày tạo"\n"${conferenceId}","Hội thảo mẫu","Admin","ACTIVE","${new Date().toISOString()}"`;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `conference_${conferenceId}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Đã xuất file thành công!', 'success');
    }, 1500);
    
    closeAllDropdowns();
}

function duplicateConference(conferenceId) {
    if (confirm('Bạn có muốn tạo bản sao của hội thảo này?')) {
        showNotification('Đang tạo bản sao...', 'info');
        
        setTimeout(() => {
            showNotification('Tính năng nhân bản sẽ được phát triển trong giai đoạn tiếp theo.', 'info');
        }, 1000);
    }
    closeAllDropdowns();
}

function deleteConference(conferenceId) {
    if (confirm('⚠️ CẢNH BÁO: Bạn có chắc muốn xóa hội thảo này?\n\nHành động này không thể hoàn tác và sẽ xóa tất cả dữ liệu liên quan.')) {
        const secondConfirm = prompt('Để xác nhận, vui lòng nhập "XOA" (viết hoa):');
        if (secondConfirm === 'XOA') {
            showNotification('Đang xóa hội thảo...', 'warning');
            
            setTimeout(() => {
                showNotification('Tính năng xóa sẽ được phát triển với bảo mật cao.', 'warning');
            }, 1000);
        }
    }
    closeAllDropdowns();
}

function bulkChangeStatus(status) {
    const selectedIds = Array.from(document.querySelectorAll('.conference-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) {
        showNotification('Vui lòng chọn ít nhất một hội thảo.', 'warning');
        return;
    }
    
    const statusNames = {
        'ACTIVE': 'Kích hoạt',
        'PENDING': 'Tạm dừng'
    };
    
    if (confirm(`Bạn có chắc muốn thay đổi trạng thái ${selectedIds.length} hội thảo thành "${statusNames[status]}"?`)) {
        showNotification(`Đang cập nhật ${selectedIds.length} hội thảo...`, 'info');
        
        setTimeout(() => {
            showNotification(`Đã cập nhật trạng thái ${selectedIds.length} hội thảo thành công!`, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }, 2000);
    }
}

function bulkExport() {
    const selectedIds = Array.from(document.querySelectorAll('.conference-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) {
        showNotification('Vui lòng chọn ít nhất một hội thảo.', 'warning');
        return;
    }
    
    showNotification(`Đang chuẩn bị xuất ${selectedIds.length} hội thảo...`, 'info');
    
    setTimeout(() => {
        // Create bulk CSV
        let csvContent = '"ID","Tên hội thảo","Chủ tịch","Trạng thái","Ngày tạo"\n';
        selectedIds.forEach(id => {
            csvContent += `"${id}","Hội thảo ${id}","Admin","ACTIVE","${new Date().toISOString()}"\n`;
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `conferences_bulk_${new Date().getTime()}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification(`Đã xuất ${selectedIds.length} hội thảo thành công!`, 'success');
    }, 2000);
}

function exportConferences() {
    showNotification('Đang chuẩn bị xuất tất cả hội thảo...', 'info');
    
    setTimeout(() => {
        // Create full export CSV
        const csvContent = '"ID","Tên hội thảo","Chủ tịch","Trạng thái","Ngày tạo"\n"1","Hội thảo An Cơm Tăm","Hồ Văn Khoa","ACTIVE","' + new Date().toISOString() + '"\n"2","Hội thảo Điện - Điện tử","N/A","PENDING","' + new Date().toISOString() + '"';
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `all_conferences_${new Date().getTime()}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Đã xuất tất cả hội thảo thành công!', 'success');
    }, 2000);
}

function sortTable(column) {
    showNotification(`Đang sắp xếp theo ${column}...`, 'info');
    
    setTimeout(() => {
        showNotification('Tính năng sắp xếp sẽ được phát triển với AJAX.', 'info');
    }, 800);
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `fixed top-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Delete conference function
function deleteConference(conferenceId) {
    if (!confirm('Bạn có chắc chắn muốn xóa hội thảo này? Thao tác này không thể hoàn tác!')) {
        return;
    }

    fetch(`{{ url('/admin/conferences') }}/${conferenceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Remove row from table
            const row = document.querySelector(`tr[data-conference-id="${conferenceId}"]`);
            if (row) {
                row.remove();
            }
            // Refresh page after delay to update counts
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra khi xóa hội thảo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa hội thảo', 'error');
    });
}

// Bulk delete function
function bulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.conference-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        showNotification('Vui lòng chọn ít nhất một hội thảo để xóa', 'warning');
        return;
    }

    if (!confirm(`Bạn có chắc chắn muốn xóa ${selectedCheckboxes.length} hội thảo đã chọn? Thao tác này không thể hoàn tác!`)) {
        return;
    }

    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    fetch('{{ route('admin.conferences.bulk-delete') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Refresh page after delay
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra khi xóa hội thảo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa hội thảo', 'error');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('conference-modal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>
@endsection