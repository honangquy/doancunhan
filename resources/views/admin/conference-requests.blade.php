@extends('layouts.admin')

@section('title', 'Yêu cầu Tạo Hội thảo')

@section('content')
<!-- Include notification component -->
@include('components.notification')

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Yêu cầu Tạo Hội thảo</h1>
            <p class="mt-2 text-sm text-gray-600">Quản lý và xét duyệt các yêu cầu tạo hội thảo từ người dùng</p>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" id="searchInput" placeholder="Tên hội thảo, người yêu cầu..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
            <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">-- Tất cả --</option>
                <option value="PENDING">Chờ duyệt</option>
                <option value="APPROVED">Đã duyệt</option>
                <option value="REJECTED">Từ chối</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cấp độ</label>
            <select id="levelFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">-- Tất cả --</option>
                <option value="KHOA">Khoa</option>
                <option value="TRUONG">Trường</option>
            </select>
        </div>
        <div class="flex items-end">
            <button onclick="applyFilters()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                Áp dụng bộ lọc
            </button>
        </div>
    </div>
</div>

<!-- Requests Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ID</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tên hội thảo</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lĩnh vực</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Cấp độ</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Người yêu cầu</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Ngày dự kiến</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Trạng thái</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Hành động</th>
            </tr>
        </thead>
        <tbody id="requestsTableBody" class="divide-y divide-gray-200">
            <!-- Populated by JavaScript -->
        </tbody>
    </table>
    
    <!-- Empty State -->
    <div id="emptyState" class="text-center py-12">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 font-medium">Không có yêu cầu nào</p>
    </div>
</div>

<!-- Request Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Modal Overlay -->
    <div class="fixed inset-0 bg-black opacity-50" onclick="closeDetailModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-xl shadow-2xl max-w-3xl mx-auto mt-20 p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Chi tiết yêu cầu</h2>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Content -->
        <div id="detailContent" class="space-y-6">
            <!-- Populated by JavaScript -->
        </div>
        
        <!-- Actions -->
        <div id="detailActions" class="flex justify-end gap-3 mt-8 border-t border-gray-200 pt-6">
            <!-- Populated by JavaScript -->
        </div>
    </div>
</div>

<script>
let allRequests = [];

// Load requests on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
});

async function loadRequests() {
    try {
        const response = await fetch('/api/conference-requests', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load requests');
        
        const data = await response.json();
        allRequests = data.data.data || [];
        
        renderTable();
    } catch (error) {
        console.error('Error loading requests:', error);
        showNotification('Lỗi khi tải dữ liệu', 'error');
    }
}

function renderTable() {
    const tbody = document.getElementById('requestsTableBody');
    const emptyState = document.getElementById('emptyState');
    
    if (allRequests.length === 0) {
        tbody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    tbody.innerHTML = allRequests.map(request => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">#${request.request_id}</td>
            <td class="px-6 py-4 text-sm text-gray-900 font-medium">${request.title}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${request.field || '-'}</td>
            <td class="px-6 py-4 text-sm">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold ${
                    request.level_code === 'KHOA' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                }">
                    ${request.level_code === 'KHOA' ? 'Khoa' : 'Trường'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">${request.requester?.full_name || 'N/A'}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(request.expected_date)}</td>
            <td class="px-6 py-4 text-sm">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadge(request.status)}">
                    ${getStatusLabel(request.status)}
                </span>
            </td>
            <td class="px-6 py-4 text-sm space-x-2">
                <button onclick="showDetail(${request.request_id})" class="text-blue-600 hover:text-blue-800 font-medium">
                    Chi tiết
                </button>
                ${request.status === 'PENDING' ? `
                    <button onclick="approveRequest(${request.request_id})" class="text-green-600 hover:text-green-800 font-medium">
                        Duyệt
                    </button>
                    <button onclick="rejectRequest(${request.request_id})" class="text-red-600 hover:text-red-800 font-medium">
                        Từ chối
                    </button>
                ` : ''}
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'PENDING': 'bg-yellow-100 text-yellow-800',
        'APPROVED': 'bg-green-100 text-green-800',
        'REJECTED': 'bg-red-100 text-red-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
}

function getStatusLabel(status) {
    const labels = {
        'PENDING': 'Chờ duyệt',
        'APPROVED': 'Đã duyệt',
        'REJECTED': 'Từ chối'
    };
    return labels[status] || status;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('vi-VN');
}

function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const level = document.getElementById('levelFilter').value;
    
    // Filter logic would go here
    renderTable();
}

async function showDetail(requestId) {
    try {
        const response = await fetch(`/api/conference-requests/${requestId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load request details');
        
        const data = await response.json();
        const request = data.data;
        
        const contentDiv = document.getElementById('detailContent');
        const actionsDiv = document.getElementById('detailActions');
        
        contentDiv.innerHTML = `
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tên hội thảo</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">${request.title}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Lĩnh vực</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">${request.field}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Cấp độ</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">${request.level_code === 'KHOA' ? 'Khoa' : 'Trường'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Ngày dự kiến</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">${formatDate(request.expected_date)}</p>
                </div>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Mục tiêu</p>
                <p class="text-gray-700 mt-2">${request.objective}</p>
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm font-semibold text-gray-900 mb-3">Thông tin Chủ tịch</p>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600">Họ tên</p>
                        <p class="font-medium text-gray-900 mt-1">N/A</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600">Email</p>
                        <p class="font-medium text-gray-900 mt-1">N/A</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600">Điện thoại</p>
                        <p class="font-medium text-gray-900 mt-1">N/A</p>
                    </div>
                </div>
            </div>
            
            ${request.co_chairs && request.co_chairs.length > 0 ? `
            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm font-semibold text-gray-900 mb-3">Thêm viên bổ sung</p>
                <div class="space-y-2">
                    ${request.co_chairs.map(chair => `
                        <div class="bg-blue-50 p-3 rounded border border-blue-200">
                            <p class="font-medium text-gray-900">${chair.fullname}</p>
                            <p class="text-sm text-gray-600">${chair.email}</p>
                            ${chair.affiliation ? `<p class="text-xs text-gray-500">${chair.affiliation}</p>` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
            
            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm font-semibold text-gray-900 mb-2">File đề xuất</p>
                <a href="/storage/${request.proposal_file}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                    Tải xuống PDF
                </a>
            </div>
        `;
        
        actionsDiv.innerHTML = `
            <button onclick="closeDetailModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Đóng
            </button>
            ${request.status === 'PENDING' ? `
                <button onclick="approveRequest(${request.request_id}); closeDetailModal();" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    ✓ Duyệt
                </button>
                <button onclick="rejectRequest(${request.request_id}); closeDetailModal();" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    ✗ Từ chối
                </button>
            ` : ''}
        `;
        
        document.getElementById('detailModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading details:', error);
        showNotification('Lỗi khi tải chi tiết', 'error');
    }
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

async function approveRequest(requestId) {
    if (!confirm('Bạn chắc chắn muốn duyệt yêu cầu này?')) return;
    
    try {
        const response = await fetch(`/api/conference-requests/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        });
        
        if (!response.ok) throw new Error('Failed to approve request');
        
        showNotification('Yêu cầu đã được duyệt thành công!', 'success');
        loadRequests();
    } catch (error) {
        console.error('Error approving request:', error);
        showNotification('Lỗi khi duyệt yêu cầu', 'error');
    }
}

async function rejectRequest(requestId) {
    const reason = prompt('Vui lòng nhập lý do từ chối:');
    if (!reason) return;
    
    try {
        const response = await fetch(`/api/conference-requests/${requestId}/reject`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ reason: reason })
        });
        
        if (!response.ok) throw new Error('Failed to reject request');
        
        showNotification('Yêu cầu đã bị từ chối', 'success');
        loadRequests();
    } catch (error) {
        console.error('Error rejecting request:', error);
        showNotification('Lỗi khi từ chối yêu cầu', 'error');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}
</script>
@endsection
