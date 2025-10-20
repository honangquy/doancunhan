@extends('layouts.admin')

@section('title', $title)

@section('content')
@include('components.notification')

<!-- Add global error handling for double-click and Alpine.js issues -->
<style>
    /* Enhanced Animation styles */
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    @keyframes slideIn {
        from { 
            opacity: 0; 
            transform: translateX(-20px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes notificationSlideIn {
        from {
            opacity: 0;
            transform: translateX(100%) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
            transform: translate3d(0,0,0);
        }
        40%, 43% {
            transform: translate3d(0, -10px, 0);
        }
        70% {
            transform: translate3d(0, -5px, 0);
        }
        90% {
            transform: translate3d(0, -2px, 0);
        }
    }

    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        10%, 30%, 50%, 70%, 90% {
            transform: translateX(-5px);
        }
        20%, 40%, 60%, 80% {
            transform: translateX(5px);
        }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animate-slideIn {
        animation: slideIn 0.5s ease-out forwards;
    }

    .animate-modalSlideIn {
        animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .animate-notificationSlideIn {
        animation: notificationSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .animate-pulse-custom {
        animation: pulse 2s infinite;
    }

    .animate-bounce-custom {
        animation: bounce 1s infinite;
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    /* Enhanced hover effects */
    .hover-scale:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    /* Enhanced button styles */
    .btn-action {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .btn-action::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-action:active::before {
        width: 300px;
        height: 300px;
    }

    /* Modal backdrop animation */
    .modal-backdrop {
        backdrop-filter: blur(5px);
        transition: backdrop-filter 0.3s ease;
    }

    /* Custom gradient backgrounds */
    .gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .gradient-error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .gradient-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    /* Loading spinner */
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid white;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Form input focus effects */
    .form-input:focus {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }

    /* Success checkmark animation */
    .checkmark {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #10b981;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #10b981;
        animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
    }

    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #10b981;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    @keyframes stroke {
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes scale {
        0%, 100% {
            transform: none;
        }
        50% {
            transform: scale3d(1.1, 1.1, 1);
        }
    }

    @keyframes fill {
        100% {
            box-shadow: inset 0px 0px 0px 30px #10b981;
        }
    }
</style>

<script>
// Global error handler for double-click and className issues
window.addEventListener('error', function(e) {
    if (e.error && e.error.message && e.error.message.includes('indexOf')) {
        console.warn('Prevented className indexOf error:', e.error);
        e.preventDefault();
        return true;
    }
});

// Prevent problematic double-click events before they cause issues
document.addEventListener('DOMContentLoaded', function() {
    // Override any problematic event handlers that might cause className issues
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        if (type === 'dblclick' && typeof listener === 'function') {
            const wrappedListener = function(e) {
                try {
                    // Ensure target has proper className
                    if (e.target && typeof e.target.className !== 'string') {
                        e.target.className = '';
                    }
                    return listener.call(this, e);
                } catch (error) {
                    console.warn('Double-click event error prevented:', error);
                    e.preventDefault();
                    e.stopPropagation();
                }
            };
            return originalAddEventListener.call(this, type, wrappedListener, options);
        }
        return originalAddEventListener.call(this, type, listener, options);
    };
});

// Prevent accidental double-click and handle potential className.indexOf errors
window.preventDoubleClick = function(button, timeout = 2000) {
    if (button.disabled) return false;
    button.disabled = true;
    setTimeout(() => {
        button.disabled = false;
    }, timeout);
    return true;
};

// Safe className manipulation to prevent indexOf errors
window.safeToggleClass = function(element, className) {
    try {
        if (element && element.classList) {
            element.classList.toggle(className);
        }
    } catch(e) {
        console.log('Toggle class error prevented:', e);
    }
};

// Handle user roles update
window.updateUserRole = function(userId, newRole) {
    if (!preventDoubleClick(event.target)) return;
    
    // Confirm role change
    if (!confirm('Bạn có chắc chắn muốn thay đổi vai trò người dùng này?')) {
        return;
    }
    
    fetch(`${getBaseUrl()}/admin/api/users/${userId}/role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ role: newRole })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra khi cập nhật vai trò');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật vai trò');
    });
};

// View user details
window.viewUser = function(userId) {
    const baseUrl = getBaseUrl();
    const viewUrl = `${baseUrl}/admin/api/users/${userId}`;
    console.log('Viewing user:', userId);
    console.log('View URL:', viewUrl);
    
    fetch(viewUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('View response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('View response data:', data);
        if (data.success) {
            // Create enhanced modal content
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop';
            modal.innerHTML = `
                <div class="relative top-10 mx-auto p-0 border-0 shadow-2xl rounded-2xl bg-white max-w-md animate-modalSlideIn">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-2xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="bg-white bg-opacity-20 p-1.5 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold">Thông tin người dùng</h3>
                            </div>
                            <button onclick="this.closest('.fixed').remove()" class="text-white hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-white hover:bg-opacity-20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="bg-gray-50 rounded-xl p-3 hover-lift">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-blue-100 p-1.5 rounded-lg">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Họ tên</p>
                                        <p class="text-sm font-semibold text-gray-900">${data.user.full_name || data.user.name || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-3 hover-lift">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-green-100 p-1.5 rounded-lg">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Email</p>
                                        <p class="text-sm font-semibold text-gray-900">${data.user.email || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-3 hover-lift">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-purple-100 p-1.5 rounded-lg">
                                        <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Vai trò</p>
                                        <p class="text-sm font-semibold text-gray-900">${data.user.roles ? data.user.roles.map(r => r.TenVaiTro || r.name).join(', ') : 'Chưa có vai trò'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-3 hover-lift">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-orange-100 p-1.5 rounded-lg">
                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6h4m-4 0v4m0-4H8m0 0v4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Ngày tạo</p>
                                        <p class="text-sm font-semibold text-gray-900">${data.user.created_at ? new Date(data.user.created_at).toLocaleDateString('vi-VN') : 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl font-medium transition-all duration-200 hover-lift btn-action">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Đóng
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            showError(data.message || 'Không thể tải thông tin người dùng');
        }
    })
    .catch(error => {
        console.error('View error:', error);
        showError('Có lỗi xảy ra khi tải thông tin người dùng: ' + error.message);
    });
};

// Bulk operations
window.bulkDelete = function() {
    const selectedUsers = Array.from(document.querySelectorAll('input[name="selected_users"]:checked')).map(cb => cb.value);
    console.log('Selected users:', selectedUsers);
    
    if (selectedUsers.length === 0) {
        alert('Vui lòng chọn ít nhất một người dùng');
        return;
    }
    
    if (!confirm(`Bạn có chắc chắn muốn xóa ${selectedUsers.length} người dùng đã chọn?`)) {
        return;
    }
    
    console.log('Sending request to delete users:', selectedUsers);
    
    fetch(`${getBaseUrl()}/admin/api/users/bulk-delete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ user_ids: selectedUsers })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showSuccess(data.message || 'Xóa người dùng thành công!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Có lỗi xảy ra khi xóa người dùng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra khi xóa người dùng: ' + error.message);
    });
};
</script>

<div class="container mx-auto px-4 py-6">
    <!-- Success Message (keeping for now but will use JS notifications) -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccess('{{ session('success') }}');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showError('{{ session('error') }}');
            });
        </script>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        <button onclick="openAddUserModal()" 
                ondblclick="event.preventDefault(); event.stopPropagation(); return false;"
                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Thêm người dùng
        </button>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Tìm kiếm tên, email...">
            </div>

            <!-- Role Filter -->
            <div>
                <select name="role" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả vai trò</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>Quản trị viên</option>
                    <option value="CHAIR" {{ request('role') === 'CHAIR' ? 'selected' : '' }}>Chủ tịch</option>
                    <option value="REVIEWER" {{ request('role') === 'REVIEWER' ? 'selected' : '' }}>Phản biện viên</option>
                    <option value="AUTHOR" {{ request('role') === 'AUTHOR' ? 'selected' : '' }}>Tác giả</option>
                    <option value="USER" {{ request('role') === 'USER' ? 'selected' : '' }}>Người dùng</option>
                </select>
            </div>

            <!-- Email Verification Filter -->
            <div>
                <select name="verified" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Đã xác thực</option>
                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Chưa xác thực</option>
                </select>
            </div>

            <!-- Search Button -->
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tìm kiếm
                </button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Đặt lại
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden" 
         x-data="{
             selectedUsers: [], 
             selectAll: false, 
             showBulkActions: false,
             toggleAll() { 
                 if (this.selectAll) { 
                     const checkboxes = document.querySelectorAll('input[name=\'selected_users\']'); 
                     this.selectedUsers = Array.from(checkboxes).map(cb => cb.value); 
                 } else { 
                     this.selectedUsers = []; 
                 } 
             }, 
             updateSelectAll() { 
                 const checkboxes = document.querySelectorAll('input[name=\'selected_users\']'); 
                 this.selectAll = checkboxes.length > 0 && this.selectedUsers.length === checkboxes.length; 
             } 
         }">
        <!-- Bulk Actions Bar -->
        <div x-show="selectedUsers.length > 0" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-blue-50 border-b border-blue-200 px-6 py-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-700">
                    Đã chọn <span x-text="selectedUsers.length"></span> người dùng
                </span>
                <div class="flex space-x-2">
                    <button onclick="bulkDelete()" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                        Xóa hàng loạt
                    </button>
                    <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors">
                        Xuất Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-4 text-left w-8 sm:w-12">
                            <input type="checkbox" 
                                   x-model="selectAll"
                                   @change="toggleAll()"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all">
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-12 sm:w-16">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                <span>ID</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-32 sm:w-auto">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Họ tên</span>
                            </div>
                        </th>
                        <th class="hidden md:table-cell px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-32 sm:w-auto">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Email</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-16 sm:w-24">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                <span>Vai trò</span>
                            </div>
                        </th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-20 sm:w-24">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Trạng thái</span>
                            </div>
                        </th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-20 sm:w-24">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Ngày tạo</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-20 sm:w-32">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zM12 13a1 1 0 110-2 1 1 0 010 2zM12 20a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                                <span>Hành động</span>
                            </div>
                        </th>
                    </tr>
                </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors duration-150 animate-fadeIn" 
                    style="animation-delay: {{ $loop->index * 50 }}ms">
                    <td class="px-3 sm:px-6 py-4">
                        <input type="checkbox" 
                               name="selected_users"
                               value="{{ $user->user_id }}"
                               x-model="selectedUsers"
                               @change="updateSelectAll()"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all">
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->user_id }}</td>
                    <td class="px-3 sm:px-6 py-4 text-sm font-medium text-gray-900">
                        <div class="max-w-[120px] sm:max-w-xs truncate" title="{{ $user->full_name }}">
                            {{ $user->full_name }}
                        </div>
                        <!-- Show email on mobile below name -->
                        <div class="md:hidden text-xs text-gray-500 mt-1 max-w-[120px] truncate" title="{{ $user->email }}">
                            {{ $user->email }}
                        </div>
                    </td>
                    <td class="hidden md:table-cell px-3 sm:px-6 py-4 text-sm text-gray-900">
                        <div class="max-w-[150px] sm:max-w-xs truncate" title="{{ $user->email }}">
                            {{ $user->email }}
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        @php
                            $primaryRole = $user->getPrimaryRole();
                            $allRoles = $user->getAllRolesString();
                        @endphp
                        <span class="px-1 sm:px-2 inline-flex text-xs leading-4 sm:leading-5 font-semibold rounded-full 
                            @if($primaryRole === 'ADMIN') bg-red-100 text-red-800 
                            @elseif($primaryRole === 'CHAIR') bg-purple-100 text-purple-800 
                            @elseif($primaryRole === 'REVIEWER') bg-blue-100 text-blue-800 
                            @elseif($primaryRole === 'AUTHOR') bg-green-100 text-green-800 
                            @else bg-gray-100 text-gray-800 @endif"
                            title="{{ $allRoles }}">
                            <span class="hidden sm:inline">{{ $primaryRole }}</span>
                            <span class="sm:hidden">{{ substr($primaryRole, 0, 3) }}</span>
                        </span>
                        @if($allRoles !== $primaryRole)
                            <span class="text-xs text-gray-500 ml-1">(+)</span>
                        @endif
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                        @if($user->hasVerifiedEmail())
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    <span class="hidden sm:inline">Đã xác thực</span>
                                    <span class="sm:hidden">✓</span>
                                </span>
                            </div>
                        @else
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    <span class="hidden sm:inline">Chưa xác thực</span>
                                    <span class="sm:hidden">✗</span>
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="hidden sm:inline">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') : 'N/A' }}</span>
                        <span class="sm:hidden">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m') : 'N/A' }}</span>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <!-- View Button -->
                            <button onclick="viewUser({{ $user->user_id }})" 
                                    class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition-all"
                                    title="Xem">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            <!-- Edit Button -->
                            <button onclick="editUser({{ $user->user_id }})" 
                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded transition-all"
                                    title="Sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            <button onclick="deleteUser({{ $user->user_id }})" 
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded transition-all"
                                    title="Xóa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Không có người dùng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

<!-- Enhanced Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 modal-backdrop">
    <div class="relative top-10 mx-auto p-0 border-0 shadow-2xl rounded-2xl bg-white max-w-md animate-modalSlideIn">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="bg-white bg-opacity-20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold">Thêm người dùng mới</h3>
                </div>
                <button onclick="closeAddUserModal()" class="text-white hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-white hover:bg-opacity-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-4">
            <form id="addUserForm" method="POST" class="space-y-3" onsubmit="return false;">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Họ tên
                        </label>
                        <input type="text" name="full_name" required 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                               placeholder="Nhập họ tên đầy đủ">
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            Email
                        </label>
                        <input type="email" name="email" required 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                               placeholder="example@email.com">
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Mật khẩu
                        </label>
                        <input type="password" name="password" required 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                               placeholder="Nhập mật khẩu">
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Vai trò
                        </label>
                        <select name="role" required 
                                class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all">
                            <option value="">Chọn vai trò</option>
                            <option value="USER">Người dùng</option>
                            <option value="AUTHOR">Tác giả</option>
                            <option value="REVIEWER">Phản biện viên</option>
                            <option value="CHAIR">Chủ tịch</option>
                            <option value="ADMIN">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeAddUserModal()" 
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-all duration-200 hover-lift">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Hủy
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 hover-lift btn-action">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 modal-backdrop">
    <div class="relative top-10 mx-auto p-0 border-0 shadow-2xl rounded-2xl bg-white max-w-md animate-modalSlideIn">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-t-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="bg-white bg-opacity-20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold">Chỉnh sửa người dùng</h3>
                </div>
                <button onclick="closeEditUserModal()" class="text-white hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-white hover:bg-opacity-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-4">
            <form id="editUserForm" method="POST" class="space-y-3" onsubmit="return false;">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="space-y-3">
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Họ tên
                        </label>
                        <input type="text" id="editFullName" name="full_name" required 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all">
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            Email
                        </label>
                        <input type="email" id="editEmail" name="email" required 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all">
                        
                        <!-- Email Verification Status and Actions -->
                        <div class="mt-2 p-3 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg id="emailVerificationIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <!-- Icon will be updated by JavaScript -->
                                    </svg>
                                    <span id="emailVerificationStatus" class="text-sm font-medium">
                                        <!-- Status will be updated by JavaScript -->
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" id="verifyEmailBtn" onclick="handleEmailVerification()" 
                                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200 hover-lift">
                                        <!-- Button content will be updated by JavaScript -->
                                    </button>
                                </div>
                            </div>
                            <p id="emailVerificationDate" class="text-xs text-gray-500 mt-1">
                                <!-- Date will be updated by JavaScript -->
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Mật khẩu mới
                        </label>
                        <input type="password" id="editPassword" name="password" 
                               class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all"
                               placeholder="Để trống nếu không thay đổi">
                        <p class="text-xs text-gray-500 mt-1">Để trống nếu không muốn thay đổi mật khẩu</p>
                    </div>
                    
                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline mr-1.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Vai trò
                        </label>
                        <select id="editRole" name="role" required 
                                class="form-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg leading-5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all">
                            <option value="">Chọn vai trò</option>
                            <option value="USER">Người dùng</option>
                            <option value="AUTHOR">Tác giả</option>
                            <option value="REVIEWER">Phản biện viên</option>
                            <option value="CHAIR">Chủ tịch</option>
                            <option value="ADMIN">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditUserModal()" 
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-all duration-200 hover-lift">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Hủy
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-lg font-medium transition-all duration-200 hover-lift btn-action">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Helper function to get correct base URL
function getBaseUrl() {
    // For XAMPP setup, get the base URL from the current location
    const path = window.location.pathname;
    const segments = path.split('/');
    // Find the 'public' segment and build base URL from there
    const publicIndex = segments.indexOf('public');
    if (publicIndex !== -1) {
        return segments.slice(0, publicIndex + 1).join('/');
    }
    // Fallback to detecting from current URL
    return window.location.origin + (path.includes('/qlyhoithao/') ? '/qly_hthao/qlyhoithao/public' : '');
}

// Enhanced success message with beautiful animation
function showSuccess(message) {
    // Create success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 transform transition-all duration-500 translate-x-full';
    notification.innerHTML = `
        <div class="bg-white rounded-lg shadow-2xl border-l-4 border-green-500 p-3 max-w-sm animate-notificationSlideIn hover-lift">
            <div class="flex items-start space-x-2">
                <div class="flex-shrink-0">
                    <svg class="checkmark w-4 h-4" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-green-800">Thành công!</p>
                    <p class="text-xs text-green-600 mt-1">${message}</p>
                </div>
                <button onclick="this.closest('.fixed').remove()" class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 500);
    }, 4000);
}

// Enhanced error message with beautiful animation
function showError(message) {
    // Create error notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 transform transition-all duration-500 translate-x-full';
    notification.innerHTML = `
        <div class="bg-white rounded-lg shadow-2xl border-l-4 border-red-500 p-3 max-w-sm animate-notificationSlideIn animate-shake hover-lift">
            <div class="flex items-start space-x-2">
                <div class="flex-shrink-0">
                    <svg class="w-4 h-4 text-red-500 animate-pulse-custom" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-red-800">Có lỗi xảy ra!</p>
                    <p class="text-xs text-red-600 mt-1">${message}</p>
                </div>
                <button onclick="this.closest('.fixed').remove()" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 6 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 500);
    }, 6000);
}

// Add User Modal Functions
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.getElementById('addUserForm').reset();
}

// Edit User Modal Functions
function openEditUserModal() {
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
}

// Edit User Function
async function editUser(userId) {
    try {
        const baseUrl = getBaseUrl();
        const response = await fetch(`${baseUrl}/admin/users/${userId}/edit`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        
        if (data.success) {
            // Populate form with user data
            document.getElementById('editUserId').value = data.user.user_id;
            document.getElementById('editFullName').value = data.user.full_name;
            document.getElementById('editEmail').value = data.user.email;
            document.getElementById('editRole').value = data.user.role_code || '';
            document.getElementById('editPassword').value = '';
            
            // Update email verification status
            updateEmailVerificationStatus(data.user.email_verified_at, data.user.user_id);
            
            openEditUserModal();
        } else {
            showError(data.message || 'Không thể tải thông tin người dùng');
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi tải thông tin người dùng');
        console.error('Error:', error);
    }
}

// Update User Function
function initEditUserForm() {
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userId = document.getElementById('editUserId').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT'); // Add method spoofing
            
            try {
                const baseUrl = getBaseUrl();
                const response = await fetch(`${baseUrl}/admin/users/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message || 'Cập nhật người dùng thành công!');
            closeEditUserModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            // Display validation errors
            if (data.errors) {
                let errorMessages = Object.values(data.errors).flat().join('<br>');
                showError(errorMessages);
            } else {
                showError(data.message || 'Có lỗi xảy ra khi cập nhật người dùng');
            }
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi cập nhật người dùng');
        console.error('Error:', error);
    }
        });
    }
}

// Enhanced Delete User Function with beautiful confirmation modal
async function deleteUser(userId) {
    // Create beautiful confirmation modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-0 border-0 shadow-2xl rounded-2xl bg-white max-w-md animate-modalSlideIn">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-t-2xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white bg-opacity-20 p-1.5 rounded-lg animate-pulse-custom">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.134 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold">Xác nhận xóa</h3>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-3 animate-bounce-custom">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-sm leading-6 font-semibold text-gray-900 mb-2">Xóa người dùng</h3>
                    <div class="bg-red-50 rounded-xl p-3 mb-4">
                        <p class="text-xs text-red-700">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Bạn có chắc chắn muốn xóa người dùng này không? 
                        </p>
                        <p class="text-xs text-red-600 mt-1 font-medium">Hành động này không thể hoàn tác!</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center space-x-3">
                    <button id="cancelDelete" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-all duration-200 hover-lift text-sm">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Hủy
                    </button>
                    <button id="confirmDelete" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-medium transition-all duration-200 hover-lift btn-action text-sm">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Xóa ngay
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle confirm button
    document.getElementById('confirmDelete').onclick = async function() {
        document.body.removeChild(modal);
        
        try {
            console.log('Deleting user:', userId);
            
            const baseUrl = getBaseUrl();
            const deleteUrl = `${baseUrl}/admin/users/${userId}`;
            console.log('Delete URL:', deleteUrl);
            
            const response = await fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    '_method': 'DELETE',
                    '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })
            });

            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.log('Error response text:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                showSuccess(data.message || 'Xóa người dùng thành công!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.message || 'Có lỗi xảy ra khi xóa người dùng');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showError('Có lỗi xảy ra khi xóa người dùng: ' + error.message);
        }
    };
    
    // Handle cancel button
    document.getElementById('cancelDelete').onclick = function() {
        document.body.removeChild(modal);
    };
    
    // Close modal when clicking outside
    modal.onclick = function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    };
}

// Email Verification Functions
async function verifyEmail(userId) {
    if (!confirm('Bạn có chắc muốn xác thực email của người dùng này?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        const response = await fetch(`${getBaseUrl()}/admin/users/${userId}/verify-email`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message || 'Email đã được xác thực thành công!');
            // Update the modal status if it's open for this user
            const editModal = document.getElementById('editUserModal');
            const editUserId = document.getElementById('editUserId');
            if (!editModal.classList.contains('hidden') && editUserId.value == userId) {
                updateEmailVerificationStatus(new Date().toISOString(), userId);
            }
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showError(data.message || 'Có lỗi xảy ra khi xác thực email');
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi xác thực email');
        console.error('Error:', error);
    }
}

async function unverifyEmail(userId) {
    if (!confirm('Bạn có chắc muốn hủy xác thực email của người dùng này?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        const response = await fetch(`${getBaseUrl()}/admin/users/${userId}/unverify-email`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message || 'Đã hủy xác thực email!');
            // Update the modal status if it's open for this user
            const editModal = document.getElementById('editUserModal');
            const editUserId = document.getElementById('editUserId');
            if (!editModal.classList.contains('hidden') && editUserId.value == userId) {
                updateEmailVerificationStatus(null, userId);
            }
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showError(data.message || 'Có lỗi xảy ra khi hủy xác thực email');
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi hủy xác thực email');
        console.error('Error:', error);
    }
}

// Update email verification status in edit modal
function updateEmailVerificationStatus(emailVerifiedAt, userId) {
    const icon = document.getElementById('emailVerificationIcon');
    const status = document.getElementById('emailVerificationStatus');
    const date = document.getElementById('emailVerificationDate');
    const button = document.getElementById('verifyEmailBtn');
    
    // Store user ID for verification actions
    button.setAttribute('data-user-id', userId);
    
    if (emailVerifiedAt) {
        // Email is verified
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>`;
        icon.className = 'w-4 h-4 text-green-500';
        status.textContent = 'Email đã được xác thực';
        status.className = 'text-sm font-medium text-green-700';
        
        // Format date
        const verifiedDate = new Date(emailVerifiedAt);
        date.textContent = `Xác thực lúc: ${verifiedDate.toLocaleString('vi-VN')}`;
        
        // Set unverify button
        button.textContent = 'Hủy xác thực';
        button.className = 'px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-md hover:bg-red-200 transition-all duration-200 hover-lift';
        button.setAttribute('data-action', 'unverify');
    } else {
        // Email is not verified
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`;
        icon.className = 'w-4 h-4 text-orange-500';
        status.textContent = 'Email chưa được xác thực';
        status.className = 'text-sm font-medium text-orange-700';
        
        date.textContent = 'Chưa được xác thực';
        
        // Set verify button
        button.textContent = 'Xác thực email';
        button.className = 'px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border border-green-200 rounded-md hover:bg-green-200 transition-all duration-200 hover-lift';
        button.setAttribute('data-action', 'verify');
    }
}

// Handle email verification/unverification from edit modal
function handleEmailVerification() {
    const button = document.getElementById('verifyEmailBtn');
    const userId = button.getAttribute('data-user-id');
    const action = button.getAttribute('data-action');
    
    if (action === 'verify') {
        verifyEmail(userId);
    } else if (action === 'unverify') {
        unverifyEmail(userId);
    }
}

// Handle Add User Form Submission
function initAddUserForm() {
    const addForm = document.getElementById('addUserForm');
    if (addForm) {
        addForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const baseUrl = getBaseUrl();
                const response = await fetch(`${baseUrl}/admin/users`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message || 'Thêm người dùng thành công!');
            closeAddUserModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            // Display validation errors
            if (data.errors) {
                let errorMessages = Object.values(data.errors).flat().join('<br>');
                showError(errorMessages);
            } else {
                showError(data.message || 'Có lỗi xảy ra khi thêm người dùng');
            }
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi thêm người dùng');
        console.error('Error:', error);
    }
        });
    }
}

// Initialize forms when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initEditUserForm();
    initAddUserForm();
    preventDoubleClickErrors();
});

// Prevent double-click errors by adding proper event handling
function preventDoubleClickErrors() {
    // Add global event listener to prevent className indexOf errors
    document.addEventListener('dblclick', function(e) {
        try {
            // Ensure target has className property as string
            if (e.target && typeof e.target.className !== 'string') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        } catch (error) {
            console.warn('Double-click event prevented due to error:', error);
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    // Prevent default double-click behavior on buttons and interactive elements
    document.addEventListener('dblclick', function(e) {
        const target = e.target;
        if (target && (
            target.tagName === 'BUTTON' ||
            target.tagName === 'A' ||
            target.classList.contains('btn') ||
            target.closest('button') ||
            target.closest('a')
        )) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, false);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addUserModal');
    const editModal = document.getElementById('editUserModal');
    
    if (event.target == addModal) {
        closeAddUserModal();
    }
    if (event.target == editModal) {
        closeEditUserModal();
    }
}
</script>

@endsection