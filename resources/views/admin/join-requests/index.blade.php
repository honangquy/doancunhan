@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu tham gia')

@push('styles')
<style>
    /* Animation keyframes */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -200px 0;
        }
        100% {
            background-position: calc(200px + 100%) 0;
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .animate-slideInLeft {
        animation: slideInLeft 0.5s ease-out;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-hover {
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-hover:before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .btn-hover:hover:before {
        width: 300px;
        height: 300px;
    }
</style>
@endpush

@section('content')
<!-- Include notification component -->
@include('components.notification')

<div x-data="joinRequestsManager()" x-init="init()">
    <!-- Page Header with gradient background -->
    <div class="relative bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-xl shadow-2xl p-8 mb-8 text-white overflow-hidden animate-fadeInUp">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-32 h-32 bg-white opacity-5 rounded-full"></div>
        
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2 animate-slideInLeft">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-green-100">
                        Quản lý yêu cầu tham gia
                    </span>
                </h1>
                <p class="text-green-100 text-lg animate-slideInLeft" style="animation-delay: 0.1s;">
                    Xem xét và duyệt các yêu cầu tham gia hội thảo khoa học
                </p>
                <div class="flex items-center mt-4 animate-slideInLeft" style="animation-delay: 0.2s;">
                    <div class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></div>
                    <span class="ml-2 text-sm text-green-200">Hệ thống đang hoạt động</span>
                </div>
            </div>
            <div class="text-right animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-30">
                    <div class="text-4xl font-bold text-white mb-1">{{ $stats['pending'] }}</div>
                    <div class="text-green-100 text-sm font-medium">Yêu cầu chờ duyệt</div>
                    @if($stats['pending'] > 0)
                        <div class="mt-2 inline-flex items-center px-2 py-1 bg-orange-500 text-white text-xs font-medium rounded-full animate-pulse">
                            <span class="w-2 h-2 bg-white rounded-full mr-1"></span>
                            Cần xử lý
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with Enhanced Animations -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Requests Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 card-hover border border-blue-200 animate-fadeInUp" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-blue-700 mb-1" x-text="animatedNumbers.total">{{ $stats['total'] }}</div>
                    <div class="text-blue-600 font-medium">Tổng yêu cầu</div>
                    <div class="text-xs text-blue-500 mt-1">Tất cả thời gian</div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 bg-blue-200 bg-opacity-50 rounded-lg p-2">
                <div class="text-xs text-blue-600 font-medium">Xu hướng: 
                    <span class="text-green-600">↗ +15%</span> so với tháng trước
                </div>
            </div>
        </div>
        
        <!-- Pending Requests Card -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-2xl p-6 card-hover border border-orange-200 animate-fadeInUp" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-orange-700 mb-1" x-text="animatedNumbers.pending">{{ $stats['pending'] }}</div>
                    <div class="text-orange-600 font-medium">Chờ duyệt</div>
                    <div class="text-xs text-orange-500 mt-1">Cần xử lý ngay</div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            @if($stats['pending'] > 0)
                <div class="mt-4 bg-orange-200 bg-opacity-50 rounded-lg p-2 flex items-center">
                    <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse mr-2"></div>
                    <div class="text-xs text-orange-600 font-medium">Ưu tiên xử lý</div>
                </div>
            @else
                <div class="mt-4 bg-green-200 bg-opacity-50 rounded-lg p-2">
                    <div class="text-xs text-green-600 font-medium flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tất cả đã được xử lý
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Approved Requests Card -->
        <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-2xl p-6 card-hover border border-green-200 animate-fadeInUp" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-green-700 mb-1" x-text="animatedNumbers.approved">{{ $stats['approved'] }}</div>
                    <div class="text-green-600 font-medium">Đã duyệt</div>
                    <div class="text-xs text-green-500 mt-1">Thành công</div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 bg-green-200 bg-opacity-50 rounded-lg p-2">
                @php $approvalRate = $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0; @endphp
                <div class="text-xs text-green-600 font-medium">Tỷ lệ duyệt: {{ $approvalRate }}%</div>
            </div>
        </div>
        
        <!-- Rejected Requests Card -->
        <div class="bg-gradient-to-br from-rose-50 to-red-100 rounded-2xl p-6 card-hover border border-red-200 animate-fadeInUp" style="animation-delay: 0.4s;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-red-700 mb-1" x-text="animatedNumbers.rejected">{{ $stats['rejected'] }}</div>
                    <div class="text-red-600 font-medium">Từ chối</div>
                    <div class="text-xs text-red-500 mt-1">Không phù hợp</div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 bg-red-200 bg-opacity-50 rounded-lg p-2">
                @php $rejectionRate = $stats['total'] > 0 ? round(($stats['rejected'] / $stats['total']) * 100) : 0; @endphp
                <div class="text-xs text-red-600 font-medium">Tỷ lệ từ chối: {{ $rejectionRate }}%</div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters with Animations -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 animate-fadeInUp" style="animation-delay: 0.5s;">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Bộ lọc thông minh
                </h3>
                <p class="text-gray-600 text-sm">Tìm kiếm và lọc yêu cầu theo nhiều tiêu chí</p>
            </div>
            <button @click="toggleFilters()" 
                    class="btn-hover px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center space-x-2">
                <span x-text="filtersVisible ? 'Thu gọn' : 'Mở rộng'">Mở rộng</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': filtersVisible}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ route('admin.join-requests.index') }}" class="space-y-6" x-show="filtersVisible" 
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 transform scale-95"
              x-transition:enter-end="opacity-100 transform scale-100">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Search Field -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Tìm kiếm
                    </label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Tên, email, tổ chức..."
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-gray-300">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Trạng thái
                    </label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-gray-300 bg-white">
                        <option value="">Tất cả trạng thái</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Bị từ chối</option>
                    </select>
                </div>
                
                <!-- Role Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Vai trò
                    </label>
                    <select name="role" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-gray-300 bg-white">
                        <option value="">Tất cả vai trò</option>
                        <option value="AUTHOR" {{ request('role') === 'AUTHOR' ? 'selected' : '' }}>✍️ Tác giả</option>
                        <option value="REVIEWER" {{ request('role') === 'REVIEWER' ? 'selected' : '' }}>Phản biện viên</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Thao tác</label>
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="flex-1 btn-hover px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>Lọc</span>
                        </button>
                        <a href="{{ route('admin.join-requests.index') }}" 
                           class="px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Filter Tags -->
            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-600 font-medium">Lọc nhanh:</span>
                <button type="button" @click="applyQuickFilter('status', 'PENDING')" 
                        class="px-3 py-1 bg-orange-100 text-orange-700 text-sm rounded-full hover:bg-orange-200 transition-colors">
                     Chờ duyệt ({{ $stats['pending'] }})
                </button>
                <button type="button" @click="applyQuickFilter('role', 'AUTHOR')" 
                        class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full hover:bg-blue-200 transition-colors">
                     Tác giả
                </button>
                <button type="button" @click="applyQuickFilter('role', 'REVIEWER')" 
                        class="px-3 py-1 bg-purple-100 text-purple-700 text-sm rounded-full hover:bg-purple-200 transition-colors">
                    Phản biện viên
                </button>
            </div>
        </form>
    </div>

    <!-- Enhanced Join Requests List -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 animate-fadeInUp" style="animation-delay: 0.6s;">
        <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 flex items-center" style="font-size: 18px;">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Danh sách yêu cầu tham gia
                    </h2>
                    <p class="text-gray-600 mt-1" style="font-size: 14px;">Tổng cộng {{ $joinRequests->total() }} yêu cầu</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="refreshData()" 
                            class="btn-hover px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" :class="{'animate-spin': isRefreshing}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Làm mới</span>
                    </button>
                    <button class="btn-hover px-4 py-2 bg-green-100 text-green-700 rounded-xl hover:bg-green-200 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Xuất Excel</span>
                    </button>
                </div>
            </div>
        </div>

        @if($joinRequests->count() > 0)
            <!-- Modern Card-based Layout -->
            <div class="p-8 space-y-6" x-show="!isLoading">
                @foreach($joinRequests as $index => $request)
                    <div class="card-hover bg-gradient-to-r from-white via-gray-50 to-white border border-gray-200 rounded-2xl p-6 animate-fadeInUp" 
                         style="animation-delay: {{ 0.1 * ($index + 1) }}s;">
                        
                        <!-- Request Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <!-- Enhanced Avatar -->
                                <div class="relative">
                                    @php
                                        $avatarColors = [
                                            'AUTHOR' => 'from-blue-500 to-blue-600',
                                            'REVIEWER' => 'from-purple-500 to-purple-600'
                                        ];
                                        $gradientClass = $avatarColors[$request->role] ?? 'from-gray-500 to-gray-600';
                                    @endphp
                                    <div class="w-16 h-16 bg-gradient-to-br {{ $gradientClass }} rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                        {{ strtoupper(substr($request->full_name ?? 'U', 0, 2)) }}
                                    </div>
                                    @if($request->status === 'PENDING')
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-orange-500 border-2 border-white rounded-full animate-pulse"></div>
                                    @endif
                                </div>
                                
                                <!-- User Info -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-sm font-bold text-gray-900" style="font-size: 14px;">{{ $request->full_name }}</h3>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $request->role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $request->role === 'AUTHOR' ? 'Tác giả' : 'Phản biện viên' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $request->email_contact }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ $request->organization ?? 'Không rõ tổ chức' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            @php
                                $statusConfig = match($request->status) {
                                    'PENDING' => ['bg-gradient-to-r from-amber-100 to-orange-100 border-orange-300', 'text-orange-800', 'Chờ duyệt', 'animate-pulse', 'clock'],
                                    'APPROVED' => ['bg-gradient-to-r from-emerald-100 to-green-100 border-green-300', 'text-green-800', 'Đã duyệt', '', 'check'],
                                    'REJECTED' => ['bg-gradient-to-r from-rose-100 to-red-100 border-red-300', 'text-red-800', 'Từ chối', '', 'x'],
                                    default => ['bg-gray-100 border-gray-300', 'text-gray-800', 'Không rõ', '', 'question']
                                };
                            @endphp
                            <div class="text-right">
                                <div class="inline-flex items-center px-4 py-2 {{ $statusConfig[0] }} {{ $statusConfig[1] }} text-sm font-semibold rounded-xl border {{ $statusConfig[3] }}">
                                    @if($statusConfig[4] === 'check')
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($statusConfig[4] === 'x')
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @elseif($statusConfig[4] === 'clock')
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($statusConfig[4] === 'question')
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                    {{ $statusConfig[2] }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <!-- Conference Info -->
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $request->conference->title ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Mã: {{ $request->conference->code ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <button @click="viewRequest({{ json_encode($request) }})" 
                                        class="btn-hover px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-xl hover:bg-blue-200 transition-all duration-200 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Chi tiết</span>
                                </button>
                                
                                @if($request->status === 'PENDING')
                                    <button @click="processRequest({{ $request->id }}, 'approve')" 
                                            :disabled="processing === {{ $request->id }}"
                                            class="btn-hover px-4 py-2 bg-green-100 text-green-700 font-medium rounded-xl hover:bg-green-200 transition-all duration-200 flex items-center space-x-2 disabled:opacity-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="processing !== {{ $request->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="processing === {{ $request->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Duyệt</span>
                                    </button>
                                    <button @click="processRequest({{ $request->id }}, 'reject')" 
                                            :disabled="processing === {{ $request->id }}"
                                            class="btn-hover px-4 py-2 bg-red-100 text-red-700 font-medium rounded-xl hover:bg-red-200 transition-all duration-200 flex items-center space-x-2 disabled:opacity-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="processing !== {{ $request->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="processing === {{ $request->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Từ chối</span>
                                    </button>
                                @endif
                            </div>
                            
                            <!-- Request ID & Time -->
                            <div class="text-right">
                                <div class="text-xs text-gray-500">ID: #{{ $request->id }}</div>
                                <div class="text-xs text-gray-400">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Loading Skeleton -->
            <div class="p-8 space-y-6" x-show="isLoading">
                @for($i = 0; $i < 3; $i++)
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 animate-pulse">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-300 rounded-2xl"></div>
                                <div>
                                    <div class="h-6 bg-gray-300 rounded-lg w-48 mb-2"></div>
                                    <div class="h-4 bg-gray-300 rounded w-64"></div>
                                </div>
                            </div>
                            <div class="h-8 bg-gray-300 rounded-xl w-24"></div>
                        </div>
                        <div class="bg-gray-100 rounded-xl p-4 mb-6">
                            <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                        </div>
                        <div class="flex space-x-3">
                            <div class="h-10 bg-gray-300 rounded-xl w-20"></div>
                            <div class="h-10 bg-gray-300 rounded-xl w-16"></div>
                            <div class="h-10 bg-gray-300 rounded-xl w-20"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Pagination -->
            @if($joinRequests->hasPages())
                <div class="p-6 border-t border-gray-200">
                    {{ $joinRequests->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có yêu cầu nào</h3>
                <p class="text-gray-600">Chưa có yêu cầu tham gia hội thảo nào.</p>
            </div>
        @endif
    </div>

    <!-- Enhanced Request Detail Modal -->
    <div x-show="showDetailModal" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         style="display: none;"
         @click.self="showDetailModal = false">
        
        <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-gray-200 modal-content"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0">
            
            <!-- Modal Header -->
            <div class="bg-blue-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold" style="font-size: 14px;">Chi tiết yêu cầu tham gia</h3>
                            <p class="text-blue-100 text-xs" style="font-size: 12px;">Xem và xử lý yêu cầu tham gia hội thảo</p>
                        </div>
                    </div>
                    <button @click="showDetailModal = false" 
                            class="p-2 hover:bg-white/20 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <template x-if="selectedRequest">
                    <div class="space-y-6">
                        <!-- Basic Information Card -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center" style="font-size: 14px;">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Thông tin cơ bản
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                         x-text="selectedRequest?.full_name ? selectedRequest.full_name.charAt(0).toUpperCase() : 'U'">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500" style="font-size: 12px;">Họ và tên</label>
                                        <p class="text-sm font-semibold text-gray-900" style="font-size: 14px;" x-text="selectedRequest?.full_name || 'Không có'"></p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Email liên hệ</label>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-900 text-sm" style="font-size: 14px;" x-text="selectedRequest?.email_contact || 'Không có'"></p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Vai trò yêu cầu</label>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                         :class="selectedRequest?.role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        <span style="font-size: 14px;" x-text="selectedRequest?.role === 'AUTHOR' ? 'Tác giả' : 'Phản biện viên'"></span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Trạng thái</label>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                         :class="{
                                             'bg-yellow-100 text-yellow-800': selectedRequest?.status === 'PENDING',
                                             'bg-green-100 text-green-800': selectedRequest?.status === 'APPROVED',
                                             'bg-red-100 text-red-800': selectedRequest?.status === 'REJECTED'
                                         }">
                                        <span style="font-size: 14px;" x-text="selectedRequest?.status === 'PENDING' ? 'Chờ duyệt' : 
                                                      selectedRequest?.status === 'APPROVED' ? 'Đã duyệt' : 'Đã từ chối'"></span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Ngày yêu cầu</label>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-900" style="font-size: 14px;" x-text="selectedRequest?.created_at ? new Date(selectedRequest.created_at).toLocaleDateString('vi-VN') : 'Không có'"></p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Ngày xử lý</label>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-gray-900" style="font-size: 14px;" x-text="selectedRequest?.updated_at && selectedRequest?.status !== 'PENDING' ? new Date(selectedRequest.updated_at).toLocaleDateString('vi-VN') : 'Chưa xử lý'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conference Information -->
                        <div class="bg-green-50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center" style="font-size: 14px;">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Thông tin hội thảo
                            </h4>
                            
                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <h5 class="text-sm font-semibold text-gray-900" style="font-size: 14px;" x-text="selectedRequest?.conference?.title || 'Không có thông tin'"></h5>
                                <div class="mt-2 flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        Mã: <span x-text="selectedRequest?.conference?.conference_code || 'N/A'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Role-Specific Details -->
                        <div x-show="selectedRequest?.role === 'AUTHOR'" class="bg-blue-50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center" style="font-size: 14px;">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                Chi tiết tác giả
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Quốc gia</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.country || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Đơn vị công tác</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.organization || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Khoa/Phòng ban</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.department || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Lĩnh vực nghiên cứu</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.field_of_study || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Chức danh/Học vị</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.academic_title || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-blue-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Số điện thoại</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.phone || 'Không có'"></p>
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedRequest?.role === 'REVIEWER'" class="bg-purple-50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center" style="font-size: 14px;">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Chi tiết phản biện viên
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-purple-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Đơn vị công tác</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.organization || 'Không có'"></p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-purple-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-1" style="font-size: 12px;">Số bài tối đa có thể phản biện</label>
                                    <p class="text-gray-900 font-medium" style="font-size: 14px;" x-text="selectedRequest?.max_papers || 'Không giới hạn'"></p>
                                </div>
                                <div class="md:col-span-2 bg-white rounded-lg p-4 border border-purple-200">
                                    <label class="block text-xs font-medium text-gray-500 mb-2" style="font-size: 12px;">Từ khóa chuyên môn</label>
                                    <p class="text-gray-900 font-medium leading-relaxed" style="font-size: 14px;" x-text="selectedRequest?.expertise_keywords || 'Không có'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <template x-if="selectedRequest?.notes || selectedRequest?.admin_notes">
                            <div class="bg-yellow-50 rounded-xl p-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center" style="font-size: 14px;">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Ghi chú
                                </h4>
                                
                                <div class="space-y-4">
                                    <div x-show="selectedRequest?.notes" class="bg-white rounded-lg p-4 border border-yellow-200">
                                        <label class="block text-xs font-medium text-gray-500 mb-2" style="font-size: 12px;">Ghi chú của người yêu cầu</label>
                                        <p class="text-gray-900 leading-relaxed" style="font-size: 14px;" x-text="selectedRequest?.notes"></p>
                                    </div>
                                    
                                    <div x-show="selectedRequest?.admin_notes" class="bg-white rounded-lg p-4 border border-yellow-200">
                                        <label class="block text-xs font-medium text-gray-500 mb-2" style="font-size: 12px;">Ghi chú từ admin</label>
                                        <p class="text-gray-900 leading-relaxed" style="font-size: 14px;" x-text="selectedRequest?.admin_notes"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            
            <!-- Modal Footer with Actions -->
            <template x-if="selectedRequest && selectedRequest.status === 'PENDING'">
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex gap-3 justify-end">
                        <button @click="showDetailModal = false"
                                class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors focus:outline-none">
                            Đóng
                        </button>
                        <button @click="processRequest(selectedRequest.id, 'reject'); showDetailModal = false"
                                :disabled="processing === selectedRequest.id"
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="processing !== selectedRequest.id" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Từ chối
                            </span>
                            <span x-show="processing === selectedRequest.id" class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang xử lý...
                            </span>
                        </button>
                        <button @click="processRequest(selectedRequest.id, 'approve'); showDetailModal = false"
                                :disabled="processing === selectedRequest.id"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="processing !== selectedRequest.id" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Duyệt yêu cầu
                            </span>
                            <span x-show="processing === selectedRequest.id" class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang xử lý...
                            </span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function joinRequestsManager() {
        return {
            // Modal states
            showDetailModal: false,
            selectedRequest: null,
            
            // UI states
            filtersVisible: true,
            isLoading: false,
            isRefreshing: false,
            processing: null,
            
            // Animated counters
            animatedNumbers: {
                total: 0,
                pending: 0,
                approved: 0,
                rejected: 0
            },

            // Initialize component
            init() {
                this.animateNumbers();
                this.setupKeyboardShortcuts();
            },

            // Animate numbers on load
            animateNumbers() {
                const targets = {
                    total: {{ $stats['total'] }},
                    pending: {{ $stats['pending'] }},
                    approved: {{ $stats['approved'] }}, 
                    rejected: {{ $stats['rejected'] }}
                };

                Object.keys(targets).forEach(key => {
                    this.animateNumber(key, targets[key]);
                });
            },

            animateNumber(key, target) {
                const duration = 1000;
                const start = 0;
                const startTime = Date.now();
                
                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const easeOutCubic = 1 - Math.pow(1 - progress, 3);
                    
                    this.animatedNumbers[key] = Math.round(start + (target - start) * easeOutCubic);
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                };
                
                requestAnimationFrame(animate);
                return target;
            },

            // Toggle filters visibility
            toggleFilters() {
                this.filtersVisible = !this.filtersVisible;
            },

            // Quick filter application
            applyQuickFilter(type, value) {
                const form = document.querySelector('form');
                const input = form.querySelector(`[name="${type}"]`);
                if (input) {
                    input.value = value;
                    form.submit();
                }
            },

            // Refresh data with animation
            async refreshData() {
                this.isRefreshing = true;
                
                try {
                    await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call
                    location.reload();
                } catch (error) {
                    console.error('Error refreshing data:', error);
                    showError('Lỗi', 'Không thể làm mới dữ liệu');
                } finally {
                    this.isRefreshing = false;
                }
            },

            // Enhanced view request with animations
            viewRequest(request) {
                this.selectedRequest = request;
                this.showDetailModal = true;
                
                // Add subtle animation to modal content
                setTimeout(() => {
                    const modalContent = document.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.classList.add('animate-fadeInUp');
                    }
                }, 100);
            },

            // Enhanced process request with better UX
            async processRequest(requestId, action) {
                console.log('processRequest called:', { requestId, action });
                
                const actionText = action === 'approve' ? 'duyệt' : 'từ chối';
                const actionIcon = action === 'approve' ? 'check' : 'x';
                
                // Set processing state
                this.processing = requestId;
                
                try {
                    // Show beautiful confirmation dialog
                    const confirmed = await this.showConfirmDialog(
                        `Xác nhận ${actionText}`,
                        `Bạn có chắc chắn muốn ${actionText} yêu cầu này không?`,
                        action === 'approve' ? 'success' : 'danger'
                    );
                    
                    if (!confirmed) {
                        this.processing = null;
                        return;
                    }

                    // Get admin notes
                    const notes = await this.showNotesDialog(`Ghi chú cho yêu cầu ${actionText}`);
                    
                    // Show processing notification
                    showInfo(
                        `Đang xử lý...`, 
                        `Đang ${actionText} yêu cầu #${requestId}, vui lòng đợi.`
                    );
                    
                    const response = await fetch(
                        `{{ route('admin.join-requests.process', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', requestId), 
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                action: action,
                                admin_notes: notes
                            })
                        }
                    );
                    
                    const data = await response.json();
                    console.log('Response received:', data);
                    
                    if (data.success) {
                        // Success animation and notification
                        showSuccess(
                            'Thành công!', 
                            `Đã ${actionText} yêu cầu thành công! Trang sẽ tự động cập nhật.`
                        );
                        
                        // Animate the card out
                        this.animateCardRemoval(requestId);
                        
                        // Reload after animation
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Có lỗi xảy ra khi xử lý yêu cầu');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    
                    showError(
                        'Lỗi xử lý', 
                        error.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
                    );
                } finally {
                    this.processing = null;
                }
            },

            // Animate card removal
            animateCardRemoval(requestId) {
                const card = document.querySelector(`[data-request-id="${requestId}"]`);
                if (card) {
                    card.style.transition = 'all 0.5s ease-out';
                    card.style.transform = 'translateX(-100%)';
                    card.style.opacity = '0';
                }
            },

            // Beautiful confirm dialog
            showConfirmDialog(title, message, type = 'info') {
                return new Promise((resolve) => {
                    const colors = {
                        success: 'bg-green-100 border-green-300 text-green-800',
                        danger: 'bg-red-100 border-red-300 text-red-800',
                        info: 'bg-blue-100 border-blue-300 text-blue-800'
                    };

                    const result = confirm(`${title}\n\n${message}`);
                    resolve(result);
                });
            },

            // Notes input dialog
            showNotesDialog(title) {
                return new Promise((resolve) => {
                    const notes = prompt(`${title}\n\nGhi chú (tùy chọn):`);
                    resolve(notes || '');
                });
            },

            // Keyboard shortcuts
            setupKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    // ESC to close modal
                    if (e.key === 'Escape' && this.showDetailModal) {
                        this.showDetailModal = false;
                    }
                    
                    // R to refresh
                    if (e.key === 'r' && e.ctrlKey) {
                        e.preventDefault();
                        this.refreshData();
                    }
                    
                    // F to toggle filters
                    if (e.key === 'f' && e.ctrlKey) {
                        e.preventDefault();
                        this.toggleFilters();
                    }
                });
            }
        };
    }

    // Global utilities
    window.addEventListener('load', () => {
        // Add smooth scrolling
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Add intersection observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        });
        
        document.querySelectorAll('.animate-fadeInUp').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    });
</script>
@endsection