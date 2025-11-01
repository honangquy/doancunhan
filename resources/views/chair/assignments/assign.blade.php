@extends('layouts.chair')

@section('title', 'Phân công phản biện - ' . $paper->title)

@section('page-title', 'Phân công phản biện')

@section('page-subtitle', 'Chọn phản biện viên cho bài báo: ' . $paper->title)

@section('content')
<div x-data="paperAssignment()" class="space-y-6">
    <!-- Thông tin bài báo -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin bài báo</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tiêu đề</label>
                        <p class="text-sm text-gray-900">{{ $paper->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tác giả chính</label>
                        <p class="text-sm text-gray-900">{{ $paper->author_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Hội thảo</label>
                        <p class="text-sm text-gray-900">{{ $paper->conference_title }}</p>
                    </div>
                    @if($paper->keywords)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Từ khóa</label>
                        <p class="text-sm text-gray-900">{{ $paper->keywords }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Trạng thái phân công</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Đã phân công:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ count($assignedReviewers) }}/3 reviewers</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" 
                             :style="'width: ' + ({{ count($assignedReviewers) }} / 3 * 100) + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviewers đã được phân công -->
    @if(count($assignedReviewers) > 0)
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Phản biện viên đã phân công</h3>
        <div class="space-y-3">
            @foreach($assignedReviewers as $reviewer)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $reviewer->full_name }}</p>
                        <p class="text-sm text-gray-600">{{ $reviewer->email }}</p>
                        <p class="text-xs text-gray-500">Phân công lúc: {{ date('d/m/Y H:i', strtotime($reviewer->assigned_at)) }}</p>
                    </div>
                </div>
                <button @click="removeAssignment({{ $reviewer->assignment_id }})" 
                        class="px-3 py-1 text-xs text-red-600 border border-red-300 rounded hover:bg-red-50">
                    Xóa
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Chọn reviewers mới -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Chọn phản biện viên</h3>
        
        @if(count($availableReviewers) > 0)
        <form @submit.prevent="assignReviewers()" class="space-y-4">
            <div class="space-y-3">
                <p class="text-sm text-gray-600 mb-3">
                    Chọn tối đa {{ 3 - count($assignedReviewers) }} phản biện viên:
                </p>
                
                @foreach($availableReviewers as $reviewer)
                <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="checkbox" 
                           x-model="selectedReviewers" 
                           value="{{ $reviewer->user_id }}"
                           :disabled="selectedReviewers.length >= maxSelection && !selectedReviewers.includes('{{ $reviewer->user_id }}')"
                           class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $reviewer->full_name }}</p>
                                <p class="text-sm text-gray-600">{{ $reviewer->email }}</p>
                                @if($reviewer->expertise)
                                <p class="text-xs text-gray-500 mt-1">Chuyên môn: {{ $reviewer->expertise }}</p>
                                @endif
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center pt-4">
                <div class="text-sm text-gray-600">
                    Đã chọn: <span x-text="selectedReviewers.length"></span>/{{ 3 - count($assignedReviewers) }}
                </div>
                <div class="space-x-3">
                    <a href="{{ route('chair.assignments.index') }}" 
                       class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit" 
                            :disabled="selectedReviewers.length === 0 || loading"
                            class="px-4 py-2 text-sm text-white bg-orange-600 rounded-lg hover:bg-orange-700 disabled:opacity-50">
                        <span x-show="!loading">Phân công</span>
                        <span x-show="loading">Đang xử lý...</span>
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Không có phản biện viên khả dụng</h3>
            <p class="mt-1 text-sm text-gray-500">
                Tất cả phản biện viên đều đã được phân công hoặc có xung đột lợi ích.
            </p>
        </div>
        @endif
    </div>
</div>

<script>
function paperAssignment() {
    return {
        selectedReviewers: [],
        maxSelection: {{ 3 - count($assignedReviewers) }},
        loading: false,

        async assignReviewers() {
            if (this.selectedReviewers.length === 0) {
                alert('Vui lòng chọn ít nhất một phản biện viên.');
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('{{ route("chair.assignments.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        paper_id: {{ $paper->paper_id }},
                        reviewer_ids: this.selectedReviewers
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Phân công thành công!');
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi phân công.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            } finally {
                this.loading = false;
            }
        },

        async removeAssignment(assignmentId) {
            if (!confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
                return;
            }

            try {
                const response = await fetch('{{ route("chair.assignments.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        assignment_id: assignmentId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Đã xóa phân công thành công!');
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi xóa phân công.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        }
    };
}
</script>
@endsection