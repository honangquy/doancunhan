@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Xác thực email</h2>
            <p class="mt-2 text-sm text-gray-600">Chúng tôi đã gửi link xác thực đến email của bạn</p>
        </div>

        <!-- Card -->
        <div class="bg-white shadow-lg rounded-lg p-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-2 text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-2 text-sm text-yellow-800">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

            <!-- Content -->
            <div class="text-center space-y-4">
                <p class="text-gray-600">
                    Chúng tôi đã gửi email xác thực đến địa chỉ: 
                    <strong class="text-gray-900">{{ Auth::user()->email }}</strong>
                </p>
                
                <p class="text-sm text-gray-500">
                    Vui lòng kiểm tra hộp thư và nhấn vào link xác thực để kích hoạt tài khoản của bạn.
                    <br><strong class="text-red-600">Lưu ý: Link xác thực chỉ có hiệu lực trong 10 phút.</strong>
                </p>

                <!-- Resend Button -->
                <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                    @csrf
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Gửi lại email xác thực
                    </button>
                </form>

                <!-- Logout Link -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Đăng xuất và đăng nhập bằng tài khoản khác
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                Không nhận được email? Kiểm tra thư mục spam hoặc 
                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">liên hệ hỗ trợ</a>
            </p>
        </div>
    </div>
</div>
@endsection