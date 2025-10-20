@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- General Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cài đặt chung</h3>
            
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên hệ thống</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="Hệ thống quản lý hội thảo">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email hệ thống</label>
                    <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="admin@conference.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Múi giờ</label>
                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option>Asia/Ho_Chi_Minh</option>
                        <option>UTC</option>
                    </select>
                </div>
                
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Lưu cài đặt
                </button>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cài đặt bảo mật</h3>
            
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Độ dài mật khẩu tối thiểu</label>
                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="8">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Thời gian hết phiên (phút)</label>
                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="120">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" class="rounded border-gray-300" checked>
                    <label class="ml-2 text-sm text-gray-700">Bắt buộc xác thực 2FA</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" class="rounded border-gray-300">
                    <label class="ml-2 text-sm text-gray-700">Ghi nhật ký đăng nhập</label>
                </div>
                
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Lưu cài đặt
                </button>
            </form>
        </div>

        <!-- Email Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cài đặt Email</h3>
            
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="smtp.gmail.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="587">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Lưu cài đặt
                </button>
            </form>
        </div>

        <!-- Database Maintenance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bảo trì hệ thống</h3>
            
            <div class="space-y-4">
                <button class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Sao lưu cơ sở dữ liệu
                </button>
                
                <button class="w-full bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Xóa cache hệ thống
                </button>
                
                <button class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Tối ưu hóa cơ sở dữ liệu
                </button>
                
                <button class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Khởi động lại hệ thống
                </button>
            </div>
        </div>
    </div>
</div>
@endsection