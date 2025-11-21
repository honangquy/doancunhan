@extends('layouts.chair')

@section('title', 'Tạo thông báo broadcast')

@section('content')
<div class="p-6" x-data="announcementForm()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tạo thông báo broadcast</h1>
        <p class="text-gray-600 mt-1">Gửi thông báo đến tất cả người dùng trong hệ thống</p>
    </div>

    <!-- Alert: Broadcast to all users -->
    <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-lg">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-orange-800">Thông báo toàn hệ thống</p>
                <p class="text-xs text-orange-700 mt-1">Thông báo sẽ được gửi đến <strong>tất cả người dùng đang hoạt động</strong> trong hệ thống, không phân biệt vai trò hay hội thảo.</p>
            </div>
        </div>
    </div>

    @if(request('from_news'))
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">Tạo thông báo từ tin tức</p>
                    <p class="text-xs text-blue-700 mt-1">Form đã được điền sẵn nội dung từ tin tức. Bạn có thể chỉnh sửa trước khi gửi.</p>
                </div>
            </div>
        </div>
    @endif

    <form @submit.prevent="submitForm()" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <svg class="w-5 h-5 inline-block mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Thông tin cơ bản
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                           placeholder="Ví dụ: Thông báo bảo trì hệ thống"
                           class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Tiêu đề ngắn gọn, dễ hiểu</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung <span class="text-red-500">*</span></label>
                    <textarea x-model="form.content" required rows="8"
                              placeholder="Nhập nội dung thông báo chi tiết..."
                              class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Mô tả chi tiết nội dung thông báo</p>
                </div>
            </div>
        </div>

        <!-- Channels -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <svg class="w-5 h-5 inline-block mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Kênh gửi <span class="text-red-500">*</span>
            </h2>

            <div class="space-y-3">
                <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" value="email" x-model="form.channels"
                           class="rounded text-orange-500 focus:ring-orange-500 w-5 h-5">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900">Email</span>
                            <p class="text-xs text-gray-500">Gửi qua email đến hộp thư người dùng</p>
                        </div>
                    </div>
                </label>

                <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" value="in-app" x-model="form.channels"
                           class="rounded text-orange-500 focus:ring-orange-500 w-5 h-5">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="bg-orange-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900">Thông báo trong app</span>
                            <p class="text-xs text-gray-500">Hiển thị trong hệ thống khi người dùng đăng nhập</p>
                        </div>
                    </div>
                </label>
            </div>

            <p class="text-xs text-gray-500 mt-3">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bạn có thể chọn một hoặc nhiều kênh gửi
            </p>
        </div>

        <!-- Scheduling Options -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <svg class="w-5 h-5 inline-block mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Thời gian gửi
            </h2>

            <div class="space-y-4">
                <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="send_timing" value="immediate" x-model="sendTiming"
                           class="mt-1 text-orange-500 focus:ring-orange-500">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Gửi ngay lập tức</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 ml-7">Thông báo sẽ được gửi ngay sau khi tạo</p>
                    </div>
                </label>

                <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="send_timing" value="scheduled" x-model="sendTiming"
                           class="mt-1 text-orange-500 focus:ring-orange-500">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Lên lịch gửi sau</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 ml-7">Chọn thời điểm cụ thể để gửi thông báo</p>
                    </div>
                </label>

                <div x-show="sendTiming === 'scheduled'" class="ml-7 mt-3 p-4 bg-gray-50 rounded-lg" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn thời gian <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" x-model="form.schedule_time"
                           :required="sendTiming === 'scheduled'"
                           :min="new Date().toISOString().slice(0, 16)"
                           class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Thông báo sẽ tự động được gửi vào thời điểm này
                    </p>
                </div>
            </div>
        </div>

        <!-- Recipient Count Display -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-900">
                        Số người nhận dự kiến: <span class="text-lg font-bold" x-text="recipientCount"></span> người dùng
                    </p>
                    <p class="text-xs text-blue-700 mt-1">Thông báo sẽ được gửi đến tất cả tài khoản đang hoạt động trong hệ thống</p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('chair.announcements.index') }}"
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>Hủy</span>
            </a>
            <button type="submit" :disabled="submitting"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed font-medium flex items-center space-x-2">
                <svg x-show="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <svg x-show="submitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!submitting">Tạo & Gửi thông báo</span>
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
        submitting: false,
        recipientCount: 0,
        sendTiming: 'immediate',
        form: {
            title: '',
            content: '',
            channels: ['in-app'], // Default to in-app
            schedule_time: ''
        },

        init() {
            console.log('=== Broadcast Notification Form Initialized ===');
            this.loadRecipientCount();

            // Check if creating from news
            const urlParams = new URLSearchParams(window.location.search);
            const newsId = urlParams.get('from_news');

            if (newsId) {
                this.loadNewsContent(newsId);
            }
        },

        async loadNewsContent(newsId) {
            try {
                const response = await fetch(`/api/news/${newsId}/summary`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    this.form.title = data.title || '';
                    this.form.content = data.summary || '';

                    console.log('Pre-filled from news:', data);
                }
            } catch (error) {
                console.error('Failed to load news content:', error);
            }
        },

        async loadRecipientCount() {
            try {
                const response = await fetch('/chair/announcements/data/active-users-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    this.recipientCount = data.count || 0;
                    console.log('Active users count:', this.recipientCount);
                }
            } catch (error) {
                console.error('Failed to load recipient count:', error);
                this.recipientCount = 0;
            }
        },

        async submitForm() {
            if (!this.validateForm()) return;

            this.submitting = true;

            try {
                const payload = {
                    title: this.form.title,
                    content: this.form.content,
                    channels: this.form.channels,
                    send_immediately: this.sendTiming === 'immediate'
                };

                // Add schedule_time if scheduling
                if (this.sendTiming === 'scheduled' && this.form.schedule_time) {
                    payload.schedule_time = this.form.schedule_time;
                }

                console.log('Submitting broadcast notification:', payload);

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
                    alert(data.message || 'Thông báo đã được tạo thành công!');
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
            if (!this.form.title || this.form.title.trim() === '') {
                alert('Vui lòng nhập tiêu đề');
                return false;
            }
            if (!this.form.content || this.form.content.trim() === '') {
                alert('Vui lòng nhập nội dung');
                return false;
            }
            if (this.form.channels.length === 0) {
                alert('Vui lòng chọn ít nhất một kênh gửi');
                return false;
            }
            if (this.sendTiming === 'scheduled' && !this.form.schedule_time) {
                alert('Vui lòng chọn thời gian gửi');
                return false;
            }
            if (this.sendTiming === 'scheduled' && this.form.schedule_time) {
                const scheduledTime = new Date(this.form.schedule_time);
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
