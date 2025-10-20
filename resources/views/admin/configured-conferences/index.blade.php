@extends('layouts.admin')

@section('title', 'Duyệt Cấu Hình Hội Thảo')

@section('content')
<div class="py-6" x-data="configuredConferences()">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Duyệt cấu hình hội thảo
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Các hội thảo đã được Chair cấu hình và chờ Admin duyệt cuối cùng
                </p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Chờ duyệt</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $conferences->where('status', 'PENDING_ADMIN_APPROVAL')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Đã duyệt</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $conferences->where('status', 'ACTIVE')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Từ chối</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $conferences->where('status', 'REJECTED')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tổng số</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $conferences->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" 
                           id="search" 
                           x-model="search"
                           placeholder="Tên hội thảo..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select id="status" 
                            x-model="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="PENDING_ADMIN_APPROVAL">Chờ duyệt</option>
                        <option value="ACTIVE">Đã duyệt</option>
                        <option value="REJECTED">Từ chối</option>
                    </select>
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Cấp độ</label>
                    <select id="level" 
                            x-model="levelFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tất cả cấp độ</option>
                        <option value="KHOA">Cấp Khoa</option>
                        <option value="TRUONG">Cấp Trường</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button @click="resetFilters()" 
                            type="button"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Đặt lại
                    </button>
                </div>
            </div>
        </div>

        <!-- Conferences List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <template x-if="filteredConferences.length === 0">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có hội thảo nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Không tìm thấy hội thảo phù hợp với bộ lọc.</p>
                    </div>
                </template>

                <div class="space-y-6">
                    <template x-for="conference in filteredConferences" :key="conference.conference_id">
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-medium text-gray-900" x-text="conference.conference_name"></h3>
                                        <span :class="getStatusBadgeClass(conference.status)" 
                                              x-text="getStatusText(conference.status)"
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                                        <span :class="getLevelBadgeClass(conference.conference_request?.level_code)" 
                                              x-text="getLevelText(conference.conference_request?.level_code)"
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-4">
                                        <div>
                                            <span class="font-medium">Chair:</span>
                                            <span x-text="conference.chair?.name || 'Chưa xác định'"></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Ngày tổ chức:</span>
                                            <span x-text="formatDate(conference.conference_date)"></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Số reviewer/bài:</span>
                                            <span x-text="conference.reviewers_per_paper"></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Deadline nộp bài:</span>
                                            <span x-text="formatDate(conference.submission_deadline)"></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">COI Check:</span>
                                            <span x-text="conference.enable_coi_check ? 'Có' : 'Không'" 
                                                  :class="conference.enable_coi_check ? 'text-green-600' : 'text-gray-600'"></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Tiểu ban:</span>
                                            <span x-text="conference.committees?.length || 0"></span>
                                        </div>
                                    </div>

                                    <div x-show="conference.conference_request?.objective" class="mb-4">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Mục tiêu:</span>
                                            <span x-text="conference.conference_request?.objective"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-2 ml-4">
                                    <a :href="`{{ route('admin.configured-conferences.show', '') }}/${conference.conference_id}`"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Chi tiết
                                    </a>

                                    <template x-if="conference.status === 'PENDING_ADMIN_APPROVAL'">
                                        <div class="flex flex-col space-y-2">
                                            <button @click="approveConference(conference.conference_id)"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Duyệt
                                            </button>
                                            <button @click="rejectConference(conference.conference_id)"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Từ chối
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
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
                    Bạn có chắc chắn muốn duyệt hội thảo này? Hội thảo sẽ được kích hoạt và hiển thị trên trang chủ.
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
                    Vui lòng nhập lý do từ chối để Chair có thể chỉnh sửa lại.
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
function configuredConferences() {
    return {
        conferences: @json($conferences),
        search: '',
        statusFilter: '',
        levelFilter: '',
        showApproveModal: false,
        showRejectModal: false,
        selectedConferenceId: null,
        rejectReason: '',

        get filteredConferences() {
            return this.conferences.filter(conference => {
                const matchesSearch = !this.search || 
                    conference.conference_name.toLowerCase().includes(this.search.toLowerCase()) ||
                    conference.chair?.name.toLowerCase().includes(this.search.toLowerCase());
                
                const matchesStatus = !this.statusFilter || conference.status === this.statusFilter;
                
                const matchesLevel = !this.levelFilter || 
                    conference.conference_request?.level_code === this.levelFilter;

                return matchesSearch && matchesStatus && matchesLevel;
            });
        },

        resetFilters() {
            this.search = '';
            this.statusFilter = '';
            this.levelFilter = '';
        },

        getStatusBadgeClass(status) {
            switch (status) {
                case 'PENDING_ADMIN_APPROVAL':
                    return 'bg-yellow-100 text-yellow-800';
                case 'ACTIVE':
                    return 'bg-green-100 text-green-800';
                case 'REJECTED':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        getStatusText(status) {
            switch (status) {
                case 'PENDING_ADMIN_APPROVAL':
                    return 'Chờ duyệt';
                case 'ACTIVE':
                    return 'Đã duyệt';
                case 'REJECTED':
                    return 'Từ chối';
                default:
                    return 'Không xác định';
            }
        },

        getLevelBadgeClass(level) {
            switch (level) {
                case 'KHOA':
                    return 'bg-blue-100 text-blue-800';
                case 'TRUONG':
                    return 'bg-purple-100 text-purple-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        getLevelText(level) {
            switch (level) {
                case 'KHOA':
                    return 'Cấp Khoa';
                case 'TRUONG':
                    return 'Cấp Trường';
                default:
                    return 'Không xác định';
            }
        },

        formatDate(dateString) {
            if (!dateString) return 'Chưa xác định';
            return new Date(dateString).toLocaleDateString('vi-VN');
        },

        approveConference(conferenceId) {
            this.selectedConferenceId = conferenceId;
            this.showApproveModal = true;
        },

        rejectConference(conferenceId) {
            this.selectedConferenceId = conferenceId;
            this.showRejectModal = true;
        },

        async confirmApprove() {
            try {
                const response = await fetch(`{{ route('admin.conference-requests.approve-conference', '') }}/${this.selectedConferenceId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const conference = this.conferences.find(c => c.conference_id === this.selectedConferenceId);
                    if (conference) {
                        conference.status = 'ACTIVE';
                    }
                    this.showApproveModal = false;
                    this.selectedConferenceId = null;
                    
                    // Show success message
                    this.showNotification('Đã duyệt hội thảo thành công!', 'success');
                } else {
                    throw new Error('Lỗi khi duyệt hội thảo');
                }
            } catch (error) {
                console.error('Error approving conference:', error);
                this.showNotification('Có lỗi xảy ra khi duyệt hội thảo', 'error');
            }
        },

        async confirmReject() {
            if (!this.rejectReason.trim()) return;

            try {
                const response = await fetch(`{{ route('admin.conference-requests.reject-conference', '') }}/${this.selectedConferenceId}`, {
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
                    const conference = this.conferences.find(c => c.conference_id === this.selectedConferenceId);
                    if (conference) {
                        conference.status = 'REJECTED';
                    }
                    this.showRejectModal = false;
                    this.selectedConferenceId = null;
                    this.rejectReason = '';
                    
                    // Show success message
                    this.showNotification('Đã từ chối hội thảo', 'info');
                } else {
                    throw new Error('Lỗi khi từ chối hội thảo');
                }
            } catch (error) {
                console.error('Error rejecting conference:', error);
                this.showNotification('Có lỗi xảy ra khi từ chối hội thảo', 'error');
            }
        },

        showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;
            
            const bgColor = type === 'success' ? 'bg-green-50' : type === 'error' ? 'bg-red-50' : 'bg-blue-50';
            const textColor = type === 'success' ? 'text-green-800' : type === 'error' ? 'text-red-800' : 'text-blue-800';
            
            notification.innerHTML = `
                <div class="p-4">
                    <div class="flex">
                        <div class="flex-1">
                            <p class="text-sm font-medium ${textColor}">${message}</p>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endsection