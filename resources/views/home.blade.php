<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>HUIT Conferences - Hệ thống Quản lý Hội thảo Khoa học</title>
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
        
        /* Offset for sticky navbar when scrolling to anchors */
        .scroll-mt-16 {
            scroll-margin-top: 4rem;
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
                        'scale-in': 'scaleIn 0.4s ease-out',
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
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Top Header - Not Sticky -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center space-x-6">
                <!-- HUIT Logo - Left -->
                <a href="{{ route('home') }}" class="flex-shrink-0 hover:opacity-90 transition">
                    <img src="https://huit.edu.vn/Images/Documents/N00CT/logo-huit-web-chinh-moi-mau-xanh-02.svg?h=80" 
                         alt="HUIT Logo" 
                         class="h-12 w-auto">
                </a>
                <!-- University Name - Center -->
                <div class="flex-1 flex flex-col items-center text-center space-y-1">
                    <span class="text-lg md:text-xl font-bold text-blue-600 uppercase tracking-wide">BỘ CÔNG THƯƠNG</span>
                    <span class="text-xl md:text-2xl lg:text-3xl font-bold text-blue-700 uppercase">TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP. HỒ CHÍ MINH</span>
                </div>
            </div>
        </div>
    </div>

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
                    <a href="#conferences" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Tin tức</a>
                    <a href="{{ route('process') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Quy trình</a>
                    <a href="{{ route('support') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Hỗ trợ</a>
                    <a href="#calendar" class="hover:text-orange-300 transition-all duration-300 font-medium">Lịch</a>
                    
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
                                @if($userData && $userData['roles']->isNotEmpty())
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
                                @if($userData && $userData['dashboardUrl'])
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
                <a href="#conferences" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hội thảo</a>
                <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
                <a href="{{ route('process') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
                <a href="{{ route('support') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
                <a href="#calendar" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-yellow-700 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-700 font-medium">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Nền tảng hội thảo khoa học của HUIT
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Tạo – Quản lý – Công bố kỹ yếu. Quy trình cho tác giả, reviewer và ban tổ chức với COI, bidding, phân công phản biện & xuất bản.
            </p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto animate-fade-in">
                <div class="flex gap-3">
                    <input type="text" 
                           placeholder="Nhập từ khóa (mã, tên hội thảo, lĩnh vực...)"
                           class="flex-1 px-6 py-4 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-lg">
                    <button class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-xl font-medium transition-all duration-300 hover:shadow-xl hover:scale-105">
                        Tìm kiếm
                    </button>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="#upcoming" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 px-6 py-3 rounded-xl transition-all duration-300 backdrop-blur-sm hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Xem hội thảo sắp tới</span>
                </a>
                @if(auth()->check() && auth()->user()->email_verified_at)
                <a href="{{ route('conference-request.create') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 px-6 py-3 rounded-xl transition-all duration-300 backdrop-blur-sm hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tạo hội thảo</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 px-6 py-3 rounded-xl transition-all duration-300 backdrop-blur-sm hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Đăng nhập để tạo hội thảo</span>
                </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-10 bg-gradient-to-b from-white to-gray-50 border-b" x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Thống kê hệ thống</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform transition-all duration-500 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 0ms">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-blue-700 mb-1">{{ $statistics['activeConferences'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 font-medium">Hội thảo đang mở</div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform transition-all duration-500 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 100ms">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-green-700 mb-1">{{ $statistics['totalPapers'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 font-medium">Bài báo đã nộp</div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform transition-all duration-500 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 200ms">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-purple-700 mb-1">{{ $statistics['totalReviewers'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 font-medium">Reviewer</div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform transition-all duration-500 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 300ms">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-orange-700 mb-1">{{ $statistics['totalAuthors'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 font-medium">Tác giả</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Papers Section -->
    <section class="py-12 bg-gradient-to-b from-blue-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Bài báo mới nhất</h2>
                <p class="text-gray-600 text-sm">Các bài báo khoa học được nộp gần đây</p>
            </div>
            
            @if($recentPapers && $recentPapers->count() > 0)
                <div class="grid md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    @foreach($recentPapers as $paper)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6">
                            <div class="text-xs text-blue-600 font-medium mb-3">{{ $paper->conference_title }}</div>
                            <h3 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2">{{ $paper->paper_title }}</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>{{ $paper->author_name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($paper->submitted_at)->format('d/m/Y') }}</span>
                            </div>
                            @if($paper->abstract)
                                <p class="text-sm text-gray-600 line-clamp-3">{{ \Illuminate\Support\Str::limit($paper->abstract, 100) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có bài báo</h3>
                    <p class="text-gray-500">Hiện tại chưa có bài báo nào được nộp.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Khám phá Hội thảo - Compact & Simple -->
    <section id="conferences" class="py-16 bg-gray-50 scroll-mt-16">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Khám phá Hội thảo</h2>
                <p class="text-gray-600">Tìm kiếm và tham gia các hội thảo khoa học phù hợp</p>
            </div>

            <!-- Compact Search & Filter Bar -->
            <div class="max-w-5xl mx-auto mb-10">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex flex-col md:flex-row gap-3 items-center">
                        <!-- Compact Search Input -->
                        <div class="flex-1 w-full">
                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input 
                                    type="text" 
                                    id="conferenceSearch"
                                    placeholder="Tìm kiếm hội thảo..."
                                    class="w-full px-4 py-2.5 pl-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm transition-all"
                                >
                            </div>
                        </div>

                        <!-- Compact Status Filters -->
                        <div class="flex gap-2 flex-wrap md:flex-nowrap">
                            <button onclick="filterConferences('all')" 
                                    class="filter-btn active px-3 py-2 text-xs font-medium rounded-lg bg-orange-600 text-white hover:bg-orange-700 transition whitespace-nowrap">
                                Tất cả
                            </button>
                            <button onclick="filterConferences('open')" 
                                    class="filter-btn px-3 py-2 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                                Đang mở
                            </button>
                            <button onclick="filterConferences('closed')" 
                                    class="filter-btn px-3 py-2 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                                Đã đóng
                            </button>
                        </div>
                    </div>

                    <!-- Sort Options - Compact -->
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-500">Sắp xếp:</span>
                        <div class="flex gap-2">
                            <button onclick="sortConferences('year')" class="sort-btn px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 text-gray-700">Năm</button>
                            <button onclick="sortConferences('title')" class="sort-btn px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 text-gray-700">Tên</button>
                            <button onclick="sortConferences('deadline')" class="sort-btn px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 text-gray-700">Hạn nộp</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conference Grid -->
            <div id="conferenceGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($recentConferences as $conference)
                    <div class="conference-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden" 
                         data-status="{{ $conference->status_display }}" 
                         data-title="{{ strtolower($conference->title) }}"
                         data-year="{{ $conference->year }}"
                         data-deadline="{{ $conference->deadline_submission }}">
                        
                        <!-- Conference Header with Gradient -->
                        <div class="bg-gradient-to-br from-blue-600 to-blue-500 p-5 text-white">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-lg backdrop-blur">
                                    CONF-{{ $conference->conference_id }}
                                </span>
                                <span class="text-xs font-semibold {{ $conference->status_class }} px-2 py-1 rounded-lg">
                                    {{ $conference->status_text }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold mb-2 line-clamp-2">{{ $conference->title }}</h3>
                            <p class="text-xs opacity-90">
                                @if($conference->start_date)
                                    {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}
                                @else
                                    Năm {{ $conference->year }}
                                @endif
                            </p>
                        </div>

                        <!-- Conference Body -->
                        <div class="p-5">
                            <div class="space-y-2 mb-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>{{ $conference->paper_count ?? 0 }} bài báo</span>
                                </div>
                                
                                @if($conference->deadline_submission)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Hạn: {{ \Carbon\Carbon::parse($conference->deadline_submission)->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>

                            <a href="{{ route('conferences.show', $conference->conference_id) }}" class="block w-full px-4 py-2.5 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition text-center">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có hội thảo nào</h3>
                        <p class="mt-2 text-sm text-gray-500">Các hội thảo sẽ được cập nhật sớm.</p>
                    </div>
                @endforelse
            </div>

            <!-- No Results State -->
            <div id="noResults" class="hidden text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Không tìm thấy hội thảo</h3>
                <p class="mt-2 text-sm text-gray-500">Thử tìm kiếm với từ khóa khác hoặc thay đổi bộ lọc.</p>
                <button onclick="resetFilters()" class="mt-4 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm">
                    Xóa bộ lọc
                </button>
            </div>
        </div>
    </section>

    <!-- Simple JavaScript for Filtering & Search -->
    <script>
        const searchInput = document.getElementById('conferenceSearch');
        const conferenceCards = document.querySelectorAll('.conference-card');
        const conferenceGrid = document.getElementById('conferenceGrid');
        const noResults = document.getElementById('noResults');
        
        let currentFilter = 'all';
        let currentSort = 'year';
        let searchTimeout;
        
        // Search with debounce
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
        
        // Filter by status
        function filterConferences(status) {
            currentFilter = status;
            
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-orange-600', 'text-white');
                btn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-700');
            });
            event.target.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-700');
            event.target.classList.add('bg-orange-600', 'text-white', 'active');
            
            applyFilters();
        }
        
        // Sort conferences
        function sortConferences(sortBy) {
            currentSort = sortBy;
            applyFilters();
        }
        
        // Apply all filters
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const cardsArray = Array.from(conferenceCards);
            let visibleCount = 0;
            
            // Filter and show/hide
            cardsArray.forEach(card => {
                const title = card.dataset.title || '';
                const status = card.dataset.status || '';
                
                const matchesSearch = !searchTerm || title.includes(searchTerm);
                const matchesFilter = currentFilter === 'all' || status === currentFilter;
                
                if (matchesSearch && matchesFilter) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Sort visible cards
            const visibleCards = cardsArray.filter(card => card.style.display !== 'none');
            visibleCards.sort((a, b) => {
                if (currentSort === 'year') {
                    return (b.dataset.year || 0) - (a.dataset.year || 0);
                } else if (currentSort === 'title') {
                    return (a.dataset.title || '').localeCompare(b.dataset.title || '');
                } else if (currentSort === 'deadline') {
                    return (b.dataset.deadline || '').localeCompare(a.dataset.deadline || '');
                }
                return 0;
            });
            
            // Re-append sorted cards
            visibleCards.forEach(card => conferenceGrid.appendChild(card));
            
            // Show/hide no results
            if (visibleCount === 0 && conferenceCards.length > 0) {
                conferenceGrid.classList.add('hidden');
                noResults.classList.remove('hidden');
            } else {
                conferenceGrid.classList.remove('hidden');
                noResults.classList.add('hidden');
            }
        }
        
        // Reset all filters
        function resetFilters() {
            searchInput.value = '';
            currentFilter = 'all';
            document.querySelectorAll('.filter-btn').forEach((btn, idx) => {
                if (idx === 0) {
                    btn.classList.add('bg-orange-600', 'text-white', 'active');
                    btn.classList.remove('bg-white', 'border', 'text-gray-700');
                } else {
                    btn.classList.remove('bg-orange-600', 'text-white', 'active');
                    btn.classList.add('bg-white', 'border', 'text-gray-700');
                }
            });
            applyFilters();
        }
        
        // Initialize
        applyFilters();
    </script>

    <!-- Conference Request Modal -->
    <div id="conferenceRequestModal" x-data="{
        showConferenceRequestModal: false,
        formData: {
            title: '',
            field: '',
            level_code: 'KHOA',
            expected_date: '',
            objective: '',
            affiliation: '',
            facility_id: '',
            chair_fullname: '',
            chair_email: '',
            chair_phone: '',
            proposal_file: null,
            coChairs: []
        },
        facilities: [],
        errors: {},
        loading: false,
        
        async initializeModal() {
            try {
                const response = await fetch('/api/facilities');
                const data = await response.json();
                this.facilities = data.facilities;
            } catch (error) {
                console.error('Error loading facilities:', error);
            }
        },
        
        addCoChair() {
            this.formData.coChairs.push({
                fullname: '',
                email: '',
                affiliation: ''
            });
        },
        
        removeCoChair(index) {
            this.formData.coChairs.splice(index, 1);
        },
        
        async submitRequest() {
            this.loading = true;
            this.errors = {};
            
            try {
                const formData = new FormData();
                formData.append('title', this.formData.title);
                formData.append('field', this.formData.field);
                formData.append('level_code', this.formData.level_code);
                formData.append('expected_date', this.formData.expected_date);
                formData.append('objective', this.formData.objective);
                formData.append('affiliation', this.formData.affiliation);
                formData.append('facility_id', this.formData.facility_id);
                formData.append('chair_fullname', this.formData.chair_fullname);
                formData.append('chair_email', this.formData.chair_email);
                formData.append('chair_phone', this.formData.chair_phone);
                formData.append('co_chairs', JSON.stringify(this.formData.coChairs));
                
                if (this.formData.proposal_file) {
                    formData.append('proposal_file', this.formData.proposal_file);
                }
                
                const response = await fetch('/api/conference-requests', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    alert('Yêu cầu tạo hội thảo đã được gửi thành công! ID: ' + data.request_id);
                    this.resetForm();
                    this.showConferenceRequestModal = false;
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể gửi yêu cầu'));
                }
            } catch (error) {
                console.error('Error submitting request:', error);
                alert('Lỗi khi gửi yêu cầu: ' + error.message);
            } finally {
                this.loading = false;
            }
        },
        
        resetForm() {
            this.formData = {
                title: '',
                field: '',
                level_code: 'KHOA',
                expected_date: '',
                objective: '',
                affiliation: '',
                facility_id: '',
                chair_fullname: '',
                chair_email: '',
                chair_phone: '',
                proposal_file: null,
                coChairs: []
            };
            this.errors = {};
        }
    }" @click.away="showConferenceRequestModal = false"
    x-show="showConferenceRequestModal"
    class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-black opacity-50"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl mx-auto mt-20 p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Yêu cầu Tạo Hội thảo</h2>
                <button @click="showConferenceRequestModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Form -->
            <form @submit.prevent="submitRequest()" class="space-y-6">
                <!-- Title & Field -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên hội thảo *</label>
                        <input x-model="formData.title" type="text" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <template x-if="errors.title"><p class="text-xs text-red-600 mt-1" x-text="errors.title[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lĩnh vực *</label>
                        <input x-model="formData.field" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <template x-if="errors.field"><p class="text-xs text-red-600 mt-1" x-text="errors.field[0]"></p></template>
                    </div>
                </div>
                
                <!-- Level & Date -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cấp độ *</label>
                        <select x-model="formData.level_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="KHOA">Khoa</option>
                            <option value="TRUONG">Trường</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày dự kiến *</label>
                        <input x-model="formData.expected_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <template x-if="errors.expected_date"><p class="text-xs text-red-600 mt-1" x-text="errors.expected_date[0]"></p></template>
                    </div>
                </div>
                
                <!-- Objective & Facility -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mục tiêu (500 ký tự) *</label>
                        <textarea x-model="formData.objective" maxlength="500" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
                        <template x-if="errors.objective"><p class="text-xs text-red-600 mt-1" x-text="errors.objective[0]"></p></template>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bộ phận/Khoa *</label>
                        <select x-model="formData.facility_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">-- Chọn bộ phận --</option>
                            <template x-for="facility in facilities" :key="facility.id">
                                <option :value="facility.id" x-text="facility.name"></option>
                            </template>
                        </select>
                        <template x-if="errors.facility_id"><p class="text-xs text-red-600 mt-1" x-text="errors.facility_id[0]"></p></template>
                    </div>
                </div>
                
                <!-- Chair Info -->
                <fieldset class="border border-gray-300 rounded-lg p-4">
                    <legend class="text-sm font-semibold text-gray-700 px-2">Thông tin Chủ tịch *</legend>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Họ tên</label>
                            <input x-model="formData.chair_fullname" type="text" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Email</label>
                            <input x-model="formData.chair_email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Điện thoại</label>
                            <input x-model="formData.chair_phone" type="tel" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </fieldset>
                
                <!-- Co-chairs -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-gray-700">Thêm viên bổ sung</label>
                        <button type="button" @click="addCoChair()" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                            + Thêm
                        </button>
                    </div>
                    <template x-for="(coChair, idx) in formData.coChairs" :key="idx">
                        <div class="grid grid-cols-3 gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                            <input x-model="coChair.fullname" type="text" placeholder="Họ tên" class="px-3 py-2 border border-gray-300 rounded text-sm">
                            <input x-model="coChair.email" type="email" placeholder="Email" class="px-3 py-2 border border-gray-300 rounded text-sm">
                            <div class="flex gap-2">
                                <input x-model="coChair.affiliation" type="text" placeholder="Cơ quan" class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm">
                                <button type="button" @click="removeCoChair(idx)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File đề xuất (PDF) *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50" @click="$refs.fileInput.click()">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Nhấp để chọn hoặc kéo thả file PDF</p>
                        <input type="file" x-ref="fileInput" @change="formData.proposal_file = $event.target.files[0]" accept=".pdf" class="hidden" required>
                    </div>
                    <template x-if="formData.proposal_file">
                        <p class="text-xs text-green-600 mt-2">✓ <span x-text="formData.proposal_file.name"></span></p>
                    </template>
                    <template x-if="errors.proposal_file"><p class="text-xs text-red-600 mt-1" x-text="errors.proposal_file[0]"></p></template>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showConferenceRequestModal = false" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" :disabled="loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!loading">Gửi yêu cầu</span>
                        <span x-show="loading">Đang gửi...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Initialize conference request modal on page load
        const modalComponent = document.getElementById('conferenceRequestModal');
        if (modalComponent && modalComponent.__x) {
            modalComponent.__x.initializeModal();
        }
    </script>


    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-12 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">HUIT Conferences</h3>
                    <p class="text-sm leading-relaxed">Trường Đại học Công nghiệp TP.HCM</p>
                    <p class="text-sm leading-relaxed mt-2">Nền tảng quản lý hội thảo khoa học đa cấp (Tin/Dữ/Nhóm, Khoa)</p>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Liên kết</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Bảng điều khiển Tác giả</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Bảng điều khiển Reviewer</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Bảng điều khiển tổ chức</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Liên hệ</h3>
                    <p class="text-sm leading-relaxed">Email: khoics@huit.edu.vn</p>
                    <p class="text-sm leading-relaxed">Điện thoại: (028) 38xx xxxx</p>
                    <p class="text-sm leading-relaxed">Địa chỉ: 140 Lê Trọng Tấn, TP.HCM</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm">
                <p>© 2025 HUIT - All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
