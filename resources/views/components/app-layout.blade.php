<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'HUIT Conferences' }} - Hệ thống Quản lý Hội thảo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-full" x-data="{ sidebarOpen: false }">
        <!-- Navigation -->
        @include('partials.header')
        
        <div class="flex">
            <!-- Sidebar -->
            @include('partials.sidebar')
            
            <!-- Main Content -->
            <main class="flex-1 min-h-screen bg-gray-50">
                <!-- Page Header -->
                @if(isset($header))
                <header class="bg-white shadow-sm">
                    <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
                @endif
                
                <!-- Flash Messages -->
                @include('partials.alerts')
                
                <!-- Page Content -->
                <div class="py-6">
                    <div class="mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
