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
                <label class="block text-sm font-medium text-gray-700 mb-2">Email phản biện viên</label>
                <input type="email" x-model="form.email" required
                       placeholder="Nhập email của phản biện viên"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Message -->
            <div x-show="message" 
                 :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                 class="border rounded-lg p-4 transition-all">
                <p x-text="message"></p>
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
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Lời mời đã gửi</h3>
            <button @click="loadSentInvitations()" 
                    class="px-4 py-2 text-orange-600 border border-orange-600 rounded-lg hover:bg-orange-50 transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Làm mới
            </button>
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
                    <template x-for="invitation in invitations" :key="invitation.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="invitation.email"></div>
                                <div x-show="invitation.full_name" class="text-xs text-gray-500" x-text="invitation.full_name"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="invitation.conference_title"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(invitation.status)" 
                                      class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getStatusText(invitation.status)">
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
                                    -
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="invitations.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Chưa có lời mời nào được gửi
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
            email: '',
            conference_id: ''
        },
        invitations: [],
        
        init() {
            this.loadSentInvitations();
        },
        
        async sendInvitation() {
            this.loading = true;
            this.message = '';
            
            try {
                const response = await fetch('{{ route("chair.reviewers.invite.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.message = data.message;
                    this.messageType = 'success';
                    this.form.email = '';
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