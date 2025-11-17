@extends('layouts.chair')

@section('title', 'Quyết định cuối cùng')

@section('content')
    <div class="max-w-5xl mx-auto"
         x-data="{
            decision: '{{ old('decision', $existingDecision->decision ?? '') }}',
            comments: '{{ old('comments', $existingDecision->decision_comments ?? '') }}',
            deadlineRevision: '{{ old('deadline_revision', $existingDecision->revision_deadline ?? '') }}',
            showConfirm: false,
            
            get commentLength() {
                return this.comments.length;
            },
            
            get isValid() {
                if (!this.decision) return false;
                if (this.comments.length < 50) return false;
                if (this.decision === 'REVISE' && !this.deadlineRevision) return false;
                return true;
            },
            
            submitForm() {
                if (!this.isValid) {
                    alert('Vui lòng điền đầy đủ thông tin!');
                    return;
                }
                this.showConfirm = true;
            },
            
            confirmSubmit() {
                document.getElementById('decisionForm').submit();
            }
         }">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-4">
                <a href="{{ route('chair.papers.show', $paper->paper_id) }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Quay lại
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Quyết định cuối cùng
                </h1>
                <p class="text-gray-600">Hãy đưa ra quyết định cho bài báo dựa trên các nhận xét của reviewer</p>
            </div>
        </div>

        <!-- Paper Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <svg class="w-5 h-5 inline-block mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Thông tin bài báo
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tiêu đề:</span>
                            <p class="text-gray-900 font-medium">{{ $paper->title }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tác giả:</span>
                            <p class="text-gray-900">{{ $paper->author_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <p class="text-gray-900">{{ $paper->author_email }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Hội thảo:</span>
                            <p class="text-gray-900">{{ $paper->conference_title }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Trạng thái:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $paper->status_name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Summary -->
        @if($reviewsData && $reviewsData->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <svg class="w-5 h-5 inline-block mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Tổng quan nhận xét
            </h3>
            
            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-lg font-bold text-blue-600">{{ $totalReviews }}</div>
                    <div class="text-xs text-gray-600">Tổng số</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <div class="text-lg font-bold text-yellow-600">{{ number_format($avgScore, 1) }}</div>
                    <div class="text-xs text-gray-600">Điểm TB</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-lg font-bold text-green-600">{{ $acceptCount }}</div>
                    <div class="text-xs text-gray-600">Chấp nhận</div>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <div class="text-lg font-bold text-red-600">{{ $rejectCount }}</div>
                    <div class="text-xs text-gray-600">Từ chối</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-lg font-bold text-gray-600">{{ $totalReviews - $acceptCount - $rejectCount }}</div>
                    <div class="text-xs text-gray-600">Khác</div>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="space-y-4">
                <h4 class="font-medium text-gray-900">Chi tiết đánh giá từng reviewer:</h4>
                @foreach($reviewsData as $review)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">{{ $review->reviewer_name }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Điểm: {{ $review->score }}</span>
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $review->recommendation_code === 'ACCEPT' ? 'bg-green-100 text-green-800' : 
                                       ($review->recommendation_code === 'REJECT' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $review->recommendation_code }}
                                </span>
                            </div>
                        </div>
                        @if($review->summary_comments)
                            <p class="text-sm text-gray-700">{{ Str::limit($review->summary_comments, 200) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-800 font-medium">Chưa có đánh giá nào hoàn thành</span>
            </div>
        </div>
        @endif

        <!-- Decision Form -->
        @if($existingDecision)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-blue-800 font-medium">Bạn đã đưa ra quyết định cho bài báo này trước đó.</span>
            </div>
        </div>
        @endif

        <form id="decisionForm" method="POST" action="{{ route('chair.papers.decision.store', $paper->paper_id) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                <svg class="w-5 h-5 inline-block mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Quyết định của chair
            </h3>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-red-800">Có lỗi xảy ra:</span>
                    </div>
                    <ul class="list-disc list-inside text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Decision Radio Buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Quyết định *</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'ACCEPT' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-300'">
                        <input type="radio" name="decision" value="ACCEPT" x-model="decision" class="w-5 h-5 text-green-600">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="font-medium text-gray-900">Chấp nhận (Accept)</p>
                            </div>
                            <p class="text-sm text-gray-600 ml-7">Bài báo đạt chất lượng và được chấp nhận</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'REVISE' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 hover:border-yellow-300'">
                        <input type="radio" name="decision" value="REVISE" x-model="decision" class="w-5 h-5 text-yellow-600">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <p class="font-medium text-gray-900">Yêu cầu sửa lại (Revise)</p>
                            </div>
                            <p class="text-sm text-gray-600 ml-7">Bài báo cần sửa đổi theo nhận xét của reviewer</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'REJECT' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-300'">
                        <input type="radio" name="decision" value="REJECT" x-model="decision" class="w-5 h-5 text-red-600">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <p class="font-medium text-gray-900">Từ chối (Reject)</p>
                            </div>
                            <p class="text-sm text-gray-600 ml-7">Bài báo không đạt yêu cầu và bị từ chối</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Revision Deadline (only if REVISE) -->
            <div x-show="decision === 'REVISE'" x-transition class="mb-6">
                <label for="deadline_revision" class="block text-sm font-medium text-gray-700 mb-2">
                    Hạn chót sửa lại *
                </label>
                <input type="date" 
                       id="deadline_revision" 
                       name="deadline_revision" 
                       x-model="deadlineRevision"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-600">Thời gian tác giả có thể sửa lại bài báo</p>
            </div>

            <!-- Comments -->
            <div class="mb-6">
                <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                    Nhận xét của chair *
                    <span class="text-xs text-gray-500">(tối thiểu 50 ký tự)</span>
                </label>
                <textarea id="comments" 
                          name="comments" 
                          rows="6" 
                          x-model="comments"
                          placeholder="Hãy viết nhận xét chi tiết cho quyết định của bạn. Điều này sẽ giúp tác giả hiểu rõ lý do và cách cải thiện bài báo..."
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          required></textarea>
                <div class="flex justify-between items-center mt-2">
                    <p class="text-sm" 
                       :class="commentLength >= 50 ? 'text-green-600' : 'text-red-600'">
                        <span x-text="commentLength"></span>/50 ký tự tối thiểu
                    </p>
                    <p class="text-xs text-gray-500"><span x-text="commentLength"></span>/5000</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('chair.papers.show', $paper->paper_id) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Hủy
                </a>
                <button type="button" 
                        @click="submitForm()"
                        :disabled="!isValid"
                        :class="isValid ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-6 py-2 rounded-lg text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    Lưu quyết định
                </button>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div x-show="showConfirm" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Xác nhận quyết định</h3>
                <p class="text-gray-600 mb-6">Bạn có chắc chắn muốn đưa ra quyết định này không? Hành động này không thể hoàn tác.</p>
                
                <div class="flex justify-end space-x-4">
                    <button @click="showConfirm = false" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button @click="confirmSubmit()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection