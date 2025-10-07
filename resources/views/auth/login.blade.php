<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập - HUIT Conferences</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        'accent-orange': '#f97316'
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        /* Decorative rings on the left panel */
        .left-rings svg { position: absolute; left: -10%; bottom: -10%; width: 90%; height: auto; opacity: 0.14; }
    </style>
</head>
<body class="h-full bg-white">
    <div class="min-h-screen flex">
        <!-- Left gradient panel -->
        <div class="hidden lg:flex w-1/2 relative items-center justify-center overflow-hidden bg-gradient-to-b from-blue-400 via-blue-600 to-blue-900">
            <div class="absolute inset-0 left-rings pointer-events-none">
                <!-- Decorative overlapping rings (SVG) -->
                <svg viewBox="0 0 1200 800" preserveAspectRatio="xMinYMin meet" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <defs>
                        <linearGradient id="g1" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.12" />
                            <stop offset="100%" stop-color="#1e3a8a" stop-opacity="0.08" />
                        </linearGradient>
                    </defs>
                    <g stroke="url(#g1)" stroke-width="6" fill="none">
                        <circle cx="200" cy="800" r="520" />
                        <circle cx="380" cy="820" r="420" />
                        <circle cx="60" cy="780" r="300" />
                    </g>
                </svg>
            </div>

            <div class="relative z-10 px-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-2xl shadow-lg mb-6">
                    <span class="text-4xl font-extrabold text-white">H</span>
                </div>
                <h1 class="text-white text-4xl font-extrabold mb-4">HUIT Conferences</h1>
                <p class="text-white/80 max-w-lg">Nền tảng quản lý hội thảo - nộp bài, phân công phản biện và điều phối chương trình một cách hiệu quả.</p>
                <div class="mt-8">
                    <a href="{{ route('home') }}" class="inline-block bg-white/10 text-white px-4 py-2 rounded-full text-sm hover:bg-white/20 transition">Tìm hiểu thêm</a>
                </div>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <div class="bg-white p-8 rounded-2xl shadow-2xl">
                    <!-- Alerts -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Xin chào!</h2>
                    <p class="text-sm text-gray-500 mb-6">Đăng nhập để tiếp tục quản lý hội thảo</p>

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email field with SVG icon -->
                        <div>
                            <label for="email" class="sr-only">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!-- Email SVG icon -->
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="Email Address"
                                       class="block w-full pl-12 pr-4 py-3 rounded-full border border-gray-200 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-accent-orange focus:border-accent-orange">
                            </div>
                        </div>

                        <!-- Password field with SVG icon -->
                        <div>
                            <label for="password" class="sr-only">Mật khẩu</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!-- Lock SVG icon -->
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Password"
                                       class="block w-full pl-12 pr-4 py-3 rounded-full border border-gray-200 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-accent-orange focus:border-accent-orange">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-accent-orange focus:ring-accent-orange border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="text-sm">
                                <a href="#" class="font-medium text-blue-600 hover:text-blue-700 transition">Quên mật khẩu?</a>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="w-full py-3 rounded-full text-white bg-accent-orange hover:bg-amber-600 shadow-md transition">Đăng nhập</button>
                        </div>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700 transition">Đăng ký</a>
                        </p>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600">← Quay lại trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
