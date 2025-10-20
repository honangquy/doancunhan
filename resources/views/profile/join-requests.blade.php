@extends('layouts.app')

@section('title', 'Yêu cầu tham gia hội thảo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Yêu cầu tham gia hội thảo</h1>
                <p class="text-gray-600 mt-2">Theo dõi trạng thái các yêu cầu tham gia hội thảo của bạn</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-orange-600">{{ $joinRequests->total() }}</div>
                <div class="text-sm text-gray-500">Tổng yêu cầu</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            $statusCounts = $joinRequests->groupBy('status');
            $pendingCount = $statusCounts->get('PENDING', collect())->count();
            $approvedCount = $statusCounts->get('APPROVED', collect())->count();
            $rejectedCount = $statusCounts->get('REJECTED', collect())->count();
            $totalCount = $joinRequests->total();
        @endphp
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalCount }}</div>
                    <div class="text-sm text-gray-600">Tổng cộng</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-600">Chờ duyệt</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $approvedCount }}</div>
                    <div class="text-sm text-gray-600">Đã duyệt</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</div>
                    <div class="text-sm text-gray-600">Bị từ chối</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Danh sách yêu cầu</h2>
        </div>

        @if($joinRequests->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($joinRequests as $request)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Conference Info -->
                                <div class="flex items-center mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mr-3">
                                        {{ $request->conference->title ?? 'Hội thảo' }}
                                    </h3>
                                    
                                    <!-- Role Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->role === 'AUTHOR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $request->role === 'AUTHOR' ? 'Tác giả' : 'Phản biện viên' }}
                                    </span>
                                </div>

                                <!-- Request Details -->
                                <div class="text-sm text-gray-600 mb-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div><strong>Họ tên:</strong> {{ $request->full_name }}</div>
                                        <div><strong>Email:</strong> {{ $request->email_contact }}</div>
                                        
                                        @if($request->isAuthorRequest())
                                            <div><strong>Đơn vị:</strong> {{ $request->organization }}</div>
                                            <div><strong>Lĩnh vực:</strong> {{ $request->field_of_study }}</div>
                                        @endif
                                        
                                        @if($request->isReviewerRequest())
                                            <div><strong>Đơn vị:</strong> {{ $request->organization }}</div>
                                            <div><strong>Số bài tối đa:</strong> {{ $request->max_papers }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timestamps -->
                                <div class="text-xs text-gray-500">
                                    <div>Gửi yêu cầu: {{ $request->created_at->format('d/m/Y H:i') }}</div>
                                    @if($request->processed_at)
                                        <div>Xử lý: {{ $request->processed_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </div>

                                <!-- Admin Notes -->
                                @if($request->admin_notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                        <div class="text-sm font-medium text-gray-700">Ghi chú từ admin:</div>
                                        <div class="text-sm text-gray-600 mt-1">{{ $request->admin_notes }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Status -->
                            <div class="ml-4 flex flex-col items-end">
                                @php
                                    $statusConfig = match($request->status) {
                                        'PENDING' => ['bg-yellow-100', 'text-yellow-800', 'Chờ duyệt', 'clock'],
                                        'APPROVED' => ['bg-green-100', 'text-green-800', 'Đã duyệt', 'check'],
                                        'REJECTED' => ['bg-red-100', 'text-red-800', 'Bị từ chối', 'x'],
                                        default => ['bg-gray-100', 'text-gray-800', 'Không xác định', 'question']
                                    };
                                @endphp
                                
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig[0] }} {{ $statusConfig[1] }}">
                                    @if($statusConfig[3] === 'clock')
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($statusConfig[3] === 'check')
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif($statusConfig[3] === 'x')
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                    {{ $statusConfig[2] }}
                                </span>

                                <!-- View Conference Button -->
                                <a href="{{ route('conferences.show', $request->conference->conference_id) }}" 
                                   class="mt-2 text-sm text-orange-600 hover:text-orange-800 font-medium">
                                    Xem hội thảo →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($joinRequests->hasPages())
                <div class="p-6 border-t border-gray-200">
                    {{ $joinRequests->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có yêu cầu tham gia nào</h3>
                <p class="text-gray-600 mb-4">Bạn chưa gửi yêu cầu tham gia hội thảo nào.</p>
                <a href="{{ route('conferences.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Tìm hội thảo
                </a>
            </div>
        @endif
    </div>
</div>
@endsection