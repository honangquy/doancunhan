@extends('layouts.chair')

@section('title', 'Tạo thông báo mới')

@section('content')
<div class="p-6" x-data="announcementForm()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tạo thông báo mới</h1>
        <p class="text-gray-600 mt-1">Gửi thông báo đến người dùng qua nhiều kênh</p>
    </div>

    <form @submit.prevent="submitForm()" class="space-y-6">
        <!-- Conference Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hội thảo <span class="text-red-500">*</span></label>
                    <select x-model="form.conference_id" @change="loadFormData()" required
                            class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Chọn hội thảo</option>
                        <template x-for="conf in conferences" :key="conf.id">
                            <option :value="conf.id" x-text="conf.ten_hoi_thao"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                           placeholder="Nhập tiêu đề thông báo"
                           class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung <span class="text-red-500">*</span></label>
                    <textarea x-model="form.body" required rows="6"
                              placeholder="Nhập nội dung thông báo"
                              class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Audience Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Đối tượng nhận</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn đối tượng <span class="text-red-500">*</span></label>
                <select x-model="form.audience" @change="updateRecipientCount()" required
                        class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Chọn đối tượng</option>
                    <option value="ALL">Tất cả người dùng</option>
                    <option value="AUTHORS">Tác giả</option>
                    <option value="REVIEWERS">Phản biện</option>
                    <option value="CHAIRS">Chairs</option>
                </select>
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-800 font-medium">
                            Số người nhận dự kiến: <span x-text="recipientCount">0</span>
                            <span x-show="loadingCount" class="ml-2 text-xs">(Đang tính...)</span>
                        </p>
                        <p class="text-xs text-blue-600 mt-1">Thông báo sẽ được gửi đến tất cả người dùng thuộc đối tượng đã chọn</p>
                        
                        <!-- Recipients List -->
                        <div x-show="recipientUsers.length > 0" class="mt-3 space-y-2 max-h-60 overflow-y-auto">
                            <p class="text-xs font-medium text-blue-700 mb-2">Danh sách người nhận:</p>
                            <template x-for="user in recipientUsers" :key="user.user_id">
                                <div class="flex items-center space-x-2 p-2 bg-white rounded border border-blue-100 text-xs">
                                    <div class="w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold text-[10px]">
                                        <span x-text="user.full_name ? user.full_name.substring(0, 1).toUpperCase() : '?'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate" x-text="user.full_name"></p>
                                        <p class="text-gray-600 truncate" x-text="user.email"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Channels -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Kênh gửi <span class="text-red-500">*</span></h2>
            
            <div class="space-y-3">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" value="email" x-model="form.channels"
                           class="rounded text-orange-500 focus:ring-orange-500">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Email</span>
                    </div>
                </label>

                <label class="flex items-center space-x-3">
                    <input type="checkbox" value="system" x-model="form.channels"
                           class="rounded text-orange-500 focus:ring-orange-500">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Thông báo hệ thống</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Scheduling Options -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Lịch gửi</h2>
            
            <div class="space-y-4">
                <label class="flex items-center space-x-3">
                    <input type="radio" name="send_timing" value="immediate" x-model="sendTiming"
                           class="text-orange-500 focus:ring-orange-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Gửi ngay</span>
                        <p class="text-xs text-gray-500">Thông báo sẽ được gửi đi ngay lập tức</p>
                    </div>
                </label>

                <label class="flex items-center space-x-3">
                    <input type="radio" name="send_timing" value="scheduled" x-model="sendTiming"
                           class="text-orange-500 focus:ring-orange-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Lên lịch gửi</span>
                        <p class="text-xs text-gray-500">Chọn thời điểm cụ thể để gửi thông báo</p>
                    </div>
                </label>

                <div x-show="sendTiming === 'scheduled'" class="ml-8 mt-2" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Thời gian gửi <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" x-model="form.scheduled_at"
                           :required="sendTiming === 'scheduled'"
                           :min="new Date().toISOString().slice(0, 16)"
                           class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Thông báo sẽ tự động được gửi vào thời điểm này</p>
                </div>
            </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('chair.announcements.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" :disabled="submitting"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg disabled:opacity-50">
                <span x-show="!submitting">Tạo thông báo</span>
                <span x-show="submitting">Đang xử lý...</span>
            </button>
        </div>
    </form>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function announcementForm() {
    return {
        conferences: [],
        submitting: false,
        recipientCount: 0,
        loadingCount: false,
        recipientUsers: [], // Array to store recipient list
        sendTiming: 'immediate',
        form: {
            conference_id: '',
            title: '',
            body: '',
            audience: '',
            channels: ['system'],
            scheduled_at: ''
        },

        init() {
            console.log('=== Alpine.js initialized ===');
            this.loadConferences();
        },

        async loadConferences() {
            try {
                console.log('Loading conferences...');
                const response = await fetch('/chair/announcements/data/conferences', {
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
                this.conferences = data.data || [];
                console.log('Loaded conferences:', this.conferences.length);
            } catch (error) {
                console.error('Failed to load conferences:', error);
                alert('Không thể tải danh sách hội thảo: ' + error.message);
            }
        },

        async loadFormData() {
            if (!this.form.conference_id) return;
            this.updateRecipientCount();
        },

        async updateRecipientCount() {
            if (!this.form.conference_id || !this.form.audience) {
                this.recipientCount = 0;
                this.recipientUsers = [];
                return;
            }

            this.loadingCount = true;
            
            try {
                // Fetch count
                const countResponse = await fetch('/chair/announcements/data/recipient-count', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        conference_id: this.form.conference_id,
                        audience: this.form.audience
                    })
                });

                if (!countResponse.ok) {
                    throw new Error(`HTTP error! status: ${countResponse.status}`);
                }

                const countData = await countResponse.json();
                this.recipientCount = countData.count || 0;
                console.log('Recipient count:', this.recipientCount);
                
                // Fetch list
                const listResponse = await fetch('/chair/announcements/data/recipient-list', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        conference_id: this.form.conference_id,
                        audience: this.form.audience
                    })
                });
                
                if (listResponse.ok) {
                    const listData = await listResponse.json();
                    this.recipientUsers = listData.users || [];
                    console.log('Recipient users loaded:', this.recipientUsers.length);
                }
            } catch (error) {
                console.error('Failed to get recipient count:', error);
                this.recipientCount = 0;
            } finally {
                this.loadingCount = false;
            }
        },

        async submitForm() {
            if (!this.validateForm()) return;

            this.submitting = true;

            try {
                const payload = {
                    conference_id: this.form.conference_id,
                    title: this.form.title,
                    body: this.form.body,
                    audience: this.form.audience,
                    channels: this.form.channels,
                    send_immediately: this.sendTiming === 'immediate'
                };

                // Thêm scheduled_at nếu lên lịch
                if (this.sendTiming === 'scheduled' && this.form.scheduled_at) {
                    payload.scheduled_at = this.form.scheduled_at;
                }

                const response = await fetch('/chair/announcements/store', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Có lỗi xảy ra');
                }

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message || 'Thông báo đã được gửi thành công!');
                    window.location.href = '/chair/announcements';
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Failed to submit:', error);
                alert('Có lỗi xảy ra: ' + error.message);
            } finally {
                this.submitting = false;
            }
        },

        validateForm() {
            if (!this.form.conference_id) {
                alert('Vui lòng chọn hội thảo');
                return false;
            }
            if (!this.form.title) {
                alert('Vui lòng nhập tiêu đề');
                return false;
            }
            if (!this.form.body) {
                alert('Vui lòng nhập nội dung');
                return false;
            }
            if (!this.form.audience) {
                alert('Vui lòng chọn đối tượng nhận');
                return false;
            }
            if (this.form.channels.length === 0) {
                alert('Vui lòng chọn ít nhất một kênh gửi');
                return false;
            }
            if (this.sendTiming === 'scheduled' && !this.form.scheduled_at) {
                alert('Vui lòng chọn thời gian gửi');
                return false;
            }
            if (this.sendTiming === 'scheduled' && this.form.scheduled_at) {
                const scheduledTime = new Date(this.form.scheduled_at);
                const now = new Date();
                if (scheduledTime <= now) {
                    alert('Thời gian gửi phải sau thời điểm hiện tại');
                    return false;
                }
            }
            return true;
        }
    }
}
</script>
@endsection
