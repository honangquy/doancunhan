<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Phân công phản biện - {{ $paper->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CRITICAL: Load data BEFORE Alpine.js -->
    <script>
        // Make sure data is available globally
        window.reviewersData = {!! json_encode($availableReviewers) !!};
    </script>
    
    <!-- Load Alpine.js AFTER data is ready -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <main class="container mx-auto p-6" x-data="assignmentComponent()">
        
    <script>
        // Define Alpine component as a function
        function assignmentComponent() {
            return {
                searchQuery: '',
                selectedReviewer: null,
                deadline: '',
                loading: false,
                message: '',
                messageType: '',
                reviewers: window.reviewersData || [],
                
                init() {
                    // Component initialized
                },
                
                get filteredReviewers() {
                    if (!this.searchQuery) return this.reviewers;
                    const query = this.searchQuery.toLowerCase();
                    return this.reviewers.filter(r => 
                        r.full_name.toLowerCase().includes(query) || 
                        r.email.toLowerCase().includes(query) ||
                        (r.organization && r.organization.toLowerCase().includes(query))
                    );
                },
        
        selectReviewer(reviewer) {
            this.selectedReviewer = reviewer;
            this.message = '';
        },
        
        async assignReviewer() {
            if (!this.selectedReviewer) {
                this.showMessage('Vui lòng chọn reviewer', 'error');
                return;
            }
            if (!this.deadline) {
                this.showMessage('Vui lòng chọn hạn chót', 'error');
                return;
            }
            
            if (this.selectedReviewer.has_coi) {
                if (!confirm('Reviewer này có xung đột lợi ích (COI). Bạn có chắc muốn phân công?')) {
                    return;
                }
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('{{ route('chair.papers.assign.store', $paper->paper_id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        reviewer_id: this.selectedReviewer.user_id,
                        deadline: this.deadline
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                this.showMessage('Lỗi kết nối: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async removeAssignment(assignmentId) {
            if (!confirm('Bạn có chắc muốn xóa phân công này?')) return;
            
            this.loading = true;
            
            try {
                const response = await fetch('{{ url('chair/assignments') }}/' + assignmentId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                this.showMessage('Lỗi kết nối: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
            };
        }
    </script>
    
        <!-- Alert Messages -->
        <div x-show="message" 
             x-transition
             :class="{
                 'bg-green-50 border-green-200 text-green-800': messageType === 'success',
                 'bg-red-50 border-red-200 text-red-800': messageType === 'error'
             }"
             class="border rounded-lg p-4 mb-6">
            <p x-text="message"></p>
        </div>

        <!-- Paper Info Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-sm font-medium text-gray-500">ID: #{{ $paper->paper_id }}</span>
                        @php
                            $statusConfig = [
                                'SUBMITTED' => ['label' => 'Đã nộp', 'class' => 'bg-blue-100 text-blue-800'],
                                'UNDER_REVIEW' => ['label' => 'Đang xét duyệt', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'REVIEWED' => ['label' => 'Đã xét duyệt', 'class' => 'bg-purple-100 text-purple-800'],
                                'ACCEPTED' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                'REJECTED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                            ];
                            $status = $statusConfig[$paper->status_code] ?? ['label' => $paper->status_code, 'class' => 'bg-gray-100 text-gray-800'];
                        @endphp
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $paper->title }}</h1>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Hội thảo:</span> {{ $paper->conference_name }}
                    </p>
                </div>
                <a href="{{ route('chair.papers.show', $paper->paper_id) }}" 
                   class="text-sm text-blue-600 hover:text-blue-800">
                    ← Quay lại
                </a>
            </div>

            <!-- Authors -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Tác giả:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($authors as $author)
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                            {{ $author->full_name }}
                            @if($author->is_contact)
                                <span class="ml-1 text-xs text-blue-600">(Liên hệ)</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Current Assignments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Reviewer đã phân công ({{ $currentAssignments->count() }})
            </h2>
            
            @if($currentAssignments->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Chưa có reviewer nào được phân công</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổ chức</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày phân công</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hạn chót</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đã nộp</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($currentAssignments as $assignment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $assignment->reviewer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $assignment->reviewer_email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $assignment->reviewer_org ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusBadge = [
                                                'INVITED' => ['label' => 'Đã mời', 'class' => 'bg-blue-100 text-blue-800'],
                                                'ACCEPTED' => ['label' => 'Đã nhận', 'class' => 'bg-green-100 text-green-800'],
                                                'DECLINED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                                'COMPLETED' => ['label' => 'Hoàn thành', 'class' => 'bg-purple-100 text-purple-800'],
                                            ];
                                            $badge = $statusBadge[$assignment->status_code] ?? ['label' => $assignment->status_code, 'class' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if($assignment->deadline)
                                            {{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($assignment->submitted_at)
                                            <span class="inline-flex items-center text-green-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!$assignment->submitted_at)
                                            <button @click="removeAssignment({{ $assignment->assignment_id }})"
                                                    :disabled="loading"
                                                    class="text-red-600 hover:text-red-800 disabled:opacity-50 text-sm font-medium">
                                                Xóa
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">Không thể xóa</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Available Reviewers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- DEBUG INFO -->
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="font-semibold text-sm mb-2">🔍 Debug Info:</p>
                <p class="text-xs">Backend count: {{ $availableReviewers->count() }}</p>
                <p class="text-xs" x-text="'Alpine reviewers.length: ' + reviewers.length"></p>
                <p class="text-xs" x-text="'Filtered count: ' + filteredReviewers.length"></p>
                <p class="text-xs" x-text="'Search query: ' + (searchQuery || 'empty')"></p>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    Thêm reviewer (<span x-text="reviewers.length"></span> khả dụng)
                </h2>
                <div class="w-64">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Tìm kiếm reviewer..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            @if($availableReviewers->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-sm">Không có reviewer khả dụng</p>
                    <p class="text-xs text-gray-400 mt-1">Tất cả reviewer đã được phân công hoặc là tác giả</p>
                </div>
            @else
                <!-- Assignment Form -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reviewer được chọn
                            </label>
                            <div class="text-sm text-gray-900" x-text="selectedReviewer ? selectedReviewer.full_name : 'Chưa chọn'"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Hạn chót phản biện *
                            </label>
                            <input type="date" 
                                   x-model="deadline"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   @if($paper->deadline_review)
                                       max="{{ $paper->deadline_review }}"
                                   @endif
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button @click="assignReviewer()"
                                    :disabled="loading || !selectedReviewer || !deadline"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
                                <span x-show="!loading">Phân công reviewer</span>
                                <span x-show="loading">Đang xử lý...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reviewers Grid -->
                <div class="mb-4 p-3 bg-gray-100 rounded text-xs">
                    <strong>Alpine.js Debug:</strong>
                    <div>reviewers array exists: <span x-text="reviewers ? 'YES' : 'NO'"></span></div>
                    <div>reviewers.length: <span x-text="reviewers.length"></span></div>
                    <div>filteredReviewers.length: <span x-text="filteredReviewers.length"></span></div>
                    <div>First reviewer: <span x-text="reviewers[0] ? reviewers[0].full_name : 'NONE'"></span></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="reviewer in filteredReviewers" :key="reviewer.user_id">
                        <div @click="selectReviewer(reviewer)"
                             :class="{
                                 'border-blue-500 bg-blue-50': selectedReviewer && selectedReviewer.user_id === reviewer.user_id,
                                 'border-gray-200 hover:border-gray-300': !selectedReviewer || selectedReviewer.user_id !== reviewer.user_id,
                                 'border-red-300': reviewer.has_coi
                             }"
                             class="border-2 rounded-lg p-4 cursor-pointer transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900" x-text="reviewer.full_name"></h3>
                                    <p class="text-xs text-gray-600" x-text="reviewer.email"></p>
                                </div>
                                <div x-show="reviewer.has_coi" class="ml-2">
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-700 text-xs rounded">COI</span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mb-2" x-text="reviewer.organization || 'Không có thông tin'"></div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Khối lượng:</span>
                                <span class="font-medium" x-text="(reviewer.workload || 0) + ' bài'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="filteredReviewers.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">Không tìm thấy reviewer phù hợp</p>
                    </div>
                </template>
            @endif
        </div>
    </main>
</body>
</html>
