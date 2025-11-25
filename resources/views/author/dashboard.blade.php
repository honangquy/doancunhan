@extends('layouts.author')

@section('title', 'Author Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard</h1>
    <p class="text-gray-600">Chào mừng trở lại! Đây là tổng quan về các bài báo của bạn.</p>
</div>

<!-- Stats Cards -->
<div class="grid md:grid-cols-4 gap-4 mb-6" x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)">
    <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
         :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         style="transition-delay: 0ms">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Tổng số bài</div>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
         :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         style="transition-delay: 100ms">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['under_review'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Đang phản biện</div>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
         :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         style="transition-delay: 200ms">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['accepted'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Đã chấp nhận</div>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
         :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         style="transition-delay: 250ms">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['published'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Đã xuất bản</div>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
         :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         style="transition-delay: 300ms">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['rejected'] ?? 0 }}</div>
                <div class="text-xs text-gray-600">Bị từ chối</div>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Papers -->
<div class="bg-white rounded-xl shadow-lg animate-slide-up">
    <div class="p-6 border-b flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Bài báo gần đây</h2>
        <a href="{{ route('author.papers.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg font-medium">
            + Nộp bài mới
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hội thảo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày nộp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($papers as $paper)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-800">
                            {{ $paper->title }}
                        </div>
                        <div class="text-xs text-gray-500">Paper #{{ $paper->paper_id }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($paper->conference_name, 30) }}</td>
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            @php
                                $statusClasses = [
                                    'ACCEPTED' => 'bg-green-100 text-green-800',
                                    'UNDER_REVIEW' => 'bg-yellow-100 text-yellow-800',
                                    'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                    'REVISION' => 'bg-orange-100 text-orange-800',
                                    'REJECTED' => 'bg-red-100 text-red-800',
                                ];
                                $class = $statusClasses[$paper->status_code] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold {{ $class }} rounded-full">
                                {{ $paper->status_name }}
                            </span>
                            
                            @if($paper->decision)
                                @if($paper->decision === 'ACCEPT')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Chấp nhận
                                    </span>
                                @elseif($paper->decision === 'REJECT')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Từ chối
                                    </span>
                                @elseif($paper->decision === 'REVISE')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Yêu cầu sửa
                                    </span>
                                @elseif($paper->decision === 'PUBLISHED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" clip-rule="evenodd"></path>
                                        </svg>
                                        Đã xuất bản
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('author.papers.show', $paper->paper_id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm font-medium">Chưa có bài báo nào</p>
                        <p class="text-xs mt-1">Bắt đầu bằng cách nộp bài báo đầu tiên của bạn</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t">
        <a href="{{ route('author.papers.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            Xem tất cả bài báo →
        </a>
    </div>
</div>
@endsection