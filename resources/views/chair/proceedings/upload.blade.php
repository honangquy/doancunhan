@extends('layouts.chair')

@section('title', 'Upload Kỷ yếu Hội thảo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Upload Kỷ yếu Hội thảo</h1>
                <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
            </div>
            <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Conference Info Card -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hội thảo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if($conference->acronym)
                <div>
                    <span class="font-medium text-gray-700">Tên viết tắt:</span>
                    <span class="text-gray-600">{{ $conference->acronym }}</span>
                </div>
                @endif

                @if($conference->start_date)
                <div>
                    <span class="font-medium text-gray-700">Ngày tổ chức:</span>
                    <span class="text-gray-600">{{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}</span>
                </div>
                @endif

                @if($conference->location)
                <div>
                    <span class="font-medium text-gray-700">Địa điểm:</span>
                    <span class="text-gray-600">{{ $conference->location }}</span>
                </div>
                @endif

                <div>
                    <span class="font-medium text-gray-700">Trạng thái:</span>
                    @if($conference->status === 'ACTIVE')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                            Hoạt động
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $conference->status }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Current Proceedings Status -->
    @if($hasProceedings)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Kỷ yếu đã được xuất bản</h3>

                    @if($conference->proceedings_published_at)
                        <p class="text-sm text-blue-700 mb-3">
                            Ngày xuất bản: {{ \Carbon\Carbon::parse($conference->proceedings_published_at)->format('d/m/Y H:i') }}
                        </p>
                    @endif

                    <!-- Download Button -->
                    <a href="{{ Storage::url($conference->proceedings_file) }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tải kỷ yếu PDF
                    </a>

                    @if($isAdmin ?? false)
                        <p class="text-sm text-blue-700 mt-4">
                            <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Với quyền Admin, bạn có thể thay thế kỷ yếu hiện tại bằng cách upload file mới bên dưới.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Form -->
    @if(!$hasProceedings || ($isAdmin ?? false))
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    @if($hasProceedings)
                        Thay thế kỷ yếu (Admin)
                    @else
                        Upload kỷ yếu mới
                    @endif
                </h2>

                @if($hasProceedings && ($isAdmin ?? false))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <strong>Cảnh báo:</strong> Upload file mới sẽ thay thế kỷ yếu hiện tại và không thể hoàn tác.
                        </div>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('chair.proceedings.upload.store', $conference->conference_id) }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    <!-- File Upload -->
                    <div>
                        <label for="proceedings_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Chọn file kỷ yếu (PDF)
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="proceedings_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Chọn file PDF</span>
                                        <input id="proceedings_file"
                                               name="proceedings_file"
                                               type="file"
                                               accept="application/pdf"
                                               class="sr-only"
                                               onchange="updateFileName(this)">
                                    </label>
                                    <p class="pl-1">hoặc kéo thả file vào đây</p>
                                </div>
                                <p class="text-xs text-gray-500">Chỉ chấp nhận file PDF, tối đa 50MB</p>
                                <p id="file-name" class="text-sm text-blue-600 font-medium mt-2"></p>
                            </div>
                        </div>

                        @error('proceedings_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Hướng dẫn:</h3>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li>Tải toàn bộ bài báo đã được chấp nhận (Accepted) về máy</li>
                            <li>Sử dụng công cụ ghép PDF để tạo file kỷ yếu hoàn chỉnh</li>
                            <li>Đảm bảo file PDF có kích thước không quá 50MB</li>
                            <li>Sau khi upload, kỷ yếu sẽ được hiển thị công khai trên trang hội thảo</li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('chair.proceedings.index', $conference->conference_id) }}"
                           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Hủy
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            @if($hasProceedings)
                                Thay thế kỷ yếu
                            @else
                                Upload & Xuất bản
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Non-admin cannot re-upload -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex items-center text-gray-600">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p>Kỷ yếu đã được xuất bản. Chỉ Admin mới có quyền thay thế kỷ yếu.</p>
            </div>
        </div>
    @endif
</div>

<script>
function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        fileNameDisplay.textContent = `Đã chọn: ${file.name} (${sizeMB} MB)`;
    } else {
        fileNameDisplay.textContent = '';
    }
}

// Drag & drop support
const dropArea = document.querySelector('[for="proceedings_file"]').closest('.border-dashed');
if (dropArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('border-blue-500', 'bg-blue-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
        });
    });

    dropArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        const input = document.getElementById('proceedings_file');
        input.files = files;
        updateFileName(input);
    });
}
</script>
@endsection
