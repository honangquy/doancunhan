@extends('layouts.chair')

@section('title', 'Quản lý thông báo broadcast')

@section('content')
<div class="p-6" x-data="announcementList()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Quản lý thông báo broadcast</h1>
        <p class="text-gray-600 mt-1">Quản lý thông báo toàn hệ thống đã gửi</p>
    </div>

    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex space-x-3">
            <a href="{{ route('chair.announcements.create') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tạo thông báo mới</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tổng thông báo</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total">0</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Đã gửi</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.sent">0</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Đã lên lịch</p>
                    <p class="text-2xl font-bold text-orange-600" x-text="stats.scheduled">0</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Thất bại</p>
                    <p class="text-2xl font-bold text-red-600" x-text="stats.failed">0</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select x-model="filters.status" @change="loadAnnouncements()"
                        class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Tất cả</option>
                    <option value="SENT">Đã gửi</option>
                    <option value="SCHEDULED">Đã lên lịch</option>
                    <option value="FAILED">Thất bại</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" x-model="filters.search" @input="loadAnnouncements()"
                       placeholder="Tìm theo tiêu đề hoặc nội dung..."
                       class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
            </div>
        </div>
    </div>

    <!-- Announcements Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kênh gửi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian gửi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người nhận</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center">
                            <div class="flex justify-center">
                                <svg class="animate-spin h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && announcements.length === 0">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Chưa có thông báo nào
                        </td>
                    </tr>
                </template>
                <template x-for="item in announcements" :key="item.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" x-text="item.title"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600" x-text="item.content_excerpt"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1" x-html="item.channels_html"></div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="{
                                'bg-gray-100 text-gray-800': item.status === 'DRAFT',
                                'bg-orange-100 text-orange-800': item.status === 'SCHEDULED',
                                'bg-green-100 text-green-800': item.status === 'SENT',
                                'bg-red-100 text-red-800': item.status === 'FAILED'
                            }" class="px-2 py-1 text-xs font-medium rounded-full" x-text="item.status_label"></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600" x-text="item.sent_at_display"></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900" x-text="item.recipient_count"></span>
                                <span class="text-xs text-gray-500">users</span>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
function announcementList() {
    return {
        announcements: [],
        loading: false,
        stats: {
            total: 0,
            sent: 0,
            scheduled: 0,
            failed: 0
        },
        filters: {
            status: '',
            search: ''
        },

        init() {
            console.log('=== Broadcast Announcement List Initialized ===');
            this.loadAnnouncements();
        },

        async loadAnnouncements() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    status: this.filters.status || '',
                    search: this.filters.search || ''
                });

                const response = await fetch(`/chair/announcements/data/list?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // Process announcements
                this.announcements = (data.announcements || []).map(item => ({
                    id: item.id,
                    title: item.title,
                    content: item.content,
                    content_excerpt: item.content ? (item.content.length > 100 ? item.content.substring(0, 100) + '...' : item.content) : '',
                    channels: item.channels || [],
                    channels_html: this.getChannelsHtml(item.channels || []),
                    status: item.status,
                    status_label: this.getStatusLabel(item.status),
                    scheduled_at: item.schedule_time,
                    sent_at: item.sent_at,
                    sent_at_display: item.sent_at || item.schedule_time || '-',
                    created_at: item.created_at,
                    recipient_count: item.recipient_count || 0
                }));

                this.stats = data.stats || { total: 0, sent: 0, scheduled: 0, failed: 0 };
                console.log('Loaded broadcast announcements:', this.announcements.length);
            } catch (error) {
                console.error('Failed to load announcements:', error);
                alert('Không thể tải danh sách thông báo: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        getStatusLabel(status) {
            const labels = {
                'DRAFT': 'Nháp',
                'SCHEDULED': 'Đã lên lịch',
                'SENT': 'Đã gửi',
                'FAILED': 'Thất bại'
            };
            return labels[status] || status;
        },

        getChannelsHtml(channels) {
            if (!channels || channels.length === 0) return '-';
            const icons = {
                'email': '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded flex items-center space-x-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg><span>Email</span></span>',
                'in-app': '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded flex items-center space-x-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg><span>In-App</span></span>'
            };
            return channels.map(ch => icons[ch] || ch).join('');
        }
    }
}
</script>
@endsection
