@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Add Alpine.js and animations -->
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

<div class="container mx-auto px-4 py-6" x-data="logManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ $title }}
        </h1>
        <div class="flex flex-wrap gap-2">
            <button @click="exportLogs()" 
                    class="bg-green-500 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 hover-lift flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Tải xuống</span>
            </button>
            <button @click="clearLogs()" 
                    class="bg-red-500 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 hover-lift flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Dọn dẹp</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-blue-100">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Tổng nhật ký</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($logs->total()) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-green-100">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6v6m0-6V9a2 2 0 012-2h2a2 2 0 012 2v3"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Hôm nay</p>
                    <p class="text-xl font-semibold text-gray-900" x-text="stats.today_logs || 0">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-red-100">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.134 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Lỗi hôm nay</p>
                    <p class="text-xl font-semibold text-gray-900" x-text="stats.error_logs_today || 0">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-purple-100">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Nghiêm trọng</p>
                    <p class="text-xl font-semibold text-gray-900" x-text="stats.critical_logs || 0">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-lg shadow p-6 mb-6 animate-fadeIn">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Loại log</label>
                <select name="log_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                    <option value="all">Tất cả</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}" {{ request('log_type') == $type ? 'selected' : '' }}>
                            {{ match($type) {
                                'LOGIN' => 'Đăng nhập',
                                'ACTION' => 'Thao tác', 
                                'ERROR' => 'Lỗi',
                                'SYSTEM' => 'Hệ thống',
                                default => $type
                            } }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mức độ</label>
                <select name="severity" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                    <option value="all">Tất cả</option>
                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Thấp</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>Cao</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Đến ngày</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 hover-lift">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                    </svg>
                    Lọc
                </button>
                <a href="{{ route('admin.logs') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </div>
        </div>
    </form>

    <!-- Log Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden animate-fadeIn" x-show="!loading">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Người dùng</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mô tả</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">IP</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-blue-50 transition-colors duration-200 cursor-pointer" 
                            @click="showLogDetail({{ json_encode($log) }})">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $log->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->nguoiDung)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($log->nguoiDung->hoten, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $log->nguoiDung->hoten ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $log->nguoiDung->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">Hệ thống</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full {{ $log->getTypeColor() }}"></div>
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $log->getTypeColor('bg') }}">
                                        {{ $log->getTypeLabel() }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $log->action }}</div>
                                @if($log->severity)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->getSeverityColor() }}">
                                        {{ $log->getSeverityLabel() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ $log->description }}
                                </div>
                                @if($log->subject_type && $log->subject_id)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">{{ $log->ip_address ?? 'N/A' }}</div>
                                @if($log->user_agent)
                                    <div class="text-xs text-gray-500 max-w-xs truncate">
                                        {{ $log->user_agent }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center" @click.stop>
                                <button @click="showLogDetail({{ json_encode($log->toArray()) }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg">Không có nhật ký nào</p>
                                    <p class="text-gray-400 text-sm">Hãy thử điều chỉnh bộ lọc</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
                <!-- Sample log entries -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15 14:30:25</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Đăng nhập
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">admin@example.com</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Đăng nhập thành công</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">192.168.1.1</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900">Xem</button>
                    </td>
                </tr>
                
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15 14:25:10</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Thao tác
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">user@example.com</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tạo hội thảo mới</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">192.168.1.100</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900">Xem</button>
                    </td>
                </tr>
                
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15 14:20:05</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Lỗi
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Kết nối database thất bại</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900">Xem</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Trước
            </a>
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                1
            </a>
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                2
            </a>
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Sau
            </a>
        </nav>
    </div>
</div>
@endsection