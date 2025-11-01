@extends('layouts.admin')

@section('title', $title)

@section('content')
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="mt-2 text-gray-600">Quản lý phân quyền và vai trò người dùng</p>
            </div>

            <!-- Permissions Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Danh sách quyền hạn</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($permissions as $key => $permission)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">{{ $permission }}</h3>
                        <p class="text-sm text-gray-600 mb-3">Quyền: {{ $key }}</p>
                        
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox" checked>
                                <span class="ml-2 text-sm">Admin</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2 text-sm">Chair</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2 text-sm">Reviewer</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2 text-sm">Author</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Lưu thay đổi
                    </button>
                </div>
            </div>

            <!-- Role Statistics -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Thống kê vai trò</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">5</div>
                        <div class="text-sm text-blue-800">Admin</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">25</div>
                        <div class="text-sm text-green-800">Chair</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">150</div>
                        <div class="text-sm text-yellow-800">Reviewer</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">350</div>
                        <div class="text-sm text-purple-800">Author</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection