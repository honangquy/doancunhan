@extends('layouts.author')

@section('title', 'Nộp bài mới')

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }
    .input-error {
        border-color: #ef4444;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        padding: 12px 32px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    .co-author-item {
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Nộp bài mới</h1>
        <p class="text-gray-600">Điền thông tin chi tiết về bài báo của bạn</p>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-bold text-white">Thông tin bài báo</h2>
            <p class="text-blue-100 mt-1">Vui lòng điền đầy đủ các thông tin bắt buộc</p>
        </div>

        <form method="POST" action="{{ route('author.papers.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf

            <!-- Conference Selection -->
            <div class="form-group">
                <label class="form-label" for="conference_id">
                    Hội thảo <span class="text-red-500">*</span>
                </label>
                <select name="conference_id" id="conference_id" 
                        class="form-input @error('conference_id') input-error @enderror"
                        required>
                    <option value="">-- Chọn hội thảo --</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->conference_id }}" 
                                {{ old('conference_id') == $conference->conference_id ? 'selected' : '' }}>
                            {{ $conference->title }} 
                            (Deadline: {{ \Carbon\Carbon::parse($conference->deadline_submission)->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('conference_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @if($conferences->isEmpty())
                    <div class="mt-2 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-yellow-800 text-sm">
                                Hiện tại bạn chưa được phép tham gia hội thảo nào với vai trò tác giả. 
                                Vui lòng gửi yêu cầu tham gia trước khi nộp bài.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Paper Title -->
            <div class="form-group">
                <label class="form-label" for="title">
                    Tiêu đề bài báo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" 
                       class="form-input @error('title') input-error @enderror"
                       value="{{ old('title') }}" 
                       placeholder="Nhập tiêu đề bài báo..."
                       maxlength="500"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Abstract -->
            <div class="form-group">
                <label class="form-label" for="abstract">
                    Tóm tắt <span class="text-red-500">*</span>
                </label>
                <textarea name="abstract" id="abstract" 
                          class="form-input form-textarea @error('abstract') input-error @enderror"
                          placeholder="Nhập tóm tắt bài báo..."
                          required>{{ old('abstract') }}</textarea>
                @error('abstract')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keywords -->
            <div class="form-group">
                <label class="form-label" for="keywords">
                    Từ khóa <span class="text-red-500">*</span>
                </label>
                <input type="text" name="keywords" id="keywords" 
                       class="form-input @error('keywords') input-error @enderror"
                       value="{{ old('keywords') }}" 
                       placeholder="Nhập từ khóa, phân cách bởi dấu phẩy..."
                       maxlength="500"
                       required>
                <p class="text-gray-500 text-sm mt-1">Ví dụ: Machine Learning, Artificial Intelligence, Data Mining</p>
                @error('keywords')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label class="form-label" for="paper_file">
                    File bài báo <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="paper_file" id="paper_file" 
                           accept=".pdf" 
                           class="hidden @error('paper_file') input-error @enderror"
                           required>
                    <label for="paper_file" class="cursor-pointer">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-600 font-medium mb-2">Click để chọn file PDF</p>
                        <p class="text-gray-500 text-sm">Chỉ chấp nhận file PDF, tối đa 10MB</p>
                    </label>
                </div>
                @error('paper_file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Co-authors Section -->
            <div class="form-group" x-data="{ coAuthors: [] }">
                <div class="flex items-center justify-between mb-4">
                    <label class="form-label mb-0">Đồng tác giả</label>
                    <button type="button" 
                            @click="coAuthors.push({ name: '', email: '', organization: '' })"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Thêm đồng tác giả</span>
                    </button>
                </div>
                
                <div x-show="coAuthors.length === 0" class="text-gray-500 text-sm italic p-4 bg-gray-50 rounded-lg">
                    Chưa có đồng tác giả nào. Bấm "Thêm đồng tác giả" để thêm.
                </div>
                
                <template x-for="(coAuthor, index) in coAuthors" :key="index">
                    <div class="co-author-item bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-800">Đồng tác giả #<span x-text="index + 1"></span></h4>
                            <button type="button" 
                                    @click="coAuthors.splice(index, 1)"
                                    class="text-red-600 hover:text-red-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
                                <input type="text" 
                                       :name="'co_authors[' + index + '][name]'"
                                       x-model="coAuthor.name"
                                       class="form-input text-sm"
                                       placeholder="Nhập họ tên...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" 
                                       :name="'co_authors[' + index + '][email]'"
                                       x-model="coAuthor.email"
                                       class="form-input text-sm"
                                       placeholder="Nhập email...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tổ chức</label>
                                <input type="text" 
                                       :name="'co_authors[' + index + '][organization]'"
                                       x-model="coAuthor.organization"
                                       class="form-input text-sm"
                                       placeholder="Nhập tổ chức...">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('author.papers.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Hủy bỏ
                </a>
                
                <button type="submit" class="btn-primary" {{ $conferences->isEmpty() ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Nộp bài báo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // File upload preview
    document.getElementById('paper_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const label = e.target.nextElementSibling;
        
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            label.innerHTML = `
                <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-600 font-medium mb-2">${fileName}</p>
                <p class="text-gray-500 text-sm">Kích thước: ${fileSize} MB</p>
            `;
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const conferenceId = document.getElementById('conference_id').value;
        if (!conferenceId) {
            e.preventDefault();
            alert('Vui lòng chọn hội thảo!');
            return;
        }
        
        const title = document.getElementById('title').value.trim();
        if (!title) {
            e.preventDefault();
            alert('Vui lòng nhập tiêu đề bài báo!');
            return;
        }
        
        const file = document.getElementById('paper_file').files[0];
        if (!file) {
            e.preventDefault();
            alert('Vui lòng chọn file bài báo!');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            e.preventDefault();
            alert('File quá lớn! Vui lòng chọn file nhỏ hơn 10MB.');
            return;
        }
    });
</script>
@endpush
@endsection