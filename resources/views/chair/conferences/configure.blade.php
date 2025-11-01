@extends('layouts.chair')

@section('title', 'Cấu hình Hội thảo')

@section('page-title', 'Cấu hình hội thảo')

@section('page-subtitle', 'Thiết lập thông tin chi tiết cho hội thảo "' . $request->title . '"')

@section('content')
<div class="max-w-4xl mx-auto" x-data="conferenceSetup()">
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
            <h4 class="font-bold">Có lỗi xảy ra:</h4>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('chair.conferences.index') }}" class="hover:text-gray-700">Quản lý hội thảo</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900">Cấu hình hội thảo</span>
        </nav>
    </div>

    <!-- Request Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-medium text-blue-900 mb-3">Thông tin yêu cầu gốc</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-blue-800">Tên hội thảo:</span>
                <span class="text-blue-700">{{ $request->title }}</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Lĩnh vực:</span>
                <span class="text-blue-700">{{ $request->field }}</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Cấp độ:</span>
                <span class="text-blue-700">{{ $request->level_code === 'KHOA' ? 'Cấp Khoa' : 'Cấp Trường' }}</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Ngày dự kiến:</span>
                <span class="text-blue-700">{{ $request->expected_date ? \Carbon\Carbon::parse($request->expected_date)->format('d/m/Y') : 'Chưa xác định' }}</span>
            </div>
        </div>
        <div class="mt-3">
            <span class="font-medium text-blue-800">Mục tiêu:</span>
            <p class="text-blue-700 mt-1">{{ $request->objective }}</p>
        </div>
    </div>

    <!-- Configuration Form -->
    <form action="{{ route('chair.conferences.store', $request->request_id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Basic Conference Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Thông tin cơ bản</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="conference_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên chính thức hội thảo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="conference_name" 
                           name="conference_name" 
                           value="{{ old('conference_name', $request->title) }}"
                           required
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('conference_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="acronym" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên viết tắt <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="acronym" 
                           name="acronym" 
                           value="{{ old('acronym') }}"
                           required
                           maxlength="50"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="VD: AICIT 2025">
                    @error('acronym')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Năm <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="year" 
                           name="year" 
                           value="{{ old('year', date('Y')) }}"
                           required
                           min="{{ date('Y') }}"
                           max="{{ date('Y') + 5 }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Địa điểm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location') }}"
                           required
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="VD: Đại học Công nghiệp Thực phẩm TP.HCM">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ old('start_date', $request->expected_date ? date('Y-m-d', strtotime($request->expected_date)) : '') }}"
                           required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           @change="updateEndDateMin"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày kết thúc <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ old('end_date', $request->expected_date ? date('Y-m-d', strtotime($request->expected_date)) : '') }}"
                           required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                        Từ khóa
                    </label>
                    <input type="text" 
                           id="keywords" 
                           name="keywords" 
                           value="{{ old('keywords') }}"
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="VD: AI, Machine Learning, Food Technology">
                    @error('keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả ngắn gọn <span class="text-red-500">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          required
                          rows="3"
                          maxlength="500"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                          placeholder="Mô tả tóm tắt về hội thảo...">{{ old('description', $request->objective) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="detailed_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả chi tiết <span class="text-red-500">*</span>
                </label>
                <textarea id="detailed_description" 
                          name="detailed_description" 
                          required
                          rows="5"
                          maxlength="2000"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                          placeholder="Mô tả chi tiết về mục tiêu, nội dung, chương trình dự kiến của hội thảo...">{{ old('detailed_description') }}</textarea>
                @error('detailed_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="submission_guidelines" class="block text-sm font-medium text-gray-700 mb-2">
                        Hướng dẫn nộp bài
                    </label>
                    <textarea id="submission_guidelines" 
                              name="submission_guidelines" 
                              rows="4"
                              maxlength="1000"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Hướng dẫn về format, cách thức nộp bài, yêu cầu kỹ thuật...">{{ old('submission_guidelines') }}</textarea>
                    @error('submission_guidelines')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cfp_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Link Call for Papers (CFP)
                    </label>
                    <input type="url" 
                           id="cfp_url" 
                           name="cfp_url" 
                           value="{{ old('cfp_url') }}"
                           maxlength="500"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="https://...">
                    @error('cfp_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="cfp_file" class="block text-sm font-medium text-gray-700 mb-2">
                        File Call for Papers (PDF)
                    </label>
                    <input type="file" 
                           id="cfp_file" 
                           name="cfp_file" 
                           accept=".pdf"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Chỉ chấp nhận file PDF, tối đa 10MB</p>
                    @error('cfp_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Thông tin liên hệ</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email liên hệ <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="contact_email" 
                           name="contact_email" 
                           value="{{ old('contact_email', $request->chair_email) }}"
                           required
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Số điện thoại liên hệ
                    </label>
                    <input type="tel" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="{{ old('contact_phone', $request->chair_phone) }}"
                           maxlength="20"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="+84 9xx xxx xxx">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="chair_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên chủ tịch <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="chair_name" 
                           name="chair_name" 
                           value="{{ old('chair_name', $request->chair_fullname) }}"
                           required
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('chair_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="chair_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email chủ tịch <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="chair_email" 
                           name="chair_email" 
                           value="{{ old('chair_email', $request->chair_email) }}"
                           required
                           maxlength="255"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('chair_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Review Configuration -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Cấu hình đánh giá</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="reviewers_per_paper" class="block text-sm font-medium text-gray-700 mb-2">
                        Số reviewer mỗi bài <span class="text-red-500">*</span>
                    </label>
                    <select id="reviewers_per_paper" 
                            name="reviewers_per_paper" 
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Chọn số reviewer</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('reviewers_per_paper') == $i ? 'selected' : '' }}>
                                {{ $i }} reviewer{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                    @error('reviewers_per_paper')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="enable_coi_check" class="flex items-center">
                        <input type="checkbox" 
                               id="enable_coi_check" 
                               name="enable_coi_check" 
                               value="1"
                               {{ old('enable_coi_check') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Kích hoạt kiểm tra COI (Conflict of Interest)</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Tự động kiểm tra xung đột lợi ích giữa reviewer và tác giả</p>
                </div>
            </div>
        </div>

        <!-- Deadlines -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Lịch trình</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="deadline_submission" class="block text-sm font-medium text-gray-700 mb-2">
                        Hạn nộp bài <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="deadline_submission" 
                           name="deadline_submission" 
                           value="{{ old('deadline_submission') }}"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('deadline_submission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline_review" class="block text-sm font-medium text-gray-700 mb-2">
                        Hạn phản biện <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="deadline_review" 
                           name="deadline_review" 
                           value="{{ old('deadline_review') }}"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('deadline_review')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline_camera_ready" class="block text-sm font-medium text-gray-700 mb-2">
                        Hạn nộp bản cuối <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="deadline_camera_ready" 
                           name="deadline_camera_ready" 
                           value="{{ old('deadline_camera_ready') }}"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('deadline_camera_ready')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result_announcement_deadline" class="block text-sm font-medium text-gray-700 mb-2">
                        Hạn thông báo kết quả
                    </label>
                    <input type="datetime-local" 
                           id="result_announcement_deadline" 
                           name="result_announcement_deadline" 
                           value="{{ old('result_announcement_deadline') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('result_announcement_deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Lưu ý về thời hạn</p>
                        <p class="text-sm text-yellow-700 mt-1">Các deadline phải được thiết lập theo thứ tự: Nộp bài → Phản biện → Chỉnh sửa → Công bố kết quả</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner Upload -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Banner hội thảo (tùy chọn)</h3>
            
            <div>
                <label for="banner" class="block text-sm font-medium text-gray-700 mb-2">
                    Tải lên banner
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer" 
                     onclick="document.getElementById('banner').click()">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">Tải lên banner</span>
                            <span class="pl-1">hoặc kéo thả file vào đây</span>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF tối đa 2MB</p>
                        <p id="bannerFileName" class="text-sm text-green-600 font-medium hidden"></p>
                    </div>
                </div>
                <input id="banner" 
                       name="banner" 
                       type="file" 
                       accept="image/*"
                       class="hidden"
                       @change="handleBannerChange($event)">
                @error('banner')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Committees -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Tiểu ban</h3>
                <button type="button" 
                        @click="addCommittee()"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Thêm tiểu ban
                </button>
            </div>
            
            <div id="committeesContainer" class="space-y-4">
                <!-- Committees will be added here dynamically -->
            </div>
            
            <template x-if="committees.length === 0">
                <div class="text-center py-6 text-gray-500">
                    <p>Chưa có tiểu ban nào. Nhấn "Thêm tiểu ban" để tạo tiểu ban mới.</p>
                </div>
            </template>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 pt-6">
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Gửi cấu hình hội thảo
            </button>
            
            <a href="{{ route('chair.conferences.index') }}" 
               class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
function conferenceSetup() {
    return {
        committees: [],

        init() {
            // Don't add default committee automatically
            // User can add committees manually by clicking "Thêm tiểu ban"
        },

        addCommittee() {
            const id = Date.now();
            this.committees.push({ id });

            const container = document.getElementById('committeesContainer');
            const committeeDiv = document.createElement('div');
            committeeDiv.id = `committee-${id}`;
            committeeDiv.className = 'bg-gray-50 p-4 rounded-lg border';
            committeeDiv.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium text-gray-800">Tiểu ban ${this.committees.length}</h4>
                    <button type="button" onclick="removeCommittee(${id})" class="text-red-600 hover:text-red-800 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên tiểu ban *</label>
                        <input type="text" name="committees[${id}][name]" required 
                               placeholder="VD: Kỹ thuật phần mềm" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả (tùy chọn)</label>
                        <textarea name="committees[${id}][description]" rows="2"
                                  placeholder="Mô tả về lĩnh vực và phạm vi của tiểu ban..."
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
            `;
            container.appendChild(committeeDiv);
        },

        updateEndDateMin() {
            const startDate = document.getElementById('start_date').value;
            const endDateField = document.getElementById('end_date');
            if (startDate && endDateField) {
                endDateField.min = startDate;
            }
        },

        updateDeadlines() {
            const submissionDate = document.getElementById('deadline_submission').value;
            if (submissionDate) {
                const submission = new Date(submissionDate);
                
                // Set review deadline to 2 weeks after submission
                const reviewDate = new Date(submission);
                reviewDate.setDate(reviewDate.getDate() + 14);
                document.getElementById('deadline_review').value = reviewDate.toISOString().slice(0, 16);
                document.getElementById('deadline_review').setAttribute('min', submissionDate);
                
                // Set camera ready deadline to 1 week after review
                const cameraDate = new Date(reviewDate);
                cameraDate.setDate(cameraDate.getDate() + 7);
                document.getElementById('deadline_camera_ready').value = cameraDate.toISOString().slice(0, 16);
                document.getElementById('deadline_camera_ready').setAttribute('min', reviewDate.toISOString().slice(0, 16));
                
                // Set result announcement to 3 days after camera ready
                const resultDate = new Date(cameraDate);
                resultDate.setDate(resultDate.getDate() + 3);
                document.getElementById('result_announcement_deadline').value = resultDate.toISOString().slice(0, 16);
                document.getElementById('result_announcement_deadline').setAttribute('min', cameraDate.toISOString().slice(0, 16));
            }
        },

        handleBannerChange(event) {
            const file = event.target.files[0];
            const fileNameElement = document.getElementById('bannerFileName');
            
            if (file) {
                if (file.type.startsWith('image/')) {
                    fileNameElement.textContent = `File đã chọn: ${file.name}`;
                    fileNameElement.classList.remove('hidden');
                } else {
                    alert('Vui lòng chọn file hình ảnh');
                    event.target.value = '';
                    fileNameElement.classList.add('hidden');
                }
            }
        }
    }
}

// Global function for removing committees
function removeCommittee(id) {
    const element = document.getElementById(`committee-${id}`);
    if (element) {
        element.remove();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.Alpine) {
        const setup = conferenceSetup();
        setup.init();
        window.conferenceSetupController = setup;
    }
});
</script>
@endsection