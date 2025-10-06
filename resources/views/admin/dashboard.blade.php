<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - HUIT Conference</title>

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
                    },
                    colors: {
                        primary: '#059669',
                        accent: '#10b981',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(5, 150, 105, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-green-800 via-green-700 to-green-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 bg-white rounded-lg p-2">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <span class="text-2xl font-black text-green-600">H</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold">HUIT Conferences</div>
                        <div class="text-xs text-green-100">Admin Dashboard</div>
                    </div>
                </div>

                <!-- Right Side Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative p-2 hover:bg-green-700 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50"
                             x-cloak>
                            <div class="p-4 bg-green-50 border-b">
                                <h3 class="text-sm font-semibold text-gray-800">Thông báo hệ thống</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="block p-4 hover:bg-gray-50 transition border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">5 tài khoản mới đang chờ phê duyệt</p>
                                            <p class="text-xs text-gray-500 mt-1">Cần xác minh danh tính</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block p-4 hover:bg-gray-50 transition border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">Database backup hoàn tất</p>
                                            <p class="text-xs text-gray-500 mt-1">2 giờ trước</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">Hội thảo mới được tạo: HUIT-AI-2026</p>
                                            <p class="text-xs text-gray-500 mt-1">Bởi Chair Nguyễn Văn A</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-3 hover:bg-green-700 rounded-lg px-3 py-2 transition">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff&bold=true" 
                                 alt="User" 
                                 class="w-8 h-8 rounded-full border-2 border-green-300">
                            <span class="font-medium hidden md:block">Admin User</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl overflow-hidden z-50"
                             x-cloak>
                            <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Hồ sơ của tôi</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition border-t">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span>Về trang chủ</span>
                                </div>
                            </a>
                            <div class="border-t">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Đăng xuất</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-50 text-green-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Quản lý người dùng</span>
                </a>
                
                <a href="/admin/conferences" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Quản lý hội thảo</span>
                </a>
                
                <a href="/admin/roles" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Phân quyền</span>
                </a>
                
                <a href="/admin/system" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Cài đặt hệ thống</span>
                </a>
                
                <a href="/admin/logs" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Nhật ký hệ thống</span>
                </a>
                
                <a href="/admin/reports" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Báo cáo & Thống kê</span>
                </a>
                
                <a href="/support" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">Quản trị hệ thống và giám sát hoạt động</p>
            </div>

            <!-- Stats Cards -->
            <div x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Users -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 transition-all duration-500"
                     style="transition-delay: 0ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng người dùng</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] ?? 0 }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Người dùng đã đăng ký</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Conferences -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 transition-all duration-500"
                     style="transition-delay: 100ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng hội thảo</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_conferences'] ?? 0 }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Hội thảo trong hệ thống</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Total Papers -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 transition-all duration-500"
                     style="transition-delay: 200ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng bài báo</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_papers'] ?? 0 }}</h3>
                            <p class="text-xs text-blue-600 mt-2">Bài báo đã nộp</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Reviews -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 transition-all duration-500"
                     style="transition-delay: 300ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng phản biện</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_reviews'] ?? 0 }}</h3>
                            <p class="text-xs text-green-600 mt-2">Reviews hoàn thành</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending User Approvals -->
            <div class="bg-white rounded-xl shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Tài khoản chờ phê duyệt</h2>
                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">5 tài khoản</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Họ tên</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vai trò</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ngày đăng ký</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Nguyen+Van+A&background=3b82f6&color=fff" 
                                             alt="User" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div class="text-sm font-medium text-gray-900">Nguyễn Văn A</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">nguyenvana@huit.edu.vn</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Reviewer
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">04/10/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Duyệt
                                    </button>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Từ chối
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Tran+Thi+B&background=f59e0b&color=fff" 
                                             alt="User" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div class="text-sm font-medium text-gray-900">Trần Thị B</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">tranthib@huit.edu.vn</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Chair
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">03/10/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Duyệt
                                    </button>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Từ chối
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Le+Van+C&background=10b981&color=fff" 
                                             alt="User" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div class="text-sm font-medium text-gray-900">Lê Văn C</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">levanc@huit.edu.vn</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Author
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">02/10/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Duyệt
                                    </button>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg font-medium transition">
                                        Từ chối
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 border-t">
                    <a href="/admin/users" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Xem tất cả người dùng →
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- System Activity -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Hoạt động gần đây</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">User <span class="font-semibold">nguyenvand@huit.edu.vn</span> đã đăng ký tài khoản</p>
                                <p class="text-xs text-gray-500 mt-1">5 phút trước</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Hội thảo <span class="font-semibold">HUIT-AI-2026</span> được tạo mới</p>
                                <p class="text-xs text-gray-500 mt-1">30 phút trước</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Database backup đã hoàn tất thành công</p>
                                <p class="text-xs text-gray-500 mt-1">2 giờ trước</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">18 bài báo mới được nộp trong 24h qua</p>
                                <p class="text-xs text-gray-500 mt-1">5 giờ trước</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">User <span class="font-semibold">spammer@email.com</span> bị khóa tài khoản</p>
                                <p class="text-xs text-gray-500 mt-1">1 ngày trước</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats & System Info -->
                <div class="space-y-6">
                    <!-- User Distribution -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Phân bổ người dùng</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Authors</div>
                                        <div class="text-sm text-gray-500">Tác giả</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $roleStats['authors'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">{{ $roleStats['authors_percent'] ?? 0 }}%</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Reviewers</div>
                                        <div class="text-sm text-gray-500">Phản biện</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $roleStats['reviewers'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">{{ $roleStats['reviewers_percent'] ?? 0 }}%</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Chairs</div>
                                        <div class="text-sm text-gray-500">Chủ tịch</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $roleStats['chairs'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">{{ $roleStats['chairs_percent'] ?? 0 }}%</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Admins</div>
                                        <div class="text-sm text-gray-500">Quản trị viên</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $roleStats['admins'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">{{ $roleStats['admins_percent'] ?? 0 }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">Trạng thái hệ thống</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-300 rounded-full"></div>
                                    <span class="font-medium">Server</span>
                                </div>
                                <span class="text-sm">Online</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-300 rounded-full"></div>
                                    <span class="font-medium">Database</span>
                                </div>
                                <span class="text-sm">Active</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-300 rounded-full"></div>
                                    <span class="font-medium">Storage</span>
                                </div>
                                <span class="text-sm">68% Used</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-yellow-300 rounded-full"></div>
                                    <span class="font-medium">Email Service</span>
                                </div>
                                <span class="text-sm">Slow</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/20">
                            <div class="text-sm">Last backup: 2 hours ago</div>
                            <div class="text-xs text-green-100 mt-1">Next backup: in 22 hours</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
