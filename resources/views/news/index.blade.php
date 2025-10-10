<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tin tức & Sự kiện - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        section {
            scroll-mt-16;
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar - Sticky -->
    <nav class="sticky top-0 z-50 bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md" />
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                    </div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="text-orange-300 font-medium">Tin tức</a>
                    <a href="{{ route('process') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Quy trình</a>
                    <a href="{{ route('support') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Hỗ trợ</a>
                    <a href="{{ route('home') }}#calendar" class="hover:text-orange-300 transition-all duration-300 font-medium">Lịch</a>
                    
                    @auth
                        <!-- Notification Bell -->
                        <div class="relative" x-data="{
                            showNotifications: false,
                            notifications: [],
                            unreadCount: 0,
                            loading: false,
                            
                            async loadNotifications() {
                                this.loading = true;
                                try {
                                    const response = await fetch('/api/notifications');
                                    const data = await response.json();
                                    this.notifications = data.notifications;
                                    this.unreadCount = data.unreadCount;
                                } catch (error) {
                                    console.error('Error loading notifications:', error);
                                } finally {
                                    this.loading = false;
                                }
                            },
                            
                            async markAsRead(id) {
                                try {
                                    const response = await fetch(`/api/notifications/${id}/read`, {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        }
                                    });
                                    if (response.ok) {
                                        this.loadNotifications();
                                    }
                                } catch (error) {
                                    console.error('Error marking notification as read:', error);
                                }
                            },
                            
                            async markAllAsRead() {
                                try {
                                    const response = await fetch('/api/notifications/read-all', {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        }
                                    });
                                    if (response.ok) {
                                        this.loadNotifications();
                                    }
                                } catch (error) {
                                    console.error('Error marking all notifications as read:', error);
                                }
                            }
                        }" x-init="loadNotifications()">
                            <button @click="showNotifications = !showNotifications; if(showNotifications && unreadCount > 0) loadNotifications()"
                                    class="relative p-2 text-white hover:text-orange-300 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount"
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold">
                                </span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div x-show="showNotifications"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="showNotifications = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl z-50 border border-gray-100"
                                 style="display: none;">
                                
                                <!-- Notifications Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                                    <h3 class="font-semibold text-gray-800">Thông báo</h3>
                                    <button @click="markAllAsRead()" 
                                            x-show="unreadCount > 0"
                                            class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Đánh dấu đã đọc tất cả
                                    </button>
                                </div>
                                
                                <!-- Loading -->
                                <div x-show="loading" class="p-4 text-center">
                                    <div class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Đang tải...</span>
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div x-show="!loading" class="max-h-96 overflow-y-auto">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer"
                                             :class="!notification.read_at ? 'bg-blue-50 border-l-4 border-l-blue-500' : ''"
                                             @click="if(!notification.read_at) markAsRead(notification.id)">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4 class="font-medium text-sm text-gray-800" x-text="notification.title"></h4>
                                                <span class="text-xs text-gray-500" x-text="notification.time_ago"></span>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                                            
                                            <!-- Type Badge -->
                                            <div class="mt-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                                      :class="{
                                                          'bg-green-100 text-green-800': notification.type === 'paper_submitted',
                                                          'bg-blue-100 text-blue-800': notification.type === 'review_assigned', 
                                                          'bg-orange-100 text-orange-800': notification.type === 'deadline_reminder',
                                                          'bg-purple-100 text-purple-800': notification.type === 'status_update'
                                                      }"
                                                      x-text="{
                                                          'paper_submitted': 'Nộp bài',
                                                          'review_assigned': 'Phân công',
                                                          'deadline_reminder': 'Hạn chót',
                                                          'status_update': 'Cập nhật'
                                                      }[notification.type] || notification.type">
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Empty State -->
                                    <div x-show="notifications.length === 0" class="p-8 text-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-2">Chưa có thông báo nào</p>
                                        <button @click="fetch('/api/notifications/sample', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}}).then(() => loadNotifications())"
                                                class="text-xs text-blue-600 hover:text-blue-700">
                                            Tạo thông báo mẫu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 hover:text-orange-300 transition-all duration-300 px-3 py-2 rounded-xl hover:bg-white/10">
                                <span class="font-medium">{{ Auth::user()->full_name ?? 'User' }}</span>
                                @if(isset($userData) && $userData['roles']->isNotEmpty())
                                    <span class="text-xs bg-orange-500 px-2 py-1 rounded-full">
                                        {{ $userData['roles']->first()->role_code }}
                                    </span>
                                @endif
                                <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                                 style="display: none;">
                                @if(isset($userData) && $userData['dashboardUrl'])
                                <a href="{{ $userData['dashboardUrl'] }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <div>
                                            <span>Dashboard</span>
                                            @if($userData['paperCount'] > 0 || $userData['assignmentCount'] > 0)
                                                <div class="text-xs text-gray-500">
                                                    {{ $userData['paperCount'] }} papers, {{ $userData['assignmentCount'] }} assignments
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                @endif
                                <a href="{{ route('profile.show') ?? '#profile' }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Hồ sơ cá nhân</span>
                                    </div>
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
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
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') ?? route('login') }}" class="bg-orange-500 hover:bg-orange-600 px-6 py-2 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
                                Đăng ký
                            </a>
                        </div>
                    @endauth
                </div>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-4 space-y-1"
                 style="display: none;">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hội thảo</a>
                <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 text-orange-300 bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
                <a href="{{ route('process') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
                <a href="{{ route('support') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
                <a href="{{ route('home') }}#calendar" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-700 to-blue-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Tin tức & Sự kiện</h1>
            <p class="text-blue-100 text-lg">Cập nhật tin tức mới nhất về các hội thảo và hoạt động khoa học tại HUIT</p>
        </div>
    </section>

    <!-- Featured News -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">Tin nổi bật</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Featured 1 -->
                <article class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="https://via.placeholder.com/600x400/1e40af/ffffff?text=HUIT+ICI+2025" 
                             alt="News" 
                             class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-4 py-2 bg-orange-500 text-white text-xs font-semibold rounded-xl shadow-lg">
                                Nổi bật
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                05/10/2025
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Hội thảo
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition">
                            Khai mạc Hội thảo Khoa học CNTT HUIT 2025
                        </h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            Sáng ngày 05/10/2025, Khoa Công nghệ Thông tin đã long trọng khai mạc Hội thảo Khoa học CNTT HUIT 2025 với sự tham gia của hơn 200 nhà khoa học, giảng viên và sinh viên từ khắp cả nước...
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold group">
                            Đọc thêm
                            <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>

                <!-- Featured 2 -->
                <article class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="https://via.placeholder.com/600x400/7c3aed/ffffff?text=Best+Papers" 
                             alt="News" 
                             class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded-xl shadow-lg">
                                Thông báo
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                01/10/2025
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Giải thưởng
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition">
                            Công bố danh sách bài báo xuất sắc nhất
                        </h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            Ban tổ chức hội thảo vui mừng thông báo danh sách 10 bài báo xuất sắc nhất được trao giải Best Paper Award tại Hội thảo Khoa học CNTT HUIT 2025...
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold group">
                            Đọc thêm
                            <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Tin tức mới nhất</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Danh mục:</span>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option>Tất cả</option>
                        <option>Hội thảo</option>
                        <option>Thông báo</option>
                        <option>Sự kiện</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- News Card 1 -->
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="relative overflow-hidden">
                        <img src="https://via.placeholder.com/400x250/14b8a6/ffffff?text=Workshop+AI" 
                             alt="News" 
                             class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-lg">
                                Sự kiện
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-xs text-gray-500 mb-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            28/09/2025
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                            Workshop về AI trong Y tế thu hút đông đảo sinh viên
                        </h3>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            Hơn 150 sinh viên đã tham gia workshop về ứng dụng Trí tuệ nhân tạo trong chẩn đoán y tế...
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            Xem chi tiết
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>

                <!-- News Card 2 -->
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="relative overflow-hidden">
                        <img src="https://via.placeholder.com/400x250/f97316/ffffff?text=Deadline+Extension" 
                             alt="News" 
                             class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 bg-orange-500 text-white text-xs font-semibold rounded-lg">
                                Thông báo
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-xs text-gray-500 mb-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            25/09/2025
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                            Gia hạn deadline nộp bài HUIT-CEE-2025
                        </h3>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            Ban tổ chức thông báo gia hạn thời gian nộp bài đến ngày 15/11/2025 để tác giả có thêm thời gian...
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            Xem chi tiết
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>

                <!-- News Card 3 -->
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="relative overflow-hidden">
                        <img src="https://via.placeholder.com/400x250/8b5cf6/ffffff?text=Keynote+Speaker" 
                             alt="News" 
                             class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 bg-purple-500 text-white text-xs font-semibold rounded-lg">
                                Hội thảo
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-xs text-gray-500 mb-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            20/09/2025
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                            Giáo sư MIT sẽ là diễn giả keynote tại HUIT-ICI-2025
                        </h3>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            Chúng tôi vinh dự được đón tiếp GS. John Smith từ MIT phát biểu tại phiên khai mạc...
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            Xem chi tiết
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>

                <!-- More news cards would repeat here -->
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex items-center justify-center space-x-2">
                <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium">1</button>
                <button class="px-4 py-2 rounded-lg hover:bg-gray-50 text-gray-600">2</button>
                <button class="px-4 py-2 rounded-lg hover:bg-gray-50 text-gray-600">3</button>
                <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold mb-4">HUIT Conferences</h3>
                    <p class="text-sm">Trường Đại học Công nghiệp TP.HCM</p>
                    <p class="text-sm">Nền tảng quản lý hội thảo khoa học đa cấp</p>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Liên kết</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">Bảng điều khiển Tác giả</a></li>
                        <li><a href="#" class="hover:text-white">Bảng điều khiển Reviewer</a></li>
                        <li><a href="#" class="hover:text-white">Bảng điều khiển tổ chức</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Liên hệ</h3>
                    <p class="text-sm">Email: khoics@huit.edu.vn</p>
                    <p class="text-sm">Điện thoại: (028) 38xx xxxx</p>
                    <p class="text-sm">Địa chỉ: 140 Lê Trọng Tấn, TP.HCM</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm">
                <p>© 2025 HUIT - All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
