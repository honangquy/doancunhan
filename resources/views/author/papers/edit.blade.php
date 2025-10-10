<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chỉnh sửa bài báo - HUIT Conferences</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            padding: 12px 32px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
        <div class="px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('author.dashboard') }}" class="flex items-center space-x-3">
                    <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md" />
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Author Portal</div>
                    </div>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('author.dashboard') }}" class="px-4 py-2 hover:bg-blue-700 rounded-lg transition">Dashboard</a>
                    <a href="{{ route('author.papers.index') }}" class="px-4 py-2 hover:bg-blue-700 rounded-lg transition">Bài báo</a>
                    <span class="text-white">{{ Auth::user()->full_name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('author.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('author.papers.index') }}" class="text-blue-600 hover:text-blue-800">Bài báo</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('author.papers.show', $paper->paper_id) }}" class="text-blue-600 hover:text-blue-800">Chi tiết</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-600">Chỉnh sửa</span>
            </nav>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa bài báo</h1>
            <p class="text-gray-600 mt-1">Cập nhật thông tin bài báo #{{ $paper->paper_id }}</p>
        </div>

        <!-- Warning -->
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-yellow-800 font-semibold">Lưu ý quan trọng:</p>
                    <ul class="text-yellow-700 text-sm mt-1 space-y-1">
                        <li>• Deadline nộp bài: <strong>{{ \Carbon\Carbon::parse($paper->deadline_submission)->format('d/m/Y') }}</strong></li>
                        <li>• Chỉ có thể chỉnh sửa bài báo ở trạng thái Nháp hoặc Đã nộp</li>
                        <li>• Nếu thay file PDF mới, file cũ sẽ bị xóa</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <p class="text-red-800 font-semibold mb-2">Có lỗi xảy ra:</p>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('author.papers.update', $paper->paper_id) }}" enctype="multipart/form-data"
              x-data="{
                  coAuthors: {{ json_encode($coAuthors->map(function($author) {
                      return [
                          'user_id' => $author->user_id,
                          'name' => $author->full_name,
                          'is_contact' => $author->is_contact
                      ];
                  })) }},
                  addCoAuthor() {
                      this.coAuthors.push({ user_id: '', name: '', is_contact: false });
                  },
                  removeCoAuthor(index) {
                      this.coAuthors.splice(index, 1);
                  },
                  fileName: '{{ $paper->file_path ? basename($paper->file_path) : '' }}',
                  fileSize: '',
                  updateFileInfo(event) {
                      const file = event.target.files[0];
                      if (file) {
                          this.fileName = file.name;
                          this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                      }
                  }
              }">
            @csrf
            @method('PUT')
            
            <div class="card p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b">Thông tin bài báo</h2>
                
                <!-- Conference -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Hội thảo <span class="text-red-500">*</span>
                    </label>
                    <select name="conference_id" class="input-field @error('conference_id') border-red-500 @enderror" required>
                        <option value="">-- Chọn hội thảo --</option>
                        @foreach($conferences as $conf)
                        <option value="{{ $conf->conference_id }}" 
                                {{ $paper->conference_id == $conf->conference_id ? 'selected' : '' }}>
                            {{ $conf->title }} (Deadline: {{ \Carbon\Carbon::parse($conf->deadline_submission)->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                    @error('conference_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tiêu đề <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $paper->title) }}" 
                           class="input-field @error('title') border-red-500 @enderror" 
                           maxlength="500" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Abstract -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tóm tắt <span class="text-red-500">*</span>
                    </label>
                    <textarea name="abstract" rows="6" 
                              class="input-field @error('abstract') border-red-500 @enderror" 
                              required>{{ old('abstract', $paper->abstract) }}</textarea>
                    @error('abstract')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keywords -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Từ khóa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="keywords" value="{{ old('keywords', $paper->keywords) }}" 
                           class="input-field @error('keywords') border-red-500 @enderror" 
                           maxlength="500" required>
                    @error('keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        File bài báo (PDF)
                    </label>
                    
                    @if($paper->file_path)
                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-blue-900">File hiện tại: {{ basename($paper->file_path) }}</p>
                                <p class="text-sm text-blue-700">Để giữ file này, không chọn file mới</p>
                            </div>
                        </div>
                        <a href="{{ route('author.papers.download', $paper->paper_id) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                    @endif
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                        <input type="file" name="paper_file" accept=".pdf" class="hidden" id="paper_file" 
                               @change="updateFileInfo($event)">
                        <label for="paper_file" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">Chọn file PDF mới (tùy chọn)</p>
                            <p class="text-sm text-gray-500 mt-1">Max 10MB</p>
                        </label>
                        <div x-show="fileSize" class="mt-4 p-3 bg-green-50 rounded-lg">
                            <p class="text-green-800 font-medium" x-text="fileName"></p>
                            <p class="text-green-600 text-sm" x-text="fileSize"></p>
                        </div>
                    </div>
                    @error('paper_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Co-Authors -->
            <div class="card p-8 mb-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Tác giả</h2>
                        <p class="text-sm text-gray-600 mt-1">Quản lý danh sách tác giả</p>
                    </div>
                </div>

                <!-- Primary Author -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">1. {{ Auth::user()->full_name }} (Bạn)</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        </div>
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Tác giả chính</span>
                    </div>
                </div>

                <!-- Co-Authors List -->
                <template x-for="(coAuthor, index) in coAuthors" :key="index">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-900" x-text="'Đồng tác giả ' + (index + 2)"></h3>
                            <button type="button" @click="removeCoAuthor(index)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div>
                            <input type="hidden" :name="'co_authors[' + index + '][user_id]'" x-model="coAuthor.user_id">
                            <p class="text-gray-900 font-medium" x-text="coAuthor.name"></p>
                        </div>
                    </div>
                </template>

                <p class="text-sm text-gray-500 italic">Lưu ý: Hiện tại chỉ hiển thị danh sách. Tính năng thêm/sửa đồng tác giả sẽ được cập nhật sau.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('author.papers.show', $paper->paper_id) }}" 
                   class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn-primary flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>

    <footer class="mt-12 py-6 text-center text-gray-600 text-sm">
        <p>&copy; 2025 HUIT Conferences. All rights reserved.</p>
    </footer>
</body>
</html>
