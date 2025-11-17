<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh sách Hội thảo - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
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
    <!-- Navigation Bar -->
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-blue-700 font-bold text-xl">H</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                    </div>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="hover:text-orange-300 transition-all duration-300 font-medium">Trang chủ</a>
                    <a href="/conferences" class="text-orange-300 font-medium">Hội thảo</a>
                    <a href="/news" class="hover:text-orange-300 transition-all duration-300 font-medium">Tin tức</a>
                    <a href="/process" class="hover:text-orange-300 transition-all duration-300 font-medium">Quy trình</a>
                    <a href="/support" class="hover:text-orange-300 transition-all duration-300 font-medium">Hỗ trợ</a>
                </div>
                
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-700 to-blue-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Danh sách Hội thảo Khoa học</h1>
            <p class="text-blue-100 text-lg">Khám phá và tham gia các hội thảo khoa học đang diễn ra tại HUIT</p>
        </div>
    </section>

    <!-- Search and Filters -->
    <section class="py-8 bg-white border-b">
        <div class="container mx-auto px-4">
            <form method="GET" action="{{ route('conferences.index') }}" class="grid md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input name="search" 
                           type="text" 
                           value="{{ $search }}"
                           placeholder="Nhập tên hội thảo, mô tả, từ khóa..." 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Đang nhận bài</option>
                        <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                        <option value="ongoing" {{ $status == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                        <option value="ended" {{ $status == 'ended' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                </div>
                
                <!-- Level Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cấp độ</label>
                    <select name="level" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả cấp độ</option>
                        @foreach($levels as $levelOption)
                            <option value="{{ $levelOption }}" {{ $level == $levelOption ? 'selected' : '' }}>
                                {{ $levelOption == 'KHOA' ? 'Cấp Khoa' : 'Cấp Trường' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                        Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </section>
                </div>
                
                <!-- Topic Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lĩnh vực</label>
                    <select x-model="selectedTopic" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all">Tất cả</option>
                        <option value="cntt">Công nghệ thông tin</option>
                        <option value="dien">Điện - Điện tử</option>
                        <option value="ktqt">Kinh tế - Quản trị</option>
                        <option value="cokhi">Cơ khí</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
            </div>
            
            <!-- Active Filters -->
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="text-sm text-gray-600 font-medium">Bộ lọc:</span>
                <template x-if="selectedStatus !== 'all'">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        <span x-text="selectedStatus"></span>
                        <button @click="selectedStatus = 'all'" class="ml-2 hover:text-blue-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                </template>
                <template x-if="selectedTopic !== 'all'">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span x-text="selectedTopic"></span>
                        <button @click="selectedTopic = 'all'" class="ml-2 hover:text-green-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                </template>
                <button @click="searchQuery = ''; selectedStatus = 'all'; selectedTopic = 'all'" 
                        class="text-xs text-gray-600 hover:text-gray-800 underline">
                    Xóa tất cả
                </button>
            </div>
        </div>
    </section>

    <!-- Conference Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">Tìm thấy <span class="font-semibold text-gray-800">8 hội thảo</span></p>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Sắp xếp:</span>
                    <select class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option>Mới nhất</option>
                        <option>Deadline gần nhất</option>
                        <option>Tên A-Z</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Conference Card 1 -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-xs font-semibold bg-white/20 px-3 py-1.5 rounded-xl backdrop-blur-sm">
                                HUIT-ICI-2025
                            </span>
                            <span class="text-xs font-semibold bg-green-500 px-3 py-1.5 rounded-xl shadow-md">
                                Đang mở
                            </span>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Hội thảo Khoa học CNTT HUIT 2025</h3>
                        <p class="text-sm text-blue-100">25-30/11/2025 – Khoa CNTT</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>45 bài báo đã nộp</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-orange-600">Hạn nộp: 15/11/2025</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>TP.HCM</span>
                            </div>
                        </div>
                        <a href="/conferences/1" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center text-sm font-medium py-3 rounded-xl transition-all duration-300 hover:shadow-lg">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Conference Card 2 -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-500 p-6 text-white">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-xs font-semibold bg-white/20 px-3 py-1.5 rounded-xl backdrop-blur-sm">
                                HUIT-CEE-2025
                            </span>
                            <span class="text-xs font-semibold bg-green-500 px-3 py-1.5 rounded-xl shadow-md">
                                Đang mở
                            </span>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Hội thảo Điện – Điện tử & Tự động hóa</h3>
                        <p class="text-sm text-purple-100">15/12/2025 – Khoa Điện</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>28 bài báo đã nộp</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-orange-600">Hạn nộp: 01/12/2025</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>TP.HCM</span>
                            </div>
                        </div>
                        <a href="/conferences/2" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center text-sm font-medium py-3 rounded-xl transition-all duration-300 hover:shadow-lg">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Conference Card 3 -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                    <div class="bg-gradient-to-r from-teal-600 to-teal-500 p-6 text-white">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-xs font-semibold bg-white/20 px-3 py-1.5 rounded-xl backdrop-blur-sm">
                                HUIT-KUS-2025
                            </span>
                            <span class="text-xs font-semibold bg-green-500 px-3 py-1.5 rounded-xl shadow-md">
                                Đang mở
                            </span>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Hội thảo Kinh tế & Quản trị 2025</h3>
                        <p class="text-sm text-teal-100">10/01/2026 – Khoa Quản trị KD</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>32 bài báo đã nộp</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-orange-600">Hạn nộp: 25/12/2025</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>TP.HCM</span>
                            </div>
                        </div>
                        <a href="/conferences/3" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center text-sm font-medium py-3 rounded-xl transition-all duration-300 hover:shadow-lg">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- More cards would be dynamically generated here -->
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex items-center justify-center space-x-2">
                <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-600 disabled:opacity-50" disabled>
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
                    <p class="text-sm">Trường Đại học Công Thương TP.HCM</p>
                    <p class="text-sm mt-2">Nền tảng quản lý hội thảo khoa học đa cấp</p>
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
