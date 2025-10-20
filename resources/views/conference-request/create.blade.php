@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 transform scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-semibold text-gray-800 mb-3">Tạo Yêu cầu Hội thảo</h1>
                <p class="text-gray-600 text-base">Điền thông tin chi tiết để gửi yêu cầu tổ chức hội thảo khoa học</p>
            </div>

            <!-- Success Message -->
            <div id="successMessage" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-green-800 font-medium ml-3" id="successText"></p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-red-800 font-medium ml-3" id="errorText"></p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <form id="conferenceRequestForm" class="p-8 space-y-8" x-data="conferenceRequestForm()" x-init="init()">
                    @csrf

                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Thông tin Cơ bản</h2>
                        </div>

                        <!-- Conference Title -->
                        <div>
                            <label for="title" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"></path>
                                </svg>
                                Tên Hội thảo <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                required
                                maxlength="255"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="VD: Hội thảo Trí tuệ nhân tạo 2025"
                            >
                            <p class="text-red-500 text-sm mt-1 hidden" id="titleError"></p>
                        </div>

                        <!-- Field & Level -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="level_code" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Cấp độ <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="level_code" 
                                    name="level_code" 
                                    required
                                    @change="handleLevelChange($event)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Chọn cấp độ</option>
                                    <option value="KHOA">Cấp Khoa</option>
                                    <option value="TRUONG">Cấp Trường</option>
                                </select>
                                <p class="text-red-500 text-sm mt-1 hidden" id="levelCodeError"></p>
                            </div>

                            <div>
                                <label for="field" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Lĩnh vực <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="field" 
                                    name="field" 
                                    required
                                    maxlength="255"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="VD: Công nghệ thông tin, Kinh tế..."
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="fieldError"></p>
                            </div>
                        </div>

                        <!-- Faculty Selection (Only for KHOA level) -->
                        <div x-show="selectedLevel === 'KHOA'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95">
                            <div>
                                <label for="faculty_name" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Khoa <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="faculty_name" 
                                    name="faculty_name" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :required="selectedLevel === 'KHOA'"
                                >
                                    <option value="">Chọn khoa</option>
                                    <option value="Công nghệ thực phẩm">Công nghệ thực phẩm</option>
                                    <option value="Đảm bảo chất lượng và an toàn thực phẩm">Đảm bảo chất lượng và an toàn thực phẩm</option>
                                    <option value="Công nghệ chế biến thuỷ sản">Công nghệ chế biến thuỷ sản</option>
                                    <option value="Kế toán">Kế toán</option>
                                    <option value="Tài chính - Ngân hàng">Tài chính - Ngân hàng</option>
                                    <option value="Quản trị kinh doanh">Quản trị kinh doanh</option>
                                    <option value="Kinh doanh quốc tế">Kinh doanh quốc tế</option>
                                    <option value="Luật kinh tế">Luật kinh tế</option>
                                    <option value="Khoa học dinh dưỡng và ẩm thực">Khoa học dinh dưỡng và ẩm thực</option>
                                    <option value="Khoa học chế biến món ăn">Khoa học chế biến món ăn</option>
                                    <option value="Quản trị dịch vụ du lịch và lữ hành">Quản trị dịch vụ du lịch và lữ hành</option>
                                    <option value="Quản trị nhà hàng và dịch vụ ăn uống">Quản trị nhà hàng và dịch vụ ăn uống</option>
                                    <option value="Quản trị khách sạn">Quản trị khách sạn</option>
                                    <option value="Ngôn ngữ Anh">Ngôn ngữ Anh</option>
                                    <option value="Ngôn ngữ Trung Quốc">Ngôn ngữ Trung Quốc</option>
                                    <option value="Công nghệ thông tin">Công nghệ thông tin</option>
                                    <option value="An toàn thông tin">An toàn thông tin</option>
                                    <option value="Công nghệ chế tạo máy">Công nghệ chế tạo máy</option>
                                    <option value="Công nghệ kỹ thuật điện, điện tử">Công nghệ kỹ thuật điện, điện tử</option>
                                    <option value="Công nghệ kỹ thuật cơ điện tử">Công nghệ kỹ thuật cơ điện tử</option>
                                    <option value="Công nghệ kỹ thuật điều khiển và tự động hóa">Công nghệ kỹ thuật điều khiển và tự động hóa</option>
                                    <option value="Công nghệ kỹ thuật hoá học">Công nghệ kỹ thuật hoá học</option>
                                    <option value="Công nghệ vật liệu">Công nghệ vật liệu</option>
                                    <option value="Công nghệ dệt, may">Công nghệ dệt, may</option>
                                    <option value="Công nghệ kỹ thuật môi trường">Công nghệ kỹ thuật môi trường</option>
                                    <option value="Quản lý tài nguyên và môi trường">Quản lý tài nguyên và môi trường</option>
                                    <option value="Công nghệ sinh học">Công nghệ sinh học</option>
                                    <option value="Kinh doanh thời trang và dệt may">Kinh doanh thời trang và dệt may</option>
                                    <option value="Quản trị kinh doanh thực phẩm">Quản trị kinh doanh thực phẩm</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Kỹ thuật nhiệt">Kỹ thuật nhiệt</option>
                                    <option value="Công nghệ tài chính">Công nghệ tài chính</option>
                                    <option value="Khoa học dữ liệu">Khoa học dữ liệu</option>
                                    <option value="Thương mại điện tử">Thương mại điện tử</option>
                                </select>
                                <p class="text-red-500 text-sm mt-1 hidden" id="facultyNameError"></p>
                            </div>
                        </div>

                        <!-- Expected Date & Facility -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="expected_date" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Ngày dự kiến <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="expected_date" 
                                    name="expected_date" 
                                    required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="expectedDateError"></p>
                            </div>
                        </div>

                        <!-- Objective -->
                        <div>
                            <label for="objective" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Mục tiêu Hội thảo <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="objective" 
                                name="objective" 
                                required
                                rows="4"
                                maxlength="500"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mô tả mục tiêu và nội dung chính của hội thảo..."
                            ></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-red-500 text-sm hidden" id="objectiveError"></p>
                                <p class="text-gray-500 text-xs" id="objectiveCount">0 / 500</p>
                            </div>
                        </div>

                        <!-- Affiliation -->
                        <div>
                            <label for="affiliation" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Đơn vị công tác (tùy chọn)
                            </label>
                            <input 
                                type="text" 
                                id="affiliation" 
                                name="affiliation" 
                                maxlength="255"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="VD: Khoa Công nghệ thông tin"
                            >
                        </div>
                    </div>

                    <!-- Chair Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Thông tin Chủ tịch Hội thảo</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="chair_fullname" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="chair_fullname" 
                                    name="chair_fullname" 
                                    required
                                    readonly
                                    maxlength="255"
                                    value="{{ auth()->user()->name ?? '' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                    placeholder="Từ hồ sơ người dùng"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="chairFullnameError"></p>
                            </div>

                            <div>
                                <label for="chair_email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="chair_email" 
                                    name="chair_email" 
                                    required
                                    readonly
                                    maxlength="255"
                                    value="{{ auth()->user()->email ?? '' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                    placeholder="Từ hồ sơ người dùng"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="chairEmailError"></p>
                            </div>
                        </div>

                        <div>
                            <label for="chair_phone" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Số điện thoại (tùy chọn)
                            </label>
                            <input 
                                type="tel" 
                                id="chair_phone" 
                                name="chair_phone" 
                                maxlength="20"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="+84 9xx xxx xxx"
                            >
                        </div>
                    </div>

                    <!-- Co-chairs -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Đồng chủ tịch (Co-chairs)</h2>
                            </div>
                            <button 
                                type="button" 
                                @click="addCoChair()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-sm rounded-lg transition-colors flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Thêm đồng chủ tịch</span>
                            </button>
                        </div>

                        <div id="coChairsContainer" class="space-y-4">
                            <!-- Co-chairs will be added here dynamically -->
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Tài liệu Đề xuất</h2>
                        </div>

                        <div>
                            <label for="proposal_file" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                File đề xuất (PDF) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors cursor-pointer" 
                                 onclick="document.getElementById('proposal_file').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">Tải lên file PDF</span>
                                        <span class="pl-1">hoặc kéo thả file vào đây</span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Chỉ chấp nhận file PDF, tối đa 10MB
                                    </p>
                                    <p id="fileName" class="text-sm text-green-600 font-medium hidden"></p>
                                </div>
                            </div>
                            <input 
                                id="proposal_file" 
                                name="proposal_file" 
                                type="file" 
                                accept=".pdf"
                                required
                                class="hidden"
                                @change="handleFileChange($event)"
                            >
                            <p class="text-red-500 text-sm mt-1 hidden" id="proposalFileError"></p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 text-sm rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span id="submitBtnText">Gửi Yêu cầu</span>
                        </button>
                        <a 
                            href="{{ route('home') }}" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-3 px-6 text-sm rounded-lg transition-colors text-center flex items-center justify-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Hủy</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function conferenceRequestForm() {
    return {
        coChairs: [],
        selectedLevel: '',
        isSubmitting: false,

        init() {
            // expose controller for debugging and prevent duplicate initializations
            window.formController = this;
            this.setupEventListeners();
        },

        handleLevelChange(event) {
            this.selectedLevel = event.target.value;
            
            // Clear field and faculty values when level changes
            const fieldInput = document.getElementById('field');
            const facultySelect = document.getElementById('faculty_name');
            
            if (fieldInput) fieldInput.value = '';
            if (facultySelect) facultySelect.value = '';
        },

        setupEventListeners() {
            // Character counter for objective
            const objectiveField = document.getElementById('objective');
            objectiveField.addEventListener('input', function() {
                document.getElementById('objectiveCount').textContent = `${this.value.length} / 500`;
            });

            // Form submission
            const form = document.getElementById('conferenceRequestForm');
            // Avoid attaching multiple submit handlers if init() runs more than once
            try {
                if (!form.dataset.submitListenerAttached) {
                    form.addEventListener('submit', (e) => this.submitForm(e));
                    form.dataset.submitListenerAttached = '1';
                }
            } catch (err) {
                // fallback: attach directly
                form.addEventListener('submit', (e) => this.submitForm(e));
            }
        },

        addCoChair() {
            const id = Date.now();
            this.coChairs.push({ id });

            const container = document.getElementById('coChairsContainer');
            const coChairDiv = document.createElement('div');
            coChairDiv.id = `cochair-${id}`;
            coChairDiv.className = 'bg-gray-50 p-4 rounded-lg border';
            coChairDiv.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <h4 class="font-medium text-gray-800 text-sm">Đồng chủ tịch ${this.coChairs.length}</h4>
                    </div>
                    <button type="button" onclick="removeCoChair(${id})" class="text-red-600 hover:text-red-800 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="cochair_name_${id}" placeholder="Họ và tên" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="email" name="cochair_email_${id}" placeholder="Email" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="text" name="cochair_affiliation_${id}" placeholder="Đơn vị (tùy chọn)" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            `;
            container.appendChild(coChairDiv);
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            const fileNameElement = document.getElementById('fileName');
            
            if (file) {
                if (file.type === 'application/pdf') {
                    fileNameElement.textContent = `File đã chọn: ${file.name}`;
                    fileNameElement.classList.remove('hidden');
                } else {
                    alert('Vui lòng chọn file PDF');
                    event.target.value = '';
                    fileNameElement.classList.add('hidden');
                }
            }
        },

        async submitForm(e) {
            e.preventDefault();

            // Prevent duplicate/rapid submits from the frontend
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            const formData = new FormData(document.getElementById('conferenceRequestForm'));
            
            // Collect co-chairs
            const coChairsData = [];
            this.coChairs.forEach(coChair => {
                const name = formData.get(`cochair_name_${coChair.id}`);
                const email = formData.get(`cochair_email_${coChair.id}`);
                const affiliation = formData.get(`cochair_affiliation_${coChair.id}`);
                
                if (name && email) {
                    coChairsData.push({ fullname: name, email, affiliation });
                }
            });

            formData.append('co_chairs', JSON.stringify(coChairsData));

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            submitBtnText.textContent = 'Đang gửi...';

            try {
                const response = await fetch('/submit-conference-request', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccess(`Yêu cầu tạo hội thảo đã được gửi thành công! Mã yêu cầu: ${result.request_id}`);
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 3000);
                } else {
                    if (result.errors) {
                        this.displayValidationErrors(result.errors);
                    }
                    this.showError(result.message || 'Có lỗi xảy ra khi gửi yêu cầu');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showError('Lỗi kết nối. Vui lòng thử lại sau.');
            } finally {
                // restore button state and allow future submits
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                submitBtnText.textContent = 'Gửi Yêu cầu';
                this.isSubmitting = false;
            }
        },

        displayValidationErrors(errors) {
            // Clear previous errors
            document.querySelectorAll('[id$="Error"]').forEach(el => el.classList.add('hidden'));

            // Display field-specific errors
            Object.keys(errors).forEach(field => {
                let errorElement = document.getElementById(`${field}Error`);
                if (!errorElement) {
                    // Handle some field name variations
                    const fieldMap = {
                        'chair_fullname': 'chairFullnameError',
                        'chair_email': 'chairEmailError',
                        'level_code': 'levelCodeError',
                        'expected_date': 'expectedDateError',
                        'faculty_name': 'facultyNameError',
                        'proposal_file': 'proposalFileError'
                    };
                    errorElement = document.getElementById(fieldMap[field]);
                }
                
                if (errorElement) {
                    errorElement.textContent = errors[field][0];
                    errorElement.classList.remove('hidden');
                }
            });
        },

        showSuccess(message) {
            const msgDiv = document.getElementById('successMessage');
            document.getElementById('successText').textContent = message;
            msgDiv.classList.remove('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
            window.scrollTo(0, 0);
        },

        showError(message) {
            const msgDiv = document.getElementById('errorMessage');
            document.getElementById('errorText').textContent = message;
            msgDiv.classList.remove('hidden');
            document.getElementById('successMessage').classList.add('hidden');
            window.scrollTo(0, 0);
        }
    }
}

// Global function for removing co-chairs
function removeCoChair(id) {
    const element = document.getElementById(`cochair-${id}`);
    if (element) {
        element.remove();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.Alpine) {
        // Alpine is already loaded
        const form = conferenceRequestForm();
        form.init();
        window.formController = form;
    }
});
</script>
@endsection