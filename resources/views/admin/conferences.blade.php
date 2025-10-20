@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Add CSS animations and styles -->
<style>
    /* Animation styles */
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
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animate-slideIn {
        animation: slideIn 0.5s ease-out forwards;
    }
    
    /* Hover effects */
    .hover-scale:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease-in-out;
    }
    
    /* Enhanced button styles */
    .btn-action {
        position: relative;
        overflow: hidden;
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
    
    /* Tooltip styles */
    .tooltip-container {
        position: relative;
    }
    
    .tooltip-container:hover .tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateY(-5px);
    }
    
    .tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(0);
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        max-width: 300px;
        white-space: normal;
        word-wrap: break-word;
    }
    
    .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0, 0, 0, 0.9);
    }
    
    /* Status dot animation */
    .status-dot {
        position: relative;
    }
    
    .status-dot.pulse::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.3;
        }
    }
    
    /* Responsive table improvements */
    @media (max-width: 768px) {
        .mobile-stack {
            display: block !important;
        }
        
        .mobile-stack td {
            display: block;
            padding: 8px 12px;
            border: none;
        }
        
        .mobile-stack td:first-child {
            border-top: 1px solid #e5e7eb;
        }
    }
</style>

<script>
    // View conference details
    window.viewConference = function(conferenceId) {
        fetch(`/admin/api/conferences/${conferenceId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Create modal content
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';
                modal.innerHTML = `
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin hội thảo</h3>
                            <div class="space-y-3">
                                <div><strong>Tên:</strong> ${data.conference.title || 'N/A'}</div>
                                <div><strong>Mô tả:</strong> ${data.conference.description || 'N/A'}</div>
                                <div><strong>Ngày bắt đầu:</strong> ${data.conference.start_date ? new Date(data.conference.start_date).toLocaleDateString('vi-VN') : 'N/A'}</div>
                                <div><strong>Ngày kết thúc:</strong> ${data.conference.end_date ? new Date(data.conference.end_date).toLocaleDateString('vi-VN') : 'N/A'}</div>
                                <div><strong>Địa điểm:</strong> ${data.conference.location || 'N/A'}</div>
                                <div><strong>Trạng thái:</strong> ${data.conference.status || 'N/A'}</div>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button onclick="this.closest('.fixed').remove()" 
                                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            } else {
                alert('Không thể tải thông tin hội thảo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thông tin hội thảo');
        });
    };

    // Bulk delete conferences
    window.bulkDeleteConferences = function() {
        const selectedConferences = Array.from(document.querySelectorAll('input[name="selected_conferences"]:checked')).map(cb => cb.value);
        if (selectedConferences.length === 0) {
            alert('Vui lòng chọn ít nhất một hội thảo');
            return;
        }
        
        if (!confirm(`Bạn có chắc chắn muốn xóa ${selectedConferences.length} hội thảo đã chọn?`)) {
            return;
        }
        
        fetch('/admin/api/conferences/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ conference_ids: selectedConferences })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa hội thảo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa hội thảo');
        });
    };
</script>

<div class="container mx-auto px-4 py-6" 
     x-data="{
         selectedConferences: [],
         selectAll: false,
         
         toggleAll() {
             if (this.selectAll) {
                 const checkboxes = document.querySelectorAll('input[name=\'selected_conferences\']');
                 this.selectedConferences = Array.from(checkboxes).map(cb => cb.value);
             } else {
                 this.selectedConferences = [];
             }
         },
         
         updateSelectAll() {
             const checkboxes = document.querySelectorAll('input[name=\'selected_conferences\']');
             this.selectAll = checkboxes.length > 0 && this.selectedConferences.length === checkboxes.length;
         }
     }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all duration-200 hover:scale-105 btn-action">
            <!-- Plus Icon -->
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Tạo hội thảo mới</span>
            </div>
        </button>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-show="selectedConferences.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 animate-slideIn">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-blue-700">
                Đã chọn <span x-text="selectedConferences.length"></span> hội thảo
            </span>
            <div class="space-x-2">
                <button onclick="bulkDeleteConferences()" 
                        class="px-3 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Xóa</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
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
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Tên hội thảo</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-32 sm:w-40">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Thời gian</span>
                            </div>
                        </th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Địa điểm</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-20">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Trạng thái</span>
                            </div>
                        </th>
                        <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider w-24">
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
                @forelse($conferences as $conference)
                <tr class="hover:bg-gray-50 transition-colors duration-150 animate-fadeIn" 
                    style="animation-delay: {{ $loop->index * 50 }}ms">
                    <td class="px-3 sm:px-6 py-4">
                        <input type="checkbox" 
                               name="selected_conferences"
                               value="{{ $conference->conference_id }}"
                               x-model="selectedConferences"
                               @change="updateSelectAll()"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all">
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 font-medium">
                        {{ $conference->conference_id }}
                    </td>
                    <td class="px-3 sm:px-6 py-4">
                        <div class="space-y-1">
                            <div class="text-sm font-medium text-gray-900 group relative">
                                <div class="truncate max-w-xs sm:max-w-sm lg:max-w-md xl:max-w-lg cursor-help" 
                                     title="{{ $conference->title }}">
                                    {{ Str::limit($conference->title, 60) }}
                                </div>
                                <!-- Tooltip -->
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded-md py-2 px-3 z-10 bottom-full left-0 mb-2 max-w-xs shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-200">
                                    {{ $conference->title }}
                                    <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                </div>
                            </div>
                            <!-- Mobile: show additional info below title -->
                            <div class="lg:hidden text-xs text-gray-500 space-y-1">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate">{{ $conference->location ?? 'Chưa có địa điểm' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                        <div class="flex items-center space-x-1 text-xs text-gray-700">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m') }}
                                <span class="text-gray-400 mx-1">-</span>
                                {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}
                            </span>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate max-w-xs" title="{{ $conference->location ?? 'Chưa có địa điểm' }}">
                                {{ Str::limit($conference->location ?? 'Chưa có địa điểm', 25) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            @php
                                $statusConfig = [
                                    'ACTIVE' => ['color' => 'bg-green-500', 'text' => 'Hoạt động', 'pulse' => false],
                                    'open' => ['color' => 'bg-green-500', 'text' => 'Mở', 'pulse' => true],
                                    'IN_PROGRESS' => ['color' => 'bg-blue-500', 'text' => 'Đang diễn ra', 'pulse' => true],
                                    'PENDING' => ['color' => 'bg-yellow-500', 'text' => 'Chờ duyệt', 'pulse' => false],
                                    'INACTIVE' => ['color' => 'bg-gray-500', 'text' => 'Không hoạt động', 'pulse' => false],
                                    'CLOSED' => ['color' => 'bg-red-500', 'text' => 'Đã đóng', 'pulse' => false],
                                ];
                                $config = $statusConfig[$conference->status] ?? ['color' => 'bg-gray-400', 'text' => $conference->status, 'pulse' => false];
                            @endphp
                            
                            <div class="flex items-center space-x-2" title="{{ $config['text'] }}">
                                <div class="relative">
                                    <div class="w-3 h-3 rounded-full {{ $config['color'] }}"></div>
                                    @if($config['pulse'])
                                        <div class="absolute inset-0 w-3 h-3 rounded-full {{ $config['color'] }} animate-ping opacity-30"></div>
                                    @endif
                                </div>
                                <span class="hidden sm:inline text-xs font-medium text-gray-700">{{ $config['text'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <!-- View Button -->
                            <button onclick="viewConference({{ $conference->conference_id }})" 
                                    class="group inline-flex items-center p-1.5 sm:p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105"
                                    title="Xem chi tiết">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            <!-- Edit Button -->
                            <button class="group inline-flex items-center p-1.5 sm:p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-all duration-200 hover:scale-105"
                                    title="Chỉnh sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            <button class="group inline-flex items-center p-1.5 sm:p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-all duration-200 hover:scale-105"
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center space-y-4">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <p class="text-lg font-medium">Chưa có hội thảo nào</p>
                                <p class="text-sm">Tạo hội thảo đầu tiên của bạn</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($conferences->hasPages())
    <div class="mt-6 flex justify-center">
        <div class="bg-white rounded-lg shadow px-6 py-3">
            {{ $conferences->links() }}
        </div>
    </div>
    @endif
</div>
@endsection