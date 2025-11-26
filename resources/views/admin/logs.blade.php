@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0" x-data="logManager()">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight flex items-center">
                            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $title }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Theo dõi và quản lý hoạt động của hệ thống
                        </p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <div class="flex space-x-3">
                            <button @click="exportLogs()" type="button" 
                                    class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Tải xuống
                            </button>
                            <button @click="clearLogs()" type="button" 
                                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Dọn dẹp
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mb-8">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Tổng nhật ký</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ isset($logs) ? number_format($logs->total()) : 0 }}</dd>
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
<svg width="64px" height="64px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#000000" stroke="#000000" stroke-width="1.7280000000000002"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><style>.a{fill:none;stroke:#ffffff;stroke-linecap:round;stroke-linejoin:round;}</style></defs><path class="a" d="M10.78,37.2717H34.76c13.0183,0,10.8419-19.5876-2.2165-15.2348,0-10.882-19.5876-10.882-19.5876,2.1764C2.0743,22.0369,2.0743,37.2717,10.78,37.2717Z"></path><polyline class="a" points="27.273 27.477 24 30.75 20.727 27.477"></polyline><line class="a" x1="24" y1="30.7497" x2="24" y2="20.7517"></line><line class="a" x1="18.2423" y1="32.7517" x2="29.7577" y2="32.7517"></line><line class="a" x1="38.6729" y1="17.5294" x2="38.6729" y2="10.7283"></line><line class="a" x1="35.2724" y1="14.1288" x2="42.0734" y2="14.1288"></line></g></svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Hôm nay</dt>
                                        <dd class="text-lg font-medium text-gray-900" x-text="stats.today_logs || 0">0</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.134 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Lỗi hôm nay</dt>
                                        <dd class="text-lg font-medium text-gray-900" x-text="stats.error_logs_today || 0">0</dd>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Nghiêm trọng</dt>
                                        <dd class="text-lg font-medium text-gray-900" x-text="stats.critical_logs || 0">0</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <form method="GET" id="filter-form" class="bg-white shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900">Bộ lọc & Tìm kiếm</h3>
                        <div class="flex items-center space-x-3">
                            <!-- Quick Search -->
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Tìm kiếm theo hành động, mô tả, IP..." 
                                       class="block w-80 rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-1">
                            <label for="log_type" class="block text-sm font-bold text-gray-700 mb-1">Loại log</label>
                            <select id="log_type" name="log_type" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option value="all">Tất cả loại</option>
                                @if(isset($logTypes))
                                    @foreach($logTypes as $type)
                                        <option value="{{ $type }}" {{ request('log_type') == $type ? 'selected' : '' }}>
                                            {{ match($type) {
                                                'LOGIN' => 'Đăng nhập',
                                                'ACTION' => 'Thao tác', 
                                                'CRUD' => 'Dữ liệu',
                                                'AUTH' => 'Xác thực',
                                                'ERROR' => 'Lỗi',
                                                'SYSTEM' => 'Hệ thống',
                                                'SECURITY' => 'Bảo mật',
                                                'TEST' => 'Kiểm thử',
                                                default => $type
                                            } }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="sm:col-span-1">
                            <label for="severity" class="block text-sm font-bold text-gray-700 mb-1">Mức độ</label>
                            <select id="severity" name="severity" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option value="all">Tất cả mức độ</option>
                                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>Cao</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Nghiêm trọng</option>
                            </select>
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1">Từ ngày</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="end_date" class="block text-sm font-bold text-gray-700 mb-1">Đến ngày</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="sm:col-span-2 flex items-end justify-start space-x-3">
                            <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                                </svg>
                                Tìm kiếm
                            </button>
                            <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-6 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Đặt lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Log Table - Responsive Design -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="min-w-full overflow-hidden overflow-x-auto align-middle">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                            <tr>
                                <!-- Thời gian - Always visible -->
                                <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Thời gian</span>
                                    </div>
                                </th>
                                
                                <!-- Loại - Always visible -->
                                <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span>Loại</span>
                                    </div>
                                </th>
                                
                                <!-- Thao tác - Always visible -->
                                <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span>Thao tác</span>
                                    </div>
                                </th>
                                
                                <!-- Người dùng - Hidden on mobile -->
                                <th scope="col" class="hidden lg:table-cell px-3 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Người dùng</span>
                                    </div>
                                </th>
                                
                                <!-- IP - Hidden on mobile and tablet -->
                                <th scope="col" class="hidden xl:table-cell px-3 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        </svg>
                                        <span>IP Address</span>
                                    </div>
                                </th>
                                
                                <!-- Actions - Always visible -->
                                <th scope="col" class="px-3 py-4 text-center text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                    <div class="flex items-center justify-center space-x-1">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                        <span>Hành động</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($logs))
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150" 
                                        @click="showLogDetail({{ json_encode($log) }})">
                                        
                                        <!-- Thời gian - Always visible -->
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 font-medium">
                                                {{ $log->created_at->format('H:i') }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $log->created_at->format('d/m') }}
                                            </div>
                                        </td>
                                        
                                        <!-- Loại - Always visible -->
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                @php $typeBadge = $log->getTypeBadgeColor(); @endphp
                                                <div class="w-3 h-3 rounded-full {{ $typeBadge['dot'] }}"></div>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $typeBadge['bg'] }} border shadow-sm">
                                                    {{ $log->type_name }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Thao tác - Always visible với tooltip -->
                                        <td class="px-3 py-4">
                                            <div class="text-sm font-bold text-gray-900 max-w-32 truncate" 
                                                 title="{{ $log->action }}">
                                                {{ $log->action }}
                                            </div>
                                            <div class="text-xs text-gray-500 max-w-32 truncate lg:hidden" 
                                                 title="{{ $log->description }}">
                                                {{ $log->description }}
                                            </div>
                                            @if($log->severity)
                                                @php $severityBadge = $log->getSeverityBadgeColor(); @endphp
                                                <div class="flex items-center mt-2">
                                                    <div class="w-2 h-2 rounded-full {{ $severityBadge['dot'] }} mr-2"></div>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold {{ $severityBadge['bg'] }} border shadow-sm">
                                                        {{ $log->severity_name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Người dùng - Hidden on mobile -->
                                        <td class="hidden lg:table-cell px-3 py-4 whitespace-nowrap">
                                            @if($log->nguoiDung)
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                            {{ strtoupper(substr($log->nguoiDung->hoten, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 max-w-32 truncate" 
                                                             title="{{ $log->nguoiDung->hoten ?? 'N/A' }}">
                                                            {{ $log->nguoiDung->hoten ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 max-w-32 truncate" 
                                                             title="{{ $log->nguoiDung->email ?? '' }}">
                                                            {{ $log->nguoiDung->email ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>Hệ thống</span>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- IP - Hidden on mobile and tablet -->
                                        <td class="hidden xl:table-cell px-3 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 font-mono text-xs" title="{{ $log->ip_address ?? 'N/A' }}">
                                                {{ $log->ip_address ?? 'N/A' }}
                                            </div>
                                            @if($log->user_agent)
                                                <div class="text-gray-500 text-xs max-w-24 truncate" 
                                                     title="{{ $log->user_agent }}">
                                                    {{ $log->user_agent }}
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Actions - Always visible -->
                                        <td class="px-3 py-4 whitespace-nowrap text-center" @click.stop>
                                            <button @click="showLogDetail({{ json_encode($log->toArray()) }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition-colors duration-150"
                                                    title="Xem chi tiết">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center space-y-3">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-gray-500 text-lg font-medium">Không có nhật ký nào</p>
                                                    <p class="text-gray-400 text-sm mt-1">Hãy thử điều chỉnh bộ lọc để tìm kiếm</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <!-- Default sample data for when no logs exist -->
                                @for($i = 1; $i <= 5; $i++)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Thời gian -->
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 font-medium">
                                                {{ now()->subHours($i)->format('H:i') }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ now()->subHours($i)->format('d/m') }}
                                            </div>
                                        </td>
                                        
                                        <!-- Loại -->
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 rounded-full {{ $i % 2 == 0 ? 'bg-green-400' : 'bg-blue-400' }}"></div>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $i % 2 == 0 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} border shadow-sm">
                                                    {{ $i % 2 == 0 ? 'Đăng nhập' : 'Thao tác' }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Thao tác -->
                                        <td class="px-3 py-4">
                                            <div class="text-sm font-bold text-gray-900 max-w-32 truncate" title="{{ ['Đăng nhập thành công', 'Tạo mới hội thảo', 'Cập nhật thông tin', 'Xóa dữ liệu'][$i % 4] }}">
                                                {{ ['Đăng nhập', 'Tạo mới', 'Cập nhật', 'Xóa'][$i % 4] }}
                                            </div>
                                            <div class="text-xs text-gray-500 max-w-32 truncate lg:hidden" title="Mô tả hoạt động {{ $i }}">
                                                Mô tả hoạt động {{ $i }}
                                            </div>
                                            <div class="flex items-center mt-2">
                                                <div class="w-2 h-2 rounded-full {{ ['bg-green-400', 'bg-yellow-400', 'bg-orange-400', 'bg-red-400'][$i % 4] }} mr-2"></div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold {{ ['bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-orange-100 text-orange-800', 'bg-red-100 text-red-800'][$i % 4] }} border shadow-sm">
                                                    {{ ['Thấp', 'Trung bình', 'Cao', 'Nghiêm trọng'][$i % 4] }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Người dùng - Hidden on mobile -->
                                        <td class="hidden lg:table-cell px-3 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                        A
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900 max-w-32 truncate" title="Admin {{ $i }}">Admin {{ $i }}</div>
                                                    <div class="text-xs text-gray-500 max-w-32 truncate" title="admin{{ $i }}@example.com">admin{{ $i }}@example.com</div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- IP - Hidden on mobile and tablet -->
                                        <td class="hidden xl:table-cell px-3 py-4 whitespace-nowrap text-sm">
                                            <div class="text-gray-900 font-mono text-xs">192.168.1.{{ $i }}</div>
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="px-3 py-4 whitespace-nowrap text-center">
                                            <button class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition-colors duration-150" title="Xem chi tiết">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(isset($logs) && $logs->hasPages())
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($logs->previousPageUrl())
                                <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Trước
                                </a>
                            @endif
                            @if($logs->nextPageUrl())
                                <a href="{{ $logs->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Sau
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Hiển thị <span class="font-medium">{{ $logs->firstItem() ?? 0 }}</span> 
                                    đến <span class="font-medium">{{ $logs->lastItem() ?? 0 }}</span> 
                                    trong tổng số <span class="font-medium">{{ number_format($logs->total()) }}</span> kết quả
                                </p>
                            </div>
                            <div>
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

    <!-- Loading State -->
    <div x-show="loading" class="bg-white rounded-lg shadow p-8 text-center">
        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Đang tải...</p>
    </div>

    <!-- Log Detail Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         @click="closeModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white" 
             @click.stop x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Chi tiết nhật ký</h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="pt-4 space-y-4" x-show="selectedLog">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Thời gian:</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.created_at"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Người dùng:</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.nguoi_dung?.hoten || 'Hệ thống'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Loại:</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.log_type"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Thao tác:</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.action"></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Mô tả:</label>
                    <p class="text-sm text-gray-900" x-text="selectedLog?.description"></p>
                </div>
                
                <div x-show="selectedLog?.properties">
                    <label class="block text-sm font-semibold text-gray-700">Thông tin bổ sung:</label>
                    <pre class="text-xs text-gray-700 bg-gray-100 p-3 rounded-lg mt-1 overflow-auto max-h-40" 
                         x-text="JSON.stringify(selectedLog?.properties, null, 2)"></pre>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">IP Address:</label>
                        <p class="text-sm text-gray-900 font-mono" x-text="selectedLog?.ip_address || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Mức độ:</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.severity"></p>
                    </div>
                </div>
                
                <div x-show="selectedLog?.user_agent">
                    <label class="block text-sm font-semibold text-gray-700">User Agent:</label>
                    <p class="text-xs text-gray-600 break-all" x-text="selectedLog?.user_agent"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function logManager() {
        return {
            loading: false,
            showModal: false,
            selectedLog: null,
            stats: {},
            
            init() {
                this.loadStats();
            },
            
            async loadStats() {
                try {
                    // Fallback route names if not defined yet
                    let statsUrl = '/admin/logs/stats';
                    if (typeof window.Laravel !== 'undefined' && window.Laravel.routes && window.Laravel.routes['admin.logs.stats']) {
                        statsUrl = window.Laravel.routes['admin.logs.stats'];
                    }
                    
                    const response = await fetch(statsUrl);
                    if (response.ok) {
                        this.stats = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                    // Set default stats
                    this.stats = {
                        today_logs: 0,
                        error_logs_today: 0,
                        critical_logs: 0
                    };
                }
            },
            
            showLogDetail(log) {
                this.selectedLog = log;
                this.showModal = true;
            },
            
            closeModal() {
                this.showModal = false;
                this.selectedLog = null;
            },
            
            async exportLogs() {
                this.loading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    let exportUrl = '/admin/logs/export';
                    if (typeof window.Laravel !== 'undefined' && window.Laravel.routes && window.Laravel.routes['admin.logs.export']) {
                        exportUrl = window.Laravel.routes['admin.logs.export'];
                    }
                    window.location.href = exportUrl + '?' + params.toString();
                } catch (error) {
                    alert('Lỗi khi tải xuống logs');
                } finally {
                    this.loading = false;
                }
            },
            
            async clearLogs() {
                if (!confirm('Bạn có chắc chắn muốn dọn dẹp các logs cũ? Hành động này không thể hoàn tác.')) {
                    return;
                }
                
                this.loading = true;
                try {
                    let clearUrl = '/admin/logs/clear';
                    if (typeof window.Laravel !== 'undefined' && window.Laravel.routes && window.Laravel.routes['admin.logs.clear']) {
                        clearUrl = window.Laravel.routes['admin.logs.clear'];
                    }
                    
                    const response = await fetch(clearUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });
                    
                    const result = await response.json();
                    if (response.ok) {
                        alert(`Đã dọn dẹp ${result.deleted_count} logs`);
                        window.location.reload();
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                } catch (error) {
                    alert('Lỗi khi dọn dẹp logs');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>

        </div>
    </div>
</div>
@endsection