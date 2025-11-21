@extends('layouts.chair')

@section('title', 'Tạo tin tức mới')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tạo tin tức mới</h1>
            <p class="text-gray-600 mt-1">Thêm tin tức, thông báo hoặc sự kiện mới</p>
        </div>
        <a href="{{ route('chair.news.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại
        </a>
    </div>

    <form action="{{ route('chair.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('chair.news.form')

        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('chair.news.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                Hủy
            </a>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Tạo tin tức
            </button>
        </div>
    </form>
</div>
@endsection
