<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.favicon')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quyết định - {{ $paper->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="main-content max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
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
        
        <!-- Back Button -->
        <div class="mb-6">
            <button onclick="if(window.Alpine && Alpine.$data(document.body).viewPaperDetail) { 
                    Alpine.$data(document.body).viewPaperDetail({{ $paper->paper_id }}); 
                } else { 
                    window.location.href = '{{ route('chair.papers.show', $paper->paper_id) }}'; 
                }"
                class="flex items-center text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại chi tiết bài báo
            </button>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                ⚖️ Quyết định cuối cùng
            </h1>
            <p class="text-gray-600">Đưa ra quyết định chấp nhận/từ chối/sửa lại cho bài báo</p>
        </div>

        <!-- Paper Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="text-sm font-medium text-gray-500">#{{ $paper->paper_id }}</span>
                        @if($paper->status_name)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $paper->status_name }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $paper->title }}</h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span>📚 {{ $paper->conference_title }}</span>
                        <span>👤 {{ $paper->author_name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Tổng quan nhận xét</h3>
            
            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-600 mb-1">Tổng số</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-purple-600 mb-1">Điểm TB</p>
                    <p class="text-2xl font-bold {{ $stats['avg_score'] >= 7 ? 'text-green-600' : ($stats['avg_score'] >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($stats['avg_score'], 1) }}
                    </p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-green-600 mb-1">Chấp nhận</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['accept_count'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-yellow-600 mb-1">Sửa lại</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['revise_count'] }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-red-600 mb-1">Từ chối</p>
                    <p class="text-2xl font-bold text-red-700">{{ $stats['reject_count'] }}</p>
                </div>
            </div>

            <!-- Consensus Indicator -->
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-700 mb-2">Mức độ đồng thuận:</p>
                @if($stats['consensus'] === 'strong_accept')
                <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded">
                    <p class="text-green-800 font-medium">🎉 Đồng thuận cao - Nên chấp nhận</p>
                    <p class="text-green-700 text-sm mt-1">Phần lớn reviewer đồng ý chấp nhận bài báo này.</p>
                </div>
                @elseif($stats['consensus'] === 'accept')
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-green-700 font-medium">✓ Nghiêng về chấp nhận</p>
                    <p class="text-green-600 text-sm mt-1">Đa số reviewer có ý kiến tích cực.</p>
                </div>
                @elseif($stats['consensus'] === 'strong_reject')
                <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-red-800 font-medium">⚠️ Đồng thuận cao - Nên từ chối</p>
                    <p class="text-red-700 text-sm mt-1">Phần lớn reviewer đề nghị từ chối bài báo.</p>
                </div>
                @elseif($stats['consensus'] === 'reject')
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-red-700 font-medium">✗ Nghiêng về từ chối</p>
                    <p class="text-red-600 text-sm mt-1">Đa số reviewer có ý kiến tiêu cực.</p>
                </div>
                @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-yellow-800 font-medium">⚡ Ý kiến trái chiều</p>
                    <p class="text-yellow-700 text-sm mt-1">Reviewer có ý kiến khác nhau. Cần xem xét kỹ từng nhận xét.</p>
                </div>
                @endif
            </div>

            <!-- Individual Reviews -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">📝 Nhận xét từng reviewer:</p>
                <div class="space-y-2">
                    @foreach($reviewsData as $review)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $review->reviewer_name }}</p>
                            @if($review->summary_comments)
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($review->summary_comments, 100) }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4 ml-4">
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Điểm</p>
                                <p class="text-lg font-bold {{ $review->overall_score >= 7 ? 'text-green-600' : ($review->overall_score >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($review->overall_score, 1) }}
                                </p>
                            </div>
                            <div>
                                @if($review->recommendation === 'ACCEPT')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    ✓ ACCEPT
                                </span>
                                @elseif($review->recommendation === 'REJECT')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    ✗ REJECT
                                </span>
                                @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    ↻ REVISE
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Decision Form -->
        @if($existingDecision)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-6">
            <p class="text-yellow-800 font-medium">⚠️ Quyết định đã tồn tại</p>
            <p class="text-yellow-700 text-sm mt-1">Bài báo này đã có quyết định trước đó. Bạn có thể cập nhật quyết định mới.</p>
        </div>
        @endif

        <form id="decisionForm" method="POST" action="{{ route('chair.papers.decision.store', $paper->paper_id) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            
            <!-- Errors -->
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
                <p class="text-red-800 font-medium">Có lỗi xảy ra:</p>
                <ul class="list-disc list-inside text-red-700 text-sm mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <h3 class="text-lg font-bold text-gray-900 mb-6">Quyết định của chủ tịch</h3>

            <!-- Decision Radio Buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Quyết định <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'ACCEPT' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-300'">
                        <input type="radio" name="decision" value="ACCEPT" x-model="decision" class="w-5 h-5 text-green-600">
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900">✓ Chấp nhận (Accept)</p>
                            <p class="text-sm text-gray-600">Bài báo đạt yêu cầu và được chấp nhận tham gia hội thảo</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'REVISE' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 hover:border-yellow-300'">
                        <input type="radio" name="decision" value="REVISE" x-model="decision" class="w-5 h-5 text-yellow-600">
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900">↻ Yêu cầu sửa lại (Revise)</p>
                            <p class="text-sm text-gray-600">Bài báo cần sửa đổi theo nhận xét của reviewer</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                           :class="decision === 'REJECT' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-300'">
                        <input type="radio" name="decision" value="REJECT" x-model="decision" class="w-5 h-5 text-red-600">
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900">✗ Từ chối (Reject)</p>
                            <p class="text-sm text-gray-600">Bài báo không đạt yêu cầu và bị từ chối</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Revision Deadline (only if REVISE) -->
            <div x-show="decision === 'REVISE'" x-collapse class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deadline sửa lại <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="deadline_revision" 
                       x-model="deadlineRevision"
                       :min="new Date().toISOString().split('T')[0]"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Thời hạn để tác giả gửi bản sửa đổi</p>
            </div>

            <!-- Comments -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nhận xét của chủ tịch <span class="text-red-500">*</span>
                </label>
                <textarea name="comments" 
                          x-model="comments"
                          rows="8" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                          placeholder="Nhập nhận xét chi tiết của bạn về bài báo. Giải thích lý do quyết định và các điểm cần cải thiện (nếu có)..."></textarea>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">Tối thiểu 50 ký tự, tối đa 5000 ký tự</p>
                    <p class="text-sm font-medium"
                       :class="commentLength >= 50 ? 'text-green-600' : 'text-red-600'">
                        <span x-text="commentLength"></span> / 50
                        <span x-show="commentLength >= 50">✓</span>
                    </p>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded mb-6">
                <p class="text-orange-800 font-medium mb-2">⚠️ Lưu ý quan trọng:</p>
                <ul class="list-disc list-inside text-orange-700 text-sm space-y-1">
                    <li>Tác giả sẽ nhận được email thông báo về quyết định này</li>
                    <li>Nhận xét của bạn sẽ được gửi cho tác giả</li>
                    <li>Quyết định có thể được cập nhật nếu cần thiết</li>
                    <li>Vui lòng kiểm tra kỹ trước khi gửi</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <button type="button"
                        onclick="if(window.Alpine && Alpine.$data(document.body).viewPaperDetail) { 
                            Alpine.$data(document.body).viewPaperDetail({{ $paper->paper_id }}); 
                        } else { 
                            window.location.href = '{{ route('chair.papers.show', $paper->paper_id) }}'; 
                        }"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Hủy
                </button>
                <button type="button"
                        @click="submitForm()"
                        :disabled="!isValid"
                        :class="isValid ? 'bg-orange-600 hover:bg-orange-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-6 py-3 text-white rounded-lg font-medium transition">
                    💾 Lưu quyết định
                </button>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div x-show="showConfirm" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.away="showConfirm = false" 
                 class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Xác nhận quyết định</h3>
                    <p class="text-gray-600 mb-4">Bạn có chắc chắn muốn lưu quyết định này?</p>
                    <div class="bg-gray-50 rounded-lg p-4 text-left mb-4">
                        <p class="text-sm text-gray-600 mb-1">Quyết định:</p>
                        <p class="font-bold text-gray-900" x-text="decision === 'ACCEPT' ? '✓ Chấp nhận' : (decision === 'REJECT' ? '✗ Từ chối' : '↻ Sửa lại')"></p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button @click="showConfirm = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                        Hủy
                    </button>
                    <button @click="confirmSubmit()"
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
