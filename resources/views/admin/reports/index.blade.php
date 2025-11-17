@extends('layouts.admin')

@section('title', 'Báo cáo & Thống kê')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center space-x-3">
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-3 rounded-xl shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Báo cáo & Thống kê</h1>
            <p class="text-sm text-gray-600">Tổng quan hệ thống quản lý hội thảo khoa học HUIT</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Năm</label>
            <select id="filterYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="">Tất cả năm</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Khoa</label>
            <select id="filterFaculty" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="">Tất cả khoa</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->faculty_id }}" {{ $selectedFaculty == $faculty->faculty_id ? 'selected' : '' }}>
                        {{ $faculty->faculty_name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Cấp hội thảo</label>
            <select id="filterLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="">Tất cả cấp</option>
                @foreach($levels as $lv)
                    <option value="{{ $lv }}" {{ $selectedLevel == $lv ? 'selected' : '' }}>{{ $lv }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-end">
            <button onclick="applyFilters()" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Lọc</span>
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-purple-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Tổng người dùng</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Admin, chair, reviewer, tác giả</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-green-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Tổng hội thảo</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalConferences) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Các khoa, cấp trường và chuyên đề</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-blue-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Tổng bài báo</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalPapers) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Đã nộp trong các hội thảo</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-yellow-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Tổng lượt đánh giá</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalReviews) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Review, nhận xét, phản hồi</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-teal-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Bài đang phản biện</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($papersUnderReview) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Chưa nhận đủ 3 phản biện</p>
                </div>
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-green-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Bài đã chấp nhận</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($papersAccepted) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Đủ tiêu chuẩn để lên kỷ yếu</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-indigo-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Reviewer hoạt động</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($activeReviewers) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Có ít nhất 1 bài đang review</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-l-4 border-orange-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Kỷ yếu xuất bản</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($publishedProceedings) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Có ISBN/ISSN và file PDF</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Hội thảo theo năm
        </h3>
        <div style="height:280px"><canvas id="conferencesByYearChart"></canvas></div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
            </svg>
            Bài báo theo tiểu ban
        </h3>
        <div style="height:280px"><canvas id="papersByTrackChart"></canvas></div>
    </div>
</div>

<!-- Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Top Reviewer
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reviewer</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Số bài</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topReviewers as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ $item->reviewer ? strtoupper(substr($item->reviewer->full_name, 0, 1)) : 'N' }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->reviewer->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->reviewer->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $item->review_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500">Chưa có dữ liệu reviewer</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Top Tác giả
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tác giả</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Số bài</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topAuthors as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ $item->submitter ? strtoupper(substr($item->submitter->full_name, 0, 1)) : 'N' }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->submitter->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->submitter->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $item->paper_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500">Chưa có dữ liệu tác giả</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Hội thảo gần đây
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tên hội thảo</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Năm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentConferences as $conf)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $conf->title }}</div>
                            <div class="text-xs text-gray-500">Cấp: {{ $conf->level_code }} • Chair: {{ $conf->chair->full_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                {{ $conf->year }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500">Chưa có dữ liệu hội thảo</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Kỷ yếu gần đây
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hội thảo</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentProceedings as $proc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $proc->title }}</div>
                            <div class="text-xs text-gray-500">Năm {{ $proc->year }} • {{ $proc->level_code }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded bg-orange-100 text-orange-800">
                                {{ $proc->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500">Chưa có kỷ yếu nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartColors = {
    primary: '#667eea',
    success: '#10b981',
    info: '#3b82f6',
    warning: '#f59e0b',
    danger: '#ef4444',
    purple: '#8b5cf6',
    teal: '#14b8a6',
    orange: '#f97316',
    pink: '#ec4899',
    indigo: '#6366f1',
    cyan: '#06b6d4',
    lime: '#84cc16'
};

// Bar Chart - Conferences by Year
const ctx1 = document.getElementById('conferencesByYearChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: @json($yearsData),
        datasets: [{
            label: 'Số hội thảo',
            data: @json($conferenceCountsData),
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#f3f4f6'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Doughnut Chart - Papers by Track
const ctx2 = document.getElementById('papersByTrackChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: @json($trackNames),
        datasets: [{
            data: @json($trackCounts),
            backgroundColor: [
                chartColors.primary,
                chartColors.info,
                chartColors.success,
                chartColors.warning,
                chartColors.orange,
                chartColors.pink,
                chartColors.purple,
                chartColors.teal,
                chartColors.cyan,
                chartColors.lime
            ],
            borderWidth: 0,
            cutout: '65%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    boxWidth: 12,
                    padding: 15,
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Filter function
function applyFilters() {
    const year = document.getElementById('filterYear').value;
    const faculty = document.getElementById('filterFaculty').value;
    const level = document.getElementById('filterLevel').value;
    
    const params = new URLSearchParams();
    if (year) params.append('year', year);
    if (faculty) params.append('faculty', faculty);
    if (level) params.append('level', level);
    
    window.location.href = '{{ route("admin.reports.index") }}?' + params.toString();
}
</script>
@endsection
