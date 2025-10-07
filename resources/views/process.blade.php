<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quy trình - HUIT Conferences</title>
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
        
        .timeline-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        }
        @media (max-width: 768px) {
            .timeline-line {
                left: 20px;
            }
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
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-blue-700 font-bold text-xl">H</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                    </div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Tin tức</a>
                    <a href="{{ route('process') }}" class="text-orange-300 font-medium">Quy trình</a>
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
                <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
                <a href="{{ route('process') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 text-orange-300 bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
                <a href="{{ route('support') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
                <a href="{{ route('home') }}#calendar" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-700 to-blue-600 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Quy trình Nộp bài & Phản biện</h1>
            <p class="text-blue-100 text-lg max-w-3xl mx-auto">
                Hướng dẫn chi tiết từng bước để tác giả nộp bài và reviewer phản biện bài báo khoa học
            </p>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="py-6 bg-white border-b">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="#author" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Quy trình Tác giả
                </a>
                <a href="#reviewer" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Quy trình Reviewer
                </a>
                <a href="#chair" class="inline-flex items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Quy trình Chair
                </a>
            </div>
        </div>
    </section>

    <!-- Author Process Timeline -->
    <section id="author" class="py-16 bg-gradient-to-b from-blue-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Quy trình dành cho Tác giả</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Từ đăng ký tài khoản đến khi bài báo được công bố trong kỷ yếu
                </p>
            </div>

            <div class="relative max-w-5xl mx-auto">
                <!-- Timeline Line -->
                <div class="timeline-line hidden md:block"></div>

                <!-- Step 1 -->
                <div class="relative flex flex-col md:flex-row items-center mb-12" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:text-right md:pr-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'" class="transition-all duration-700">
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-end mb-3">
                                <span class="text-sm font-semibold text-blue-600">Bước 1</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Đăng ký tài khoản</h3>
                            <p class="text-gray-600 mb-3">
                                Tạo tài khoản với vai trò Tác giả. Điền đầy đủ thông tin cá nhân, chuyên ngành và đơn vị công tác.
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>✓ Email xác thực</li>
                                <li>✓ Thông tin cá nhân</li>
                                <li>✓ Lĩnh vực nghiên cứu</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        1
                    </div>
                    <div class="flex-1 md:pl-12"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative flex flex-col md:flex-row items-center mb-12" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:pr-12"></div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        2
                    </div>
                    <div class="flex-1 md:pl-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" class="transition-all duration-700 delay-100">
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-3">
                                <span class="text-sm font-semibold text-blue-600">Bước 2</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Chọn hội thảo & Nộp bài</h3>
                            <p class="text-gray-600 mb-3">
                                Tìm kiếm hội thảo phù hợp với lĩnh vực nghiên cứu. Upload file PDF và điền metadata.
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>✓ Chọn chủ đề phù hợp</li>
                                <li>✓ Upload PDF (max 10MB)</li>
                                <li>✓ Khai báo tác giả</li>
                                <li>✓ Từ khóa & Abstract</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative flex flex-col md:flex-row items-center mb-12" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:text-right md:pr-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'" class="transition-all duration-700 delay-200">
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-end mb-3">
                                <span class="text-sm font-semibold text-yellow-600">Bước 3</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Chờ phân công Reviewer</h3>
                            <p class="text-gray-600 mb-3">
                                Hệ thống tự động phân công reviewer dựa trên COI (Conflict of Interest) và bidding.
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>✓ Auto COI check</li>
                                <li>✓ Reviewer bidding</li>
                                <li>✓ Chair assignment</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-yellow-600 to-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        3
                    </div>
                    <div class="flex-1 md:pl-12"></div>
                </div>

                <!-- Step 4 -->
                <div class="relative flex flex-col md:flex-row items-center mb-12" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:pr-12"></div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        4
                    </div>
                    <div class="flex-1 md:pl-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" class="transition-all duration-700 delay-300">
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-3">
                                <span class="text-sm font-semibold text-purple-600">Bước 4</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Nhận kết quả phản biện</h3>
                            <p class="text-gray-600 mb-3">
                                Nhận thông báo qua email về kết quả (Accept/Revise/Reject) kèm nhận xét chi tiết.
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>✓ Email thông báo</li>
                                <li>✓ Reviewer comments</li>
                                <li>✓ Overall decision</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative flex flex-col md:flex-row items-center mb-12" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:text-right md:pr-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'" class="transition-all duration-700 delay-400">
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-end mb-3">
                                <span class="text-sm font-semibold text-orange-600">Bước 5</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Sửa bài (nếu cần)</h3>
                            <p class="text-gray-600 mb-3">
                                Nếu bài được yêu cầu revise, thực hiện chỉnh sửa theo góp ý và nộp lại trong thời hạn.
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>✓ Upload revised version</li>
                                <li>✓ Response letter</li>
                                <li>✓ Track changes</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-orange-600 to-orange-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        5
                    </div>
                    <div class="flex-1 md:pl-12"></div>
                </div>

                <!-- Step 6 -->
                <div class="relative flex flex-col md:flex-row items-center" x-data="{ show: false }" x-intersect="show = true">
                    <div class="flex-1 md:pr-12"></div>
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-green-600 to-green-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg z-10 my-4 md:my-0">
                        ✓
                    </div>
                    <div class="flex-1 md:pl-12" :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" class="transition-all duration-700 delay-500">
                        <div class="bg-gradient-to-br from-green-600 to-green-500 text-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-3">
                                <span class="text-sm font-semibold">Hoàn thành</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Công bố trong Proceedings</h3>
                            <p class="mb-3">
                                Bài báo được xuất bản trong kỷ yếu hội thảo (proceedings) và có thể được index trên các cơ sở dữ liệu.
                            </p>
                            <ul class="text-sm space-y-1">
                                <li>✓ DOI assignment</li>
                                <li>✓ Published in proceedings</li>
                                <li>✓ Certificate of presentation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviewer Process -->
    <section id="reviewer" class="py-16 bg-gradient-to-b from-purple-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Quy trình dành cho Reviewer</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Từ bidding bài báo đến hoàn thành phản biện và đưa ra quyết định
                </p>
            </div>

            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6">
                <!-- Reviewer Step 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-purple-600">1</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Bidding bài báo</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Xem danh sách bài báo được gán cho conference, chọn những bài phù hợp với chuyên môn để phản biện.
                    </p>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Interested</span>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">Maybe</span>
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded">Not interested</span>
                    </div>
                </div>

                <!-- Reviewer Step 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-purple-600">2</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Nhận phân công</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Chair phân công bài dựa trên bidding và COI. Reviewer nhận email thông báo assignment.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Email notification</li>
                        <li>✓ Deadline reminder</li>
                        <li>✓ Access full paper</li>
                    </ul>
                </div>

                <!-- Reviewer Step 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Đọc & Đánh giá</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Đọc kỹ bài báo, đánh giá theo các tiêu chí: originality, methodology, results, writing quality.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Review form template</li>
                        <li>✓ Scoring criteria</li>
                        <li>✓ Confidential comments</li>
                    </ul>
                </div>

                <!-- Reviewer Step 4 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-green-600">✓</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Submit Review</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Gửi kết quả phản biện với recommendation: Accept, Minor Revision, Major Revision, hoặc Reject.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Overall rating</li>
                        <li>✓ Detailed comments</li>
                        <li>✓ Recommendation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Chair Process -->
    <section id="chair" class="py-16 bg-gradient-to-b from-orange-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Quy trình dành cho Chair</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Quản lý hội thảo từ tạo conference đến xuất bản proceedings
                </p>
            </div>

            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Chair Phase 1 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-orange-600 to-orange-500 text-white p-4">
                            <h3 class="text-lg font-bold">Setup Conference</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Tạo conference mới</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Thiết lập thông tin, topics</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Import reviewer pool</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Set deadlines</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Chair Phase 2 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-500 text-white p-4">
                            <h3 class="text-lg font-bold">Manage Review</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Monitor submissions</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Assign reviewers</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Track review progress</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Make final decisions</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Chair Phase 3 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-green-600 to-green-500 text-white p-4">
                            <h3 class="text-lg font-bold">Publish Proceedings</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Collect camera-ready</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Generate proceedings PDF</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Assign DOI</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Publish & Index</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Resources -->
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tài liệu hướng dẫn</h2>
                <p class="text-gray-600 mb-8">Tải về các tài liệu chi tiết cho từng vai trò</p>
                
                <div class="grid md:grid-cols-3 gap-4">
                    <a href="#" class="flex items-center justify-center space-x-3 bg-white hover:bg-blue-50 text-blue-700 px-6 py-4 rounded-xl font-semibold transition-all duration-300 shadow hover:shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Author Guide</span>
                    </a>
                    <a href="#" class="flex items-center justify-center space-x-3 bg-white hover:bg-purple-50 text-purple-700 px-6 py-4 rounded-xl font-semibold transition-all duration-300 shadow hover:shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Reviewer Guide</span>
                    </a>
                    <a href="#" class="flex items-center justify-center space-x-3 bg-white hover:bg-orange-50 text-orange-700 px-6 py-4 rounded-xl font-semibold transition-all duration-300 shadow hover:shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Chair Guide</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-8">
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
