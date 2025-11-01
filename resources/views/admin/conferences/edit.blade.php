@extends('layouts.admin')

@section('title', 'Chỉnh sửa Hội Thảo')

@section('content')
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}
</style>

<div class="py-6 opacity-0 animate-fade-in-up">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.conferences.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Quản lý hội thảo
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Chỉnh sửa</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Chỉnh sửa hội thảo
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $conference->title }}
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.conferences.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
                <a href="{{ route('conferences.show', $conference->conference_id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Xem trang public
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <form id="conference-edit-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin cơ bản</h3>
                    <p class="mt-1 text-sm text-gray-500">Cập nhật thông tin chính của hội thảo</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Title and Acronym -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Tên hội thảo *</label>
                            <input type="text" name="title" id="title" value="{{ $conference->title }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="acronym" class="block text-sm font-medium text-gray-700">Mã hội thảo</label>
                            <input type="text" name="acronym" id="acronym" value="{{ $conference->acronym }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ $conference->description }}</textarea>
                    </div>

                    <!-- Level and Year -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="level_code" class="block text-sm font-medium text-gray-700">Cấp độ</label>
                            <select name="level_code" id="level_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="">Chọn cấp độ</option>
                                <option value="INTERNATIONAL" {{ $conference->level_code == 'INTERNATIONAL' ? 'selected' : '' }}>Quốc tế</option>
                                <option value="NATIONAL" {{ $conference->level_code == 'NATIONAL' ? 'selected' : '' }}>Quốc gia</option>
                                <option value="REGIONAL" {{ $conference->level_code == 'REGIONAL' ? 'selected' : '' }}>Khu vực</option>
                                <option value="INSTITUTIONAL" {{ $conference->level_code == 'INSTITUTIONAL' ? 'selected' : '' }}>Cơ sở</option>
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Năm</label>
                            <input type="number" name="year" id="year" value="{{ $conference->year }}" min="2020" max="2030"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Địa điểm</label>
                        <input type="text" name="location" id="location" value="{{ $conference->location }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Dates Section -->
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thời gian quan trọng</h3>
                    <p class="mt-1 text-sm text-gray-500">Các mốc thời gian của hội thảo</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Conference Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $conference->start_date ? $conference->start_date->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $conference->end_date ? $conference->end_date->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Deadlines -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="deadline_submission" class="block text-sm font-medium text-gray-700">Hạn nộp bài</label>
                            <input type="date" name="deadline_submission" id="deadline_submission" value="{{ $conference->deadline_submission ? $conference->deadline_submission->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="deadline_review" class="block text-sm font-medium text-gray-700">Hạn review</label>
                            <input type="date" name="deadline_review" id="deadline_review" value="{{ $conference->deadline_review ? $conference->deadline_review->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="deadline_camera_ready" class="block text-sm font-medium text-gray-700">Hạn camera ready</label>
                            <input type="date" name="deadline_camera_ready" id="deadline_camera_ready" value="{{ $conference->deadline_camera_ready ? $conference->deadline_camera_ready->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chair Information (Read-only) -->
            @if($conference->chair)
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin chủ tịch</h3>
                    <p class="mt-1 text-sm text-gray-500">Thông tin người chịu trách nhiệm hội thảo</p>
                </div>
                
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $conference->chair->full_name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $conference->chair->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-end space-x-4">
                <button type="button" onclick="resetForm()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Đặt lại
                </button>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span id="submit-text">Cập nhật hội thảo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('conference-edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('[type="submit"]');
    const submitText = document.getElementById('submit-text');
    const originalText = submitText.textContent;
    
    // Disable submit button and show loading
    submitBtn.disabled = true;
    submitText.textContent = 'Đang cập nhật...';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('{{ route("admin.conferences.update", $conference->conference_id) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Cập nhật thành công!', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("admin.conferences.index") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Có lỗi xảy ra khi cập nhật', 'error');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitText.textContent = originalText;
    });
});

function resetForm() {
    if (confirm('Bạn có chắc muốn đặt lại form? Các thay đổi sẽ bị mất.')) {
        document.getElementById('conference-edit-form').reset();
        // Restore original values
        document.getElementById('title').value = '{{ $conference->title }}';
        document.getElementById('acronym').value = '{{ $conference->acronym }}';
        document.getElementById('description').value = '{{ $conference->description }}';
        document.getElementById('level_code').value = '{{ $conference->level_code }}';
        document.getElementById('year').value = '{{ $conference->year }}';
        document.getElementById('location').value = '{{ $conference->location }}';
        document.getElementById('start_date').value = '{{ $conference->start_date ? $conference->start_date->format("Y-m-d") : "" }}';
        document.getElementById('end_date').value = '{{ $conference->end_date ? $conference->end_date->format("Y-m-d") : "" }}';
        document.getElementById('deadline_submission').value = '{{ $conference->deadline_submission ? $conference->deadline_submission->format("Y-m-d") : "" }}';
        document.getElementById('deadline_review').value = '{{ $conference->deadline_review ? $conference->deadline_review->format("Y-m-d") : "" }}';
        document.getElementById('deadline_camera_ready').value = '{{ $conference->deadline_camera_ready ? $conference->deadline_camera_ready->format("Y-m-d") : "" }}';
        
        showNotification('Đã đặt lại form', 'info');
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `fixed top-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Form validation
document.getElementById('start_date').addEventListener('change', function() {
    const endDate = document.getElementById('end_date');
    if (this.value) {
        endDate.min = this.value;
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date');
    if (this.value && startDate.value && this.value < startDate.value) {
        showNotification('Ngày kết thúc không thể nhỏ hơn ngày bắt đầu', 'warning');
        this.value = '';
    }
});
</script>
@endsection