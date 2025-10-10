@extends('layouts.chair')

@section('title', 'Chair Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Tổng quan quản lý hội thảo')

@push('styles')

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
                        primary: '#ea580c',
                        accent: '#f97316',
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
            box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-orange-800 via-orange-700 to-orange-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 bg-white rounded-full p-2">
                        <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-8 h-8 rounded-full object-cover" />
                    </div>
                    <div>
                        <div class="text-xl font-bold">HUIT Conferences</div>
                        <div class="text-xs text-orange-100">Chair Dashboard</div>
                    </div>
                </div>

                <!-- Right Side Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative p-2 hover:bg-orange-700 rounded-lg transition">
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
                            <div class="p-4 bg-orange-50 border-b">
                                <h3 class="text-sm font-semibold text-gray-800">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="block p-4 hover:bg-gray-50 transition border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">Có 12 bài báo mới cần phân công reviewer</p>
                                            <p class="text-xs text-gray-500 mt-1">Hội thảo HUIT-ICI-2025</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block p-4 hover:bg-gray-50 transition border-b">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">Deadline bidding sắp hết hạn</p>
                                            <p class="text-xs text-gray-500 mt-1">Còn 3 ngày</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">8 reviews đã hoàn thành</p>
                                            <p class="text-xs text-gray-500 mt-1">Sẵn sàng ra quyết định</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-3 hover:bg-orange-700 rounded-lg px-3 py-2 transition">
                            <img src="https://ui-avatars.com/api/?name=Chair+User&background=ea580c&color=fff&bold=true" 
                                 alt="User" 
                                 class="w-8 h-8 rounded-full border-2 border-orange-300">
                            <span class="font-medium hidden md:block">Chair User</span>
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
                            <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 transition">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Hồ sơ của tôi</span>
                                </div>
                            </a>
                            <a href="{{ route('chair.dashboard') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 transition border-t">
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
                <a href="{{ route('chair.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 text-orange-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Hội thảo của tôi</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Quản lý bài báo</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Quản lý reviewer</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Phân công phản biện</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Kiểm tra COI</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
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
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Quản lý hội thảo và phản biện</p>
            </div>

            <!-- Stats Cards -->
            <div x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Papers -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 transition-all duration-500"
                     style="transition-delay: 0ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng bài báo</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_papers'] ?? 0 }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Từ {{ $conferenceInfo->conference_name ?? 'hội thảo' }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Accepted Papers -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 transition-all duration-500"
                     style="transition-delay: 100ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đã chấp nhận</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['accepted'] ?? 0 }}</h3>
                            <p class="text-xs text-green-600 mt-2">✓ Đã duyệt</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Under Review -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 transition-all duration-500"
                     style="transition-delay: 200ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đang review</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['under_review'] ?? 0 }}</h3>
                            <p class="text-xs text-blue-600 mt-2">⏳ Đang xử lý</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Need Reviewers -->
                <div :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" 
                     class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 transition-all duration-500"
                     style="transition-delay: 300ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Cần reviewer</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['needs_reviewers'] ?? 0 }}</h3>
                            <p class="text-xs text-orange-600 mt-2">⚠ Cần phân công</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Papers Requiring Action -->
            <div class="bg-white rounded-xl shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Bài báo gần đây</h2>
                        <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $recentPapers->count() }} bài</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tiêu đề</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tác giả</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reviewers</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPapers as $paper)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $paper->paper_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($paper->title, 50) }}</div>
                                    <div class="text-xs text-gray-500">Nộp: {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->author_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if($paper->reviews_total > 0)
                                            <span class="text-blue-600 font-medium">{{ $paper->reviews_total }} reviewer(s)</span>
                                            @if($paper->reviews_completed > 0)
                                                <div class="text-xs text-green-600 mt-1">
                                                    {{ $paper->reviews_completed }}/{{ $paper->reviews_total }} hoàn thành
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-orange-600 text-xs">⚠ Chưa phân công</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'ACCEPTED' => 'bg-green-100 text-green-800',
                                            'REJECTED' => 'bg-red-100 text-red-800',
                                            'UNDER_REVIEW' => 'bg-blue-100 text-blue-800',
                                            'SUBMITTED' => 'bg-yellow-100 text-yellow-800',
                                            'REVISION' => 'bg-purple-100 text-purple-800',
                                        ];
                                        $class = $statusClasses[$paper->status_code] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $class }}">
                                        {{ $paper->status_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($paper->status_code == 'UNDER_REVIEW')
                                        <a href="#" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition">
                                            Xem reviews
                                        </a>
                                    @elseif($paper->reviews_total == 0)
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                                            Phân công
                                        </a>
                                    @else
                                        <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">
                                            Chi tiết
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Chưa có bài báo nào</p>
                                    <p class="text-xs mt-1">Bài báo sẽ xuất hiện sau khi được nộp vào hội thảo</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 border-t">
                    <a href="/chair/papers" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                        Xem tất cả bài báo →
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Conference Overview -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Hội thảo đang hoạt động</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="border-l-4 border-orange-500 bg-orange-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">HUIT International Conference on ICT 2025</h3>
                                    <p class="text-sm text-gray-600 mt-1">15/10/2025 - 18/10/2025</p>
                                    <div class="flex items-center space-x-4 mt-3">
                                        <div class="text-xs">
                                            <span class="text-gray-500">Bài báo:</span>
                                            <span class="font-semibold text-gray-900 ml-1">28</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviewer:</span>
                                            <span class="font-semibold text-gray-900 ml-1">18</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviews:</span>
                                            <span class="font-semibold text-gray-900 ml-1">84/84</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>

                        <div class="border-l-4 border-blue-500 bg-blue-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">HUIT Security Summit 2025</h3>
                                    <p class="text-sm text-gray-600 mt-1">20/11/2025 - 22/11/2025</p>
                                    <div class="flex items-center space-x-4 mt-3">
                                        <div class="text-xs">
                                            <span class="text-gray-500">Bài báo:</span>
                                            <span class="font-semibold text-gray-900 ml-1">12</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviewer:</span>
                                            <span class="font-semibold text-gray-900 ml-1">8</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviews:</span>
                                            <span class="font-semibold text-gray-900 ml-1">36/36</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>

                        <div class="border-l-4 border-purple-500 bg-purple-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">HUIT AI & Data Science Forum 2025</h3>
                                    <p class="text-sm text-gray-600 mt-1">05/12/2025 - 07/12/2025</p>
                                    <div class="flex items-center space-x-4 mt-3">
                                        <div class="text-xs">
                                            <span class="text-gray-500">Bài báo:</span>
                                            <span class="font-semibold text-gray-900 ml-1">5</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviewer:</span>
                                            <span class="font-semibold text-gray-900 ml-1">6</span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="text-gray-500">Reviews:</span>
                                            <span class="font-semibold text-gray-900 ml-1">8/15</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    In Progress
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t">
                        <a href="/chair/conferences" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                            Xem tất cả hội thảo →
                        </a>
                    </div>
                </div>

                <!-- Quick Stats & Actions -->
                <div class="space-y-6">
                    <!-- Reviewer Performance -->
                    <div class="bg-white rounded-xl shadow-md">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Hiệu suất reviewer</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name=Reviewer+A&background=10b981&color=fff" 
                                         alt="Reviewer" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-900">Dr. Reviewer A</div>
                                        <div class="text-xs text-gray-500">8 reviews hoàn thành</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-green-600">100%</div>
                                    <div class="text-xs text-gray-500">Đúng hạn</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name=Reviewer+B&background=3b82f6&color=fff" 
                                         alt="Reviewer" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-900">Prof. Reviewer B</div>
                                        <div class="text-xs text-gray-500">6 reviews hoàn thành</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-green-600">100%</div>
                                    <div class="text-xs text-gray-500">Đúng hạn</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name=Reviewer+C&background=f59e0b&color=fff" 
                                         alt="Reviewer" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-900">Dr. Reviewer C</div>
                                        <div class="text-xs text-gray-500">4 reviews, 2 đang làm</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-yellow-600">67%</div>
                                    <div class="text-xs text-gray-500">Có chậm</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-md p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">Hành động nhanh</h3>
                        <div class="space-y-3">
                            <a href="/chair/assignments" class="block bg-white/20 hover:bg-white/30 rounded-lg p-3 transition">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span class="font-medium">Phân công reviewer mới</span>
                                </div>
                            </a>
                            <a href="/chair/coi" class="block bg-white/20 hover:bg-white/30 rounded-lg p-3 transition">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Kiểm tra COI tự động</span>
                                </div>
                            </a>
                            <a href="/chair/papers" class="block bg-white/20 hover:bg-white/30 rounded-lg p-3 transition">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span class="font-medium">Export danh sách bài báo</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
