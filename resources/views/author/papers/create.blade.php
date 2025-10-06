<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nộp bài mới - HUIT Conferences</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
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
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
        <div class="px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('author.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                        <span class="text-blue-700 font-bold text-xl">H</span>
                    </div>
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

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('author.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('author.papers.index') }}" class="text-blue-600 hover:text-blue-800">Bài báo</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-600">Nộp bài mới</span>
            </nav>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Nộp bài báo mới</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin bài báo và tải lên file PDF</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-red-800 font-semibold mb-2">Có lỗi xảy ra:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Submission Form -->
        <form method="POST" action="{{ route('author.papers.store') }}" enctype="multipart/form-data" 
              x-data="{
                  coAuthors: [],
                  addCoAuthor() {
                      this.coAuthors.push({ name: '', email: '', organization: '', is_contact: false });
                  },
                  removeCoAuthor(index) {
                      this.coAuthors.splice(index, 1);
                  },
                  fileName: '',
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
            
            <div class="card p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b">Thông tin bài báo</h2>
                
                <!-- Conference Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Hội thảo <span class="text-red-500">*</span>
                    </label>
                    @if($conferences->count() > 0)
                    <select name="conference_id" class="input-field @error('conference_id') input-error @enderror" required>
                        <option value="">-- Chọn hội thảo --</option>
                        @foreach($conferences as $conf)
                        <option value="{{ $conf->conference_id }}" {{ old('conference_id') == $conf->conference_id ? 'selected' : '' }}>
                            {{ $conf->title }} (Deadline: {{ \Carbon\Carbon::parse($conf->deadline_submission)->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                    @error('conference_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800">Hiện tại không có hội thảo nào đang mở nộp bài.</p>
                    </div>
                    @endif
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tiêu đề bài báo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                           class="input-field @error('title') input-error @enderror" 
                           placeholder="Nhập tiêu đề bài báo (tối đa 500 ký tự)" 
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
                              class="input-field @error('abstract') input-error @enderror" 
                              placeholder="Nhập tóm tắt nội dung bài báo" 
                              required>{{ old('abstract') }}</textarea>
                    @error('abstract')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Mô tả ngắn gọn về nội dung, phương pháp và kết quả nghiên cứu</p>
                </div>

                <!-- Keywords -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Từ khóa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="keywords" value="{{ old('keywords') }}" 
                           class="input-field @error('keywords') input-error @enderror" 
                           placeholder="Ví dụ: machine learning, neural networks, deep learning" 
                           maxlength="500" required>
                    @error('keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Các từ khóa cách nhau bởi dấu phẩy</p>
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        File bài báo (PDF) <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition">
                        <input type="file" name="paper_file" accept=".pdf" 
                               class="hidden" id="paper_file" 
                               @change="updateFileInfo($event)" required>
                        <label for="paper_file" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">Nhấn để chọn file PDF</p>
                            <p class="text-sm text-gray-500 mt-1">Dung lượng tối đa: 10MB</p>
                        </label>
                        <div x-show="fileName" class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-blue-800 font-medium" x-text="fileName"></p>
                            <p class="text-blue-600 text-sm" x-text="fileSize"></p>
                        </div>
                    </div>
                    @error('paper_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Co-Authors Section -->
            <div class="card p-8 mb-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Tác giả</h2>
                        <p class="text-sm text-gray-600 mt-1">Bạn là tác giả chính. Có thể thêm đồng tác giả nếu có.</p>
                    </div>
                </div>

                <!-- Primary Author (Current User) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">1. {{ Auth::user()->full_name }} (Bạn)</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->organization }}</p>
                        </div>
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            Tác giả chính
                        </span>
                    </div>
                </div>

                <!-- Co-Authors List -->
                <template x-for="(coAuthor, index) in coAuthors" :key="index">
                    <div class="co-author-item bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-900" x-text="'Đồng tác giả ' + (index + 2)"></h3>
                            <button type="button" @click="removeCoAuthor(index)" 
                                    class="text-red-600 hover:text-red-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                                <input type="text" :name="'co_authors[' + index + '][name]'" 
                                       class="input-field" placeholder="Nhập họ tên" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" :name="'co_authors[' + index + '][email]'" 
                                       class="input-field" placeholder="example@email.com" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                                <input type="text" :name="'co_authors[' + index + '][organization]'" 
                                       class="input-field" placeholder="Tên tổ chức/trường" required>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Add Co-Author Button -->
                <button type="button" @click="addCoAuthor()" 
                        class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-gray-600 hover:border-blue-400 hover:text-blue-600 transition flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium">Thêm đồng tác giả</span>
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('author.papers.index') }}" 
                   class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn-primary flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Nộp bài báo</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="mt-12 py-6 text-center text-gray-600 text-sm">
        <p>&copy; 2025 HUIT Conferences. All rights reserved.</p>
    </footer>
</body>
</html>
