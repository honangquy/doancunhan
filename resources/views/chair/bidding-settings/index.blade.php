@extends('layouts.chair')

@section('title', 'Cài đặt Bidding')
@section('page-title', 'Cài đặt Bidding')

@push('styles')
<link href="{{ asset('css/animations.css') }}" rel="stylesheet">
@endpush

@section('content')
<div x-data="biddingSettingsApp()" x-init="init()" class="space-y-6 animate-fade-in-up">
    
    <!-- Conference Selection -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Chọn Hội thảo</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="conference in conferences" :key="conference.conference_id">
                <div @click="selectConference(conference.conference_id)" 
                     :class="selectedConference == conference.conference_id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                     class="border-2 rounded-lg p-4 cursor-pointer transition-all">
                    <h3 class="font-medium text-gray-900" x-text="conference.title"></h3>
                    <p class="text-sm text-gray-600 mt-1" x-text="conference.description || 'Không có mô tả'"></p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-xs text-gray-500" x-text="new Date(conference.start_date).toLocaleDateString('vi-VN')"></span>
                        <span :class="conference.status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                              class="px-2 py-1 text-xs rounded-full" x-text="conference.status"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Bidding Settings Panel -->
    <div x-show="selectedConference" x-transition class="bg-white rounded-xl shadow-sm">
        <!-- Header with Statistics Toggle -->
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Cài đặt Bidding</h2>
            <button @click="showStatistics = !showStatistics"
                    :class="showStatistics ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all hover:shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span x-text="showStatistics ? 'Ẩn thống kê' : 'Xem thống kê'"></span>
            </button>
        </div>

        <!-- Statistics Panel -->
        <div x-show="showStatistics && statistics" x-transition class="p-6 bg-gray-50 border-b border-gray-200">
            <h3 class="font-medium text-gray-900 mb-4">Thống kê Bidding</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600" x-text="statistics?.total_papers || 0"></div>
                    <div class="text-sm text-gray-600">Tổng bài báo</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" x-text="statistics?.total_reviewers || 0"></div>
                    <div class="text-sm text-gray-600">Tổng reviewer</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600" 
                         x-text="statistics?.keyword_matching_enabled ? 'Bật' : 'Tắt'"></div>
                    <div class="text-sm text-gray-600">Lọc từ khóa</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600"
                         x-text="statistics?.reviewer_stats ? Math.round(statistics.reviewer_stats.reduce((sum, r) => sum + r.visible_papers, 0) / statistics.reviewer_stats.length) : 0"></div>
                    <div class="text-sm text-gray-600">TB bài/reviewer</div>
                </div>
            </div>

            <!-- Reviewer Details Table -->
            <div x-show="statistics?.reviewer_stats?.length" class="mt-4">
                <h4 class="font-medium text-gray-700 mb-3">Chi tiết theo Reviewer</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Tên Reviewer</th>
                                <th class="px-4 py-2 text-left">Từ khóa chuyên môn</th>
                                <th class="px-4 py-2 text-center">Bài báo hiển thị</th>
                                <th class="px-4 py-2 text-center">Tổng bài báo</th>
                                <th class="px-4 py-2 text-center">Tỷ lệ (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="reviewer in statistics?.reviewer_stats" :key="reviewer.reviewer_name">
                                <tr>
                                    <td class="px-4 py-2 font-medium" x-text="reviewer.reviewer_name"></td>
                                    <td class="px-4 py-2 text-xs text-gray-600" x-text="reviewer.expertise_keywords || 'Không có'"></td>
                                    <td class="px-4 py-2 text-center font-medium" x-text="reviewer.visible_papers"></td>
                                    <td class="px-4 py-2 text-center text-gray-600" x-text="reviewer.total_papers"></td>
                                    <td class="px-4 py-2 text-center">
                                        <span :class="reviewer.visible_papers / reviewer.total_papers * 100 > 50 ? 'text-green-600' : reviewer.visible_papers / reviewer.total_papers * 100 > 25 ? 'text-yellow-600' : 'text-red-600'"
                                              class="font-medium"
                                              x-text="Math.round(reviewer.visible_papers / reviewer.total_papers * 100)"></span>%
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                
                <!-- Enable Keyword Matching -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="font-medium text-gray-900">Lọc bài báo theo từ khóa</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Khi bật, reviewer chỉ thấy các bài báo có từ khóa trùng khớp với chuyên môn của họ
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="settings.enable_keyword_matching" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Advanced Settings (shown when keyword matching is enabled) -->
                <div x-show="settings.enable_keyword_matching" x-transition class="space-y-4 border rounded-lg p-4 bg-blue-50">
                    
                    <!-- Partial Match Option -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Cho phép khớp từng phần</label>
                            <p class="text-sm text-gray-600">Bài báo sẽ hiển thị nếu có chứa một phần của từ khóa</p>
                        </div>
                        <input type="checkbox" x-model="settings.allow_partial_keyword_match" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>

                    <!-- Similarity Threshold -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            Ngưỡng độ tương đồng (0.0 - 1.0)
                        </label>
                        <input type="number" x-model="settings.keyword_similarity_threshold" 
                               min="0" max="1" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-600 mt-1">
                            Mức độ tương đồng tối thiểu giữa từ khóa reviewer và bài báo (hiện tại chưa sử dụng)
                        </p>
                    </div>

                    <!-- Excluded Keywords -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            Từ khóa loại trừ (cách nhau bởi dấu phẩy)
                        </label>
                        <textarea x-model="settings.excluded_keywords" rows="3"
                                  placeholder="ví dụ: machine learning, artificial intelligence, deep learning"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="text-sm text-gray-600 mt-1">
                            Các bài báo có chứa những từ khóa này sẽ bị ẩn khỏi tất cả reviewer
                        </p>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="loadSettings()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Khôi phục
                    </button>
                    <button type="submit" :disabled="saving"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium shadow-md hover:shadow-lg">
                        <span x-show="!saving" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Lưu cài đặt
                        </span>
                        <span x-show="saving" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang lưu...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="fixed top-4 right-4 max-w-sm p-4 border rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg x-show="messageType === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg x-show="messageType === 'error'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span x-text="message"></span>
        </div>
    </div>
</div>

<script>
function biddingSettingsApp() {
    return {
        conferences: @json($conferences),
        selectedConference: null,
        settings: {
            enable_keyword_matching: false,
            keyword_similarity_threshold: 0.5,
            allow_partial_keyword_match: true,
            excluded_keywords: ''
        },
        statistics: null,
        showStatistics: false,
        saving: false,
        message: '',
        messageType: 'success',

        init() {
            // Auto-select first conference if available
            if (this.conferences.length > 0) {
                this.selectConference(this.conferences[0].conference_id);
            }
        },

        async selectConference(conferenceId) {
            this.selectedConference = conferenceId;
            await this.loadSettings();
            await this.loadStatistics();
        },

        async loadSettings() {
            if (!this.selectedConference) return;
            
            try {
                const response = await fetch(`/chair/conferences/${this.selectedConference}/bidding-settings`);
                const data = await response.json();
                
                if (data.success) {
                    this.settings = data.settings;
                } else {
                    this.showMessage('Không thể tải cài đặt', 'error');
                }
            } catch (error) {
                console.error('Error loading settings:', error);
                this.showMessage('Lỗi khi tải cài đặt', 'error');
            }
        },

        async saveSettings() {
            if (!this.selectedConference) return;
            
            this.saving = true;
            try {
                const response = await fetch(`/chair/conferences/${this.selectedConference}/bidding-settings`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.settings)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showMessage(data.message, 'success');
                    await this.loadStatistics(); // Reload statistics after saving
                } else {
                    this.showMessage(data.message || 'Không thể lưu cài đặt', 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                this.showMessage('Lỗi khi lưu cài đặt', 'error');
            } finally {
                this.saving = false;
            }
        },

        async loadStatistics() {
            if (!this.selectedConference) return;
            
            try {
                const response = await fetch(`/chair/conferences/${this.selectedConference}/bidding-statistics`);
                const data = await response.json();
                
                if (data.success) {
                    this.statistics = data.statistics;
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        },

        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>
@endsection