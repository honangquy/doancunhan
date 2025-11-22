<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="https://foodtech.huit.edu.vn/images_new/logo_en.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://foodtech.huit.edu.vn/images_new/logo_en.png">
    <link rel="apple-touch-icon" href="https://foodtech.huit.edu.vn/images_new/logo_en.png">
    <meta name="msapplication-TileImage" content="https://foodtech.huit.edu.vn/images_new/logo_en.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Prevent text selection on buttons to avoid double-click issues */
        button, .btn, [onclick] {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>

    <!-- Global error handling for JavaScript issues -->
    <script>
        // Global error handler to prevent className indexOf errors
        window.addEventListener('error', function(e) {
            if (e.error && e.error.message && (
                e.error.message.includes('indexOf') ||
                e.error.message.includes('className')
            )) {
                console.warn('Prevented JavaScript error:', e.error.message);
                e.preventDefault();
                return true;
            }
        });

        // Prevent double-click issues globally
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('dblclick', function(e) {
                // Prevent double-click on interactive elements
                if (e.target && (
                    e.target.tagName === 'BUTTON' ||
                    e.target.closest('button') ||
                    e.target.hasAttribute('onclick') ||
                    e.target.closest('[onclick]')
                )) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        });
    </script>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-green-800 via-green-700 to-green-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <a href="{{ route('home') }}" class="flex items-center space-x-4 hover:opacity-90 transition">
                    <div class="flex-shrink-0 bg-white rounded-full p-2">
                        <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-8 h-8 rounded-full object-cover" />
                    </div>
                    <div>
                        <div class="text-xl font-bold">HUIT Conferences</div>
                        <div class="text-xs text-green-100">Admin Dashboard</div>
                    </div>
                </a>

                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-white hover:bg-green-700 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Right Side Menu -->
                <div class="hidden lg:flex items-center space-x-4">
                    <!-- Notifications -->
                    <div x-data="{
                        open: false,
                        notifications: [],
                        unreadCount: 0,
                        loading: false,
                        async loadNotifications() {
                            this.loading = true;
                            try {
                                const response = await fetch('{{ route('admin.notifications.unread') }}');
                                const data = await response.json();
                                if (data.success) {
                                    this.notifications = data.notifications;
                                    this.unreadCount = data.unread_count;
                                }
                            } catch (error) {
                                console.error('Error loading notifications:', error);
                            } finally {
                                this.loading = false;
                            }
                        },
                        async markAsRead(id) {
                            try {
                                const response = await fetch(`/admin/notifications/${id}/read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    await this.loadNotifications();
                                }
                            } catch (error) {
                                console.error('Error marking notification as read:', error);
                            }
                        },
                        async markAllAsRead() {
                            try {
                                const response = await fetch('{{ route('admin.notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    await this.loadNotifications();
                                }
                            } catch (error) {
                                console.error('Error marking all notifications as read:', error);
                            }
                        }
                    }" x-init="loadNotifications()" class="relative">
                        <button @click="open = !open; if(open && unreadCount > 0) loadNotifications()"
                                class="relative p-2 hover:bg-green-700 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="absolute -top-1 -right-1 min-w-[20px] h-5 flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full px-1.5"></span>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl overflow-hidden z-50"
                             style="display: none;">
                            <div class="p-4 bg-green-50 border-b flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-800">Thông báo hệ thống</h3>
                                <button @click="markAllAsRead()"
                                        x-show="unreadCount > 0"
                                        class="text-xs text-green-700 hover:text-green-800 font-medium">
                                    Đánh dấu tất cả đã đọc
                                </button>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="p-4 text-center text-gray-500">
                                        <svg class="animate-spin h-5 w-5 mx-auto text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </template>
                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-sm">Không có thông báo mới</p>
                                    </div>
                                </template>
                                <template x-for="notification in notifications" :key="notification.id">
                                    <a :href="`/admin/notifications/${notification.id}/redirect`"
                                       class="block p-4 hover:bg-gray-50 transition border-b">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2"
                                                 :class="{
                                                     'bg-red-500': notification.level === 'error',
                                                     'bg-yellow-500': notification.level === 'warning',
                                                     'bg-blue-500': notification.level === 'info',
                                                     'bg-green-500': notification.level === 'success'
                                                 }"></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 font-medium" x-text="notification.title"></p>
                                                <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notification.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at"></p>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <a href="{{ route('admin.notifications.index') }}"
                               class="block p-3 text-center text-sm text-green-700 hover:bg-green-50 font-medium border-t">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-3 hover:bg-green-700 rounded-lg px-3 py-2 transition">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name ?? 'Admin User') }}&background=059669&color=fff&bold=true"
                                 alt="User"
                                 class="w-8 h-8 rounded-full border-2 border-green-300">
                            <span class="font-medium hidden md:block">{{ Auth::user()->full_name ?? 'Admin User' }}</span>
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
                            <a href="{{ route('profile.show') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Hồ sơ của tôi</span>
                                </div>
                            </a>
                            <a href="{{ route('home') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition border-t">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span>Về trang chủ</span>
                                </div>
                            </a>
                            <a href="{{ route('role.selection') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition border-t">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    <span>Chuyển đổi vai trò</span>
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
        <aside class="fixed top-16 left-0 z-20 w-64 h-screen transition-transform duration-300 transform lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-show="sidebarOpen || !window.matchMedia('(max-width: 1024px)').matches">

            <div class="h-full px-3 py-4 overflow-y-auto bg-white border-r border-gray-200">
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                    <li x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center w-full p-2 rounded-lg {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.join-requests.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="ml-3">Người dùng</span>
                            <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="ml-6 mt-2 space-y-1">

                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Quản lý người dùng
                            </a>

                            <a href="{{ route('admin.join-requests.index') }}"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm {{ request()->routeIs('admin.join-requests.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Yêu cầu vai trò
                                @if(isset($adminStats) && $adminStats['pending_join_requests'] > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $adminStats['pending_join_requests'] }}</span>
                                @endif
                            </a>
                        </div>
                    </li>

                    <li x-data="{ conferenceMenuOpen: false }">
                        <button @click="conferenceMenuOpen = !conferenceMenuOpen"
                                class="flex items-center w-full p-2 rounded-lg {{ request()->routeIs('admin.conferences.*') || request()->routeIs('admin.conference-requests.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="ml-3">Hội thảo</span>
                            <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': conferenceMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="conferenceMenuOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="ml-6 mt-2 space-y-1">

                            <a href="{{ route('admin.conferences.index') }}"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm {{ request()->routeIs('admin.conferences.index') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Danh sách hội thảo
                            </a>

                            <a href="{{ route('admin.conference-requests.index') }}"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm {{ request()->routeIs('admin.conference-requests.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Duyệt yêu cầu
                                @if(isset($adminStats) && $adminStats['pending_conference_requests'] > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $adminStats['pending_conference_requests'] }}</span>
                                @endif
                            </a>

                            <a href="{{ route('admin.configured-conferences.index') }}"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm {{ request()->routeIs('admin.configured-conferences.*') ? 'bg-green-100 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Duyệt cấu hình
                                @if(isset($adminStats) && $adminStats['pending_configured_conferences'] > 0)
                                    <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $adminStats['pending_configured_conferences'] }}</span>
                                @endif
                            </a>

                            <a href="#"
                               class="flex items-center p-2 pl-4 rounded-lg text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tạo hội thảo mới
                            </a>
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('admin.news.index') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.news.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <span class="ml-3">Tin tức & Sự kiện</span>
                            @if(isset($adminStats) && $adminStats['pending_news'] > 0)
                                <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $adminStats['pending_news'] }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.permissions.index') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.permissions.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="ml-3">Phân quyền</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="ml-3">Cài đặt hệ thống</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.logs.index') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.logs.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="ml-3">Nhật ký hệ thống</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.reports.index') }}"
                           class="flex items-center p-2 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-green-50 text-green-700' : 'text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="ml-3">Báo cáo & Thống kê</span>
                        </a>
                    </li>
                </ul>

                <!-- Back to main site -->
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                        </svg>
                        <span class="ml-3">Về trang chính</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="p-4 lg:ml-64 w-full">
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-10 bg-gray-600 bg-opacity-50 lg:hidden"></div>
</body>
</html>
