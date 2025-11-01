@extends('layouts.reviewer')

@section('title', 'Phân công của tôi - Bidding & COI')
@section('page-title', 'Phân công của tôi')

@push('styles')
<link href="{{ asset('css/animations.css') }}" rel="stylesheet">
@endpush

@section('content')
<div x-data="reviewerBiddingApp()" x-init="init()" class="space-y-6 animate-fade-in-up">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tổng số bài</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_papers"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Đã bidding</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.completed_bids"></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500 transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Phân công</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.assignments"></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500 transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">COI declared</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.coi_count"></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Conference Filter -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Lọc theo hội thảo</h3>
            <button @click="loadData()" class="px-4 py-2 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Làm mới
            </button>
        </div>
        <select x-model="selectedConference" @change="loadPapers()" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">-- Chọn hội thảo --</option>
            <template x-for="conference in conferences" :key="conference.conference_id">
                <option :value="conference.conference_id" x-text="conference.title"></option>
            </template>
        </select>
    </div>

    <!-- Papers List for Bidding -->
    <div class="bg-white rounded-xl shadow-sm" x-show="papers.length > 0" x-transition.opacity>
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Danh sách bài báo - Khai báo Bidding & COI</h3>
            <p class="text-sm text-gray-600 mt-1">Chọn mức độ quan tâm và khai báo xung đột lợi ích (nếu có)</p>
        </div>

        <div class="p-6">
            <!-- Bidding Legend -->
            <div class="mb-6 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Thang điểm Bidding:
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">0</span>
                        <span class="text-sm text-gray-600">Không muốn</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">1</span>
                        <span class="text-sm text-gray-600">Sẵn sàng</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">2</span>
                        <span class="text-sm text-gray-600">Có thể</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">3</span>
                        <span class="text-sm text-gray-600">Rất muốn</span>
                    </div>
                </div>
            </div>

            <!-- Papers Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài báo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tác giả</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bidding</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">COI</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="paper in papers" :key="paper.paper_id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900" x-text="paper.title"></div>
                                    <div class="text-xs text-gray-500" x-text="paper.keywords || 'Không có từ khóa'"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900" x-text="paper.author_names || paper.submitted_by_name"></div>
                                    <div class="text-xs text-gray-500" x-text="paper.author_affiliations || 'Không có thông tin'"></div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center space-x-1">
                                        <template x-for="bid in [0,1,2,3]" :key="bid">
                                            <button @click="setBidding(paper.paper_id, bid)"
                                                    :class="getBiddingButtonClass(paper.bidding_value, bid)"
                                                    class="w-8 h-8 rounded-full text-xs font-semibold transition-all transform hover:scale-110 shadow-sm"
                                                    x-text="bid">
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button @click="toggleCOI(paper.paper_id)"
                                                :class="paper.coi ? 'bg-red-100 text-red-800 ring-2 ring-red-200' : 'bg-gray-100 text-gray-400 hover:bg-red-50 hover:text-red-600'"
                                                class="px-3 py-1 text-xs font-semibold rounded-full border transition-all">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span x-text="paper.coi ? 'Có COI' : 'Không COI'"></span>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button @click="openBiddingModal(paper)" 
                                            class="px-3 py-1 text-xs text-blue-600 border border-blue-600 rounded hover:bg-blue-50 transition">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Save Button -->
            <div class="mt-6 flex justify-end">
                <button @click="saveAllBiddings()" :disabled="loading"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium shadow-md hover:shadow-lg">
                    <span x-show="!loading" class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Lưu tất cả
                    </span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang lưu...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- My Assignments -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Phân công của tôi
            </h3>
            <p class="text-sm text-gray-600 mt-1">Danh sách các bài báo đã được phân công phản biện</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài báo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ngày phân công</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="assignment in assignments" :key="assignment.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900" x-text="assignment.paper_title"></div>
                                <div class="text-xs text-gray-500" x-text="'ID: ' + assignment.paper_id"></div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span :class="getStatusColor(assignment.status)" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full"
                                      x-text="getStatusLabel(assignment.status)">
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-sm text-gray-600" x-text="formatDate(assignment.assigned_at)"></td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button x-show="assignment.status === 'PENDING'" 
                                            @click="respondToAssignment(assignment.id, 'ACCEPTED')"
                                            class="px-3 py-1 text-xs text-green-600 border border-green-600 rounded hover:bg-green-50 transition">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Chấp nhận
                                    </button>
                                    <button x-show="assignment.status === 'PENDING'" 
                                            @click="respondToAssignment(assignment.id, 'DECLINED')"
                                            class="px-3 py-1 text-xs text-red-600 border border-red-600 rounded hover:bg-red-50 transition">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Từ chối
                                    </button>
                                    <button x-show="assignment.status === 'ACCEPTED'" 
                                            class="px-3 py-1 text-xs text-blue-600 border border-blue-600 rounded hover:bg-blue-50 transition">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Bắt đầu review
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="assignments.length === 0">
                        <td colspan="4" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Chưa có phân công nào
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bidding Detail Modal -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div @click.away="showModal = false" 
             class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900">Chi tiết Bidding & COI</h3>
                <p class="text-sm text-gray-600 mt-1" x-text="selectedPaper?.title"></p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Bidding Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Mức độ quan tâm</label>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="(label, value) in biddingLabels" :key="value">
                            <button @click="selectedPaper.bidding_value = parseInt(value)"
                                    :class="selectedPaper?.bidding_value == value ? getBiddingColor(value) + ' ring-2 ring-offset-2 ring-blue-500 transform scale-105' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                    class="p-4 rounded-lg text-sm font-medium transition-all text-center shadow-sm">
                                <div class="font-bold text-lg mb-1" x-text="value"></div>
                                <div x-text="label"></div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- COI Declaration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Khai báo xung đột lợi ích (COI)</label>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center space-x-3 mb-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="selectedPaper.coi" 
                                       class="form-checkbox text-red-600 border-red-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Tôi có xung đột lợi ích với bài báo này</span>
                            </label>
                        </div>
                        
                        <div x-show="selectedPaper?.coi" x-transition class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lý do COI <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="selectedPaper.coi_reason" rows="3" 
                                      placeholder="Vui lòng mô tả chi tiết lý do xung đột lợi ích (ví dụ: đồng tác giả, cùng đơn vị, quan hệ thầy trò...)"
                                      class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Note -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
                    <textarea x-model="selectedPaper.note" rows="2" 
                              placeholder="Thêm ghi chú về chuyên môn, kinh nghiệm liên quan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                <button @click="showModal = false" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </button>
                <button @click="saveBidding(selectedPaper)" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="fixed top-4 right-4 max-w-sm p-4 border rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg x-show="messageType === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="messageType === 'error'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p x-text="message"></p>
        </div>
    </div>
</div>

<script>
function reviewerBiddingApp() {
    return {
        loading: false,
        message: '',
        messageType: 'success',
        showModal: false,
        selectedConference: '',
        selectedPaper: null,
        conferences: [],
        papers: [],
        assignments: [],
        stats: {
            total_papers: 0,
            completed_bids: 0,
            assignments: 0,
            coi_count: 0
        },
        biddingLabels: {
            0: 'Không muốn',
            1: 'Sẵn sàng', 
            2: 'Có thể',
            3: 'Rất muốn'
        },

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                // Load conferences
                const conferenceResponse = await fetch('/reviewer/conferences');
                if (conferenceResponse.ok) {
                    const conferenceData = await conferenceResponse.json();
                    this.conferences = conferenceData.conferences || [];
                }

                // Load assignments
                const assignmentResponse = await fetch('/reviewer/assignments');
                if (assignmentResponse.ok) {
                    const assignmentData = await assignmentResponse.json();
                    this.assignments = assignmentData.assignments || [];
                    this.updateStats();
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.showMessage('Có lỗi xảy ra khi tải dữ liệu', 'error');
            }
        },

        async loadPapers() {
            if (!this.selectedConference) {
                this.papers = [];
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/reviewer/conference/${this.selectedConference}/papers`);
                const data = await response.json();
                
                if (data.success) {
                    this.papers = data.papers || [];
                    this.updateStats();
                } else {
                    this.showMessage(data.message || 'Không thể tải danh sách bài báo', 'error');
                }
            } catch (error) {
                console.error('Error loading papers:', error);
                this.showMessage('Có lỗi xảy ra khi tải danh sách bài báo', 'error');
            }
            this.loading = false;
        },

        setBidding(paperId, biddingValue) {
            const paper = this.papers.find(p => p.paper_id === paperId);
            if (paper) {
                paper.bidding_value = biddingValue;
                paper.modified = true;
                this.updateStats();
            }
        },

        toggleCOI(paperId) {
            const paper = this.papers.find(p => p.paper_id === paperId);
            if (paper) {
                paper.coi = !paper.coi;
                if (!paper.coi) {
                    paper.coi_reason = '';
                }
                paper.modified = true;
                this.updateStats();
            }
        },

        openBiddingModal(paper) {
            this.selectedPaper = { ...paper };
            this.showModal = true;
        },

        async saveBidding(paper) {
            if (paper.coi && !paper.coi_reason?.trim()) {
                this.showMessage('Vui lòng nhập lý do COI', 'error');
                return;
            }

            try {
                const response = await fetch('/reviewer/bidding', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        paper_id: paper.paper_id,
                        conference_id: this.selectedConference,
                        bidding_value: paper.bidding_value || 0,
                        coi: paper.coi || false,
                        coi_reason: paper.coi_reason || '',
                        note: paper.note || ''
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update paper in list
                    const index = this.papers.findIndex(p => p.paper_id === paper.paper_id);
                    if (index !== -1) {
                        this.papers[index] = { ...paper, modified: false };
                    }
                    
                    this.showModal = false;
                    this.showMessage('Đã lưu thành công!', 'success');
                    this.updateStats();
                } else {
                    this.showMessage(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error saving bidding:', error);
                this.showMessage('Có lỗi xảy ra khi lưu', 'error');
            }
        },

        async saveAllBiddings() {
            const modifiedPapers = this.papers.filter(p => p.modified);
            
            if (modifiedPapers.length === 0) {
                this.showMessage('Không có thay đổi nào để lưu', 'error');
                return;
            }

            // Check COI validation
            const invalidCOI = modifiedPapers.find(p => p.coi && !p.coi_reason?.trim());
            if (invalidCOI) {
                this.showMessage('Vui lòng nhập lý do COI cho tất cả các bài đã khai báo', 'error');
                return;
            }

            this.loading = true;
            try {
                const biddingData = modifiedPapers.map(paper => ({
                    paper_id: paper.paper_id,
                    conference_id: this.selectedConference,
                    bidding_value: paper.bidding_value || 0,
                    coi: paper.coi || false,
                    coi_reason: paper.coi_reason || '',
                    note: paper.note || ''
                }));

                const response = await fetch('/reviewer/bidding/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ biddings: biddingData })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Mark all as not modified
                    this.papers.forEach(p => p.modified = false);
                    this.showMessage(`Đã lưu thành công ${modifiedPapers.length} thay đổi!`, 'success');
                    this.updateStats();
                } else {
                    this.showMessage(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error saving biddings:', error);
                this.showMessage('Có lỗi xảy ra khi lưu', 'error');
            }
            this.loading = false;
        },

        async respondToAssignment(assignmentId, status) {
            let declineReason = '';
            
            if (status === 'DECLINED') {
                declineReason = prompt('Vui lòng nhập lý do từ chối:');
                if (declineReason === null) return; // User cancelled
            }

            try {
                const response = await fetch(`/reviewer/assignment/${assignmentId}/respond`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status,
                        decline_reason: declineReason
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update assignment in list
                    const assignment = this.assignments.find(a => a.id === assignmentId);
                    if (assignment) {
                        assignment.status = status;
                        assignment.responded_at = new Date().toISOString();
                        if (declineReason) {
                            assignment.decline_reason = declineReason;
                        }
                    }
                    
                    this.showMessage(data.message, 'success');
                    this.updateStats();
                } else {
                    this.showMessage(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error responding to assignment:', error);
                this.showMessage('Có lỗi xảy ra', 'error');
            }
        },

        updateStats() {
            this.stats = {
                total_papers: this.papers.length,
                completed_bids: this.papers.filter(p => (p.bidding_value > 0) || p.coi).length,
                assignments: this.assignments.length,
                coi_count: this.papers.filter(p => p.coi).length
            };
        },

        getBiddingButtonClass(currentBid, buttonBid) {
            if (currentBid == buttonBid) {
                return this.getBiddingColor(buttonBid) + ' ring-2 ring-offset-1';
            }
            return 'bg-gray-100 text-gray-400 hover:bg-gray-200';
        },

        getBiddingColor(bid) {
            const colors = {
                0: 'bg-gray-100 text-gray-800',
                1: 'bg-yellow-100 text-yellow-800',
                2: 'bg-blue-100 text-blue-800', 
                3: 'bg-green-100 text-green-800'
            };
            return colors[bid] || 'bg-gray-100 text-gray-800';
        },

        getStatusColor(status) {
            const colors = {
                'PENDING': 'bg-yellow-100 text-yellow-800',
                'ACCEPTED': 'bg-blue-100 text-blue-800',
                'DECLINED': 'bg-red-100 text-red-800',
                'COMPLETED': 'bg-green-100 text-green-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusLabel(status) {
            const labels = {
                'PENDING': 'Chờ phản hồi',
                'ACCEPTED': 'Đã chấp nhận',
                'DECLINED': 'Đã từ chối', 
                'COMPLETED': 'Hoàn thành'
            };
            return labels[status] || status;
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('vi-VN');
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