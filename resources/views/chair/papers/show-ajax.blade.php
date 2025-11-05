<!-- Paper Header -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <div class="flex items-center space-x-2 mb-3">
                <span class="text-sm font-medium text-gray-500">ID: #{{ $paper->paper_id }}</span>
                @php
                    $statusConfig = [
                        'SUBMITTED' => ['label' => 'Đã nộp', 'class' => 'bg-blue-100 text-blue-800'],
                        'UNDER_REVIEW' => ['label' => 'Đang phản biện', 'class' => 'bg-yellow-100 text-yellow-800'],
                        'REVIEWED' => ['label' => 'Đã phản biện', 'class' => 'bg-purple-100 text-purple-800'],
                        'ACCEPTED' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                        'REJECTED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                        'REVISION_REQUIRED' => ['label' => 'Yêu cầu sửa', 'class' => 'bg-orange-100 text-orange-800']
                    ];
                    $currentStatus = $statusConfig[$paper->status_code] ?? ['label' => $paper->status_name, 'class' => 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentStatus['class'] }}">
                    {{ $currentStatus['label'] }}
                </span>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $paper->title }}</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Người nộp:</span>
                    <p class="text-gray-900">{{ $paper->author_name }}</p>
                    <p class="text-gray-500">{{ $paper->author_email }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Ngày nộp:</span>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Cập nhật cuối:</span>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($paper->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conference Info -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Thông tin hội thảo</h2>
    <p class="text-gray-900 font-medium">{{ $paper->conference_name }}</p>
</div>

<!-- Authors Section -->
@if($authors && count($authors) > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <span class="mr-2">👥</span>
        Tác giả ({{ count($authors) }})
    </h2>
    <div class="space-y-3">
        @foreach($authors as $index => $author)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-sm font-medium text-gray-700">
                        {{ $index + 1 }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $author->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ $author->email }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($author->is_corresponding)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        Liên hệ chính
                    </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Review Statistics -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <span class="mr-2">📈</span>
        Thống kê phản biện
    </h2>
    
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">{{ $reviewStats['total_assigned'] }}</div>
            <div class="text-sm text-gray-600">Tổng số</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">{{ $reviewStats['pending'] }}</div>
            <div class="text-sm text-gray-600">Chờ xác nhận</div>
        </div>
        <div class="text-center p-4 bg-green-50 rounded-lg">
            <div class="text-2xl font-bold text-green-600">{{ $reviewStats['completed'] }}</div>
            <div class="text-sm text-gray-600">Đã chấp nhận</div>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">{{ $reviewStats['completed'] }}</div>
            <div class="text-sm text-gray-600">Hoàn thành</div>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-lg">
            <div class="text-2xl font-bold text-red-600">{{ $reviewStats['declined'] }}</div>
            <div class="text-sm text-gray-600">Điểm TB</div>
        </div>
    </div>
</div>

<!-- Review Assignments -->
@if($assignments && count($assignments) > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Phân công phản biện</h2>
    <div class="space-y-4">
        @foreach($assignments as $assignment)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($assignment->reviewer_name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $assignment->reviewer_name }}</p>
                        <p class="text-sm text-gray-500">{{ $assignment->reviewer_email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $statusClass = match($assignment->status) {
                            'PENDING' => 'bg-yellow-100 text-yellow-800',
                            'ACCEPTED' => 'bg-green-100 text-green-800',
                            'DECLINED' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $assignment->status ?? 'PENDING' }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Completed Reviews -->
@if($reviews && count($reviews) > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">✅ Phản biện đã hoàn thành</h2>
    <div class="space-y-4">
        @foreach($reviews as $review)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $review->reviewer_name }}</p>
                    <p class="text-xs text-gray-500">
                        Nộp ngày: {{ \Carbon\Carbon::parse($review->submitted_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-blue-600">{{ $review->score }}/5</div>
                    @if($review->recommendation_code)
                        @php
                            $recClass = match($review->recommendation_code) {
                                'ACCEPT' => 'bg-green-100 text-green-800',
                                'REJECT' => 'bg-red-100 text-red-800',
                                'REVISION' => 'bg-orange-100 text-orange-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recClass }}">
                            {{ $review->recommendation_code }}
                        </span>
                    @endif
                </div>
            </div>
            
            @if($review->detailed_comments)
            <div class="mt-3 p-3 bg-gray-50 rounded">
                <p class="text-sm text-gray-700 font-medium mb-1">Nhận xét chi tiết:</p>
                <p class="text-sm text-gray-600">{{ Str::limit($review->detailed_comments, 200) }}</p>
            </div>
            @endif
            @if($review->comment_author)
            <div class="mt-3 p-3 bg-blue-50 rounded">
                <p class="text-sm text-blue-700 font-medium mb-1">Nhận xét cho tác giả:</p>
                <p class="text-sm text-gray-600">{{ Str::limit($review->comment_author, 200) }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif