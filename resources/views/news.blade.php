<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>Tin tức & Sự kiện - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="flex-shrink-0 hover:opacity-90 transition">
                    <img src="https://huit.edu.vn/Images/Documents/N00CT/logo-huit-web-chinh-moi-mau-xanh-02.svg?h=80"
                         alt="HUIT Logo" class="h-12 w-auto">
                </a>
                <div class="flex-1 flex flex-col items-center text-center space-y-1">
                    <span class="text-lg md:text-xl font-bold text-blue-600 uppercase tracking-wide">BỘ CÔNG THƯƠNG</span>
                    <span class="text-xl md:text-2xl lg:text-3xl font-bold text-blue-700 uppercase">Trường Đại học Công Thương TP. Hồ Chí Minh TP. HỒ CHÍ MINH</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md" />
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                    </div>
                </a>

                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="hover:text-orange-300 transition font-medium">Hội thảo</a>
                    <a href="{{ route('news.index') }}" class="text-orange-300 font-bold">Tin tức</a>
                    <a href="{{ route('process') }}" class="hover:text-orange-300 transition font-medium">Quy trình</a>
                    <a href="{{ route('support') }}" class="hover:text-orange-300 transition font-medium">Hỗ trợ</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-full font-semibold transition">
                            {{ Auth::user()->full_name ?? Auth::user()->email }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-orange-300 transition font-medium">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-full font-semibold transition">
                            Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Tin tức & Sự kiện
            </h1>
            <p class="text-xl text-blue-100 mb-8">
                Cập nhật tin tức mới nhất về các hội thảo và hoạt động khoa học tại HUIT
            </p>

            <!-- Search Box -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text"
                           placeholder="Tìm kiếm tin tức, sự kiện..."
                           class="w-full px-6 py-4 rounded-full text-gray-800 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-full font-semibold transition">
                        Tìm kiếm
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-blue-50 rounded-lg">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-blue-600">{{ $statistics['conferences'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Hội thảo</div>
                </div>

                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-green-600">{{ $statistics['papers'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Bài báo</div>
                </div>

                <div class="text-center p-6 bg-purple-50 rounded-lg">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-purple-600">{{ $statistics['reviewers'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Phản biện</div>
                </div>

                <div class="text-center p-6 bg-orange-50 rounded-lg">
                    <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-orange-600">{{ $recentNews->count() }}</div>
                    <div class="text-gray-600 text-sm">Tin tức mới</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent News Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Tin tức mới nhất</h2>
                <a href="{{ route('articles.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    Xem tất cả →
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @forelse($recentNews as $news)
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition">
                    @if($news->cover_image)
                    <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                            <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                        </svg>
                    </div>
                    @endif
                    <div class="p-6">
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                {{ $news->category }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $news->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $news->summary }}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ \Carbon\Carbon::parse($news->published_at)->format('d/m/Y') }}</span>
                            <a href="{{ route('articles.show', $news->slug) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                Đọc thêm →
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-gray-500 text-lg">Chưa có tin tức nào được xuất bản</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Recent Conferences Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Hội thảo sắp diễn ra</h2>
                <a href="{{ route('home') }}#conferences" class="text-blue-600 hover:text-blue-700 font-semibold">
                    Xem tất cả →
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentConferences as $conf)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2">{{ $conf->title }}</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($conf->start_date)->format('d/m/Y') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            {{ $conf->paper_count }} bài báo
                        </div>
                    </div>
                    <a href="{{ route('conferences.show', $conf->conference_id) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-700 font-semibold text-sm">
                        Xem chi tiết →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white font-bold mb-4">HUIT Conferences</h3>
                    <p class="text-sm text-gray-400">Hệ thống quản lý hội thảo khoa học</p>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Liên kết</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white">Trang chủ</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-gray-400 hover:text-white">Tin tức</a></li>
                        <li><a href="{{ route('process') }}" class="text-gray-400 hover:text-white">Quy trình</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Hỗ trợ</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('support') }}" class="text-gray-400 hover:text-white">Trợ giúp</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Liên hệ</h3>
                    <p class="text-sm text-gray-400">Email: khoacs@huit.edu.vn</p>
                    <p class="text-sm text-gray-400">Điện thoại: (028) 38xx xxxx</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>© 2025 HUIT - All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
