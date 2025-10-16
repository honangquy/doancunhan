
<!-- Sticky Navigation (from homepage) -->
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
                    <!-- Notifications + User menu placeholder (keeps complexity low) -->
                    <div class="relative">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-white hover:text-orange-300 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-orange-300 transition-all duration-300 font-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 font-medium">Đăng ký</a>
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
            <a href="#conferences" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hội thảo</a>
            <a href="{{ route('news.index') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Tin tức</a>
            <a href="{{ route('process') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Quy trình</a>
            <a href="{{ route('support') }}" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Hỗ trợ</a>
            <a href="#calendar" class="block py-2 px-2 hover:text-orange-300 hover:bg-white/10 rounded-xl transition-all duration-300">Lịch</a>
        </div>
    </div>
</nav>
