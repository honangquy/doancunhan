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

                        <!-- Acronym & Year -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="acronym" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Tên viết tắt <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="acronym" 
                                    name="acronym" 
                                    required
                                    maxlength="50"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="VD: AICIT 2025"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="acronymError"></p>
                            </div>

                            <div>
                                <label for="year" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Năm <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="year" 
                                    name="year" 
                                    required
                                    min="{{ date('Y') }}"
                                    max="{{ date('Y') + 5 }}"
                                    value="{{ date('Y') }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="yearError"></p>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Ngày bắt đầu <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="start_date" 
                                    name="start_date" 
                                    required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="updateEndDateMin($event)"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="startDateError"></p>
                            </div>

                            <div>
                                <label for="end_date" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Ngày kết thúc <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="end_date" 
                                    name="end_date" 
                                    required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="endDateError"></p>
                            </div>
                        </div>

                        <!-- Location & Keywords -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="location" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Địa điểm <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    name="location" 
                                    required
                                    maxlength="255"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="VD: Đại học Công nghiệp Thực phẩm TP.HCM"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="locationError"></p>
                            </div>

                            <div>
                                <label for="keywords" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Từ khóa
                                </label>
                                <input 
                                    type="text" 
                                    id="keywords" 
                                    name="keywords" 
                                    maxlength="255"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="VD: AI, Machine Learning, Food Technology"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="keywordsError"></p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Mô tả ngắn gọn <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                required
                                rows="3"
                                maxlength="500"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mô tả tóm tắt về hội thảo..."
                            ></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-red-500 text-sm hidden" id="descriptionError"></p>
                                <p class="text-gray-500 text-xs" id="descriptionCount">0 / 500</p>
                            </div>
                        </div>

                        <!-- Detailed Description -->
                        <div>
                            <label for="detailed_description" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Mô tả chi tiết <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="detailed_description" 
                                name="detailed_description" 
                                required
                                rows="5"
                                maxlength="2000"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mô tả chi tiết về mục tiêu, nội dung, chương trình dự kiến của hội thảo..."
                            ></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-red-500 text-sm hidden" id="detailedDescriptionError"></p>
                                <p class="text-gray-500 text-xs" id="detailedDescriptionCount">0 / 2000</p>
                            </div>
                        </div>

                        <!-- Submission Guidelines -->
                        <div>
                            <label for="submission_guidelines" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Hướng dẫn nộp bài
                            </label>
                            <textarea 
                                id="submission_guidelines" 
                                name="submission_guidelines" 
                                rows="4"
                                maxlength="1000"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Hướng dẫn về format, cách thức nộp bài, yêu cầu kỹ thuật..."
                            ></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-red-500 text-sm hidden" id="submissionGuidelinesError"></p>
                                <p class="text-gray-500 text-xs" id="submissionGuidelinesCount">0 / 1000</p>
                            </div>
                        </div>

                        <!-- CFP URL -->
                        <div>
                            <label for="cfp_url" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                Link Call for Papers (CFP)
                            </label>
                            <input 
                                type="url" 
                                id="cfp_url" 
                                name="cfp_url" 
                                maxlength="500"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="https://..."
                            >
                            <p class="text-red-500 text-sm mt-1 hidden" id="cfpUrlError"></p>
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

                    <!-- Deadlines -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Thời hạn quan trọng</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="submission_deadline" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Hạn nộp bài <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="submission_deadline" 
                                    name="submission_deadline" 
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="submissionDeadlineError"></p>
                            </div>

                            <div>
                                <label for="review_deadline" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Hạn phản biện <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="review_deadline" 
                                    name="review_deadline" 
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="reviewDeadlineError"></p>
                            </div>

                            <div>
                                <label for="camera_ready_deadline" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Hạn nộp bản cuối <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="camera_ready_deadline" 
                                    name="camera_ready_deadline" 
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="cameraReadyDeadlineError"></p>
                            </div>

                            <div>
                                <label for="result_announcement_deadline" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Hạn thông báo kết quả
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="result_announcement_deadline" 
                                    name="result_announcement_deadline" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="resultAnnouncementDeadlineError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Cấu hình Hội thảo</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="reviewers_per_paper" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Số phản biện/bài <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="reviewers_per_paper" 
                                    name="reviewers_per_paper" 
                                    required
                                    min="2"
                                    max="5"
                                    value="3"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="reviewersPerPaperError"></p>
                            </div>

                            <div>
                                <label for="enable_coi_check" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Kiểm tra xung đột lợi ích
                                </label>
                                <select 
                                    id="enable_coi_check" 
                                    name="enable_coi_check" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="1" selected>Bật</option>
                                    <option value="0">Tắt</option>
                                </select>
                                <p class="text-red-500 text-sm mt-1 hidden" id="enableCoiCheckError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-200 pb-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Thông tin Liên hệ</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contact_email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email liên hệ <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="contact_email" 
                                    name="contact_email" 
                                    required
                                    maxlength="255"
                                    value="{{ auth()->user()->email ?? '' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="email@example.com"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="contactEmailError"></p>
                            </div>

                            <div>
                                <label for="contact_phone" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Số điện thoại liên hệ
                                </label>
                                <input 
                                    type="tel" 
                                    id="contact_phone" 
                                    name="contact_phone" 
                                    maxlength="20"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="+84 9xx xxx xxx"
                                >
                                <p class="text-red-500 text-sm mt-1 hidden" id="contactPhoneError"></p>
                            </div>
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

        updateEndDateMin(event) {
            const startDate = event.target.value;
            const endDateField = document.getElementById('end_date');
            if (startDate && endDateField) {
                endDateField.min = startDate;
            }
        },

        setupEventListeners() {
            // Character counters for text areas
            const textFields = [
                { id: 'description', countId: 'descriptionCount', max: 500 },
                { id: 'detailed_description', countId: 'detailedDescriptionCount', max: 2000 },
                { id: 'submission_guidelines', countId: 'submissionGuidelinesCount', max: 1000 }
            ];

            textFields.forEach(field => {
                const element = document.getElementById(field.id);
                const counter = document.getElementById(field.countId);
                if (element && counter) {
                    element.addEventListener('input', function() {
                        counter.textContent = `${this.value.length} / ${field.max}`;
                    });
                }
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