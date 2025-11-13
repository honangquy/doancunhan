@extends('layouts.app')

@section('title', $notification->title)

@section('content')
@php
    // Color scheme mapping
    $colorScheme = $colorScheme ?? 'blue';
    $colors = [
        'orange' => [
            'gradient' => 'from-orange-500 to-orange-600',
            'bg' => 'bg-orange-50',
            'text' => 'text-orange-600',
            'icon_bg' => 'bg-orange-100',
            'dot' => 'bg-orange-600',
            'hover' => 'hover:bg-orange-700',
            'button' => 'bg-orange-600'
        ],
        'blue' => [
            'gradient' => 'from-blue-500 to-blue-600',
            'bg' => 'bg-blue-50',
            'text' => 'text-blue-600',
            'icon_bg' => 'bg-blue-100',
            'dot' => 'bg-blue-600',
            'hover' => 'hover:bg-blue-700',
            'button' => 'bg-blue-600'
        ],
        'purple' => [
            'gradient' => 'from-purple-500 to-purple-600',
            'bg' => 'bg-purple-50',
            'text' => 'text-purple-600',
            'icon_bg' => 'bg-purple-100',
            'dot' => 'bg-purple-600',
            'hover' => 'hover:bg-purple-700',
            'button' => 'bg-purple-600'
        ]
    ];
    
    $color = $colors[$colorScheme];
@endphp

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-6">
            <button onclick="window.history.back()" 
                    class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Quay lại</span>
            </button>
        </div>

        <!-- Notification Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r {{ $color['gradient'] }} p-6 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">{{ strtoupper($notification->type) }}</span>
                        </div>
                        <h1 class="text-2xl font-bold mb-2">{{ $notification->title }}</h1>
                        <div class="flex items-center space-x-4 text-sm opacity-90">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($notification->is_read)
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Đã đọc {{ \Carbon\Carbon::parse($notification->read_at)->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    @if($notification->is_read)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20">
                        Đã đọc
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-400 text-yellow-900">
                        Chưa đọc
                    </span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="prose max-w-none">
                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $notification->message }}</div>
                </div>

                <!-- Conference Info (if available) -->
                @if($notification->conference_id)
                @php
                    $conference = DB::table('hoithao')->where('conference_id', $notification->conference_id)->first();
                @endphp
                @if($conference)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Liên quan đến hội thảo</h3>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 {{ $color['icon_bg'] }} rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 {{ $color['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $conference->title }}</h4>
                                @if($conference->description)
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($conference->description, 200) }}</p>
                                @endif
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    @if($conference->start_date)
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}</span>
                                    </div>
                                    @endif
                                    @if($conference->location)
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $conference->location }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <button onclick="window.history.back()" 
                            class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition">
                        ← Quay lại danh sách
                    </button>
                    
                    @if(!$notification->is_read)
                    <form action="{{ route('web.notifications.read', $notification->notification_id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 {{ $color['button'] }} text-white rounded-lg {{ $color['hover'] }} transition font-medium">
                            Đánh dấu đã đọc
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
