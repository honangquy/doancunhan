<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>{{ $title ?? 'HUIT Conferences' }} - Hệ thống Quản lý Hội thảo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="/" class="inline-flex items-center space-x-3">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-blue-700 font-bold text-2xl">H</span>
                </div>
                <div class="text-left">
                    <div class="text-white font-bold text-xl">HUIT Conferences</div>
                    <div class="text-blue-200 text-sm">Hệ thống quản lý hội thảo</div>
                </div>
            </a>
        </div>

        <!-- Flash Messages -->
        @include('partials.alerts')

        <!-- Content -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-2xl sm:rounded-2xl sm:px-10">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-blue-100">
                © {{ date('Y') }} HUIT - Trường Đại học Công nghiệp TP.HCM
            </p>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
