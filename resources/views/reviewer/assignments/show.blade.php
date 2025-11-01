@extends('layouts.reviewer')

@section('title', 'Chi tiết phân công')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('reviewer.assignments.index') }}" class="flex items-center space-x-2 text-purple-600 hover:text-purple-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Quay lại danh sách</span>
                </a>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chi tiết phân công</h1>
                <p class="text-gray-600">Thông tin chi tiết về phân công phản biện bài báo</p>
            </div>
        </div>
    </div>

    <!-- Assignment Details -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Thông tin phân công</h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Paper Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Thông tin bài báo</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tiêu đề:</label>
                        <p class="text-gray-900 font-medium mt-1">{{ $assignment->paper_title }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Hội thảo:</label>
                        <p class="text-gray-900 font-medium mt-1">{{ $assignment->conference_name ?? 'Chưa xác định' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tác giả:</label>
                        <p class="text-gray-900 font-medium mt-1">{{ $assignment->assigned_by_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Trạng thái:</label>
                        <div class="mt-1">
                            @php
                                $statusConfig = [
                                    'PENDING' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Chờ xử lý'],
                                    'ACCEPTED' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Đã chấp nhận'],
                                    'DECLINED' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Đã từ chối'],
                                    'COMPLETED' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Hoàn thành']
                                ];
                                $config = $statusConfig[$assignment->status] ?? $statusConfig['PENDING'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['class'] }}">
                                {{ $config['text'] }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($assignment->paper_abstract)
                <div class="mt-6">
                    <label class="text-sm font-medium text-gray-600">Tóm tắt:</label>
                    <p class="text-gray-800 mt-2 leading-relaxed bg-white p-4 rounded-lg border">{{ $assignment->paper_abstract }}</p>
                </div>
                @endif
            </div>

            <!-- Assignment Actions -->
            @if($assignment->status == 'PENDING')
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Thao tác</span>
                </h3>
                <div class="flex space-x-4">
                    <form action="{{ route('reviewer.assignments.accept', $assignment->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center space-x-2 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Chấp nhận phân công</span>
                        </button>
                    </form>
                    
                    <button onclick="openDeclineModal()" class="inline-flex items-center space-x-2 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Từ chối phân công</span>
                    </button>
                </div>
            </div>
            @endif

            @if($assignment->status == 'ACCEPTED')
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Thao tác phản biện</span>
                </h3>
                <div class="flex space-x-4">
                    <a href="#" class="inline-flex items-center space-x-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Tải bài báo</span>
                    </a>
                    
                    <a href="#" class="inline-flex items-center space-x-2 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Nộp phản biện</span>
                    </a>
                </div>
            </div>
            @endif
            </div>
        </div>
    </div>

    <!-- Decline Modal -->
    <div id="declineModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="{{ route('reviewer.assignments.decline', $assignment->id) }}" method="POST">
                    @csrf
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Từ chối phân công
                            </h3>
                        </div>
                        
                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Lý do từ chối (bắt buộc)
                            </label>
                            <textarea name="reason" id="reason" rows="4" required
                                      class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Vui lòng cho biết lý do từ chối phân công này..."></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeDeclineModal()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                                Hủy
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Xác nhận từ chối</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeclineModal() {
    document.getElementById('declineModal').classList.remove('hidden');
}

function closeDeclineModal() {
    document.getElementById('declineModal').classList.add('hidden');
}
</script>
@endpush

@endsection