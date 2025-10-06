<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
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
                    <a href="/conferences" class="hover:text-orange-300 transition-all duration-300 font-medium">Hội thảo</a>
                    <a href="/news" class="text-orange-300 font-medium">Tin tức</a>
                    <a href="/process" class="hover:text-orange-300 transition-all duration-300 font-medium">Quy trình</a>
                    <a href="/support" class="hover:text-orange-300 transition-all duration-300 font-medium">Hỗ trợ</a>
                </div>
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
