@extends('layouts.chair')

@section('title', 'Chi tiết bài báo - ' . $paper->title)

@section('page-title', 'Chi tiết bài báo')

@section('page-subtitle', $paper->title)

@section('content')
        <!-- Paper Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-sm font-medium text-gray-500">ID: #{{ $paper->paper_id }}</span>
                        @php
                            $statusConfig = [
                                'SUBMITTED' => ['label' => 'Đã nộp', 'class' => 'bg-blue-100 text-blue-800'],
                                'UNDER_REVIEW' => ['label' => 'Đang xét duyệt', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'REVIEWED' => ['label' => 'Đã xét duyệt', 'class' => 'bg-purple-100 text-purple-800'],
                                'ACCEPTED' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                'REJECTED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                            ];
                            $status = $statusConfig[$paper->status_code] ?? ['label' => $paper->status_name, 'class' => 'bg-gray-100 text-gray-800'];
                        @endphp
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $paper->title }}</h1>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Hội thảo:</span> {{ $paper->conference_name }}
                    </p>
                </div>
            </div>

            <!-- Paper Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Người nộp</div>
                    <div class="text-sm font-medium text-gray-900">{{ $paper->author_name }}</div>
                    <div class="text-xs text-gray-600">{{ $paper->author_email }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Ngày nộp</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Cập nhật cuối</div>
                    <div class="text-sm font-medium text-gray-900">
                        @if(isset($paper->updated_at) && $paper->updated_at)
                            {{ \Carbon\Carbon::parse($paper->updated_at)->format('d/m/Y H:i') }}
                        @else
                            {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Paper Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Nội dung bài báo
            </h2>
            
            <!-- Abstract -->
            @if($paper->abstract)
            <div class="mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-2">Tóm tắt</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $paper->abstract }}</p>
                </div>
            </div>
            @endif

            <!-- Keywords -->
            @if($paper->keywords)
            <div class="mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-2">Từ khóa</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $paper->keywords) as $keyword)
                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                        {{ trim($keyword) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Track Information -->
            @if(isset($paper->track_name) && $paper->track_name)
            <div class="mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-2">Tiểu ban</h3>
                <div class="inline-block px-3 py-1 text-sm font-medium bg-purple-100 text-purple-700 rounded">
                    {{ $paper->track_name }}
                </div>
            </div>
            @endif

            <!-- Conference Information -->
            <div class="mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-2">Thông tin hội thảo</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500">Tên hội thảo:</span>
                            <div class="text-sm font-medium text-gray-900">{{ $paper->conference_name }}</div>
                        </div>
                        @if(isset($paper->conference_acronym) && $paper->conference_acronym)
                        <div>
                            <span class="text-xs text-gray-500">Acronym:</span>
                            <div class="text-sm font-medium text-gray-900">{{ $paper->conference_acronym }}</div>
                        </div>
                        @endif
                        @if(isset($paper->conference_year) && $paper->conference_year)
                        <div>
                            <span class="text-xs text-gray-500">Năm:</span>
                            <div class="text-sm font-medium text-gray-900">{{ $paper->conference_year }}</div>
                        </div>
                        @endif
                        @if(isset($paper->author_organization) && $paper->author_organization)
                        <div>
                            <span class="text-xs text-gray-500">Tổ chức tác giả chính:</span>
                            <div class="text-sm font-medium text-gray-900">{{ $paper->author_organization }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Paper Versions Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Phiên bản bài báo
                </h3>
            @if((isset($versions) && $versions->count() > 0) || $paper->file_path)
            <div x-data="{ expandedVersions: false }">
                @if(isset($versions) && $versions->count() > 0)
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        Tổng số phiên bản: <strong>{{ $versions->count() }}</strong>
                    </span>
                    @if($versions->count() >= 2)
                    <button @click="expandedVersions = !expandedVersions" 
                            class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center space-x-1">
                        <span x-text="expandedVersions ? 'Thu gọn' : 'Xem tất cả'"></span>
                        <svg class="w-4 h-4 transition-transform" :class="expandedVersions ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach($versions as $index => $version)
                    <div class="bg-white rounded-lg border-2 {{ $index === 0 ? 'border-purple-300' : 'border-gray-200' }} overflow-hidden"
                         @if($index !== 0) x-show="expandedVersions" @endif
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">
                        
                        <!-- Version Header -->
                        <div class="px-4 py-3 {{ $index === 0 ? 'bg-purple-50' : 'bg-gray-50' }} border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 {{ $index === 0 ? 'bg-purple-100' : 'bg-blue-50' }} rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 {{ $index === 0 ? 'text-purple-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-bold text-gray-900">Phiên bản {{ $version->version_no }}</h4>
                                            @if($index === 0)
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-bold rounded-full">MỚI NHẤT</span>
                                            @endif
                                            @if($version->version_no > 1)
                                                <span class="px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-bold rounded-full">REVISION</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-600 mt-0.5">
                                            Nộp ngày: {{ \Carbon\Carbon::parse($version->submitted_at)->format('d/m/Y H:i') }}
                                            @if($version->note) • {{ $version->note }} @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('chair.papers.download', $paperId) }}?version={{ $version->version_no }}" 
                                   target="_blank"
                                   class="inline-flex items-center space-x-1 px-3 py-2 text-sm font-semibold text-white {{ $index === 0 ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700' }} rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Tải xuống</span>
                                </a>
                            </div>
                        </div>

                        <!-- Version Content -->
                        <div class="p-4 space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tiêu đề</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $paper->title }}</p>
                            </div>

                            <!-- Abstract -->
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tóm tắt</label>
                                <p class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $paper->abstract }}</p>
                            </div>

                            <!-- Keywords -->
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Từ khóa</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach(explode(',', $paper->keywords) as $keyword)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Authors -->
                            @if($authors->count() > 0)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Đồng tác giả ({{ $authors->count() }})</label>
                                <div class="mt-2 space-y-2">
                                    @foreach($authors as $author)
                                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded-lg">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($author->full_name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 flex items-center space-x-2">
                                                <span>{{ $author->full_name }}</span>
                                                @if($author->is_contact)
                                                    <span class="px-1.5 py-0.5 bg-green-100 text-green-800 text-xs rounded">Liên hệ chính</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $author->email }}
                                                @if($author->organization) • {{ $author->organization }} @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- File Info -->
                            <div class="pt-3 border-t border-gray-200">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-mono text-xs">{{ basename($version->file_path) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Fallback: Hiển thị file từ baibao nếu chưa có versions -->
                <h3 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    File bài báo
                </h3>
                <div class="p-4 bg-white rounded-lg border-2 border-blue-300 bg-blue-50">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <h4 class="text-base font-semibold text-gray-900">Bài báo gốc</h4>
                                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">PHIÊN BẢN ĐẦU</span>
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span><strong>Nộp ngày:</strong> {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-mono text-xs truncate"><strong>File:</strong> {{ basename($paper->file_path) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('chair.papers.download', $paperId) }}" 
                               target="_blank"
                               class="inline-flex items-center space-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Tải xuống</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Chưa có file bài báo</p>
                </div>
            @endif
            </div>

            <!-- Legacy File Attachment Section (fallback) -->
            @if(!isset($versions) || $versions->count() == 0)
            @if($paper->file_path)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-2">File đính kèm</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ basename($paper->file_path) }}</div>
                            <div class="text-xs text-gray-500">Bài báo chính thức</div>
                        </div>
                        <a href="{{ asset('storage/' . $paper->file_path) }}" 
                           target="_blank"
                           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Tải xuống
                        </a>
                    </div>
                </div>
            </div>
            @endif
            @endif

        <!-- Authors Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Tác giả ({{ $authors->count() }})
            </h2>
            <div class="space-y-3">
                @foreach($authors as $author)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <span class="text-orange-600 font-bold text-sm">{{ $author->author_order }}</span>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">{{ $author->full_name }}</span>
                                @if($author->is_contact)
                                <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded">
                                    Liên hệ chính
                                </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600">{{ $author->email }}</div>
                            @if($author->organization)
                            <div class="text-xs text-gray-500">{{ $author->organization }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Review Statistics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Thống kê phản biện
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">{{ $reviewStats['total'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Tổng số</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $reviewStats['completed'] }}</div>
                        <div class="text-xs text-green-700 mt-1">Hoàn thành</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $reviewStats['pending'] }}</div>
                        <div class="text-xs text-blue-700 mt-1">Chờ hoàn thành</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $reviewStats['accepted'] }}</div>
                        <div class="text-xs text-purple-700 mt-1">Đã chấp nhận</div>
                    </div>
                </div>

                <!-- Average Scores -->
                @if($averageScores)
                <div class="bg-orange-50 rounded-lg p-4">
                    <h3 class="font-semibold text-orange-900 mb-3">Điểm trung bình</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-orange-700">Tính mới:</span>
                            <span class="font-bold text-orange-900">{{ $averageScores['novelty'] }}/10</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-orange-700">Liên quan:</span>
                            <span class="font-bold text-orange-900">{{ $averageScores['relevance'] }}/10</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-orange-700">Kỹ thuật:</span>
                            <span class="font-bold text-orange-900">{{ $averageScores['technical_quality'] }}/10</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-orange-700">Trình bày:</span>
                            <span class="font-bold text-orange-900">{{ $averageScores['presentation'] }}/10</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-orange-700">Tài liệu tham khảo:</span>
                            <span class="font-bold text-orange-900">{{ $averageScores['references'] }}/10</span>
                        </div>
                        <div class="border-t border-orange-200 pt-2 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-orange-800">Tổng điểm:</span>
                                <span class="text-xl font-bold text-orange-900">{{ $averageScores['total'] }}/10</span>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-sm">Chưa có điểm trung bình</p>
                        <p class="text-xs">Cần hoàn thành ít nhất 1 review</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Final Decision Section -->
        @php
            $allReviewsCompleted = $reviewStats['completed'] > 0 && $reviewStats['pending'] == 0;
            $hasDecision = !empty($paper->final_decision ?? null);
        @endphp
        
        @if($allReviewsCompleted || $hasDecision)
        <div class="bg-white rounded-lg shadow-sm border-2 {{ $hasDecision ? 'border-green-500' : 'border-orange-500' }} p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2 {{ $hasDecision ? 'text-green-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Quyết định cuối cùng
                    </h3>
                    
                    @if($hasDecision)
                        <div class="flex items-center space-x-4 mb-3">
                            @if($paper->decision === 'ACCEPT')
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-medium">
                                ✓ Đã chấp nhận
                            </span>
                            @elseif($paper->decision === 'REJECT')
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg font-medium">
                                ✗ Đã từ chối
                            </span>
                            @elseif($paper->decision === 'REVISE')
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg font-medium">
                                ↻ Yêu cầu sửa lại
                            </span>
                            @endif
                            
                            @if($paper->decision_date)
                            <span class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($paper->decision_date)->format('d/m/Y H:i') }}
                            </span>
                            @endif
                        </div>
                        
                        @if($paper->decision_comments)
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 mb-3">
                            <p class="font-medium text-gray-900 mb-2">Nhận xét:</p>
                            <p class="whitespace-pre-wrap">{{ $paper->decision_comments }}</p>
                        </div>
                        @endif
                        
                        @if($paper->decision === 'REVISE' && $paper->revision_deadline)
                        <p class="text-sm text-gray-600 mb-3">
                            📅 Deadline sửa lại: <span class="font-medium">{{ \Carbon\Carbon::parse($paper->revision_deadline)->format('d/m/Y') }}</span>
                        </p>
                        @endif
                        
                        <button onclick="if(window.Alpine && Alpine.$data(document.body).viewDecision) { Alpine.$data(document.body).viewDecision({{ $paper->paper_id }}); } else { window.location.href = '{{ route('chair.papers.decision', $paper->paper_id) }}'; }"
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                            Cập nhật quyết định
                        </button>
                    @else
                        <p class="text-gray-600 mb-4">
                            Tất cả nhận xét đã hoàn thành. Bạn có thể đưa ra quyết định cuối cùng cho bài báo này.
                        </p>
                        <button onclick="if(window.Alpine && Alpine.$data(document.body).viewDecision) { Alpine.$data(document.body).viewDecision({{ $paper->paper_id }}); } else { window.location.href = '{{ route('chair.papers.decision', $paper->paper_id) }}'; }"
                           class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Đưa ra quyết định cuối cùng
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @elseif($reviewStats['pending'] > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-6">
            <p class="text-yellow-800 font-medium">⏳ Chờ hoàn thành nhận xét</p>
            <p class="text-yellow-700 text-sm mt-1">
                Còn {{ $reviewStats['pending'] }} nhận xét chưa hoàn thành. Bạn cần chờ tất cả nhận xét hoàn thành trước khi đưa ra quyết định.
            </p>
        </div>
        @endif

        <!-- Review Assignments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Phân công phản biện ({{ $assignments->count() }})
                </span>
                <div class="flex space-x-2">
                    @if($reviewStats['completed'] > 0)
                    <button onclick="if(window.Alpine && Alpine.$data(document.body).viewReviews) { Alpine.$data(document.body).viewReviews({{ $paper->paper_id }}); } else { window.location.href = '{{ route('chair.papers.reviews', $paper->paper_id) }}'; }"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Xem tất cả nhận xét
                    </button>
                    @endif
                    <button onclick="if(window.Alpine && Alpine.$data(document.body).viewAssignReviewer) { Alpine.$data(document.body).viewAssignReviewer({{ $paper->paper_id }}); } else { window.location.href = '{{ route('chair.papers.assign', $paper->paper_id) }}'; }"
                       class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                        + Phân công thêm
                    </button>
                </div>
            </h2>

            @if($assignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phân công</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $assignment->reviewer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->reviewer_email }}</div>
                                @if($assignment->reviewer_org)
                                <div class="text-xs text-gray-400">{{ $assignment->reviewer_org }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $assignmentStatus = [
                                        'PENDING' => ['label' => 'Chờ xác nhận', 'class' => 'bg-blue-100 text-blue-800'],
                                        'ACCEPTED' => ['label' => 'Đã chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                        'DECLINED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                        'COMPLETED' => ['label' => 'Hoàn thành', 'class' => 'bg-purple-100 text-purple-800'],
                                    ];
                                    $status = $assignmentStatus[$assignment->status] ?? ['label' => $assignment->status, 'class' => 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($assignment->total_score)
                                    <span class="font-bold text-orange-600">{{ number_format($assignment->total_score, 1) }}/10</span>
                                @else
                                    <span class="text-gray-400">--</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($assignment->review_id)
                                <button onclick="viewReview({{ $assignment->assignment_id }}, {{ $assignment->review_id }})" 
                                        class="text-orange-600 hover:text-orange-700 font-medium focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1 rounded px-2 py-1 transition-colors">
                                    Xem review
                                </button>
                                @else
                                <span class="text-gray-400">Chưa có</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-sm font-medium">Chưa có reviewer nào được phân công</p>
                <button class="mt-4 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                    Phân công reviewer
                </button>
            </div>
            @endif
        </div>

        <!-- Completed Reviews -->
        @if($reviews->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Reviews hoàn thành ({{ $reviews->count() }})
            </h2>
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-medium text-gray-900">{{ $review->reviewer_name }}</div>
                            <div class="text-xs text-gray-500">
                                Nộp: {{ \Carbon\Carbon::parse($review->submitted_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($review->total_score, 1) }}/10</div>
                            @php
                                $recommendConfig = [
                                    'ACCEPT' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                    'STRONG_ACCEPT' => ['label' => 'Chấp nhận mạnh', 'class' => 'bg-green-100 text-green-800'],
                                    'WEAK_ACCEPT' => ['label' => 'Chấp nhận yếu', 'class' => 'bg-green-100 text-green-800'],
                                    'MINOR_REVISION' => ['label' => 'Sửa nhỏ', 'class' => 'bg-blue-100 text-blue-800'],
                                    'MAJOR_REVISION' => ['label' => 'Sửa lớn', 'class' => 'bg-yellow-100 text-yellow-800'],
                                    'WEAK_REJECT' => ['label' => 'Từ chối yếu', 'class' => 'bg-red-100 text-red-800'],
                                    'REJECT' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                    'STRONG_REJECT' => ['label' => 'Từ chối mạnh', 'class' => 'bg-red-100 text-red-800'],
                                ];
                                $recommend = $recommendConfig[$review->recommendation_code] ?? ['label' => $review->recommendation_code, 'class' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded {{ $recommend['class'] }}">
                                {{ $recommend['label'] }}
                            </span>
                        </div>
                    </div>
                    @if($review->detailed_comments)
                    <div class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                        <div class="font-medium text-gray-900 mb-1">Nhận xét chi tiết:</div>
                        {{ Str::limit($review->detailed_comments, 200) }}
                    </div>
                    @endif
                    @if($review->comment_author)
                    <div class="mt-3 p-3 bg-blue-50 rounded text-sm text-gray-700">
                        <div class="font-medium text-blue-900 mb-1">Nhận xét cho tác giả:</div>
                        {{ Str::limit($review->comment_author, 200) }}
                    </div>
                    @endif
                    <div class="mt-3 text-right">
                        <button onclick="viewReview({{ $review->assignment_id }}, {{ $review->review_id }})" 
                                class="text-orange-600 hover:text-orange-700 font-medium text-sm">
                            Xem chi tiết →
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
@endsection

@push('scripts')
<script>
// Helper function to extract filename from path
function getFileName(filePath) {
    if (!filePath) return '';
    return filePath.split('/').pop().split('\\').pop();
}

function viewReview(assignmentId, reviewId) {
    console.log('viewReview called with:', assignmentId, reviewId);
    
    // Tạo modal để hiển thị review details
    const modalHtml = `
        <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-bold text-gray-900">Chi tiết Review #${reviewId}</h3>
                    <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="reviewContent" class="mt-4">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600 mx-auto"></div>
                        <p class="mt-2 text-sm text-gray-600">Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Fetch review details
    fetch(`/chair/reviews/${reviewId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('reviewContent').innerHTML = `
                <div class="space-y-4">
                    <!-- Reviewer Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Thông tin Reviewer</h4>
                        <p class="text-sm"><strong>Tên:</strong> ${data.reviewer_name}</p>
                        <p class="text-sm"><strong>Email:</strong> ${data.reviewer_email}</p>
                        <p class="text-sm"><strong>Nộp lúc:</strong> ${new Date(data.submitted_at).toLocaleString('vi-VN')}</p>
                    </div>
                    
                    <!-- Scores -->
                    <div class="bg-orange-50 rounded-lg p-4">
                        <h4 class="font-semibold text-orange-900 mb-3">Điểm đánh giá</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex justify-between">
                                <span class="text-sm">Tính mới:</span>
                                <span class="font-bold">${data.score_novelty || '--'}/10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm">Liên quan:</span>
                                <span class="font-bold">${data.score_relevance || '--'}/10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm">Kỹ thuật:</span>
                                <span class="font-bold">${data.score_technical_quality || '--'}/10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm">Trình bày:</span>
                                <span class="font-bold">${data.score_presentation || '--'}/10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm">Tài liệu:</span>
                                <span class="font-bold">${data.score_references || '--'}/10</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="font-medium">Tổng điểm:</span>
                                <span class="text-lg font-bold text-orange-600">${data.total_score || '--'}/10</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendation -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Khuyến nghị</h4>
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded ${getRecommendationClass(data.recommendation_code)}">
                            ${getRecommendationLabel(data.recommendation_code)}
                        </span>
                    </div>
                    
                    <!-- Comments -->
                    ${data.detailed_comments ? `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Nhận xét chi tiết</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${data.detailed_comments}</p>
                    </div>
                    ` : ''}
                    
                    ${data.comment_author ? `
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-semibold text-green-900 mb-2">Nhận xét cho tác giả</h4>
                        <p class="text-sm text-green-800 whitespace-pre-wrap">${data.comment_author}</p>
                    </div>
                    ` : ''}
                    
                    ${data.comment_chair ? `
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h4 class="font-semibold text-purple-900 mb-2">Nhận xét riêng cho Chair</h4>
                        <p class="text-sm text-purple-800 whitespace-pre-wrap">${data.comment_chair}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Review File Attachment -->
                    ${data.review_file_path ? `
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h4 class="font-semibold text-indigo-900 mb-2">File phản biện bổ sung</h4>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-indigo-900">${getFileName(data.review_file_path)}</div>
                                <div class="text-xs text-indigo-700">File phản biện chi tiết</div>
                            </div>
                            <a href="/storage/${data.review_file_path}" 
                               target="_blank" 
                               class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 transition-colors">
                                Tải xuống
                            </a>
                        </div>
                    </div>
                    ` : `
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Không có file phản biện bổ sung</p>
                    </div>
                    `}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reviewContent').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Không thể tải chi tiết review</p>
                    <p class="text-sm mt-1">Error: ${error.message}</p>
                </div>
            `;
        });
}

function closeReviewModal() {
    const modal = document.getElementById('reviewModal');
    if (modal) {
        modal.remove();
    }
}

function getRecommendationClass(code) {
    const classes = {
        'ACCEPT': 'bg-green-100 text-green-800',
        'STRONG_ACCEPT': 'bg-green-100 text-green-800',
        'WEAK_ACCEPT': 'bg-green-100 text-green-800',
        'MINOR_REVISION': 'bg-blue-100 text-blue-800',
        'MAJOR_REVISION': 'bg-yellow-100 text-yellow-800',
        'WEAK_REJECT': 'bg-red-100 text-red-800',
        'REJECT': 'bg-red-100 text-red-800',
        'STRONG_REJECT': 'bg-red-100 text-red-800'
    };
    return classes[code] || 'bg-gray-100 text-gray-800';
}

function getRecommendationLabel(code) {
    const labels = {
        'ACCEPT': 'Chấp nhận',
        'STRONG_ACCEPT': 'Chấp nhận mạnh',
        'WEAK_ACCEPT': 'Chấp nhận yếu',
        'MINOR_REVISION': 'Sửa đổi nhỏ',
        'MAJOR_REVISION': 'Sửa đổi lớn', 
        'WEAK_REJECT': 'Từ chối yếu',
        'REJECT': 'Từ chối',
        'STRONG_REJECT': 'Từ chối mạnh'
    };
    return labels[code] || code;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('reviewModal');
    if (modal && event.target === modal) {
        closeReviewModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeReviewModal();
    }
});
</script>
@endpush
