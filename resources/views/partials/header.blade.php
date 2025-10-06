<nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <div class="px-4">
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
                <a href="{{ route('conferences.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                    Hội thảo
                </a>
                <a href="{{ route('news.index') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                    Tin tức
                </a>
                <a href="{{ route('process') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                    Quy trình
                </a>
                <a href="{{ route('support') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                    Hỗ trợ
                </a>
                
                @auth
                <!-- User Dropdown -->
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" 
                            class="flex items-center space-x-2 hover:text-orange-300 transition-all duration-300 px-3 py-2 rounded-xl hover:bg-white/10">
                        <span class="font-medium">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 transition-transform duration-300" 
                             :class="{ 'rotate-180': userMenuOpen }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="userMenuOpen" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="userMenuOpen = false" 
                         x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Hồ sơ cá nhân</span>
                            </div>
                        </a>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </div>
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
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
                <a href="{{ route('login') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">
                    Đăng nhập
                </a>
                <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 font-medium">
                    Đăng ký
                </a>
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
            <a href="{{ route('conferences.index') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hội thảo</a>
            <a href="{{ route('news.index') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
            <a href="{{ route('process') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
            <a href="{{ route('support') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
            @guest
            <a href="{{ route('login') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Đăng nhập</a>
            <a href="{{ route('register') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Đăng ký</a>
            @endguest
        </div>
    </div>
</nav>
