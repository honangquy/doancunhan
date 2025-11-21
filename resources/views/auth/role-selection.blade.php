<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chọn Vai Trò - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modern-bg {
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            filter: blur(50px);
            z-index: -1;
            animation: float 20s infinite;
        }
        .shape-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: linear-gradient(to right, #4f46e5, #818cf8);
            border-radius: 50%;
            opacity: 0.4;
            animation-delay: 0s;
        }
        .shape-2 {
            bottom: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: linear-gradient(to left, #ec4899, #f472b6);
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            opacity: 0.4;
            animation-delay: -5s;
        }
        .shape-3 {
            top: 40%;
            left: 40%;
            width: 300px;
            height: 300px;
            background: linear-gradient(to bottom, #06b6d4, #22d3ee);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            opacity: 0.3;
            animation-delay: -10s;
        }
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -50px) rotate(10deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="h-full modern-bg">
    <!-- Background Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <div class="sm:mx-auto sm:w-full sm:max-w-md" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex justify-center">
                <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT Logo" class="h-24 w-24 bg-white rounded-full p-2 shadow-xl object-contain ring-4 ring-white/50">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 drop-shadow-sm">
                Chào mừng trở lại!
            </h2>
            <p class="mt-2 text-center text-lg text-gray-600 font-medium">
                {{ $user->full_name }}
            </p>
            <p class="mt-1 text-center text-sm text-gray-500">
                Vui lòng chọn vai trò để tiếp tục truy cập hệ thống
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg"
             x-show="show"
             x-transition:enter="transition ease-out duration-700 delay-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="glass-effect py-8 px-4 shadow-2xl sm:rounded-2xl sm:px-10 border border-white/20">
                <div class="space-y-4">
                    @foreach($roles as $roleCode => $roleItems)
                        <div x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false"
                             class="transform transition-all duration-300"
                             :class="{ 'scale-[1.02]': hover }">
                            <form action="{{ route('role.select') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $roleCode }}">
                                
                                <button type="submit" class="w-full group relative flex items-center justify-between p-5 border-2 border-gray-100 rounded-2xl hover:border-blue-500 hover:bg-blue-50/50 hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 bg-white">
                                    <div class="flex items-center">
                                        <span class="h-14 w-14 rounded-2xl flex items-center justify-center shadow-sm transition-transform group-hover:scale-110 duration-300
                                            {{ $roleCode === 'ADMIN' ? 'bg-red-100 text-red-600' : '' }}
                                            {{ $roleCode === 'CHAIR' ? 'bg-purple-100 text-purple-600' : '' }}
                                            {{ $roleCode === 'REVIEWER' ? 'bg-green-100 text-green-600' : '' }}
                                            {{ $roleCode === 'AUTHOR' ? 'bg-blue-100 text-blue-600' : '' }}
                                        ">
                                            <!-- Icons based on role -->
                                            @if($roleCode === 'ADMIN')
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            @elseif($roleCode === 'CHAIR')
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            @elseif($roleCode === 'REVIEWER')
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                            @else
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            @endif
                                        </span>
                                        <div class="ml-5 text-left">
                                            <p class="text-lg font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                @if($roleCode === 'ADMIN') Quản trị viên (Admin)
                                                @elseif($roleCode === 'CHAIR') Chủ trì hội thảo (Chair)
                                                @elseif($roleCode === 'REVIEWER') Người phản biện (Reviewer)
                                                @elseif($roleCode === 'AUTHOR') Tác giả (Author)
                                                @else {{ $roleCode }}
                                                @endif
                                            </p>
                                            @if($roleItems->first()->conference_title)
                                                <p class="text-sm text-gray-500 mt-1 group-hover:text-blue-600/70 transition-colors">
                                                    Tham gia {{ $roleItems->count() }} hội thảo
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-500 mt-1 group-hover:text-blue-600/70 transition-colors">
                                                    Truy cập bảng điều khiển
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="h-10 w-10 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-all duration-300 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-gray-600 bg-gray-50 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
            <p class="mt-6 text-center text-xs text-white/60">
                &copy; {{ date('Y') }} HUIT Conferences. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>