@extends('layouts.author')

@section('title', 'Bài báo của tôi')

@push('styles')
<style>
    .card {
        @apply bg-white rounded-xl shadow-lg border border-gray-200;
    }
    .stat-card {
        border-left: 4px solid;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bài báo của tôi</h1>
            <p class="text-gray-600 mt-1">Quản lý và theo dõi các bài báo đã nộp</p>
        </div>
        <a href="{{ route('author.papers.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nộp bài mới</span>
        </a>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-green-800 font-medium">{{ session('success') }}</p>
    </div>
</div>
@endif

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
    <!-- Total Papers -->
    <div class="card stat-card border-blue-500 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Tổng số</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Draft -->
    <div class="card stat-card border-gray-400 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Nháp</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['draft'] }}</p>
            </div>
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Submitted -->
    <div class="card stat-card border-blue-400 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Đã nộp</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['submitted'] }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Under Review -->
    <div class="card stat-card border-yellow-400 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Đang phản biện</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['under_review'] }}</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Accepted -->
    <div class="card stat-card border-green-500 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Chấp nhận</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['accepted'] }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="card stat-card border-red-500 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase">Từ chối</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Papers Table -->
<div class="card">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Danh sách bài báo</h2>
        
        @if($papers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">ID</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Tiêu đề</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Hội thảo</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Ngày nộp</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Trạng thái</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700 text-sm">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papers as $paper)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                            <span class="text-gray-600 font-mono text-sm">#{{ $paper->paper_id }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <a href="{{ route('author.papers.show', $paper->paper_id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                {{ Str::limit($paper->title, 60) }}
                            </a>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-gray-600 text-sm">{{ Str::limit($paper->conference_title, 30) }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-gray-600 text-sm">
                                {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            @php
                                $statusColors = [
                                    'DRAFT' => 'bg-gray-200 text-gray-800',
                                    'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                    'UNDER_REVIEW' => 'bg-yellow-100 text-yellow-800',
                                    'ACCEPTED' => 'bg-green-100 text-green-800',
                                    'REJECTED' => 'bg-red-100 text-red-800',
                                    'WITHDRAWN' => 'bg-gray-300 text-gray-600',
                                ];
                                $colorClass = $statusColors[$paper->status_code] ?? 'bg-gray-200 text-gray-800';
                            @endphp
                            <span class="badge {{ $colorClass }}">
                                {{ $paper->status_name }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('author.papers.show', $paper->paper_id) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition"
                                   title="Xem chi tiết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($paper->can_edit)
                                    <a href="{{ route('author.papers.edit', $paper->paper_id) }}" 
                                       class="text-orange-600 hover:text-orange-800 p-2 hover:bg-orange-50 rounded transition"
                                       title="Chỉnh sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                @else
                                    <span class="p-2 text-gray-400 cursor-not-allowed" 
                                          title="{{ $paper->edit_reason }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $papers->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Chưa có bài báo nào</h3>
            <p class="text-gray-500 mb-6">Bắt đầu bằng cách nộp bài báo đầu tiên của bạn</p>
            <a href="{{ route('author.papers.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Nộp bài mới</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection