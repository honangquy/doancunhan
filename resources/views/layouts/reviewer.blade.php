<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.favicon')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reviewer Dashboard') - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    colors: { primary: '#1e40af', accent: '#f97316' },
                    animation: { 'slide-up': 'slideUp 0.6s ease-out' },
                    keyframes: { slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } } }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Top Navigation (sticky) -->
    <nav class="bg-gradient-to-r from-purple-800 via-purple-700 to-purple-600 text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-full px-6">
            <div class="flex items-center justify-between h-16"> 
                <a href="{{ route('reviewer.dashboard') }}" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md" />
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-purple-200">Reviewer Dashboard</div>
                    </div>
                </a>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ 
                        open: false,
                        notifications: [],
                        unreadCount: 0,
                        loading: false,
                        async loadNotifications() {
                            this.loading = true;
                            try {
                                const response = await fetch('/reviewer/notifications');
                                const data = await response.json();
                                
                                if (data.success) {
                                    this.notifications = data.notifications || [];
                                    this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                                }
                            } catch (error) {
                                console.error('Failed to load notifications:', error);
                            }
                            this.loading = false;
                        },
                        async markAsRead(notificationId) {
                            try {
                                await fetch(`/reviewer/notifications/${notificationId}/read`, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                await this.loadNotifications();
                            } catch (error) {
                                console.error('Failed to mark as read:', error);
                            }
                        }
                    }" x-init="loadNotifications(); setInterval(loadNotifications, 60000)">
                        <button @click="open = !open; if(open && notifications.length === 0) loadNotifications()" class="relative p-2 hover:bg-purple-600 rounded-xl transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" class="absolute top-0 right-0 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center animate-pulse"></span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl z-50">
                            <div class="p-4 border-b">
                                <h3 class="font-semibold text-gray-800">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="p-4 text-center text-gray-500">
                                        <svg class="animate-spin h-5 w-5 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Đang tải...
                                    </div>
                                </template>
                                
                                <template x-if="!loading && notifications.length > 0">
                                    <div>
                                        <template x-for="notification in notifications" :key="notification.id">
                                            <div @click="markAsRead(notification.id)" class="p-4 hover:bg-gray-50 border-b cursor-pointer">
                                                <div class="flex items-start space-x-3">
                                                    <div :class="!notification.read_at ? 'bg-purple-500' : 'bg-gray-300'" class="w-2 h-2 rounded-full mt-2"></div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-800" x-text="notification.title"></p>
                                                        <p class="text-xs text-gray-600 mt-1" x-text="notification.message"></p>
                                                        <p class="text-xs text-gray-400 mt-1" x-text="new Date(notification.created_at).toLocaleDateString('vi-VN')"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                
                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p class="text-sm">Không có thông báo nào</p>
                                    </div>
                                </template>
                            </div>
                            <template x-if="notifications.length > 0">
                                <div class="p-3 border-t text-center">
                                    <button @click="notifications.forEach(n => markAsRead(n.id))" class="text-sm text-purple-700 hover:text-purple-800 font-medium">
                                        Đánh dấu tất cả đã đọc
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 hover:bg-purple-600 px-3 py-2 rounded-xl transition-all">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name ?? 'Reviewer') }}&background=7c3aed&color=fff" 
                                 class="w-8 h-8 rounded-xl" alt="Avatar">
                            <span class="font-medium">{{ Auth::user()->full_name ?? 'Reviewer' }}</span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ</a>
                            <a href="{{ route('reviewer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Trang chủ</a>
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

    <div class="flex items-start">
    <!-- Sidebar (sticky below topbar on all screen sizes) -->
    <aside class="w-64 bg-white border-r min-h-screen sticky top-16 self-start z-40">
            <nav class="p-4 space-y-1">
                <a href="{{ route('reviewer.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reviewer.dashboard') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('reviewer.bidding') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reviewer.bidding*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>Bidding & COI</span>
                </a>
                
                <a href="{{ route('reviewer.assignments.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reviewer.assignments*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Phân công của tôi</span>
                </a>
                
                <a href="{{ route('reviewer.reviews') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reviewer.reviews*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Reviews của tôi</span>
                </a>
                
                <hr class="my-4">
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
