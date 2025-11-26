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
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
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
                <a href="{{ route('home') }}" aria-label="Go to HUIT Conferences homepage" class="flex items-center space-x-3 hover:opacity-90 transition">
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
                                console.log('Loading notifications from /web/notifications');
                                const response = await fetch('/web/notifications');
                                console.log('Response status:', response.status);
                                
                                if (response.ok) {
                                    const data = await response.json();
                                    console.log('Notifications loaded:', data);
                                    this.notifications = data.notifications || [];
                                    this.unreadCount = data.unreadCount || 0;
                                } else {
                                    const errorText = await response.text();
                                    console.error('Failed to load notifications:', response.status, errorText);
                                }
                            } catch (error) {
                                console.error('Error loading notifications:', error);
                            }
                            this.loading = false;
                        },
                        async markAsRead(notificationId) {
                            try {
                                const response = await fetch(`/web/notifications/${notificationId}/read`, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    await this.loadNotifications();
                                }
                            } catch (error) {
                                console.error('Failed to mark as read:', error);
                            }
                        },
                        async markAllAsRead() {
                            try {
                                const response = await fetch('/web/notifications/read-all', {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    await this.loadNotifications();
                                }
                            } catch (error) {
                                console.error('Failed to mark all as read:', error);
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
                            <div class="p-4 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800">Thông báo</h3>
                                <button @click="markAllAsRead()" 
                                        x-show="unreadCount > 0"
                                        class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                                    Đánh dấu tất cả đã đọc
                                </button>
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
                                        <template x-for="notif in notifications" :key="notif.id">
                                            <a :href="`/web/notifications/${notif.id}`"
                                               class="block p-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-0"
                                               :class="{ 'bg-purple-50': !notif.is_read }">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                             :class="notif.is_read ? 'bg-gray-200' : 'bg-purple-100'">
                                                            <svg class="w-5 h-5" :class="notif.is_read ? 'text-gray-500' : 'text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900" x-text="notif.title"></p>
                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notif.message"></p>
                                                        <div class="flex items-center justify-between mt-1">
                                                            <p class="text-xs text-gray-400" x-text="notif.time"></p>
                                                            <span class="text-xs text-purple-600 font-medium">Xem chi tiết →</span>
                                                        </div>
                                                    </div>
                                                    <template x-if="!notif.is_read">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-2 h-2 bg-purple-600 rounded-full"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                
                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-8 text-center">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">Không có thông báo mới</p>
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
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Trang chủ</a>
                            <a href="{{ route('role.selection') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Chuyển đổi vai trò</a>
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
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>
