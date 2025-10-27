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
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $reviewStats['total_assigned'] }}</div>
                    <div class="text-xs text-gray-600 mt-1">Tổng số</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $reviewStats['pending'] }}</div>
                    <div class="text-xs text-blue-700 mt-1">Chờ xác nhận</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $reviewStats['accepted'] }}</div>
                    <div class="text-xs text-green-700 mt-1">Đã chấp nhận</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $reviewStats['completed'] }}</div>
                    <div class="text-xs text-purple-700 mt-1">Hoàn thành</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ $reviewStats['avg_score'] ? number_format($reviewStats['avg_score'], 1) : '--' }}
                    </div>
                    <div class="text-xs text-orange-700 mt-1">Điểm TB</div>
                </div>
            </div>
        </div>

        <!-- Final Decision Section -->
        @php
            $allReviewsCompleted = $reviewStats['completed'] > 0 && $reviewStats['pending'] == 0;
            $hasDecision = !empty($paper->decision);
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
                            🔄 Cập nhật quyết định
                        </button>
                    @else
                        <p class="text-gray-600 mb-4">
                            ✅ Tất cả nhận xét đã hoàn thành. Bạn có thể đưa ra quyết định cuối cùng cho bài báo này.
                        </p>
                        <button onclick="if(window.Alpine && Alpine.$data(document.body).viewDecision) { Alpine.$data(document.body).viewDecision({{ $paper->paper_id }}); } else { window.location.href = '{{ route('chair.papers.decision', $paper->paper_id) }}'; }"
                           class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ⚖️ Đưa ra quyết định cuối cùng
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
                                        'INVITED' => ['label' => 'Chờ xác nhận', 'class' => 'bg-blue-100 text-blue-800'],
                                        'ACCEPTED' => ['label' => 'Đã chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                        'DECLINED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                        'COMPLETED' => ['label' => 'Hoàn thành', 'class' => 'bg-purple-100 text-purple-800'],
                                    ];
                                    $status = $assignmentStatus[$assignment->status_code] ?? ['label' => $assignment->status_code, 'class' => 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($assignment->score)
                                    <span class="font-bold text-orange-600">{{ $assignment->score }}/10</span>
                                @else
                                    <span class="text-gray-400">--</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($assignment->review_id)
                                <button class="text-orange-600 hover:text-orange-700 font-medium">Xem review</button>
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
                            <div class="text-2xl font-bold text-orange-600">{{ $review->score }}/10</div>
                            @php
                                $recommendConfig = [
                                    'ACCEPT' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                    'MINOR_REVISION' => ['label' => 'Sửa nhỏ', 'class' => 'bg-blue-100 text-blue-800'],
                                    'MAJOR_REVISION' => ['label' => 'Sửa lớn', 'class' => 'bg-yellow-100 text-yellow-800'],
                                    'REJECT' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                ];
                                $recommend = $recommendConfig[$review->recommendation_code] ?? ['label' => $review->recommendation_code, 'class' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded {{ $recommend['class'] }}">
                                {{ $recommend['label'] }}
                            </span>
                        </div>
                    </div>
                    @if($review->comments)
                    <div class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                        <div class="font-medium text-gray-900 mb-1">Nhận xét:</div>
                        {{ Str::limit($review->comments, 200) }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
@endsection
