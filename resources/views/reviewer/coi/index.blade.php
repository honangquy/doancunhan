<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COI đã khai báo - Reviewer Dashboard</title>
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

        <!-- Header with Action Button -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">COI đã khai báo</h1>
                <p class="text-gray-600 mt-1">Danh sách các xung đột lợi ích bạn đã tự khai báo</p>
            </div>
            <a href="{{ route('reviewer.coi.create') }}" 
               class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Khai báo COI mới</span>
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Tổng số COI</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unresolved -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Chưa xử lý</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['unresolved'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolved -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Đã xử lý</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['resolved'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- By Type -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <p class="text-sm text-gray-600 font-medium mb-3">Theo loại COI</p>
                    <div class="space-y-2 max-h-24 overflow-y-auto">
                        @forelse($stats['by_type'] as $type)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 truncate">{{ $type->coi_name }}</span>
                            <span class="font-semibold text-purple-600">{{ $type->count }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400">Chưa có dữ liệu</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- COI Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-purple-100 border-b">
                <h2 class="text-lg font-bold text-gray-800">📋 Danh sách COI đã khai báo</h2>
                <p class="text-sm text-gray-600 mt-1">Hiển thị {{ $declaredCOI->count() }} khai báo</p>
            </div>

            @if($declaredCOI->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài báo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hội thảo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại COI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày khai báo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($declaredCOI as $coi)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $coi->paper_title }}</div>
                                <div class="text-xs text-gray-500">Paper #{{ $coi->paper_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $coi->conference_code }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($coi->conference_title, 30) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                    {{ $coi->coi_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($coi->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($coi->resolution_id)
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Đã xử lý
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $coi->resolution_name }}</div>
                                @else
                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Chờ xử lý
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('reviewer.coi.show', $coi->coi_id) }}" 
                                       class="text-purple-600 hover:text-purple-800 text-sm font-medium transition">
                                        Chi tiết
                                    </a>
                                    @if(!$coi->resolution_id)
                                    <form action="{{ route('reviewer.coi.retract', $coi->coi_id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn rút lại khai báo COI này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition">
                                            Rút lại
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có khai báo COI</h3>
                <p class="text-gray-500 mb-6">Bạn chưa khai báo bất kỳ xung đột lợi ích nào</p>
                <a href="{{ route('reviewer.coi.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Khai báo COI đầu tiên
                </a>
            </div>
            @endif
        </div>

        <!-- Info Card -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">Thông tin quan trọng về COI</p>
                    <p class="text-xs text-blue-700 mt-1">
                        • Vui lòng khai báo COI ngay khi phát hiện xung đột lợi ích với bài báo được phân công<br>
                        • Chair sẽ xem xét và quyết định phương án xử lý phù hợp<br>
                        • Bạn có thể rút lại khai báo nếu chưa được Chair xử lý<br>
                        • Sau khi Chair xử lý, bạn không thể rút lại khai báo
                    </p>
                </div>
            </div>
        </div>
            </div>
        </main>
    </div>
</body>
</html>
