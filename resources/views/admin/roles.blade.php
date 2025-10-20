@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title }}</h1>

    <!-- Role Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($roleStats as $role)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-full 
                    @if($role->role === 'ADMIN') bg-red-100 
                    @elseif($role->role === 'CHAIR') bg-purple-100 
                    @elseif($role->role === 'REVIEWER') bg-blue-100 
                    @else bg-green-100 @endif">
                    <svg class="w-6 h-6 
                        @if($role->role === 'ADMIN') text-red-600 
                        @elseif($role->role === 'CHAIR') text-purple-600 
                        @elseif($role->role === 'REVIEWER') text-blue-600 
                        @else text-green-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ $role->role }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($role->count) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Role Management -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Quản lý phân quyền</h3>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Thêm quyền mới
            </button>
        </div>

        <div class="space-y-4">
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900">ADMIN</h4>
                        <p class="text-sm text-gray-600">Quản trị hệ thống - Toàn quyền truy cập</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900">Sửa</button>
                        <button class="text-red-600 hover:text-red-900">Xóa</button>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900">CHAIR</h4>
                        <p class="text-sm text-gray-600">Chủ tọa hội thảo - Quản lý hội thảo và đánh giá</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900">Sửa</button>
                        <button class="text-red-600 hover:text-red-900">Xóa</button>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900">REVIEWER</h4>
                        <p class="text-sm text-gray-600">Phản biện - Đánh giá bài báo</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900">Sửa</button>
                        <button class="text-red-600 hover:text-red-900">Xóa</button>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900">AUTHOR</h4>
                        <p class="text-sm text-gray-600">Tác giả - Nộp và quản lý bài báo</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-indigo-600 hover:text-indigo-900">Sửa</button>
                        <button class="text-red-600 hover:text-red-900">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection