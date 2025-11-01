@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slide-up {
    animation: slideInUp 0.8s ease-out forwards;
}

.animate-slide-left {
    animation: slideInLeft 0.8s ease-out forwards;
}

.animate-slide-right {
    animation: slideInRight 0.8s ease-out forwards;
}

.animate-bounce-in {
    animation: bounceIn 0.6s ease-out forwards;
}

.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
.stagger-5 { animation-delay: 0.5s; }
.stagger-6 { animation-delay: 0.6s; }

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.opacity-0 {
    opacity: 0;
}
</style>

<!-- Include notification component -->
@include('components.notification')

<!-- Page Header with Animation -->
<div class="mb-8 opacity-0 animate-slide-up">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-600">Chào mừng bạn đến với hệ thống quản trị hội thảo khoa học</p>
        </div>
    </div>
</div>

<!-- Statistics Cards with Enhanced Animation -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 card-hover opacity-0 animate-bounce-in stagger-1">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tổng người dùng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Hoạt động
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Conferences -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 card-hover opacity-0 animate-bounce-in stagger-2">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tổng hội thảo</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_conferences'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $stats['active_conferences'] ?? 0 }} đang hoạt động
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Papers -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 card-hover opacity-0 animate-bounce-in stagger-3">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tổng bài báo</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_papers'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Chờ duyệt
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 card-hover opacity-0 animate-bounce-in stagger-4">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Yêu cầu chờ duyệt</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Cần xử lý
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section with Animation -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Pending Join Requests -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 card-hover opacity-0 animate-slide-left stagger-5" x-data="joinRequestManager()">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Duyệt yêu cầu tham gia
                </h2>
                <span class="bg-gradient-to-r from-red-400 to-pink-500 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-lg">
                    {{ ($joinRequestStats['pending'] ?? 0) + ($pendingConferenceRequests->count() ?? 0) }} yêu cầu
                </span>
            </div>
        </div>
        
        @if((isset($pendingJoinRequests) && $pendingJoinRequests->count() > 0) || (isset($pendingConferenceRequests) && $pendingConferenceRequests->count() > 0))
        <div class="divide-y divide-gray-200">
            {{-- Hiển thị yêu cầu tham gia hội thảo --}}
            @if(isset($pendingJoinRequests) && $pendingJoinRequests->count() > 0)
                @foreach($pendingJoinRequests as $request)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">{{ substr($request->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $request->full_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $request->email_contact }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $request->role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $request->role === 'AUTHOR' ? 'Tác giả' : 'Phản biện' }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $request->conference_title ?? 'Hội thảo #' . $request->conference_code }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button @click="processRequest({{ $request->id }}, 'approve')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Duyệt
                            </button>
                            <button @click="processRequest({{ $request->id }}, 'reject')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Từ chối
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
            
            {{-- Hiển thị yêu cầu tổ chức hội thảo --}}
            @if(isset($pendingConferenceRequests) && $pendingConferenceRequests->count() > 0)
                @foreach($pendingConferenceRequests as $request)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">{{ substr($request->chair_fullname, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500">Chủ tịch: {{ $request->chair_fullname }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Yêu cầu tổ chức
                                        </span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button onclick="approveConferenceRequest({{ $request->request_id }})" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Phê duyệt
                            </button>
                            <button onclick="rejectConferenceRequest({{ $request->request_id }})" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Từ chối
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        
        <div class="p-4 bg-gray-50 rounded-b-xl">
            <a href="{{ route('admin.join-requests.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                Xem tất cả yêu cầu →
            </a>
        </div>
        @else
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Không có yêu cầu nào</h3>
            <p class="text-sm text-gray-500">Tất cả yêu cầu tham gia đã được xử lý.</p>
        </div>
        @endif
    </div>

    <!-- User Role Distribution -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 card-hover opacity-0 animate-slide-right stagger-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                Phân bố vai trò người dùng
            </h2>
        </div>
        
        <div class="p-6">
            @if(isset($userRoles) && $userRoles->count() > 0)
            <div class="space-y-4">
                @foreach($userRoles as $roleData)
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            @if($roleData->role === 'ADMIN')
                                <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2m6 0V7a2 2 0 00-2-2H9a2 2 0 00-2 2v0m6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0V5"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Quản trị viên</span>
                            @elseif($roleData->role === 'CHAIR')
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Chủ tịch</span>
                            @elseif($roleData->role === 'REVIEWER')
                                <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Phản biện</span>
                            @elseif($roleData->role === 'AUTHOR')
                                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Tác giả</span>
                            @elseif($roleData->role === 'USER')
                                <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Người dùng</span>
                            @else
                                <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">{{ $roleData->role }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-lg font-bold text-gray-900">{{ $roleData->count }}</span>
                        <span class="text-sm text-gray-500">người</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Chưa có dữ liệu</h3>
                <p class="text-sm text-gray-500">Dữ liệu phân bổ vai trò sẽ hiển thị ở đây.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced System Activity & Configuration Section -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Duyệt cấu hình hội thảo - HIDDEN -->
    <div class="hidden bg-white rounded-xl shadow-lg border border-gray-200 card-hover opacity-0 animate-slide-left stagger-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Duyệt cấu hình hội thảo
            </h2>
        </div>
        
        <div class="p-6">
            @if(isset($pendingConferenceRequests) && $pendingConferenceRequests->count() > 0)
            <div class="space-y-3">
                @foreach($pendingConferenceRequests as $request)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $request->title }}</h4>
                        <p class="text-xs text-gray-600">Chủ tịch: {{ $request->chair_fullname ?: 'Hồ Văn Khoa' }}</p>
                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full mt-1 inline-block">
                            Yêu cầu tổ chức
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="approveConferenceRequest({{ $request->request_id }})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition-colors">
                            Phê duyệt
                        </button>
                        <button onclick="rejectConferenceRequest({{ $request->request_id }})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors">
                            Từ chối
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Tất cả đã được duyệt</h3>
                <p class="text-xs text-gray-500">Không có yêu cầu tổ chức hội thảo nào cần duyệt.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Enhanced System Logs -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 card-hover opacity-0 animate-slide-right stagger-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Nhật ký hệ thống
            </h2>
            <span class="text-sm text-gray-500">Hoạt động gần đây</span>
        </div>
        
        <div class="p-6">
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @if(isset($recentLogs) && $recentLogs->count() > 0)
                    @foreach($recentLogs as $log)
                    @php
                        $bgColor = 'bg-gray-50';
                        $iconColor = 'bg-gray-500';
                        $textColor = 'text-gray-600';
                        $icon = '';
                        
                        switch($log->log_type) {
                            case 'LOGIN':
                                $bgColor = 'bg-blue-50';
                                $iconColor = 'bg-blue-500';
                                $textColor = 'text-blue-600';
                                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>';
                                break;
                            case 'ACTION':
                                $bgColor = 'bg-green-50';
                                $iconColor = 'bg-green-500';
                                $textColor = 'text-green-600';
                                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>';
                                break;
                            case 'ERROR':
                                $bgColor = 'bg-red-50';
                                $iconColor = 'bg-red-500';
                                $textColor = 'text-red-600';
                                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                break;
                            case 'SYSTEM':
                                $bgColor = 'bg-purple-50';
                                $iconColor = 'bg-purple-500';
                                $textColor = 'text-purple-600';
                                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                                break;
                            default:
                                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        }
                    @endphp
                    <div class="flex items-start space-x-3 p-3 {{ $bgColor }} rounded-lg">
                        <div class="w-7 h-7 {{ $iconColor }} rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $icon !!}
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                            <p class="text-xs text-gray-600">{{ $log->description }}</p>
                            @if($log->user_name)
                            <p class="text-xs text-gray-500 mt-1">Người dùng: {{ $log->user_name }}</p>
                            @endif
                            <p class="text-xs {{ $textColor }} mt-1">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Chưa có nhật ký</h3>
                        <p class="text-xs text-gray-500">Nhật ký hệ thống sẽ hiển thị ở đây khi có hoạt động.</p>
                    </div>
                @endif
            </div>
            
            <div class="mt-3 pt-3 border-t border-gray-200">
                <a href="{{ route('admin.logs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    Xem tất cả nhật ký
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Join Request Management -->
<script>
function joinRequestManager() {
    return {
        async processRequest(requestId, action) {
            console.log('processRequest called:', { requestId, action });
            
            const actionText = action === 'approve' ? 'duyệt' : 'từ chối';
            const actionIcon = action === 'approve' ? '✓' : '✗';
            
            // Show loading notification
            showInfo(
                `${actionIcon} Đang ${actionText}...`, 
                `Đang xử lý yêu cầu #${requestId}, vui lòng đợi.`
            );

            try {
                const response = await fetch(`{{ route('admin.join-requests.process', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', requestId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        action: action,
                        admin_notes: null 
                    })
                });

                const data = await response.json();
                console.log('Response received:', data);
                
                if (data.success) {
                    showSuccess(
                        '🎉 Thành công!', 
                        `Đã ${actionText} yêu cầu thành công! Trang sẽ tự động tải lại.`
                    );
                    
                    // Reload page after showing notification
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showError(
                        '❌ Lỗi xử lý yêu cầu', 
                        data.message || 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.'
                    );
                }
            } catch (error) {
                console.error('Error:', error);
                
                showError(
                    '🌐 Lỗi kết nối', 
                    'Có lỗi xảy ra khi kết nối với server. Vui lòng thử lại.'
                );
            }
        }
    }
}

// Functions for conference organization request approval
function approveConferenceRequest(requestId) {
    if (!confirm('Bạn có chắc chắn muốn phê duyệt yêu cầu tổ chức hội thảo này?')) {
        return;
    }
    
    fetch(`{{ url('/admin/conference-requests') }}/${requestId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ approval_note: 'Đã duyệt từ dashboard' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('✅ Thành công!', 'Đã phê duyệt yêu cầu tổ chức hội thảo.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showError('❌ Lỗi', data.message || 'Có lỗi xảy ra khi phê duyệt.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('🌐 Lỗi kết nối', 'Không thể kết nối với server.');
    });
}

function rejectConferenceRequest(requestId) {
    const reason = prompt('Lý do từ chối (không bắt buộc):');
    
    if (!confirm('Bạn có chắc chắn muốn từ chối yêu cầu này?')) {
        return;
    }
    
    fetch(`{{ url('/admin/conference-requests') }}/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ approval_note: reason || 'Từ chối từ dashboard' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('✅ Thành công!', 'Đã từ chối yêu cầu tổ chức hội thảo.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showError('❌ Lỗi', data.message || 'Có lỗi xảy ra khi từ chối.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('🌐 Lỗi kết nối', 'Không thể kết nối với server.');
    });
}
</script>
@endsection