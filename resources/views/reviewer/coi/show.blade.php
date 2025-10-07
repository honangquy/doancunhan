<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết COI - Reviewer Dashboard</title>
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
            <div class="container mx-auto">
        <div class="max-w-6xl mx-auto">
            <!-- Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (2 columns) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- COI Information -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-red-500">
                        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                            <h2 class="text-lg font-bold text-red-800">⚠️ Thông tin COI</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Loại COI:</label>
                                <div class="mt-1">
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full font-medium">
                                        {{ $coi->coi_name }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $coi->coi_description }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nguồn:</label>
                                <div class="mt-1">
                                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                                        Tự khai báo
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Bằng chứng / Lý do:</label>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded border">{{ $coi->evidence }}</p>
                            </div>
                            
                            @if($coi->note)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Ghi chú:</label>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded border">{{ $coi->note }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Ngày khai báo:</label>
                                <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($coi->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paper Information -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500">
                        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                            <h2 class="text-lg font-bold text-blue-800">📄 Thông tin bài báo</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Paper ID:</label>
                                <p class="mt-1 font-mono text-gray-900">#{{ $coi->paper_id }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tiêu đề:</label>
                                <p class="mt-1 text-gray-900 font-semibold">{{ $coi->paper_title }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tóm tắt:</label>
                                <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($coi->paper_abstract, 300) }}</p>
                            </div>
                            
                            @if($coi->paper_keywords)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Từ khóa:</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(explode(',', $coi->paper_keywords) as $keyword)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Trạng thái:</label>
                                    <span class="inline-block mt-1 px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                                        {{ $coi->paper_status }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Ngày nộp:</label>
                                    <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($coi->submitted_at)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (1 column) -->
                <div class="space-y-6">
                    <!-- Resolution Status -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        @if($coi->resolution_id)
                        <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                            <h2 class="text-lg font-bold text-green-800">✅ Đã xử lý</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Phương án:</label>
                                <p class="mt-1 text-gray-900 font-semibold">{{ $coi->resolution_name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $coi->resolution_description }}</p>
                            </div>
                            
                            @if($coi->resolution_note)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Ghi chú từ Chair:</label>
                                <p class="mt-1 text-gray-700 text-sm bg-gray-50 p-3 rounded border">{{ $coi->resolution_note }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Xử lý bởi:</label>
                                <p class="mt-1 text-gray-900">{{ $coi->resolved_by_name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Ngày xử lý:</label>
                                <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($coi->resolved_at)->format('d/m/Y H:i') }}</p>
                            </div>

                            <!-- Assignment Status After Resolution -->
                            @if(!$assignment)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-xs text-yellow-800 font-medium">
                                    ⚠️ Phân công đã bị xóa do COI
                                </p>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                            <h2 class="text-lg font-bold text-yellow-800">⏳ Chờ xử lý</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">
                                Khai báo COI của bạn đang chờ Chair xem xét và xử lý. 
                                Bạn sẽ nhận được thông báo khi có quyết định.
                            </p>
                            
                            <form action="{{ route('reviewer.coi.retract', $coi->coi_id) }}" 
                                  method="POST" 
                                  class="mt-4"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn rút lại khai báo COI này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                                    Rút lại khai báo
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <!-- Conference Info -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b">
                            <h2 class="text-lg font-bold text-gray-800">🎯 Hội thảo</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Mã hội thảo:</label>
                                <p class="mt-1 text-gray-900 font-semibold">{{ $coi->conference_code }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tên hội thảo:</label>
                                <p class="mt-1 text-gray-900">{{ $coi->conference_title }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b">
                            <h2 class="text-lg font-bold text-gray-800">⚡ Hành động</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('reviewer.coi.index') }}" 
                               class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-center font-semibold rounded-lg transition">
                                ← Quay lại danh sách
                            </a>
                            
                            @if($assignment)
                            <a href="{{ route('reviewer.papers.download', $assignment->assignment_id) }}" 
                               class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold rounded-lg transition">
                                Tải bài báo
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
            </div>
        </main>
    </div>
</body>
</html>
