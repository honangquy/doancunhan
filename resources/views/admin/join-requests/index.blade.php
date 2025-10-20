@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu tham gia')

@section('content')
<!-- Include notification component -->
@include('components.notification')

<div x-data="joinRequestsManager()">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý yêu cầu tham gia</h1>
                <p class="text-gray-600 mt-2">Xem xét và duyệt các yêu cầu tham gia hội thảo</p>
                
                <!-- Debug Section -->
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">🐛 Debug: 
                        <button onclick="console.log('Testing notifications...'); showSuccess('Test!', 'Notification system works!');" 
                                class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                            Test Notifications
                        </button>
                        <button onclick="console.log('Available functions:', typeof showSuccess, typeof showError, typeof showInfo);" 
                                class="ml-1 px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                            Check Functions
                        </button>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</div>
                <div class="text-sm text-gray-500">Chờ duyệt</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#435fea"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="#2e7eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="#2e7eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600">Tổng yêu cầu</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                    <div class="text-sm text-gray-600">Chờ duyệt</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                    <div class="text-sm text-gray-600">Đã duyệt</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                    <div class="text-sm text-gray-600">Bị từ chối</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tên hoặc email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Tất cả</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Bị từ chối</option>
                </select>
            </div>
            
            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Tất cả</option>
                    <option value="AUTHOR" {{ request('role') === 'AUTHOR' ? 'selected' : '' }}>Tác giả</option>
                    <option value="REVIEWER" {{ request('role') === 'REVIEWER' ? 'selected' : '' }}>Phản biện viên</option>
                </select>
            </div>
            
            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-4 py-2 bg-orange-600 text-white font-medium rounded-md hover:bg-orange-700 transition-colors">
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Join Requests List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Danh sách yêu cầu</h2>
        </div>

        @if($joinRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hội thảo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày gửi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($joinRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ substr($request->full_name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $request->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $request->email_contact }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $request->conference->title ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->conference->code ?? 'N/A' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $request->role === 'AUTHOR' ? 'Tác giả' : 'Phản biện viên' }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = match($request->status) {
                                            'PENDING' => ['bg-yellow-100', 'text-yellow-800', 'Chờ duyệt'],
                                            'APPROVED' => ['bg-green-100', 'text-green-800', 'Đã duyệt'],
                                            'REJECTED' => ['bg-red-100', 'text-red-800', 'Bị từ chối'],
                                            default => ['bg-gray-100', 'text-gray-800', 'Không xác định']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig[0] }} {{ $statusConfig[1] }}">
                                        {{ $statusConfig[2] }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button @click="viewRequest({{ json_encode($request) }})" 
                                                class="text-orange-600 hover:text-orange-900 font-medium">
                                            Chi tiết
                                        </button>
                                        
                                        @if($request->status === 'PENDING')
                                            <button @click="processRequest({{ $request->id }}, 'approve')" 
                                                    class="text-green-600 hover:text-green-900 font-medium">
                                                Duyệt
                                            </button>
                                            <button @click="processRequest({{ $request->id }}, 'reject')" 
                                                    class="text-red-600 hover:text-red-900 font-medium">
                                                Từ chối
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    <!-- Request Detail Modal -->
    <div x-show="showDetailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
             @click.away="showDetailModal = false">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Chi tiết yêu cầu tham gia</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div x-show="selectedRequest">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                            <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.full_name"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.email_contact"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hội thảo</label>
                            <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.conference?.title"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="selectedRequest?.role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                      x-text="selectedRequest?.role === 'AUTHOR' ? 'Tác giả' : 'Phản biện viên'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Role-specific Details -->
                    <div x-show="selectedRequest?.role === 'AUTHOR'" class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Chi tiết tác giả</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quốc gia</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.country"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Đơn vị công tác</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.organization"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Khoa/Phòng ban</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.department"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lĩnh vực</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.field_of_study"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Chức danh/Học vị</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.academic_title"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.phone"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="selectedRequest?.role === 'REVIEWER'" class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Chi tiết phản biện viên</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Đơn vị công tác</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.organization"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số bài tối đa</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.max_papers"></div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Từ khóa chuyên môn</label>
                                <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.expertise_keywords"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div x-show="selectedRequest?.notes" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Ghi chú</label>
                        <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.notes"></div>
                    </div>
                    
                    <!-- Admin Notes -->
                    <div x-show="selectedRequest?.admin_notes" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Ghi chú admin</label>
                        <div class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.admin_notes"></div>
                    </div>
                    
                    <!-- Actions for pending requests -->
                    <div x-show="selectedRequest?.status === 'PENDING'" class="border-t pt-6">
                        <div class="flex space-x-3">
                            <button @click="processRequest(selectedRequest.id, 'approve')"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors">
                                Duyệt yêu cầu
                            </button>
                            <button @click="processRequest(selectedRequest.id, 'reject')"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors">
                                Từ chối yêu cầu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function joinRequestsManager() {
        return {
            showDetailModal: false,
            selectedRequest: null,
            
            viewRequest(request) {
                this.selectedRequest = request;
                this.showDetailModal = true;
            },
            
            processRequest(requestId, action) {
                console.log('processRequest called:', { requestId, action });
                
                const actionText = action === 'approve' ? 'duyệt' : 'từ chối';
                const actionIcon = action === 'approve' ? '✓' : '✗';
                
                // Get notes first (optional)
                const notes = prompt(`💬 Ghi chú cho người dùng (tùy chọn):`);
                
                // Show loading notification
                showInfo(
                    `${actionIcon} Đang ${actionText}...`, 
                    `Đang xử lý yêu cầu #${requestId}, vui lòng đợi.`
                );
                
                fetch(`{{ route('admin.join-requests.process', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', requestId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        action: action,
                        admin_notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response received:', data);
                    
                    if (data.success) {
                        showSuccess(
                            '🎉 Thành công!', 
                            `Đã ${actionText} yêu cầu thành công! Trang sẽ tự động tải lại.`
                        );
                        
                        // Reload page after a short delay to show notification
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showError(
                            '❌ Lỗi xử lý yêu cầu', 
                            data.message || 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    showError(
                        '🌐 Lỗi kết nối', 
                        'Có lỗi xảy ra khi kết nối với server. Vui lòng kiểm tra kết nối mạng và thử lại.'
                    );
                });
            }
        };
    }
</script>
@endsection