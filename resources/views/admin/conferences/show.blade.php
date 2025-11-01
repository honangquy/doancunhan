@extends('layouts.admin')

@section('title', 'Chi tiết Hội thảo')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Chi tiết hội thảo: {{ $conference->conference_name }}
                </h2>
                <div class="mt-2 flex items-center space-x-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $conference->status === 'PENDING_ADMIN_APPROVAL' ? 'bg-yellow-100 text-yellow-800' : 
                           ($conference->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ $conference->status === 'PENDING_ADMIN_APPROVAL' ? 'Chờ duyệt' : 
                           ($conference->status === 'ACTIVE' ? 'Đã duyệt' : 'Từ chối') }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $conference->level_code }}
                    </span>
                </div>
            </div>
            <div class="mt-4 flex-shrink-0 flex md:mt-0 md:ml-4">
                <a href="{{ route('admin.configured-conferences.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    ← Quay lại
                </a>
            </div>
        </div>

        <!-- Conference Details -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin cơ bản</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Thông tin tổng quan về hội thảo</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tên hội thảo</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->conference_name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tên rút gọn (Acronym)</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->acronym ?: 'Chưa xác định' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Năm tổ chức</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->year }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Thời gian tổ chức</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->start_date ? $conference->start_date->format('d/m/Y') : 'Chưa xác định' }} - 
                            {{ $conference->end_date ? $conference->end_date->format('d/m/Y') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Địa điểm</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->location ?: 'Chưa xác định' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Từ khóa</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->keywords ?: 'Chưa có từ khóa' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Mô tả ngắn</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->description ?: 'Chưa có mô tả' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Mô tả chi tiết</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="max-w-none">
                                {{ $conference->detailed_description ?: 'Chưa có mô tả chi tiết' }}
                            </div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Hướng dẫn nộp bài</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="max-w-none">
                                {{ $conference->submission_guidelines ?: 'Chưa có hướng dẫn' }}
                            </div>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Call for Papers (URL)</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conference->cfp_url)
                                <a href="{{ $conference->cfp_url }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    {{ $conference->cfp_url }}
                                </a>
                            @else
                                Chưa có URL
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Call for Papers (File PDF)</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conference->cfp_file_path)
                                <a href="{{ asset('storage/' . $conference->cfp_file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    📄 Xem file CFP
                                </a>
                            @else
                                Chưa có file CFP
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Chair Information -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin Chair & Liên hệ</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Thông tin người chủ trì và liên hệ</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tên Chair</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->chair_name ?: 'Chưa xác định' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Email Chair</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->chair_email ?: 'Chưa xác định' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Email liên hệ</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->contact_email ?: 'Chưa xác định' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Số điện thoại liên hệ</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->contact_phone ?: 'Chưa xác định' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Timeline & Deadlines -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Lịch trình & Deadline</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Các mốc thời gian quan trọng</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Deadline nộp bài</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->deadline_submission ? $conference->deadline_submission->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Deadline review</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->deadline_review ? $conference->deadline_review->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Deadline camera ready</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->deadline_camera_ready ? $conference->deadline_camera_ready->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Deadline công bố kết quả</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conference->result_announcement_deadline ? $conference->result_announcement_deadline->format('d/m/Y H:i') : 'Chưa xác định' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Configuration -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Cấu hình hệ thống</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Các thiết lập về review và hệ thống</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Số reviewer mỗi bài</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->reviewers_per_paper }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Kiểm tra COI (Conflict of Interest)</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conference->enable_coi_check ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $conference->enable_coi_check ? 'Có' : 'Không' }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Cấp độ</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conference->level_code }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Banner hội thảo</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conference->banner_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $conference->banner_path) }}" alt="Banner hội thảo" class="max-w-md h-auto rounded-lg shadow">
                                </div>
                            @else
                                Chưa có banner
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Committees -->
        @if($conference->committees && $conference->committees->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tiểu ban ({{ $conference->committees->count() }})</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Các tiểu ban được tạo cho hội thảo</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="px-4 py-5 sm:px-6">
                    <div class="space-y-4">
                        @foreach($conference->committees as $committee)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $committee->committee_name }}</h4>
                            @if($committee->description)
                                <p class="mt-1 text-sm text-gray-600">{{ $committee->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        @if($conference->status === 'PENDING_ADMIN_APPROVAL')
        <div class="mt-6 flex justify-end space-x-3">
            <form action="{{ route('admin.conference-requests.reject-conference', $conference->conference_id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        onclick="return confirm('Bạn có chắc chắn muốn từ chối hội thảo này?')">
                    Từ chối
                </button>
            </form>
            <form action="{{ route('admin.conference-requests.approve-conference', $conference->conference_id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Duyệt hội thảo
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection