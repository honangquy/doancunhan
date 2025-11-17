@extends('layouts.reviewer')

@section('title', 'Bài báo được phân công')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-2">
            <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bài báo được phân công</h1>
                <p class="text-gray-600">Quản lý các bài báo bạn được phân công phản biện</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Tổng cộng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assignments->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $assignments->where('status', 'PENDING')->count() }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Đã chấp nhận</p>
                    <p class="text-2xl font-bold text-green-600">{{ $assignments->where('status', 'ACCEPTED')->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Hoàn thành</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $assignments->where('status', 'COMPLETED')->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Đã từ chối</p>
                    <p class="text-2xl font-bold text-red-600">{{ $assignments->where('status', 'DECLINED')->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Danh sách phân công</h2>
        </div>

        @forelse($assignments as $assignment)
        <div class="border-b border-gray-200 hover:bg-gray-50 transition-colors" x-data="{ 
            expanded: false,
            status: '{{ $assignment->status }}',
            async updateStatus(newStatus, reason = null) {
                try {
                    const url = newStatus === 'ACCEPTED' 
                        ? `/reviewer/assignments/${this.assignmentId}/accept`
                        : `/reviewer/assignments/${this.assignmentId}/decline`;
                    
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: reason ? JSON.stringify({ reason }) : '{}'
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.status = data.status;
                        location.reload(); // Refresh to update stats
                    }
                } catch (error) {
                    console.error('Failed to update status:', error);
                }
            },
            assignmentId: {{ $assignment->id }}
        }">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $assignment->paper_title }}</h3>
                            @if($assignment->is_revision)
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>REVISION</span>
                                </span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>MỚI</span>
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>ID: {{ $assignment->paper_id }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Assigned by: {{ $assignment->assigned_by_name }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 8h6M4 7h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
                                </svg>
                                <span>{{ $assignment->assigned_at->format('d/m/Y H:i') }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>{{ $assignment->assignment_method ?? 'MANUAL' }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Status Badge -->
                        @php
                            $statusConfig = [
                                'PENDING' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Chờ xử lý'],
                                'ACCEPTED' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Đã chấp nhận'],
                                'COMPLETED' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Hoàn thành'],
                                'DECLINED' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Đã từ chối']
                            ];
                            $config = $statusConfig[$assignment->status] ?? $statusConfig['PENDING'];
                        @endphp
                        
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['class'] }}">
                            {{ $config['text'] }}
                        </span>

                        <!-- Action Buttons -->
                        @if($assignment->status === 'PENDING')
                        <div class="flex space-x-2">
                            <button @click="updateStatus('ACCEPTED')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Chấp nhận</span>
                            </button>
                            <button @click="expanded = !expanded" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Từ chối</span>
                            </button>
                        </div>
                        @endif

                        <a href="{{ route('reviewer.assignments.show', $assignment->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>Chi tiết</span>
                        </a>
                    </div>
                </div>

                <!-- Paper Abstract -->
                @if($assignment->paper_abstract)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Tóm tắt:</strong> 
                        {{ Str::limit($assignment->paper_abstract, 200) }}
                    </p>
                </div>
                @endif

                <!-- Decline Reason Form -->
                <div x-show="expanded" x-transition class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                    <h4 class="font-semibold text-red-800 mb-3">Lý do từ chối phân công</h4>
                    <textarea 
                        x-model="declineReason" 
                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                        rows="3" 
                        placeholder="Vui lòng cho biết lý do từ chối phân công..."
                    ></textarea>
                    <div class="flex justify-end space-x-2 mt-3">
                        <button @click="expanded = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            Hủy
                        </button>
                        <button @click="updateStatus('DECLINED', declineReason); expanded = false" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            Xác nhận từ chối
                        </button>
                    </div>
                </div>

                <!-- Assignment Metadata -->
                @if($assignment->assignment_metadata)
                <div class="flex items-center space-x-4 text-xs text-gray-500 mt-4">
                    @php $metadata = $assignment->assignment_metadata; @endphp
                    @if(isset($metadata['bid_value']))
                    <span class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Bid: {{ $metadata['bid_value'] }}/3</span>
                    </span>
                    @endif
                    @if(isset($metadata['coi_status']))
                    <span class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span>COI: {{ $metadata['coi_status'] ? 'Yes' : 'No' }}</span>
                    </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
            @empty
        <div class="p-12 text-center">
            <div class="mb-4">
                <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Chưa có phân công nào</h3>
            <p class="text-gray-600">Bạn chưa được phân công phản biện bài báo nào.</p>
            <a href="{{ route('reviewer.dashboard') }}" class="inline-block mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Về Dashboard</span>
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection