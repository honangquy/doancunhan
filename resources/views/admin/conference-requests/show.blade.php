@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu hội thảo')

@section('content')
<div class="max-w-4xl mx-auto" x-data="conferenceRequestDetail()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                    <a href="{{ route('admin.conference-requests.index') }}" class="hover:text-gray-700">Duyệt yêu cầu</a>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-900">Chi tiết yêu cầu #{{ $request->request_id }}</span>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $request->title }}</h1>
                    
                    <!-- Status Badge -->
                    @if($request->status === 'PENDING')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Chờ duyệt
                        </span>
                    @elseif($request->status === 'APPROVED')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Đã duyệt
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Từ chối
                        </span>
                    @endif
                </div>
            </div>
            
            @if($request->status === 'PENDING')
                <div class="flex items-center space-x-3">
                    <button @click="showApprovalModal('approve')" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Phê duyệt
                    </button>
                    
                    <button @click="showApprovalModal('reject')" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Từ chối
                    </button>
                </div>
            @endif
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin cơ bản</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cấp độ</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $request->level_code === 'KHOA' ? 'Cấp Khoa' : 'Cấp Trường' }}
                            </p>
                        </div>
                        
                        @if($request->faculty_name)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Khoa</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $request->faculty_name }}</p>
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lĩnh vực</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $request->field }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ngày dự kiến</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $request->expected_date ? \Carbon\Carbon::parse($request->expected_date)->format('d/m/Y') : 'Chưa xác định' }}
                            </p>
                        </div>
                        
                        @if($request->affiliation)
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Đơn vị công tác</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $request->affiliation }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mục tiêu hội thảo</label>
                        <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded p-3">
                            {{ $request->objective }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chair Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin Chủ tịch</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $request->chair_fullname ?: ($request->user ? $request->user->name : 'Không xác định') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php $email = $request->chair_email ?: ($request->user ? $request->user->email : null) @endphp
                                @if($email)
                                    <a href="mailto:{{ $email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $email }}
                                    </a>
                                @else
                                    <span class="text-gray-500">Không xác định</span>
                                @endif
                            </p>
                        </div>
                        
                        @if($request->chair_phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $request->chair_phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $request->chair_phone }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Co-Chairs Information -->
            @if($request->coChairs && $request->coChairs->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                            </svg>
                            Đồng chủ tịch (Co-chairs)
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @foreach($request->coChairs as $index => $coChair)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">{{ $index + 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $coChair->fullname }}</p>
                                        <p class="text-sm text-gray-600">
                                            <a href="mailto:{{ $coChair->email }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $coChair->email }}
                                            </a>
                                        </p>
                                        @if($coChair->affiliation)
                                            <p class="text-sm text-gray-500 mt-1">{{ $coChair->affiliation }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Proposal File -->
            @if($request->proposal_file)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tài liệu đề xuất</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $request->proposal_file }}</p>
                                <p class="text-sm text-gray-500">Tài liệu đề xuất hội thảo</p>
                            </div>
                            <a href="{{ route('admin.conference-requests.download', $request->request_id) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Tải xuống
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval/Rejection History -->
            @if($request->status !== 'PENDING')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Lịch sử xử lý</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if($request->status === 'APPROVED')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $request->status === 'APPROVED' ? 'Đã phê duyệt' : 'Đã từ chối' }}
                                    </p>
                                    <span class="text-sm text-gray-500">
                                        {{ $request->approved_at ? \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') : '' }}
                                    </span>
                                </div>
                                @if($request->approval_note)
                                    <div class="mt-2 text-sm text-gray-700 bg-gray-50 rounded p-3">
                                        {{ $request->approval_note }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin yêu cầu</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mã yêu cầu</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">#{{ $request->request_id }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ngày gửi</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'Không xác định' }}
                        </p>
                    </div>
                    
                    @if($request->user)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Người gửi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $request->user->name ?? $request->user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                        </div>
                    @endif
                    
                    @if($request->status !== 'PENDING' && $request->approved_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ngày xử lý</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thao tác nhanh</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('admin.conference-requests.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                        </svg>
                        Quay lại danh sách
                    </a>
                    
                    @if($request->proposal_file)
                        <a href="{{ route('admin.conference-requests.download', $request->request_id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Tải tài liệu
                        </a>
                    @endif
                </div>
            </div>
        </div>
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
                                    <span x-show="actionType === 'approve'">Bạn có chắc chắn muốn phê duyệt yêu cầu "{{ $request->title }}" không?</span>
                                    <span x-show="actionType === 'reject'">Bạn có chắc chắn muốn từ chối yêu cầu "{{ $request->title }}" không?</span>
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
</div>

<script>
function conferenceRequestDetail() {
    return {
        showModal: false,
        actionType: '',
        modalAction: '',

        showApprovalModal(action) {
            this.actionType = action;
            this.modalAction = `/admin/conference-requests/{{ $request->request_id }}/${action}`;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.actionType = '';
            this.modalAction = '';
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection