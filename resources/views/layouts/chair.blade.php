<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.favicon')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chair Dashboard') - HUIT Conference</title>

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
                    },
                    animation: {
                        'fadeIn': 'fadeIn 0.5s ease-in-out',
                        'slideInLeft': 'slideInLeft 0.6s ease-out',
                        'slideInRight': 'slideInRight 0.6s ease-out',
                        'slideInUp': 'slideInUp 0.5s ease-out',
                        'scaleIn': 'scaleIn 0.4s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideInLeft: {
                            '0%': { transform: 'translateX(-100%)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(100%)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        },
                        slideInUp: {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-orange-600 to-orange-700 text-white flex-shrink-0 hidden md:flex flex-col animate-slideInLeft">
            <!-- Logo -->
            <div class="p-6 border-b border-orange-500">
                <!-- Logo links to site homepage -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                            <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="Logo" class="w-full h-full object-contain p-1">
                        </div>
                    <div>
                        <h1 class="text-lg font-bold">HUIT Conferences</h1>
                        <p class="text-xs text-orange-200">Chair Dashboard</p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('chair.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('chair.dashboard') ? 'bg-orange-500 font-semibold' : 'hover:bg-orange-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('chair.conferences.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('chair.conferences*') ? 'bg-orange-500 font-semibold' : 'hover:bg-orange-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Quản lý hội thảo</span>
                </a>

                <a href="{{ route('chair.papers') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('chair.papers*') ? 'bg-orange-500 font-semibold' : 'hover:bg-orange-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Quản lý bài báo</span>
                </a>

                <!-- Phân công Dropdown Menu -->
                <div x-data="{ open: {{ request()->routeIs('chair.reviewers*') || request()->routeIs('chair.assignments*') || request()->routeIs('chair.coi*') ? 'true' : 'false' }} }" class="space-y-1">
                    <!-- Main Menu Item -->
                    <button @click="open = !open" 
                            :class="open ? 'bg-orange-500 font-semibold' : 'hover:bg-orange-500'"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition text-left">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Phân công</span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Submenu Items -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="ml-6 space-y-1">
                        
                        <!-- Mời reviewer -->
                        <a href="{{ route('chair.reviewers.index') }}" 
                           class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm {{ request()->routeIs('chair.reviewers*') ? 'bg-orange-400 font-medium' : 'hover:bg-orange-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Mời reviewer</span>
                        </a>

                        <!-- Phân công phản biện -->
                        <a href="{{ route('chair.assignments.index') }}" 
                           class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm {{ request()->routeIs('chair.assignments*') ? 'bg-orange-400 font-medium' : 'hover:bg-orange-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Phân công</span>
                        </a>

                        <!-- Kiểm tra COI (submenu) -->
                        <a href="{{ route('chair.coi.index') }}" 
                           class="flex items-center space-x-3 px-4 py-2 rounded-lg transition text-sm {{ request()->routeIs('chair.coi*') ? 'bg-orange-400 font-medium' : 'hover:bg-orange-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Kiểm tra COI</span>
                        </a>
                    </div>
                    </div>

                <a href="{{ route('chair.bidding-settings') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('chair.bidding-settings*') ? 'bg-orange-500 font-semibold' : 'hover:bg-orange-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Cài đặt Bidding</span>
                </a>

                <a href="#" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition hover:bg-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Báo cáo & Thống kê</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-orange-500">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-400 rounded-full flex items-center justify-center font-bold shadow-lg">
                        {{ substr(Auth::user()->full_name ?? 'C', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->full_name ?? 'Chair User' }}</p>
                        <p class="text-xs text-orange-200 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-orange-800 hover:bg-orange-900 rounded-lg text-sm font-medium transition">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-600">@yield('page-subtitle', '')</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 font-semibold text-sm">{{ substr(Auth::user()->full_name ?? 'C', 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->full_name ?? 'Chair User' }}</span>
                            <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Hồ sơ cá nhân</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cài đặt</a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area (Scrollable) -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="animate-slideInUp">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
