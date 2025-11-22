<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Hồ sơ cá nhân' }} - HUIT Conferences</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        html {
            scroll-behavior: smooth;
            height: 100%;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .scroll-mt-16 { scroll-margin-top: 4rem; }
        .main-content {
            flex: 1;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Full Navbar from Home -->
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
                    <a href="{{ route('home') }}#conferences" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Tin tức</a>
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
                                @if($userRoles && $userRoles->isNotEmpty())
                                    <span class="text-xs bg-orange-500 px-2 py-1 rounded-full">
                                        {{ $userRoles->first()->role_code }}
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
                                @php
                                    $userData = null;
                                    if(Auth::check()) {
                                        $roles = DB::table('VaiTroNguoiDung')->where('user_id', Auth::id())->get();
                                        if($roles->isNotEmpty()) {
                                            $firstRole = $roles->first()->role_code;
                                            $dashboardUrl = match($firstRole) {
                                                'ADMIN' => route('admin.dashboard'),
                                                'CHAIR' => route('chair.dashboard'),
                                                'REVIEWER' => route('reviewer.dashboard'),
                                                'AUTHOR' => route('author.dashboard'),
                                                default => route('home')
                                            };
                                            $paperCount = DB::table('BaiBao')->where('submitter_id', Auth::id())->count();
                                            $assignmentCount = DB::table('PhanCongPhanBien')->where('reviewer_id', Auth::id())->count();
                                            $userData = ['dashboardUrl' => $dashboardUrl, 'paperCount' => $paperCount, 'assignmentCount' => $assignmentCount];
                                        }
                                    }
                                @endphp
                                @if($userData && isset($userData['dashboardUrl']))
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
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-blue-600 bg-blue-50 font-medium">
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
                            <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 px-6 py-2 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105">
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
                <a href="{{ route('home') }}#conferences" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hội thảo</a>
                <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
                <a href="{{ route('process') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
                <a href="{{ route('support') }}" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
                <a href="{{ route('home') }}#calendar" @click="mobileMenuOpen = false" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
            </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="container mx-auto px-4 py-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            @foreach ($errors->all() as $error)
                                <p class="text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6">
            <!-- Sidebar - User Info -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <!-- Avatar Section with Upload -->
                    <div class="text-center mb-6" x-data="{
                        showModal: false,
                        avatarUrl: '{{ $user->avatar_url ?? '' }}',
                        uploadMode: 'device',

                        updateAvatar(url) {
                            this.avatarUrl = url;
                            this.showModal = false;
                        }
                    }">
                        <div class="relative inline-block">
                            <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4 border-4 border-blue-100">
                                <template x-if="avatarUrl">
                                    <img :src="avatarUrl" alt="Avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarUrl">
                                    <div class="w-full h-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center">
                                        <span class="text-3xl font-bold text-white">{{ substr($user->full_name, 0, 1) }}</span>
                                    </div>
                                </template>
                            </div>
                            <button @click="showModal = true"
                                    class="absolute bottom-4 right-1/2 translate-x-1/2 translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Avatar Upload Modal -->
                        <div x-show="showModal"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click.self="showModal = false"
                             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                             style="display: none;">
                            <div class="bg-white rounded-2xl max-w-md w-full p-6"
                                 @click.stop>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-800">Cập nhật ảnh đại diện</h3>
                                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Upload Mode Selector -->
                                <div class="flex gap-2 mb-4">
                                    <button @click="uploadMode = 'device'"
                                            :class="uploadMode === 'device' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                            class="flex-1 py-2 px-4 rounded-lg font-medium transition">
                                        Tải lên từ thiết bị
                                    </button>
                                    <button @click="uploadMode = 'url'"
                                            :class="uploadMode === 'url' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                            class="flex-1 py-2 px-4 rounded-lg font-medium transition">
                                        Dùng link ảnh
                                    </button>
                                </div>

                                <!-- Upload from Device -->
                                <div x-show="uploadMode === 'device'" class="space-y-4">
                                    <form id="avatarUploadForm" enctype="multipart/form-data">
                                        @csrf
                                        <label class="block border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition">
                                            <input type="file"
                                                   name="avatar"
                                                   accept="image/*"
                                                   class="hidden"
                                                   @change="async (e) => {
                                                       const file = e.target.files[0];
                                                       if (file) {
                                                           const formData = new FormData();
                                                           formData.append('avatar', file);
                                                           formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

                                                           try {
                                                               const response = await fetch('{{ route('profile.avatar') }}', {
                                                                   method: 'POST',
                                                                   body: formData
                                                               });
                                                               const data = await response.json();
                                                               if (data.success) {
                                                                   updateAvatar(data.avatar_url);
                                                                   location.reload();
                                                               } else {
                                                                   alert(data.message || 'Có lỗi xảy ra');
                                                               }
                                                           } catch (error) {
                                                               alert('Có lỗi xảy ra khi tải ảnh');
                                                           }
                                                       }
                                                   }">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-sm text-gray-600">Nhấp để chọn ảnh</p>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF tối đa 2MB</p>
                                        </label>
                                    </form>
                                </div>

                                <!-- Upload from URL -->
                                <div x-show="uploadMode === 'url'" class="space-y-4">
                                    <form @submit.prevent="async (e) => {
                                        const url = e.target.avatar_url.value;
                                        if (url) {
                                            try {
                                                const response = await fetch('{{ route('profile.avatar') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                    },
                                                    body: JSON.stringify({ avatar_url: url })
                                                });
                                                const data = await response.json();
                                                if (data.success) {
                                                    updateAvatar(data.avatar_url);
                                                    location.reload();
                                                } else {
                                                    alert(data.message || 'Có lỗi xảy ra');
                                                }
                                            } catch (error) {
                                                alert('Có lỗi xảy ra khi cập nhật ảnh');
                                            }
                                        }
                                    }">
                                        <input type="url"
                                               name="avatar_url"
                                               placeholder="https://example.com/avatar.jpg"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="submit"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition">
                                            Cập nhật
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-xl font-bold text-gray-800">{{ $user->full_name }}</h2>
                        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                    </div>

                    <!-- Roles -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Vai trò</h3>
                        @if($userRoles->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-yellow-700 text-sm">Đang chờ Admin phê duyệt</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($userRoles->unique('role_name') as $role)
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                            {{ $role->role_name }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="border-t pt-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Thống kê</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Bài báo</span>
                                <span class="font-semibold text-gray-800">{{ $stats['totalPapers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Được chấp nhận</span>
                                <span class="font-semibold text-green-600">{{ $stats['acceptedPapers'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Phản biện</span>
                                <span class="font-semibold text-gray-800">{{ $stats['reviewAssignments'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Hoàn thành</span>
                                <span class="font-semibold text-blue-600">{{ $stats['completedReviews'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <!-- Update Profile Form -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Thông tin cá nhân</h3>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Họ và tên <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name"
                                   value="{{ old('full_name', $user->full_name) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" id="email"
                                   value="{{ $user->email }}"
                                   readonly
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label for="organization" class="block text-sm font-medium text-gray-700 mb-2">
                                Đơn vị / Tổ chức
                            </label>
                            <input type="text" id="organization" name="organization"
                                   value="{{ old('organization', $user->organization) }}"
                                   placeholder="Nhập tên đơn vị, trường đại học..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                Cập nhật thông tin
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Đổi mật khẩu</h3>

                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mật khẩu hiện tại <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="current_password" name="current_password"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mật khẩu mới <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Tối thiểu 6 ký tự</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- End Main Content Wrapper -->

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-12 mt-0">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">HUIT Conferences</h3>
                    <p class="text-sm leading-relaxed">Trường Đại học Công Thương TP. Hồ Chí Minh</p>
                    <p class="text-sm leading-relaxed mt-2">Nền tảng quản lý hội thảo khoa học đa cấp</p>
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
