@extends('layouts.admin')

@section('title', 'Duyệt yêu cầu hội thảo')

@section('content')
<div class="max-w-7xl mx-auto" x-data="conferenceRequestsManagement()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Duyệt yêu cầu hội thảo</h1>
                <p class="mt-2 text-sm text-gray-600">Quản lý và duyệt các yêu cầu tổ chức hội thảo từ người dùng</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="mt-4 sm:mt-0 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500">Tổng số</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-gray-500">Chờ duyệt</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                    <div class="text-xs text-gray-500">Đã duyệt</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                    <div class="text-xs text-gray-500">Từ chối</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.conference-requests.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">Tìm kiếm</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ $search }}" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm" 
                           placeholder="Tìm kiếm theo tên hội thảo, chủ tịch, email...">
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="sm:w-48">
                <label for="status" class="sr-only">Trạng thái</label>
                <select name="status" id="status" 
                        class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6" x-show="selectedRequests.length > 0" x-cloak>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900">
                        Đã chọn <span x-text="selectedRequests.length"></span> yêu cầu
                    </span>
                    <button @click="clearSelection()" class="ml-4 text-sm text-gray-500 hover:text-gray-700">
                        Bỏ chọn tất cả
                    </button>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button @click="bulkAction('approve')" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Phê duyệt
                    </button>
                    
                    <button @click="bulkAction('reject')" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Từ chối
                    </button>
                    
                    <button @click="bulkAction('delete')" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conference Requests Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($requests->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($requests as $request)
                    <li class="hover:bg-gray-50 transition-colors">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Checkbox -->
                                    <input type="checkbox" 
                                           x-model="selectedRequests" 
                                           value="{{ $request->request_id }}"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    
                                    <!-- Request Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                                {{ $request->title }}
                                            </h3>
                                            
                                            <!-- Status Badge -->
                                            @if($request->status === 'PENDING')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Chờ duyệt
                                                </span>
                                            @elseif($request->status === 'APPROVED')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Đã duyệt
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Từ chối
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $request->chair_fullname ?: ($request->user ? $request->user->name : 'Không xác định') }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $request->chair_email ?: ($request->user ? $request->user->email : 'Không xác định') }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $request->expected_date ? \Carbon\Carbon::parse($request->expected_date)->format('d/m/Y') : 'Chưa xác định' }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-1 text-sm text-gray-500">
                                            <span class="inline-flex items-center">
                                                Lĩnh vực: {{ $request->field }} | 
                                                Cấp độ: {{ $request->level_code === 'KHOA' ? 'Cấp Khoa' : 'Cấp Trường' }}
                                                @if($request->faculty_name)
                                                    | Khoa: {{ $request->faculty_name }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.conference-requests.show', $request->request_id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Xem chi tiết
                                    </a>
                                    
                                    @if($request->status === 'PENDING')
                                        <button @click="showApprovalModal({{ $request->request_id }}, 'approve')" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Duyệt
                                        </button>
                                        
                                        <button @click="showApprovalModal({{ $request->request_id }}, 'reject')" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Từ chối
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có yêu cầu nào</h3>
                <p class="mt-1 text-sm text-gray-500">Chưa có yêu cầu tổ chức hội thảo nào được gửi lên.</p>
            </div>
        @endif
    </div>

    <!-- Approval/Rejection Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="closeModal()"></div>
            </div>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <form :action="modalAction" method="POST">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="actionType === 'approve' ? 'bg-green-100' : 'bg-red-100'">
                            <svg x-show="actionType === 'approve'" class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="actionType === 'reject'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <span x-show="actionType === 'approve'">Phê duyệt yêu cầu</span>
                                <span x-show="actionType === 'reject'">Từ chối yêu cầu</span>
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    <span x-show="actionType === 'approve'">Bạn có chắc chắn muốn phê duyệt yêu cầu này không?</span>
                                    <span x-show="actionType === 'reject'">Bạn có chắc chắn muốn từ chối yêu cầu này không?</span>
                                </p>
                                
                                <div class="mt-4">
                                    <label for="approval_note" class="block text-sm font-medium text-gray-700">
                                        <span x-show="actionType === 'approve'">Ghi chú phê duyệt (tùy chọn):</span>
                                        <span x-show="actionType === 'reject'">Lý do từ chối (bắt buộc):</span>
                                    </label>
                                    <textarea id="approval_note" name="approval_note" rows="3" 
                                              :required="actionType === 'reject'"
                                              class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Nhập ghi chú hoặc lý do..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :class="actionType === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-show="actionType === 'approve'">Phê duyệt</span>
                            <span x-show="actionType === 'reject'">Từ chối</span>
                        </button>
                        <button type="button" @click="closeModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div x-show="showBulkModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showBulkModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="closeBulkModal()"></div>
            </div>

            <div x-show="showBulkModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <form action="{{ route('admin.conference-requests.bulk-action') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" x-model="bulkActionType">
                    <input type="hidden" name="ids" x-model="selectedRequestsInput">
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="bulkActionType === 'approve' ? 'bg-green-100' : bulkActionType === 'reject' ? 'bg-red-100' : 'bg-gray-100'">
                            <svg x-show="bulkActionType === 'approve'" class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="bulkActionType === 'reject'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <svg x-show="bulkActionType === 'delete'" class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <span x-show="bulkActionType === 'approve'">Phê duyệt hàng loạt</span>
                                <span x-show="bulkActionType === 'reject'">Từ chối hàng loạt</span>
                                <span x-show="bulkActionType === 'delete'">Xóa hàng loạt</span>
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    <span x-show="bulkActionType === 'approve'">Bạn có chắc chắn muốn phê duyệt <span x-text="selectedRequests.length"></span> yêu cầu được chọn không?</span>
                                    <span x-show="bulkActionType === 'reject'">Bạn có chắc chắn muốn từ chối <span x-text="selectedRequests.length"></span> yêu cầu được chọn không?</span>
                                    <span x-show="bulkActionType === 'delete'">Bạn có chắc chắn muốn xóa <span x-text="selectedRequests.length"></span> yêu cầu được chọn không? Hành động này không thể hoàn tác.</span>
                                </p>
                                
                                <div class="mt-4" x-show="bulkActionType !== 'delete'">
                                    <label for="bulk_note" class="block text-sm font-medium text-gray-700">
                                        <span x-show="bulkActionType === 'approve'">Ghi chú phê duyệt (tùy chọn):</span>
                                        <span x-show="bulkActionType === 'reject'">Lý do từ chối (bắt buộc):</span>
                                    </label>
                                    <textarea id="bulk_note" name="bulk_note" rows="3" 
                                              :required="bulkActionType === 'reject'"
                                              class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Nhập ghi chú hoặc lý do..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :class="bulkActionType === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : bulkActionType === 'reject' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500'"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-show="bulkActionType === 'approve'">Phê duyệt tất cả</span>
                            <span x-show="bulkActionType === 'reject'">Từ chối tất cả</span>
                            <span x-show="bulkActionType === 'delete'">Xóa tất cả</span>
                        </button>
                        <button type="button" @click="closeBulkModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function conferenceRequestsManagement() {
    return {
        selectedRequests: [],
        showModal: false,
        showBulkModal: false,
        actionType: '',
        bulkActionType: '',
        modalAction: '',
        selectedRequestsInput: '',

        showApprovalModal(requestId, action) {
            this.actionType = action;
            this.modalAction = `/admin/conference-requests/${requestId}/${action}`;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.actionType = '';
            this.modalAction = '';
        },

        bulkAction(action) {
            if (this.selectedRequests.length === 0) {
                alert('Vui lòng chọn ít nhất một yêu cầu.');
                return;
            }
            
            this.bulkActionType = action;
            this.selectedRequestsInput = JSON.stringify(this.selectedRequests);
            this.showBulkModal = true;
        },

        closeBulkModal() {
            this.showBulkModal = false;
            this.bulkActionType = '';
            this.selectedRequestsInput = '';
        },

        clearSelection() {
            this.selectedRequests = [];
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection