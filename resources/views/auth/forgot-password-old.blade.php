@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2h-3m-6 3h.01M9 19h.01M15 19h.01M6 7h3m6 0h3M9 7c0-1.1-.9-2-2-2s-2 .9-2 2v8c0 1.1.9 2 2 2s2-.9 2-2V7z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Quên mật khẩu?</h2>
            <p class="mt-2 text-sm text-gray-600">Nhập email để nhận link đặt lại mật khẩu</p>
            <p class="mt-1 text-center text-sm text-blue-200">
                Nhập email để nhận link đặt lại mật khẩu
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-2xl sm:rounded-2xl sm:px-10">
                <!-- Success Message -->
                @if (session('status'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
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

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required 
                                autocomplete="email"
                                value="{{ old('email') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="your.email@example.com">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Chúng tôi sẽ gửi link đặt lại mật khẩu đến email này
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300 hover:shadow-lg hover:scale-105">
                            Gửi link đặt lại mật khẩu
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Hoặc</span>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <div class="mt-6">
                    <a href="{{ route('login') }}" 
                       class="w-full flex justify-center items-center py-3 px-4 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 hover:shadow-md group">
                        <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Quay lại đăng nhập
                    </a>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-blue-100">
                    Cần hỗ trợ? 
                    <a href="{{ route('support') }}" class="font-medium text-white hover:text-blue-200 transition-colors">
                        Liên hệ với chúng tôi
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
