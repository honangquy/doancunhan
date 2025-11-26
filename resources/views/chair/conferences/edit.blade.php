@extends('layouts.chair')

@section('title', 'Chỉnh sửa Hội thảo')

@section('page-title', 'Chỉnh sửa Hội thảo')

@section('page-subtitle', 'Cập nhật thông tin hội thảo')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('chair.conferences.show', $conference->conference_id) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại chi tiết
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Chỉnh sửa: {{ isset($conference->title) ? $conference->title : 'N/A' }}</h1>
            <p class="mt-1 text-sm text-gray-500">Cập nhật thông tin hội thảo (chỉ khi chưa được admin duyệt cuối cùng)</p>
        </div>
        
        <form method="POST" action="{{ route('chair.conferences.update', isset($conference->conference_id) ? $conference->conference_id : '#') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Conference Name -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Tên hội thảo</label>
                    <input type="text" name="title" id="title" 
                           value="{{ old('title', isset($conference->title) ? $conference->title : '') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           readonly>
                    <p class="mt-1 text-sm text-gray-500">Tên hội thảo không thể thay đổi</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" id="description" rows="4" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', isset($conference->description) ? $conference->description : '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                        <input type="date" name="start_date" id="start_date" 
                               value="{{ old('start_date', (isset($conference->start_date) && $conference->start_date) ? \Carbon\Carbon::parse($conference->start_date)->format('Y-m-d') : '') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                        <input type="date" name="end_date" id="end_date" 
                               value="{{ old('end_date', (isset($conference->end_date) && $conference->end_date) ? \Carbon\Carbon::parse($conference->end_date)->format('Y-m-d') : '') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Địa điểm</label>
                    <input type="text" name="location" id="location" 
                           value="{{ old('location', isset($conference->location) ? $conference->location : '') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Participants -->
                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700">Số lượng tham gia tối đa</label>
                    <input type="number" name="max_participants" id="max_participants" 
                           value="{{ old('max_participants', isset($conference->max_participants) ? $conference->max_participants : '') }}" 
                           min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('max_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadlines -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="submission_deadline" class="block text-sm font-medium text-gray-700">Hạn nộp bài</label>
                        <input type="datetime-local" name="submission_deadline" id="submission_deadline" 
                               value="{{ old('submission_deadline', (isset($conference->submission_deadline) && $conference->submission_deadline) ? \Carbon\Carbon::parse($conference->submission_deadline)->format('Y-m-d\TH:i') : '') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('submission_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="review_deadline" class="block text-sm font-medium text-gray-700">Hạn phản biện</label>
                        <input type="datetime-local" name="review_deadline" id="review_deadline" 
                               value="{{ old('review_deadline', (isset($conference->review_deadline) && $conference->review_deadline) ? \Carbon\Carbon::parse($conference->review_deadline)->format('Y-m-d\TH:i') : '') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('review_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Reviewers per Paper -->
                <div>
                    <label for="reviewers_per_paper" class="block text-sm font-medium text-gray-700">Số reviewer mỗi bài báo</label>
                    <select name="reviewers_per_paper" id="reviewers_per_paper" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="2" {{ old('reviewers_per_paper', isset($conference->reviewers_per_paper) ? $conference->reviewers_per_paper : 3) == 2 ? 'selected' : '' }}>2 reviewer</option>
                        <option value="3" {{ old('reviewers_per_paper', isset($conference->reviewers_per_paper) ? $conference->reviewers_per_paper : 3) == 3 ? 'selected' : '' }}>3 reviewer</option>
                        <option value="4" {{ old('reviewers_per_paper', isset($conference->reviewers_per_paper) ? $conference->reviewers_per_paper : 3) == 4 ? 'selected' : '' }}>4 reviewer</option>
                        <option value="5" {{ old('reviewers_per_paper', isset($conference->reviewers_per_paper) ? $conference->reviewers_per_paper : 3) == 5 ? 'selected' : '' }}>5 reviewer</option>
                    </select>
                    @error('reviewers_per_paper')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('chair.conferences.show', isset($conference->conference_id) ? $conference->conference_id : '#') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

</div>

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
</style>
@endpush
@endsection