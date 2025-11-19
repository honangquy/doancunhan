@extends('layouts.chair')

@section('title', 'Quản lý bài báo')

@section('page-title', 'Quản lý bài báo')

@section('page-subtitle', 'Xem và quản lý tất cả bài báo trong hội thảo')

@section('content')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('papersManager', () => ({
        currentView: 'papers-list',
        selectedPaperId: null,
        paperDetailData: null,
        assignReviewerData: null,
        loading: false,
        
        async viewPaperDetail(paperId) {
            this.selectedPaperId = paperId;
            this.currentView = 'paper-detail';
            this.loading = true;
            this.paperDetailData = null;
            
            try {
                const response = await fetch('/chair/papers/' + paperId + '/ajax');
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                
                const html = await response.text();
                this.paperDetailData = html;
                
            } catch (error) {
                this.paperDetailData = '<div class="p-6 text-center text-red-500">Không thể tải chi tiết bài báo</div>';
            } finally {
                this.loading = false;
            }
        },
        
        async assignReviewer(paperId) {
            this.selectedPaperId = paperId;
            this.currentView = 'assign-reviewer';
            this.loading = true;
            this.assignReviewerData = null;
            
            try {
                const response = await fetch('/chair/papers/' + paperId + '/assign');
                const html = await response.text();
                
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const mainContent = doc.querySelector('main');
                
                if (mainContent) {
                    this.assignReviewerData = mainContent.innerHTML;
                } else {
                    this.assignReviewerData = '<div class="p-6 text-center text-red-500">Không thể tải form phân công reviewer</div>';
                }
            } catch (error) {
                this.assignReviewerData = '<div class="p-6 text-center text-red-500">Không thể tải form phân công reviewer</div>';
            } finally {
                this.loading = false;
            }
        },
        
        backToList() {
            this.currentView = 'papers-list';
            this.selectedPaperId = null;
            this.paperDetailData = null;
            this.assignReviewerData = null;
        }
    }))
});
</script>

<div x-data="papersManager" class="space-y-6">

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button 
                @click="currentView = 'papers-list'"
                :class="currentView === 'papers-list' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                Danh sách bài báo
            </button>
            <button 
                @click="currentView = 'statistics'"
                :class="currentView === 'statistics' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                Thống kê
            </button>
        </nav>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-600"></div>
            <span class="text-gray-700">Đang tải...</span>
        </div>
    </div>

    <!-- Papers List View -->
    <div x-show="currentView === 'papers-list'" x-transition class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-200 hover:shadow-2xl" style="box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.5), 0 8px 10px -6px rgba(124, 58, 237, 0.5);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-purple-100 mb-1">Tổng số bài báo</p>
                        <p class="text-2xl font-bold">{{ $papers->total() ?? 0 }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-200 hover:shadow-2xl" style="box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.5), 0 8px 10px -6px rgba(251, 191, 36, 0.5);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-yellow-100 mb-1">Đang chờ duyệt</p>
                        <p class="text-2xl font-bold">{{ $pendingCount ?? 0 }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-200 hover:shadow-2xl" style="box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.5), 0 8px 10px -6px rgba(34, 197, 94, 0.5);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-green-100 mb-1">Đã chấp nhận</p>
                        <p class="text-2xl font-bold">{{ $acceptedCount ?? 0 }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-400 to-pink-600 rounded-lg shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-200 hover:shadow-2xl" style="box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.5), 0 8px 10px -6px rgba(239, 68, 68, 0.5);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-100 mb-1">Đã từ chối</p>
                        <p class="text-2xl font-bold">{{ $rejectedCount ?? 0 }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('chair.papers') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Tìm theo tiêu đề, tác giả, từ khóa..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                
                <div class="min-w-40">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Tất cả</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Đang chờ duyệt</option>
                        <option value="ACCEPTED" {{ request('status') === 'ACCEPTED' ? 'selected' : '' }}>Đã chấp nhận</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Đã từ chối</option>
                        <option value="UNDER_REVIEW" {{ request('status') === 'UNDER_REVIEW' ? 'selected' : '' }}>Đang phản biện</option>
                        <option value="PENDING_CHAIR_REVIEW" {{ request('status') === 'PENDING_CHAIR_REVIEW' ? 'selected' : '' }}>Chờ Chair duyệt lại</option>
                    </select>
                </div>
                
                <div class="min-w-40">
                    <label for="conference" class="block text-sm font-medium text-gray-700 mb-1">Hội thảo</label>
                    <select name="conference" id="conference" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Tất cả hội thảo</option>
                        @if(isset($conferences))
                            @foreach($conferences as $conference)
                                <option value="{{ $conference->conference_id }}" {{ request('conference') == $conference->conference_id ? 'selected' : '' }}>
                                    {{ $conference->title }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Tìm kiếm
                </button>
                
                @if(request()->hasAny(['search', 'status', 'conference']))
                    <a href="{{ route('chair.papers') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                        Xóa bộ lọc
                    </a>
                @endif
            </form>
        </div>

        <!-- Papers Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                @if(isset($papers) && $papers->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-purple-50 to-indigo-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Bài báo</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Tác giả</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Hội thảo</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Reviewers</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Trạng thái</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Ngày nộp</span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Thao tác</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($papers as $paper)
                                <tr class="hover:bg-purple-50 transition-colors">
                                    <td class="px-4 py-4 max-w-xs">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 truncate" title="{{ $paper->title ?? 'N/A' }}">
                                                {{ Str::limit($paper->title ?? 'N/A', 50) }}
                                            </h4>
                                            @if($paper->keywords)
                                                <p class="text-xs text-gray-500 mt-1 truncate" title="{{ $paper->keywords }}">
                                                    <span class="font-medium">Từ khóa:</span> {{ Str::limit($paper->keywords, 40) }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium truncate max-w-[150px]" title="{{ $paper->author_name ?? 'N/A' }}">
                                                {{ $paper->author_name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 max-w-xs">
                                        <span class="text-sm text-gray-900 truncate block" title="{{ $paper->conference_name ?? 'N/A' }}">
                                            {{ Str::limit($paper->conference_name ?? 'N/A', 30) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1.5">
                                            @if($paper->reviewers_assigned > 0)
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                    </svg>
                                                    <span class="text-gray-700 font-medium">{{ $paper->reviewers_assigned }} phân công</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-xs">
                                                    @if($paper->reviewers_accepted > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $paper->reviewers_accepted }}
                                                        </span>
                                                    @endif
                                                    @if($paper->reviewers_pending > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $paper->reviewers_pending }}
                                                        </span>
                                                    @endif
                                                    @if($paper->reviewers_declined > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $paper->reviewers_declined }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($paper->reviews_completed > 0)
                                                    <div class="flex items-center space-x-2 text-xs">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span class="text-gray-700">{{ $paper->reviews_completed }} đánh giá</span>
                                                        @if($paper->avg_score)
                                                            <span class="font-semibold text-purple-600">({{ $paper->avg_score }}/10)</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <div class="flex items-center space-x-1 text-xs text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                    </svg>
                                                    <span>Chưa phân công</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $statusClasses = [
                                                'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                                'UNDER_REVIEW' => 'bg-yellow-100 text-yellow-800',
                                                'REVIEWED' => 'bg-purple-100 text-purple-800',
                                                'PENDING_CHAIR_REVIEW' => 'bg-purple-100 text-purple-800',
                                                'ACCEPTED' => 'bg-green-100 text-green-800',
                                                'REJECTED' => 'bg-red-100 text-red-800',
                                                'WITHDRAWN' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusLabels = [
                                                'SUBMITTED' => 'Đã nộp',
                                                'UNDER_REVIEW' => 'Đang duyệt',
                                                'REVIEWED' => 'Đã phản biện',
                                                'PENDING_CHAIR_REVIEW' => 'Chờ Chair duyệt',
                                                'ACCEPTED' => 'Chấp nhận',
                                                'REJECTED' => 'Từ chối',
                                                'WITHDRAWN' => 'Đã rút'
                                            ];
                                            $currentStatus = $paper->status_code ?? 'SUBMITTED';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 font-medium">
                                        {{ $paper->created_at ? \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('chair.papers.show', $paper->paper_id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Xem
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    @if($papers->hasPages())
                        <div class="px-6 py-3 border-t border-gray-200">
                            {{ $papers->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có bài báo</h3>
                        <p class="mt-2 text-gray-500">Chưa có bài báo nào được nộp vào hội thảo.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics View -->
    <div x-show="currentView === 'statistics'" x-transition class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê tổng quan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Statistics content would go here -->
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $papers->total() ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Tổng số bài báo</p>
                </div>
                <!-- Add more statistics as needed -->
            </div>
        </div>
    </div>

    <!-- Paper Detail View -->
    <div x-show="currentView === 'paper-detail'" x-transition class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Chi tiết bài báo</h3>
            <button @click="backToList()" class="text-gray-500 hover:text-gray-700 transition-colors">
                ← Quay lại danh sách
            </button>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div x-html="paperDetailData"></div>
        </div>
    </div>

    <!-- Assign Reviewer View -->
    <div x-show="currentView === 'assign-reviewer'" x-transition class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Phân công reviewer</h3>
            <button @click="backToList()" class="text-gray-500 hover:text-gray-700 transition-colors">
                ← Quay lại danh sách
            </button>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div x-html="assignReviewerData"></div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
