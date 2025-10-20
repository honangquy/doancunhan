@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Include notification component -->
@include('components.notification')

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-600">Chào mừng bạn đến với trang quản trị</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-right">
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng người dùng</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Conferences -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng hội thảo</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_conferences'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Papers -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng bài báo</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_papers'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Join Requests -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Yêu cầu chờ duyệt</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $joinRequestStats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Pending Join Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="joinRequestManager()">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Yêu cầu tham gia chờ duyệt</h2>
                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $joinRequestStats['pending'] ?? 0 }} yêu cầu</span>
            </div>
        </div>
        
        @if(isset($pendingJoinRequests) && $pendingJoinRequests->count() > 0)
        <div class="divide-y divide-gray-200">
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Phân bổ vai trò người dùng</h2>
        </div>
        
        <div class="p-6">
            @if(isset($userRoles) && $userRoles->count() > 0)
            <div class="space-y-4">
                @foreach($userRoles as $roleData)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full 
                            {{ $roleData->role === 'ADMIN' ? 'bg-red-500' : 
                               ($roleData->role === 'CHAIR' ? 'bg-blue-500' : 
                               ($roleData->role === 'REVIEWER' ? 'bg-yellow-500' : 
                               ($roleData->role === 'AUTHOR' ? 'bg-green-500' : 'bg-gray-500'))) }}"></div>
                        <span class="text-sm font-medium text-gray-900">
                            @if($roleData->role === 'ADMIN') 
                                Quản trị viên
                            @elseif($roleData->role === 'CHAIR') 
                                Chủ tịch
                            @elseif($roleData->role === 'REVIEWER') 
                                Phản biện
                            @elseif($roleData->role === 'AUTHOR') 
                                Tác giả
                            @elseif($roleData->role === 'USER') 
                                Người dùng
                            @else 
                                {{ $roleData->role }}
                            @endif
                        </span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $roleData->count }} người</span>
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
</script>
@endsection