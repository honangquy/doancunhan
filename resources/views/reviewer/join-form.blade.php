@extends('layouts.app')

@section('title', 'Khai báo thông tin Reviewer')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Khai báo thông tin Reviewer</h1>
            <p class="mt-2 text-gray-600">Vui lòng cập nhật thông tin để hoàn tất đăng ký</p>
        </div>

        <!-- Conference Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Thông tin hội thảo</h3>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="font-medium text-orange-900">{{ $invitation->conference_title }}</p>
                <p class="text-sm text-orange-700 mt-1">Bạn được mời làm phản biện viên cho hội thảo này</p>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Thông tin cơ bản</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email được mời</label>
                    <input type="text" value="{{ $user->email }}" disabled 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" value="{{ $user->full_name }}" disabled 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">⚠️ Email và tên không thể chỉnh sửa - được lấy từ hồ sơ của bạn</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">📋 Thông tin chuyên môn</h3>
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-red-800">Có lỗi xảy ra:</h4>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                        </svg>
                        <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('reviewer.join.submit') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Organization -->
                <div>
                    <label for="organization" class="block text-sm font-medium text-gray-700 mb-2">
                        Tổ chức/Trường đại học <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="organization" name="organization" required
                           value="{{ old('organization', $user->organization) }}"
                           placeholder="VD: Đại học Công nghiệp Hà Nội"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Chức vụ/Vị trí <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="position" name="position" required
                           value="{{ old('position', $user->position) }}"
                           placeholder="VD: Giảng viên, Phó Giáo sư, Giáo sư..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Experience Years -->
                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">
                        Số năm kinh nghiệm <span class="text-red-500">*</span>
                    </label>
                    <select id="experience_years" name="experience_years" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">-- Chọn số năm kinh nghiệm --</option>
                        @for($i = 0; $i <= 50; $i++)
                            <option value="{{ $i }}" {{ old('experience_years', $user->experience_years) == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 0 ? 'năm (Mới bắt đầu)' : ($i == 1 ? 'năm' : 'năm') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Specialization -->
                <div>
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                        Chuyên môn/Lĩnh vực nghiên cứu <span class="text-red-500">*</span>
                    </label>
                    <textarea id="specialization" name="specialization" rows="3" required
                              placeholder="VD: Trí tuệ nhân tạo, Học máy, Xử lý ngôn ngữ tự nhiên..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('specialization', $user->specialization) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Mô tả ngắn gọn các lĩnh vực chuyên môn của bạn</p>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Tiểu sử nghề nghiệp (tùy chọn)
                    </label>
                    <textarea id="bio" name="bio" rows="4"
                              placeholder="Mô tả ngắn gọn về quá trình học tập, công tác và thành tích nghiên cứu..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('bio', $user->bio) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Thông tin này sẽ giúp ban tổ chức hiểu rõ hơn về bạn</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-orange-600 hover:to-orange-700 focus:ring-4 focus:ring-orange-200 transition duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Gửi yêu cầu và hoàn tất đăng ký
                    </button>
                </div>
            </form>

            <!-- Note -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>
                    </svg>
                    <div class="ml-3 text-sm text-blue-700">
                        <p class="font-medium">Lưu ý quan trọng:</p>
                        <ul class="mt-2 list-disc list-inside">
                            <li>Email được mời phải trùng với email mà Chair đã gửi</li>
                            <li>Thông tin họ tên được lấy từ hồ sơ và không thể chỉnh sửa</li>
                            <li>Sau khi gửi yêu cầu, bạn sẽ trở thành phản biện viên của hội thảo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection