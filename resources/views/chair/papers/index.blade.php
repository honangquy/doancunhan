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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tổng số bài báo</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $papers->total() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đang chờ duyệt</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đã chấp nhận</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $acceptedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Đã từ chối</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $rejectedCount ?? 0 }}</p>
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
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài báo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hội thảo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày nộp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($papers as $paper)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900 line-clamp-2">{{ $paper->title ?? 'N/A' }}</h4>
                                            @if($paper->keywords)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <span class="font-medium">Từ khóa:</span> {{ Str::limit($paper->keywords, 60) }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium">{{ $paper->author_name ?? 'N/A' }}</p>
                                            {{-- <p class="text-gray-500">{{ $paper->author_email ?? 'N/A' }}</p> --}}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ $paper->conference_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusClasses = [
                                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                'UNDER_REVIEW' => 'bg-blue-100 text-blue-800',
                                                'ACCEPTED' => 'bg-green-100 text-green-800',
                                                'REJECTED' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusLabels = [
                                                'PENDING' => 'Đang chờ',
                                                'UNDER_REVIEW' => 'Đang duyệt',
                                                'ACCEPTED' => 'Đã duyệt',
                                                'REJECTED' => 'Từ chối'
                                            ];
                                            $currentStatus = $paper->status_code ?? 'PENDING';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $paper->created_at ? \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button @click="viewPaperDetail({{ $paper->paper_id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium transition-colors">
                                                Xem
                                            </button>
                                            
                                            @if($currentStatus === 'PENDING')
                                                <button @click="assignReviewer({{ $paper->paper_id }})" 
                                                        class="text-green-600 hover:text-green-900 text-sm font-medium transition-colors">
                                                    Phân công
                                                </button>
                                            @endif
                                            
                                            @if(in_array($currentStatus, ['UNDER_REVIEW', 'PENDING']))
                                                <a href="{{ route('chair.papers.decision', $paper->paper_id) }}" 
                                                   class="text-orange-600 hover:text-orange-900 text-sm font-medium transition-colors">
                                                    Quyết định
                                                </a>
                                            @endif
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
