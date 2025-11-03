<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết COI - Chair Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-orange-800 via-orange-700 to-orange-600 text-white shadow-lg sticky top-0 z-50">
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
                        <button type="submit" class="text-sm hover:text-orange-200 transition">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <a href="{{ route('chair.dashboard') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Quản lý reviewer</span>
                </button>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Phân công phản biện</span>
                </button>

                <a href="{{ route('chair.coi.index') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 text-orange-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Kiểm tra COI</span>
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
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- COI Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                        <h2 class="text-lg font-bold text-red-800">⚠️ Thông tin Xung đột Lợi ích</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Loại COI:</label>
                            <p class="mt-1 text-gray-900 font-semibold">{{ $coi->coi_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $coi->coi_description }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Nguồn phát hiện:</label>
                            <div class="mt-1">
                                @if($coi->source_type === 'DECLARED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                    </svg>
                                    Tự khai báo bởi Reviewer
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"></path>
                                    </svg>
                                    Tự động phát hiện bởi hệ thống
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($coi->evidence)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Bằng chứng/Ghi chú:</label>
                            <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $coi->evidence }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-600">Ngày phát hiện:</label>
                            <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($coi->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Paper Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                        <h2 class="text-lg font-bold text-blue-800">📄 Thông tin Bài báo</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Mã bài báo:</label>
                            <p class="mt-1 text-gray-900 font-mono">#{{ $coi->paper_id }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Tiêu đề:</label>
                            <p class="mt-1 text-gray-900 font-semibold">{{ $coi->paper_title }}</p>
                        </div>

                        @if($coi->abstract)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tóm tắt:</label>
                            <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($coi->abstract, 300) }}</p>
                        </div>
                        @endif

                        @if($coi->keywords)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Từ khóa:</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach(explode(',', $coi->keywords) as $keyword)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-600">Tác giả chính:</label>
                            <p class="mt-1 text-gray-900">{{ $coi->author_name }} ({{ $coi->author_email }})</p>
                        </div>

                        @if(count($coAuthors) > 1)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Đồng tác giả:</label>
                            <div class="mt-2 space-y-2">
                                @foreach($coAuthors as $author)
                                <div class="text-sm text-gray-700">
                                    {{ $author->author_order }}. {{ $author->full_name }} - {{ $author->email }}
                                    @if($author->organization)
                                    <span class="text-gray-500">({{ $author->organization }})</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-600">Trạng thái bài báo:</label>
                            <span class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium
                                @if($coi->paper_status === 'SUBMITTED') bg-blue-100 text-blue-800
                                @elseif($coi->paper_status === 'UNDER_REVIEW') bg-yellow-100 text-yellow-800
                                @elseif($coi->paper_status === 'ACCEPTED') bg-green-100 text-green-800
                                @elseif($coi->paper_status === 'REJECTED') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $coi->paper_status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reviewer Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h2 class="text-lg font-bold text-purple-800">👤 Thông tin Reviewer</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Họ tên:</label>
                            <p class="mt-1 text-gray-900 font-semibold">{{ $coi->reviewer_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Email:</label>
                            <p class="mt-1 text-gray-900">{{ $coi->reviewer_email }}</p>
                        </div>

                        @if($coi->reviewer_org)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tổ chức:</label>
                            <p class="mt-1 text-gray-900">{{ $coi->reviewer_org }}</p>
                        </div>
                        @endif

                        @if($assignment)
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm font-medium text-yellow-800">⚠️ Reviewer này đã được phân công cho bài báo</p>
                            <div class="mt-2 text-xs text-yellow-700">
                                <p>Ngày phân công: {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}</p>
                                @if($assignment->deadline)
                                <p>Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y') }}</p>
                                @endif
                                <p>Trạng thái: {{ $assignment->status }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-gray-800">Trạng thái</h3>
                    </div>
                    <div class="p-6">
                        @if($coi->resolution_id)
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-green-800">Đã giải quyết</p>
                                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($coi->resolved_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Phương án:</label>
                                <p class="mt-1 font-medium text-gray-900">{{ $coi->resolution_name }}</p>
                                @if($coi->resolution_description)
                                <p class="text-xs text-gray-600 mt-1">{{ $coi->resolution_description }}</p>
                                @endif
                            </div>

                            @if($coi->resolution_note)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Ghi chú:</label>
                                <p class="mt-1 text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $coi->resolution_note }}</p>
                            </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-gray-600">Giải quyết bởi:</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $coi->resolved_by_name }}</p>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="font-semibold text-red-800">Chưa giải quyết</p>
                            <p class="text-xs text-gray-600 mt-1">COI case này cần được giải quyết</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-gray-800">Hành động</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(!$coi->resolution_id)
                        <a href="{{ route('chair.coi.resolve-form', $coi->coi_id) }}" 
                           class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                            ⚖️ Giải quyết COI
                        </a>
                        @endif

                        <a href="{{ route('chair.papers.show', $coi->paper_id) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                            📄 Xem bài báo
                        </a>

                        <a href="{{ route('chair.coi.index') }}" 
                           class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg text-center transition">
                            ← Quay lại danh sách
                        </a>
                    </div>
                </div>

                <!-- Conference Info -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-gray-800">Hội thảo</h3>
                    </div>
                    <div class="p-6">
                        <p class="font-mono text-sm text-gray-600">{{ $coi->conference_code }}</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $coi->conference_name }}</p>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>
</body>
</html>
