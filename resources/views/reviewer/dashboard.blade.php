@extends('layouts.reviewer')

@section('title', 'Dashboard')

@push('styles')
<link href="{{ asset('css/animations.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center space-x-3 mb-2">
        <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Quản lý các bài báo được phân công phản biện</p>
        </div>
    </div>
</div>

            <!-- Stats Cards -->
            <div class="grid md:grid-cols-4 gap-4 mb-6" x-data="{ animate: false }" x-init="setTimeout(() => animate = true, 100)">
                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 0ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Tổng phân công</div>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['pending'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Chờ chấp nhận</div>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 transform transition-all duration-500 hover:scale-105 hover:shadow-lg" 
                     :class="animate ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 200ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['completed'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Đã hoàn thành</div>
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
                     style="transition-delay: 300ms">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 mb-1">{{ $stats['in_progress'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Đang làm</div>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews -->
            <div class="bg-white rounded-xl shadow-lg mb-6 animate-slide-up">
                <div class="p-6 border-b flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Phân công của tôi</h2>
                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $assignments->count() }} bài</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paper ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hội thảo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">#{{ $assignment->paper_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-800">{{ Str::limit($assignment->paper_title, 45) }}</div>
                                    <div class="text-xs text-gray-500">Tác giả: {{ $assignment->author_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($assignment->conference_name, 20) }}</td>
                                <td class="px-6 py-4">
                                    @if($assignment->review_id)
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                            ✓ Đã review
                                        </span>
                                        @if($assignment->recommendation_name)
                                            <div class="text-xs text-gray-500 mt-1">{{ $assignment->recommendation_name }}</div>
                                        @endif
                                    @elseif($assignment->assignment_status == 'ACCEPTED')
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                            ⏳ Đang làm
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                            📨 Chờ chấp nhận
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($assignment->deadline)
                                        @php
                                            $deadline = \Carbon\Carbon::parse($assignment->deadline);
                                            $daysLeft = $deadline->diffInDays(now(), false);
                                            $colorClass = $daysLeft > 0 ? 'red' : ($daysLeft > -3 ? 'orange' : 'green');
                                        @endphp
                                        <div class="text-sm text-{{ $colorClass }}-600 font-medium">{{ $deadline->format('d/m/Y') }}</div>
                                        <div class="text-xs text-{{ $colorClass }}-500">
                                            @if($daysLeft > 0)
                                                Quá hạn {{ abs($daysLeft) }} ngày!
                                            @elseif($daysLeft == 0)
                                                Hôm nay!
                                            @else
                                                Còn {{ abs($daysLeft) }} ngày
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">Chưa có</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($assignment->review_id)
                                        <a href="{{ route('reviewer.reviews.show', $assignment->review_id) }}" class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                                            Xem review
                                        </a>
                                    @elseif($assignment->assignment_status == 'ACCEPTED')
                                        <a href="{{ route('reviewer.reviews.create', $assignment->assignment_id) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-4 py-2 rounded-lg font-medium transition-all inline-block">
                                            Review ngay
                                        </a>
                                    @elseif($assignment->assignment_status == 'INVITED')
                                        <a href="{{ route('reviewer.assignments.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg font-medium transition-all inline-block">
                                            Chấp nhận
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Chưa có phân công nào</p>
                                    <p class="text-xs mt-1">Bạn sẽ nhận được thông báo khi được phân công review bài báo</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Completed Reviews -->
                <div class="bg-white rounded-xl shadow-lg">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-bold text-gray-800">Reviews đã hoàn thành</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Paper #32 - Deep Learning Methods</div>
                                <div class="text-xs text-gray-500 mt-1">Recommendation: Accept</div>
                                <div class="text-xs text-gray-400 mt-1">Submitted 2 days ago</div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Paper #28 - Cloud Computing Security</div>
                                <div class="text-xs text-gray-500 mt-1">Recommendation: Minor Revision</div>
                                <div class="text-xs text-gray-400 mt-1">Submitted 5 days ago</div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Paper #21 - Neural Networks</div>
                                <div class="text-xs text-gray-500 mt-1">Recommendation: Accept</div>
                                <div class="text-xs text-gray-400 mt-1">Submitted 1 week ago</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t">
                        <a href="/reviewer/reviews" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Xem tất cả reviews →
                        </a>
                    </div>
                </div>

                <!-- Available for Bidding -->
                <div class="bg-white rounded-xl shadow-lg">
                    <div class="p-6 border-b flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800">Có thể bid</h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $availablePapers->count() }} bài</span>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($availablePapers as $paper)
                        <div class="border rounded-lg p-4 hover:border-purple-300 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ Str::limit($paper->title, 40) }}</div>
                                    <div class="text-xs text-gray-500">{{ $paper->conference_name }}</div>
                                    <div class="text-xs text-blue-600 mt-1">Paper #{{ $paper->paper_id }} • {{ $paper->status_code }}</div>
                                </div>
                                @if($paper->keywords)
                                    @php
                                        $keywords = explode(',', $paper->keywords);
                                        $firstKeyword = trim($keywords[0] ?? '');
                                    @endphp
                                    @if($firstKeyword)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ Str::limit($firstKeyword, 10) }}</span>
                                    @endif
                                @endif
                            </div>
                            <div class="flex space-x-2 mt-3">
                                <a href="{{ route('reviewer.bidding') }}" class="flex-1 bg-purple-100 hover:bg-purple-200 text-purple-800 text-xs px-3 py-2 rounded font-medium text-center">
                                    Bid ngay
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm font-medium">Chưa có bài báo nào để bid</p>
                            <p class="text-xs mt-1">Các bài báo mới sẽ xuất hiện ở đây ngay sau khi được nộp</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="p-4 border-t">
                        <a href="{{ route('reviewer.bidding') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Xem tất cả bài có thể bid →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger stagger animations
        setTimeout(() => {
            document.querySelectorAll('.stagger-item').forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('animate-fade-in-up');
                }, index * 100);
            });
        }, 300);
    });
</script>
@endpush

@endsection
