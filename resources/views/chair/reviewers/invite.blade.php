@extends('layouts.chair')

@section('title', 'Mời phản biện viên')

@section('page-title', 'Mời phản biện viên')

@section('page-subtitle', 'Gửi lời mời tham gia làm phản biện viên cho hội thảo')

@section('content')
<div x-data="reviewerInvitation()" class="space-y-6">
    <!-- Form mời reviewer -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Gửi lời mời phản biện viên</h3>

        <form @submit.prevent="sendInvitation()" class="space-y-4">
            <!-- Chọn hội thảo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hội thảo</label>
                <select x-model="form.conference_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">-- Chọn hội thảo --</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->conference_id }}">{{ $conference->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email phản biện viên
                    <span class="text-xs text-gray-500 font-normal">(Nhập nhiều email, cách nhau bởi dấu phẩy hoặc xuống dòng)</span>
                </label>
                <textarea x-model="form.emails" rows="4"
                       placeholder="Ví dụ:&#10;reviewer1@example.com, reviewer2@example.com&#10;reviewer3@example.com"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                <p class="text-xs text-gray-500 mt-1">Hoặc tải lên file CSV bên dưới</p>
            </div>

            <!-- CSV Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tải lên file CSV
                    <span class="text-xs text-gray-500 font-normal">(Cột đầu tiên chứa email)</span>
                </label>
                <div class="flex items-center space-x-3">
                    <label class="flex-1 cursor-pointer">
                        <div class="flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-orange-500 transition">
                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span x-text="csvFileName || 'Chọn file CSV'" class="text-sm text-gray-600"></span>
                        </div>
                        <input type="file" @change="handleCsvUpload($event)" accept=".csv,.txt" class="hidden">
                    </label>
                    <button type="button" x-show="csvFile" @click="clearCsv()"
                            class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <a href="#" @click.prevent="downloadSampleCsv()" class="text-orange-600 hover:underline">Tải file mẫu CSV</a>
                </p>
            </div>

            <!-- Message -->
            <div x-show="message"
                 :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                 class="border rounded-lg p-4 transition-all">
                <p x-html="message.replace(/\n/g, '<br>')"></p>
            </div>

            <!-- Submit button -->
            <div class="flex justify-end">
                <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium">
                    <span x-show="!loading">Gửi lời mời</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang gửi...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách lời mời đã gửi -->
    <div class="bg-white rounded-xl shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Lời mời đã gửi</h3>
                <button @click="loadSentInvitations()"
                        class="px-4 py-2 text-orange-600 border border-orange-600 rounded-lg hover:bg-orange-50 transition">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Làm mới
                </button>
            </div>

            <!-- Filter by status -->
            <div class="flex flex-wrap gap-2">
                <button @click="filterStatus = 'ALL'"
                        :class="filterStatus === 'ALL' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Tất cả (<span x-text="getCountByStatus('ALL')"></span>)
                </button>
                <button @click="filterStatus = 'PENDING'"
                        :class="filterStatus === 'PENDING' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Chờ phản hồi (<span x-text="getCountByStatus('PENDING')"></span>)
                </button>
                <button @click="filterStatus = 'ACCEPTED'"
                        :class="filterStatus === 'ACCEPTED' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Đã chấp nhận (<span x-text="getCountByStatus('ACCEPTED')"></span>)
                </button>
                <button @click="filterStatus = 'REJECTED'"
                        :class="filterStatus === 'REJECTED' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Đã từ chối (<span x-text="getCountByStatus('REJECTED')"></span>)
                </button>
                <button @click="filterStatus = 'EXPIRED'"
                        :class="filterStatus === 'EXPIRED' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Đã hết hạn (<span x-text="getCountByStatus('EXPIRED')"></span>)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hội thảo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày gửi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hết hạn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="invitation in filteredInvitations" :key="invitation.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="invitation.email"></div>
                                <div x-show="invitation.full_name" class="text-xs text-gray-500" x-text="invitation.full_name"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" x-text="invitation.conference_title"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(invitation.status)"
                                      class="px-3 py-1 text-xs font-semibold rounded-full inline-flex items-center gap-1">
                                    <svg x-show="invitation.status === 'ACCEPTED'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg x-show="invitation.status === 'REJECTED'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg x-show="invitation.status === 'PENDING'" class="w-3 h-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="getStatusText(invitation.status)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="formatDate(invitation.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="formatDate(invitation.expires_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div x-show="invitation.status === 'PENDING'" class="flex space-x-2">
                                    <button @click="resendInvitation(invitation.id, invitation.email, invitation.conference_id)"
                                            class="px-3 py-1 text-xs text-blue-600 border border-blue-600 rounded hover:bg-blue-50 transition">
                                        Gửi lại
                                    </button>
                                    <button @click="revokeInvitation(invitation.id)"
                                            class="px-3 py-1 text-xs text-red-600 border border-red-600 rounded hover:bg-red-50 transition">
                                        Thu hồi
                                    </button>
                                </div>
                                <div x-show="invitation.status !== 'PENDING'" class="text-xs text-gray-400">
                                    <span x-show="invitation.responded_at" x-text="'Phản hồi: ' + formatDate(invitation.responded_at)"></span>
                                    <span x-show="!invitation.responded_at">-</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredInvitations.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <span x-text="filterStatus === 'ALL' ? 'Chưa có lời mời nào được gửi' : 'Không có lời mời nào với trạng thái này'"></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function reviewerInvitation() {
    return {
        loading: false,
        message: '',
        messageType: '',
        form: {
            emails: '',
            conference_id: ''
        },
        csvFile: null,
        csvFileName: '',
        invitations: [],
        filterStatus: 'ALL',

        init() {
            this.loadSentInvitations();
        },

        handleCsvUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.csvFile = file;
                this.csvFileName = file.name;
            }
        },

        clearCsv() {
            this.csvFile = null;
            this.csvFileName = '';
        },

        downloadSampleCsv() {
            const csvContent = "email\nreviewer1@example.com\nreviewer2@example.com\nreviewer3@example.com";
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sample_reviewers.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        },

        get filteredInvitations() {
            if (this.filterStatus === 'ALL') {
                return this.invitations;
            }
            return this.invitations.filter(inv => inv.status === this.filterStatus);
        },

        getCountByStatus(status) {
            if (status === 'ALL') {
                return this.invitations.length;
            }
            return this.invitations.filter(inv => inv.status === status).length;
        },

        async sendInvitation() {
            this.loading = true;
            this.message = '';

            try {
                const formData = new FormData();
                formData.append('conference_id', this.form.conference_id);

                if (this.csvFile) {
                    formData.append('csv_file', this.csvFile);
                } else {
                    formData.append('emails', this.form.emails);
                }

                const response = await fetch('{{ route("chair.reviewers.invite.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.message = data.message;
                    if (data.details) {
                        let detailMsg = '\n\n';
                        if (data.details.success.length > 0) {
                            detailMsg += `✓ Thành công (${data.details.success.length}): ${data.details.success.join(', ')}\n`;
                        }
                        if (data.details.skipped.length > 0) {
                            detailMsg += `⊘ Bỏ qua (${data.details.skipped.length}):\n`;
                            data.details.skipped.forEach(item => {
                                detailMsg += `  - ${item.email}: ${item.reason}\n`;
                            });
                        }
                        if (data.details.failed.length > 0) {
                            detailMsg += `✗ Thất bại (${data.details.failed.length}):\n`;
                            data.details.failed.forEach(item => {
                                detailMsg += `  - ${item.email}: ${item.reason}\n`;
                            });
                        }
                        this.message += detailMsg;
                    }
                    this.messageType = 'success';
                    this.form.emails = '';
                    this.clearCsv();
                    this.loadSentInvitations();
                } else {
                    this.message = data.message;
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Có lỗi xảy ra. Vui lòng thử lại.';
                this.messageType = 'error';
            }

            this.loading = false;

            // Clear message after 5 seconds
            setTimeout(() => {
                this.message = '';
            }, 5000);
        },

        async loadSentInvitations() {
            try {
                const response = await fetch('{{ route("chair.reviewers.invite.list") }}');
                const data = await response.json();

                if (data.success) {
                    this.invitations = data.invitations;
                }
            } catch (error) {
                console.error('Error loading invitations:', error);
            }
        },

        getStatusClass(status) {
            const classes = {
                'PENDING': 'bg-yellow-100 text-yellow-800',
                'ACCEPTED': 'bg-green-100 text-green-800',
                'REJECTED': 'bg-red-100 text-red-800',
                'EXPIRED': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'PENDING': 'Chờ phản hồi',
                'ACCEPTED': 'Đã chấp nhận',
                'REJECTED': 'Đã từ chối',
                'EXPIRED': 'Đã hết hạn'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('vi-VN');
        },

        async resendInvitation(invitationId, email, conferenceId) {
            if (!confirm('Bạn có chắc chắn muốn gửi lại lời mời? Link cũ sẽ không còn hiệu lực.')) {
                return;
            }

            this.loading = true;
            this.message = '';

            try {
                const response = await fetch(`/chair/reviewers/invite/${invitationId}/resend`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: email,
                        conference_id: conferenceId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.message = data.message || 'Đã gửi lại lời mời thành công!';
                    this.messageType = 'success';
                    this.loadSentInvitations();
                } else {
                    this.message = data.message || 'Có lỗi xảy ra khi gửi lại lời mời.';
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Có lỗi xảy ra. Vui lòng thử lại.';
                this.messageType = 'error';
                console.error('Resend invitation error:', error);
            }

            this.loading = false;

            // Clear message after 5 seconds
            setTimeout(() => {
                this.message = '';
            }, 5000);
        },

        async revokeInvitation(invitationId) {
            if (!confirm('Bạn có chắc chắn muốn thu hồi lời mời này? Link sẽ không còn hiệu lực.')) {
                return;
            }

            this.loading = true;
            this.message = '';

            try {
                const response = await fetch(`/chair/reviewers/invite/${invitationId}/revoke`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.message = data.message || 'Đã thu hồi lời mời thành công!';
                    this.messageType = 'success';
                    this.loadSentInvitations();
                } else {
                    this.message = data.message || 'Có lỗi xảy ra khi thu hồi lời mời.';
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Có lỗi xảy ra. Vui lòng thử lại.';
                this.messageType = 'error';
                console.error('Revoke invitation error:', error);
            }

            this.loading = false;

            // Clear message after 5 seconds
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>
@endsection
