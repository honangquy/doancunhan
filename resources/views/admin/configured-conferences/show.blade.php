@extends('layouts.admin')

@section('title', 'Chi tiết Hội Thảo - ' . $conference->conference_name)

@section('content')
<div class="py-6" x-data="conferenceDetail()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('admin.configured-conferences.index') }}" class="hover:text-gray-700">Duyệt cấu hình hội thảo</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900">Chi tiết hội thảo</span>
        </nav>

        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $conference->conference_name }}</h1>
                    <div class="mt-2 flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $conference->status === 'PENDING_ADMIN_APPROVAL' ? 'bg-yellow-100 text-yellow-800' : 
                               ($conference->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ $conference->status === 'PENDING_ADMIN_APPROVAL' ? 'Chờ duyệt' : 
                               ($conference->status === 'ACTIVE' ? 'Đã duyệt' : 'Từ chối') }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $conference->conferenceRequest->level_code === 'KHOA' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $conference->conferenceRequest->level_code === 'KHOA' ? 'Cấp Khoa' : 'Cấp Trường' }}
                        </span>
                    </div>
                </div>

                @if($conference->status === 'PENDING_ADMIN_APPROVAL')
                <div class="flex space-x-3">
                    <button @click="approveConference()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Duyệt hội thảo
                    </button>
                    <button @click="rejectConference()"
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Conference Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Thông tin hội thảo</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên hội thảo</label>
                            <p class="text-sm text-gray-900">{{ $conference->conference_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tổ chức</label>
                            <p class="text-sm text-gray-900">{{ $conference->conference_date ? \Carbon\Carbon::parse($conference->conference_date)->format('d/m/Y') : 'Chưa xác định' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chair</label>
                            <p class="text-sm text-gray-900">{{ $conference->chair->name ?? 'Chưa xác định' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã hội thảo</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $conference->acronym ?? 'Chưa tạo' }}</p>
                        </div>
                    </div>

                    @if($conference->conferenceRequest->objective)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mục tiêu hội thảo</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $conference->conferenceRequest->objective }}</p>
                    </div>
                    @endif
                </div>

                <!-- Review Configuration -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Cấu hình đánh giá</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số reviewer mỗi bài</label>
                            <p class="text-sm text-gray-900">{{ $conference->reviewers_per_paper }} reviewer(s)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kiểm tra COI</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $conference->enable_coi_check ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $conference->enable_coi_check ? 'Có' : 'Không' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Deadlines -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Lịch trình</h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-blue-900">Deadline nộp bài</p>
                                    <p class="text-sm text-blue-700">{{ $conference->submission_deadline ? \Carbon\Carbon::parse($conference->submission_deadline)->format('d/m/Y') : 'Chưa xác định' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-4 bg-yellow-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-yellow-900">Deadline phản biện</p>
                                    <p class="text-sm text-yellow-700">{{ $conference->review_deadline ? \Carbon\Carbon::parse($conference->review_deadline)->format('d/m/Y') : 'Chưa xác định' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-4 bg-green-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-green-900">Deadline chỉnh sửa</p>
                                    <p class="text-sm text-green-700">{{ $conference->camera_ready_deadline ? \Carbon\Carbon::parse($conference->camera_ready_deadline)->format('d/m/Y') : 'Chưa xác định' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-purple-900">Công bố kết quả</p>
                                    <p class="text-sm text-purple-700">{{ $conference->result_announcement_deadline ? \Carbon\Carbon::parse($conference->result_announcement_deadline)->format('d/m/Y') : 'Chưa xác định' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Committees -->
                @if($conference->committees && $conference->committees->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Tiểu ban ({{ $conference->committees->count() }})</h3>
                    
                    <div class="space-y-4">
                        @foreach($conference->committees as $committee)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $committee->committee_name }}</h4>
                            @if($committee->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $committee->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Original Request Info -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Yêu cầu gốc</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Tiêu đề:</span>
                            <p class="text-gray-900 mt-1">{{ $conference->conferenceRequest->title }}</p>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700">Lĩnh vực:</span>
                            <p class="text-gray-900 mt-1">{{ $conference->conferenceRequest->field }}</p>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700">Ngày yêu cầu:</span>
                            <p class="text-gray-900 mt-1">{{ $conference->conferenceRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700">Trạng thái yêu cầu:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                Đã duyệt
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Banner -->
                @if($conference->banner_path)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Banner hội thảo</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="{{ Storage::url($conference->banner_path) }}" 
                             alt="Banner {{ $conference->conference_name }}"
                             class="w-full h-32 object-cover rounded-md border border-gray-200">
                    </div>
                </div>
                @endif

                <!-- Timeline -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Lịch sử</h3>
                    
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Yêu cầu được tạo
                                                    <time class="font-medium text-gray-900">{{ $conference->conferenceRequest->created_at->format('d/m/Y H:i') }}</time>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($conference->conferenceRequest->approved_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Admin duyệt yêu cầu
                                                    <time class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($conference->conferenceRequest->approved_at)->format('d/m/Y H:i') }}</time>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            @if($conference->created_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Chair cấu hình hội thảo
                                                    <time class="font-medium text-gray-900">{{ $conference->created_at->format('d/m/Y H:i') }}</time>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $conference->status === 'ACTIVE' ? 'bg-green-500' : ($conference->status === 'REJECTED' ? 'bg-red-500' : 'bg-gray-300') }} flex items-center justify-center ring-8 ring-white">
                                                @if($conference->status === 'ACTIVE')
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @elseif($conference->status === 'REJECTED')
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $conference->status === 'ACTIVE' ? 'Hội thảo được kích hoạt' : 
                                                       ($conference->status === 'REJECTED' ? 'Hội thảo bị từ chối' : 'Chờ Admin duyệt cuối cùng') }}
                                                    @if($conference->status !== 'PENDING_ADMIN_APPROVAL')
                                                        <time class="font-medium text-gray-900">{{ $conference->updated_at->format('d/m/Y H:i') }}</time>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modals -->
    <!-- Approve Modal -->
    <div x-show="showApproveModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50"
         style="display: none;">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Duyệt hội thảo</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bạn có chắc chắn muốn duyệt hội thảo "{{ $conference->conference_name }}"? 
                    Hội thảo sẽ được kích hoạt và hiển thị trên trang chủ.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button @click="showApproveModal = false"
                        type="button"
                        class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Hủy
                </button>
                <button @click="confirmApprove()"
                        type="button"
                        class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Xác nhận duyệt
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50"
         style="display: none;">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Từ chối hội thảo</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Vui lòng nhập lý do từ chối để Chair có thể chỉnh sửa lại cấu hình.
                </p>
                <textarea x-model="rejectReason"
                          rows="3"
                          placeholder="Nhập lý do từ chối..."
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
            </div>
            <div class="flex justify-end space-x-3 mt-4">
                <button @click="showRejectModal = false; rejectReason = ''"
                        type="button"
                        class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Hủy
                </button>
                <button @click="confirmReject()"
                        type="button"
                        :disabled="!rejectReason.trim()"
                        :class="rejectReason.trim() ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-300 cursor-not-allowed'"
                        class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Xác nhận từ chối
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function conferenceDetail() {
    return {
        showApproveModal: false,
        showRejectModal: false,
        rejectReason: '',

        approveConference() {
            this.showApproveModal = true;
        },

        rejectConference() {
            this.showRejectModal = true;
        },

        async confirmApprove() {
            try {
                const response = await fetch(`{{ route('admin.conference-requests.approve-conference', $conference->conference_id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Lỗi khi duyệt hội thảo');
                }
            } catch (error) {
                console.error('Error approving conference:', error);
                alert('Có lỗi xảy ra khi duyệt hội thảo');
            }
        },

        async confirmReject() {
            if (!this.rejectReason.trim()) return;

            try {
                const response = await fetch(`{{ route('admin.conference-requests.reject-conference', $conference->conference_id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reason: this.rejectReason
                    })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Lỗi khi từ chối hội thảo');
                }
            } catch (error) {
                console.error('Error rejecting conference:', error);
                alert('Có lỗi xảy ra khi từ chối hội thảo');
            }
        }
    }
}
</script>
@endsection