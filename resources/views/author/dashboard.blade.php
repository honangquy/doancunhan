<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Author Dashboard - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        'primary-dark': '#1e3a8a',
                        accent: '#f97316',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'slide-up': 'slideUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
        <div class="px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('author.dashboard') }}" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-blue-700 font-bold text-xl">H</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Author Dashboard</div>
                    </div>
                </a>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 hover:bg-blue-600 rounded-xl transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl z-50">
                            <div class="p-4 border-b">
                                <h3 class="font-semibold text-gray-800">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="block p-4 hover:bg-gray-50 border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Bài báo của bạn đã được chấp nhận</p>
                                            <p class="text-xs text-gray-600 mt-1">Paper #45 - HUIT-ICI-2025</p>
                                            <p class="text-xs text-gray-400 mt-1">2 giờ trước</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block p-4 hover:bg-gray-50 border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Deadline nộp bài sắp hết hạn</p>
                                            <p class="text-xs text-gray-600 mt-1">HUIT-CEE-2025 - Còn 3 ngày</p>
                                            <p class="text-xs text-gray-400 mt-1">5 giờ trước</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="#" class="block p-3 text-center text-sm text-blue-700 hover:bg-gray-50 font-medium">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 hover:bg-blue-600 px-3 py-2 rounded-xl transition-all duration-300">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Author') }}&background=f97316&color=fff" 
                                 class="w-8 h-8 rounded-xl" alt="Avatar">
                            <span class="font-medium">{{ Auth::user()->name ?? 'Author' }}</span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ</a>
                            <a href="{{ route('author.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Trang chủ</a>
                            <hr>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r min-h-screen">
            <nav class="p-4 space-y-1">
                <a href="{{ route('author.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('author.papers.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Bài báo của tôi</span>
                </a>
                
                <a href="{{ route('author.papers.create') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Nộp bài mới</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Hội thảo</span>
                </a>
                
                <hr class="my-4">
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard</h1>
                <p class="text-gray-600">Chào mừng trở lại! Đây là tổng quan về các bài báo của bạn.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid md:grid-cols-4 gap-4 mb-6" x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)">
                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 0ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Tổng số bài</div>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 100ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['under_review'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Đang phản biện</div>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 200ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['accepted'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Đã chấp nhận</div>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 300ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['rejected'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Bị từ chối</div>
                        </div>
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Papers -->
            <div class="bg-white rounded-xl shadow-lg animate-slide-up">
                <div class="p-6 border-b flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Bài báo gần đây</h2>
                    <a href="{{ route('author.papers.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg font-medium">
                        + Nộp bài mới
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hội thảo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày nộp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($papers as $paper)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $paper->title }}
                                    </div>
                                    <div class="text-xs text-gray-500">Paper #{{ $paper->paper_id }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($paper->conference_name, 30) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'ACCEPTED' => 'bg-green-100 text-green-800',
                                            'UNDER_REVIEW' => 'bg-yellow-100 text-yellow-800',
                                            'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                            'REVISION' => 'bg-orange-100 text-orange-800',
                                            'REJECTED' => 'bg-red-100 text-red-800',
                                        ];
                                        $class = $statusClasses[$paper->status_code] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold {{ $class }} rounded-full">
                                        {{ $paper->status_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Chưa có bài báo nào</p>
                                    <p class="text-xs mt-1">Bắt đầu bằng cách nộp bài báo đầu tiên của bạn</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t">
                    <a href="/author/papers" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Xem tất cả bài báo →
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
