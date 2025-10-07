<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Khai báo COI - Reviewer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar (Purple) -->
    <nav class="bg-gradient-to-r from-purple-800 via-purple-700 to-purple-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    <span class="text-xl font-bold">HUIT Conferences</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ Auth::user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:text-purple-200 transition">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <a href="{{ route('reviewer.dashboard') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Phân công của tôi</span>
                </button>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Đánh giá của tôi</span>
                </button>

                <a href="{{ route('reviewer.coi.index') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-purple-50 text-purple-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Khai báo COI</span>
                </a>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="container mx-auto" x-data="coiForm()">
        <div class="max-w-4xl mx-auto">
            <!-- Messages -->
            @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                {{ session('error') }}
            </div>
            @endif

            <!-- Info Card -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">Khi nào cần khai báo COI?</p>
                        <p class="text-xs text-blue-700 mt-1">
                            Bạn phải khai báo COI nếu có bất kỳ xung đột lợi ích nào với bài báo được phân công phản biện, 
                            bao gồm: đồng tác giả, cùng tổ chức, quan hệ thầy trò, cộng tác gần đây, hoặc bất kỳ mối quan hệ 
                            nào có thể ảnh hưởng đến tính khách quan của bạn.
                        </p>
                    </div>
                </div>
            </div>

            <!-- COI Declaration Form -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-purple-100 border-b">
                    <h2 class="text-lg font-bold text-gray-800">📝 Khai báo Conflict of Interest</h2>
                    <p class="text-sm text-gray-600 mt-1">Vui lòng cung cấp đầy đủ thông tin</p>
                </div>

                <form action="{{ route('reviewer.coi.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Conference Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hội thảo: *
                        </label>
                        <select x-model="selectedConference" 
                                @change="loadPapers()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">-- Chọn hội thảo --</option>
                            @foreach($conferences as $conf)
                            <option value="{{ $conf->conference_id }}">{{ $conf->conference_code }} - {{ $conf->title }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Chọn hội thảo để xem bài báo được phân công</p>
                    </div>

                    <!-- Paper Search and Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bài báo: *
                        </label>
                        
                        <!-- Search Input -->
                        <div class="relative mb-3">
                            <input type="text" 
                                   x-model="searchQuery"
                                   @input.debounce.500ms="loadPapers()"
                                   placeholder="Tìm kiếm theo tiêu đề hoặc Paper ID..."
                                   class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- Papers List -->
                        <div class="border border-gray-300 rounded-lg max-h-64 overflow-y-auto" x-show="papers.length > 0">
                            <template x-for="paper in papers" :key="paper.paper_id">
                                <label class="flex items-start p-4 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition"
                                       :class="selectedPaper === paper.paper_id ? 'bg-purple-50 border-purple-200' : ''">
                                    <input type="radio" 
                                           name="paper_id" 
                                           :value="paper.paper_id"
                                           x-model="selectedPaper"
                                           required
                                           class="mt-1 text-purple-600 focus:ring-purple-500">
                                    <div class="ml-3 flex-1">
                                        <p class="font-semibold text-gray-900" x-text="paper.title"></p>
                                        <div class="flex items-center space-x-3 mt-1">
                                            <span class="text-xs text-gray-500" x-text="'Paper #' + paper.paper_id"></span>
                                            <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded" x-text="paper.status"></span>
                                            <span class="text-xs text-gray-400" x-text="'Phân công: ' + formatDate(paper.assigned_at)"></span>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loading" class="text-center py-4 text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang tải...
                        </div>

                        <!-- Empty State -->
                        <div x-show="!loading && papers.length === 0 && selectedConference" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">Không tìm thấy bài báo phù hợp</p>
                            <p class="text-xs text-gray-400 mt-1">Hoặc bạn đã khai báo COI cho tất cả bài báo</p>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">Chỉ hiển thị bài báo được phân công và chưa khai báo COI</p>
                    </div>

                    <!-- COI Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Loại COI: *
                        </label>
                        <div class="space-y-3">
                            @foreach($coiTypes as $type)
                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50"
                                   :class="selectedCoiType === '{{ $type->coi_code }}' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                <input type="radio" 
                                       name="coi_code" 
                                       value="{{ $type->coi_code }}"
                                       x-model="selectedCoiType"
                                       required
                                       class="mt-1 text-purple-600 focus:ring-purple-500">
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-gray-900">{{ $type->coi_name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $type->description }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Evidence -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bằng chứng / Lý do: *
                        </label>
                        <textarea name="evidence" 
                                  rows="4"
                                  required
                                  placeholder="Vui lòng mô tả chi tiết lý do tại sao bạn có xung đột lợi ích với bài báo này..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  x-model="evidence"
                                  maxlength="1000"></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">Tối đa 1000 ký tự</p>
                            <p class="text-xs text-gray-500" x-text="evidence.length + '/1000'"></p>
                        </div>
                    </div>

                    <!-- Note (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ghi chú thêm (tùy chọn):
                        </label>
                        <textarea name="note" 
                                  rows="3"
                                  placeholder="Thêm ghi chú nếu cần..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  x-model="note"
                                  maxlength="500"></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">Tối đa 500 ký tự</p>
                            <p class="text-xs text-gray-500" x-text="note.length + '/500'"></p>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">Lưu ý</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Sau khi khai báo, Chair sẽ xem xét và quyết định phương án xử lý. 
                                    Bạn chỉ có thể rút lại khai báo nếu Chair chưa xử lý.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between pt-4 border-t">
                        <a href="{{ route('reviewer.coi.index') }}" 
                           class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">
                            ← Hủy
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition
                                disabled:bg-gray-300 disabled:cursor-not-allowed"
                                :disabled="!selectedPaper || !selectedCoiType || !evidence">
                            Gửi khai báo →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function coiForm() {
            return {
                selectedConference: '',
                selectedPaper: '',
                selectedCoiType: '',
                searchQuery: '',
                evidence: '',
                note: '',
                papers: [],
                loading: false,

                async loadPapers() {
                    if (!this.selectedConference) {
                        this.papers = [];
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('reviewer.coi.search-papers') }}?conference_id=${this.selectedConference}&search=${encodeURIComponent(this.searchQuery)}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.papers = data;
                    } catch (error) {
                        console.error('Error loading papers:', error);
                        this.papers = [];
                    }
                    this.loading = false;
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('vi-VN');
                }
            }
        }
    </script>
        </main>
    </div>
</body>
</html>
