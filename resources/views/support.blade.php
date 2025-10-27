<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>Hỗ trợ - HUIT Conferences</title>
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
<img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md">
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                    </div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Tin tức</a>
                    <a href="{{ route('process') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Quy trình</a>
                    <a href="{{ route('support') }}" class="text-orange-300 font-medium">Hỗ trợ</a>
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
                <a href="{{ route('process') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
                <a href="{{ route('support') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 text-orange-300 bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
                <a href="{{ route('home') }}#calendar" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-700 to-blue-600 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Trung tâm Hỗ trợ</h1>
            <p class="text-blue-100 text-lg max-w-3xl mx-auto">
                Tìm câu trả lời nhanh chóng hoặc liên hệ với chúng tôi để được hỗ trợ
            </p>
        </div>
    </section>

    <!-- Quick Search -->
    <section class="py-8 bg-white border-b">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" 
                           placeholder="Tìm kiếm câu hỏi thường gặp..." 
                           class="w-full px-6 py-4 pl-12 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Câu hỏi thường gặp (FAQ)</h2>

                <!-- FAQ Categories Tabs -->
                <div class="flex flex-wrap justify-center gap-2 mb-8" x-data="{ activeTab: 'author' }">
                    <button @click="activeTab = 'author'" 
                            :class="activeTab === 'author' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                            class="px-6 py-2 rounded-xl font-medium transition-all duration-300">
                        Tác giả
                    </button>
                    <button @click="activeTab = 'reviewer'" 
                            :class="activeTab === 'reviewer' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                            class="px-6 py-2 rounded-xl font-medium transition-all duration-300">
                        Reviewer
                    </button>
                    <button @click="activeTab = 'chair'" 
                            :class="activeTab === 'chair' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                            class="px-6 py-2 rounded-xl font-medium transition-all duration-300">
                        Chair
                    </button>
                    <button @click="activeTab = 'technical'" 
                            :class="activeTab === 'technical' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                            class="px-6 py-2 rounded-xl font-medium transition-all duration-300">
                        Kỹ thuật
                    </button>
                </div>

                <!-- Author FAQs -->
                <div x-show="activeTab === 'author'" x-data="{ openFaq: null }" class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Làm thế nào để nộp bài báo?</span>
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 1 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Để nộp bài báo, bạn cần:</p>
                            <ol class="list-decimal list-inside space-y-2 ml-2">
                                <li>Đăng ký tài khoản với vai trò "Tác giả"</li>
                                <li>Đăng nhập và chọn hội thảo muốn tham gia</li>
                                <li>Click nút "Nộp bài mới" trong dashboard</li>
                                <li>Điền đầy đủ thông tin: tiêu đề, abstract, keywords, tác giả</li>
                                <li>Upload file PDF (max 10MB, định dạng chuẩn)</li>
                                <li>Review và submit</li>
                            </ol>
                            <p class="mt-3 text-sm text-blue-600">
                                <a href="/process#author" class="hover:underline">→ Xem hướng dẫn chi tiết</a>
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">File PDF cần đáp ứng yêu cầu gì?</span>
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 2 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">File PDF cần đảm bảo:</p>
                            <ul class="list-disc list-inside space-y-2 ml-2">
                                <li><strong>Kích thước:</strong> Tối đa 10MB</li>
                                <li><strong>Định dạng:</strong> PDF/A hoặc PDF version 1.4+</li>
                                <li><strong>Font:</strong> Embed all fonts</li>
                                <li><strong>Số trang:</strong> 6-8 trang (full paper), 2-4 trang (short paper)</li>
                                <li><strong>Template:</strong> Sử dụng template chuẩn của hội thảo</li>
                                <li><strong>Ẩn danh:</strong> Bỏ thông tin tác giả nếu review mù đôi</li>
                            </ul>
                            <p class="mt-3">
                                <a href="#" class="text-blue-600 hover:underline text-sm">→ Tải template LaTeX/Word</a>
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 3 ? null : 3" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Khi nào tôi biết kết quả phản biện?</span>
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 3 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 3" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Thời gian thông báo kết quả phụ thuộc vào lịch trình của từng hội thảo:</p>
                            <ul class="list-disc list-inside space-y-2 ml-2">
                                <li>Thường là <strong>4-6 tuần</strong> sau deadline nộp bài</li>
                                <li>Bạn sẽ nhận <strong>email thông báo</strong> tự động</li>
                                <li>Dashboard cũng hiển thị status real-time</li>
                                <li>Có thể theo dõi tiến độ review trong "My Papers"</li>
                            </ul>
                            <p class="mt-3 text-sm bg-blue-50 text-blue-700 p-3 rounded-lg">
                                💡 <strong>Tip:</strong> Bật notification để nhận thông báo ngay lập tức!
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 4 ? null : 4" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Làm thế nào để sửa bài sau khi nộp?</span>
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 4 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 4" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Có 2 trường hợp:</p>
                            <div class="space-y-3">
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <strong class="text-yellow-800">Trước deadline:</strong>
                                    <p class="text-sm mt-1">Bạn có thể withdraw và nộp lại version mới. Vào "My Papers" → Click "Withdraw" → Upload lại.</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <strong class="text-green-800">Sau khi nhận kết quả "Revise":</strong>
                                    <p class="text-sm mt-1">Upload revised version kèm response letter trong mục "Submit Revision". Có deadline riêng cho revision.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviewer FAQs -->
                <div x-show="activeTab === 'reviewer'" x-data="{ openFaq: null }" class="space-y-4">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Bidding là gì và tại sao quan trọng?</span>
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 1 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3"><strong>Bidding</strong> là quá trình reviewer chọn những bài báo mà mình muốn phản biện dựa trên:</p>
                            <ul class="list-disc list-inside space-y-2 ml-2">
                                <li><strong>Chuyên môn:</strong> Bài thuộc lĩnh vực bạn am hiểu</li>
                                <li><strong>Thời gian:</strong> Bạn có đủ thời gian review chất lượng</li>
                                <li><strong>Không COI:</strong> Không có conflict of interest với tác giả</li>
                            </ul>
                            <p class="mt-3 text-sm bg-purple-50 text-purple-700 p-3 rounded-lg">
                                <strong>Lợi ích:</strong> Bidding giúp Chair phân công chính xác hơn, bạn review những bài phù hợp nhất với mình!
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Review form có những tiêu chí gì?</span>
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 2 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Review form chuẩn bao gồm:</p>
                            <ol class="list-decimal list-inside space-y-2 ml-2">
                                <li><strong>Originality (1-5):</strong> Tính mới, độc đáo của nghiên cứu</li>
                                <li><strong>Technical Quality (1-5):</strong> Phương pháp, thí nghiệm, kết quả</li>
                                <li><strong>Clarity (1-5):</strong> Cách trình bày, viết, cấu trúc</li>
                                <li><strong>Significance (1-5):</strong> Ý nghĩa khoa học, ứng dụng thực tế</li>
                                <li><strong>Overall Rating (1-10):</strong> Đánh giá tổng thể</li>
                                <li><strong>Comments:</strong> Nhận xét chi tiết (for authors + for chairs)</li>
                                <li><strong>Recommendation:</strong> Accept / Minor Revision / Major Revision / Reject</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Chair FAQs -->
                <div x-show="activeTab === 'chair'" x-data="{ openFaq: null }" class="space-y-4">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Làm thế nào để tạo conference mới?</span>
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 1 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Quy trình tạo conference:</p>
                            <ol class="list-decimal list-inside space-y-2 ml-2">
                                <li>Đăng nhập với role Chair/Admin</li>
                                <li>Vào "Chair Dashboard" → "Create New Conference"</li>
                                <li>Điền thông tin: Code, Name, Description, Dates</li>
                                <li>Thiết lập Topics (AI, Security, IoT...)</li>
                                <li>Import hoặc invite reviewers</li>
                                <li>Cấu hình review settings (blind review, số reviewer/paper...)</li>
                                <li>Publish conference</li>
                            </ol>
                            <p class="mt-3">
                                <a href="/process#chair" class="text-orange-600 hover:underline text-sm">→ Xem hướng dẫn chi tiết</a>
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Làm thế nào để phân công reviewer?</span>
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 2 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Hệ thống hỗ trợ 2 cách:</p>
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <strong class="text-blue-800">Auto Assignment:</strong>
                                    <p class="text-sm mt-1">Dựa trên bidding, COI check, reviewer workload. Hệ thống tự động gợi ý matching score.</p>
                                </div>
                                <div class="bg-orange-50 p-4 rounded-lg">
                                    <strong class="text-orange-800">Manual Assignment:</strong>
                                    <p class="text-sm mt-1">Chair tự chọn reviewer cho từng paper. Drag & drop hoặc click assign.</p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm">
                                <strong>Best practice:</strong> Dùng Auto gợi ý, sau đó review và adjust manually nếu cần.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQs -->
                <div x-show="activeTab === 'technical'" x-data="{ openFaq: null }" class="space-y-4">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Tôi quên mật khẩu, làm sao lấy lại?</span>
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 1 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <ol class="list-decimal list-inside space-y-2">
                                <li>Vào trang <a href="/login" class="text-blue-600 hover:underline">đăng nhập</a></li>
                                <li>Click "Quên mật khẩu?"</li>
                                <li>Nhập email đã đăng ký</li>
                                <li>Check email để nhận link reset (check cả spam folder)</li>
                                <li>Click link và tạo mật khẩu mới</li>
                            </ol>
                            <p class="mt-3 text-sm bg-yellow-50 text-yellow-700 p-3 rounded-lg">
                                ⚠️ Link reset có hiệu lực trong 60 phút. Nếu hết hạn, yêu cầu lại.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2" 
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-all">
                            <span class="font-semibold text-gray-800 pr-4">Trình duyệt nào được hỗ trợ?</span>
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 transition-transform" 
                                 :class="{ 'rotate-180': openFaq === 2 }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-6 pb-6 text-gray-600">
                            <p class="mb-3">Hệ thống tương thích với:</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                    <span class="text-2xl">🌐</span>
                                    <div>
                                        <p class="font-semibold text-sm">Chrome</p>
                                        <p class="text-xs text-gray-500">Version 90+</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                    <span class="text-2xl">🦊</span>
                                    <div>
                                        <p class="font-semibold text-sm">Firefox</p>
                                        <p class="text-xs text-gray-500">Version 88+</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                    <span class="text-2xl">🧭</span>
                                    <div>
                                        <p class="font-semibold text-sm">Safari</p>
                                        <p class="text-xs text-gray-500">Version 14+</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                    <span class="text-2xl">📱</span>
                                    <div>
                                        <p class="font-semibold text-sm">Mobile</p>
                                        <p class="text-xs text-gray-500">iOS & Android</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Vẫn cần hỗ trợ?</h2>
                    <p class="text-gray-600">Gửi câu hỏi cho chúng tôi, team sẽ phản hồi trong vòng 24 giờ</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Contact Form -->
                    <div class="bg-white rounded-2xl shadow-xl p-8" x-data="{ 
                        formData: { name: '', email: '', subject: '', message: '' },
                        submitted: false
                    }">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Gửi câu hỏi</h3>
                        
                        <form @submit.prevent="submitted = true" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                                <input type="text" 
                                       x-model="formData.name"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Nguyễn Văn A">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" 
                                       x-model="formData.email"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="email@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Chủ đề *</label>
                                <select x-model="formData.subject"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">-- Chọn chủ đề --</option>
                                    <option value="submission">Nộp bài báo</option>
                                    <option value="review">Phản biện</option>
                                    <option value="technical">Vấn đề kỹ thuật</option>
                                    <option value="account">Tài khoản</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung *</label>
                                <textarea x-model="formData.message"
                                          required
                                          rows="5"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                          placeholder="Mô tả chi tiết vấn đề của bạn..."></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105">
                                Gửi câu hỏi
                            </button>
                        </form>

                        <!-- Success Message -->
                        <div x-show="submitted" 
                             x-transition
                             class="mt-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-semibold">Đã gửi thành công!</span>
                            </div>
                            <p class="text-sm mt-1">Chúng tôi sẽ phản hồi qua email trong vòng 24 giờ.</p>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-6">
                        <!-- Email Support -->
                        <div class="bg-gradient-to-br from-blue-600 to-blue-500 text-white rounded-2xl shadow-xl p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Email Support</h4>
                                    <p class="text-sm text-blue-100">Phản hồi trong 24h</p>
                                </div>
                            </div>
                            <a href="mailto:support@huit-conferences.vn" class="text-white hover:text-blue-100 font-semibold">
                                support@huit-conferences.vn
                            </a>
                        </div>

                        <!-- Phone Support -->
                        <div class="bg-gradient-to-br from-green-600 to-green-500 text-white rounded-2xl shadow-xl p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Hotline</h4>
                                    <p class="text-sm text-green-100">8:00 - 17:00 (T2-T6)</p>
                                </div>
                            </div>
                            <a href="tel:+842838940390" class="text-white hover:text-green-100 font-semibold">
                                (028) 3894 0390
                            </a>
                        </div>

                        <!-- Office Hours -->
                        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-gray-800">Giờ làm việc</h4>
                                    <p class="text-sm text-gray-600">Khoa Công nghệ Thông tin</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Thứ 2 - Thứ 6:</strong> 08:00 - 17:00</p>
                                <p><strong>Thứ 7:</strong> 08:00 - 12:00</p>
                                <p><strong>Chủ nhật:</strong> Nghỉ</p>
                            </div>
                        </div>
                    </div>
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
