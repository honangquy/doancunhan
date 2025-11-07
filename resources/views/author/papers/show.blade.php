@extends('layouts.author')

@section('title', $paper->title)

@push('styles')
<style>
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
    .section { border-left: 4px solid; padding-left: 16px; margin-bottom: 24px; }
</style>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('author.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('author.papers.index') }}" class="text-blue-600 hover:text-blue-800">Bài báo</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-600">Chi tiết bài báo</span>
            </nav>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <p class="text-red-800 font-semibold mb-2">Có lỗi xảy ra:</p>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Header with Actions -->
        <div class="card p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="text-gray-500 font-mono text-sm">#{{ $paper->paper_id }}</span>
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
                        <span class="badge {{ $colorClass }}">{{ $paper->status_name }}</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $paper->title }}</h1>
                    <p class="text-gray-600">
                        <span class="font-medium">Hội thảo:</span> {{ $paper->conference_title }}
                    </p>
                    <p class="text-gray-600 text-sm mt-1">
                        Nộp ngày: {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
                
                <div class="flex flex-col space-y-2 ml-4">
                    @if($paper->file_path)
                    <a href="{{ route('author.papers.download', $paper->paper_id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Tải PDF</span>
                    </a>
                    @endif
                    
                    @if($editPermission['can_edit'])
                    <a href="{{ route('author.papers.edit', $paper->paper_id) }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Chỉnh sửa</span>
                    </a>
                    @else
                    <div class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg font-medium flex items-center space-x-2" title="{{ $editPermission['reason'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Không thể chỉnh sửa</span>
                    </div>
                    @endif
                    
                    @if($withdrawPermission['can_withdraw'])
                    <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Rút bài</span>
                    </button>
                    @else
                    <div class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg font-medium flex items-center space-x-2" title="{{ $withdrawPermission['reason'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Không thể rút bài</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Abstract -->
                <div class="card p-6">
                    <div class="section border-blue-500">
                        <h2 class="text-xl font-bold text-gray-900 mb-3">Tóm tắt</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $paper->abstract }}</p>
                    </div>
                </div>

                <!-- Keywords -->
                <div class="card p-6">
                    <div class="section border-green-500">
                        <h2 class="text-xl font-bold text-gray-900 mb-3">Từ khóa</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $paper->keywords) as $keyword)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ trim($keyword) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Authors -->
                <div class="card p-6">
                    <div class="section border-purple-500">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tác giả</h2>
                        <div class="space-y-3">
                            @foreach($authors as $author)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-purple-700 font-semibold">{{ $author->author_order }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $author->full_name }}
                                        @if($author->is_contact)
                                        <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-semibold">Tác giả liên hệ</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $author->email }}</p>
                                    <p class="text-sm text-gray-600">{{ $author->organization }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                @if(in_array($paper->status_code, ['UNDER_REVIEW', 'ACCEPTED', 'REJECTED']) && $reviews->count() > 0)
                <div class="card p-6">
                    <div class="section border-yellow-500">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Kết quả phản biện</h2>
                        <div class="space-y-6">
                            @foreach($reviews as $index => $review)
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="font-semibold text-gray-900">
                                            @if($review->reviewer_name)
                                                {{ $review->reviewer_name }}
                                            @else
                                                Reviewer #{{ $index + 1 }}
                                            @endif
                                        </h3>
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                            {{ \Carbon\Carbon::parse($review->submitted_at)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    @if($review->total_score)
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($review->total_score, 1) }}/10</div>
                                        <div class="text-xs text-gray-600">Điểm tổng</div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Detailed Scores -->
                                @if($review->score_novelty || $review->score_relevance || $review->score_technical_quality)
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                                    @if($review->score_novelty)
                                    <div class="text-center bg-white rounded-lg p-3">
                                        <div class="text-lg font-bold text-purple-600">{{ $review->score_novelty }}/10</div>
                                        <div class="text-xs text-gray-600">Tính mới</div>
                                    </div>
                                    @endif
                                    @if($review->score_relevance)
                                    <div class="text-center bg-white rounded-lg p-3">
                                        <div class="text-lg font-bold text-green-600">{{ $review->score_relevance }}/10</div>
                                        <div class="text-xs text-gray-600">Liên quan</div>
                                    </div>
                                    @endif
                                    @if($review->score_technical_quality)
                                    <div class="text-center bg-white rounded-lg p-3">
                                        <div class="text-lg font-bold text-blue-600">{{ $review->score_technical_quality }}/10</div>
                                        <div class="text-xs text-gray-600">Kỹ thuật</div>
                                    </div>
                                    @endif
                                    @if($review->score_presentation)
                                    <div class="text-center bg-white rounded-lg p-3">
                                        <div class="text-lg font-bold text-orange-600">{{ $review->score_presentation }}/10</div>
                                        <div class="text-xs text-gray-600">Trình bày</div>
                                    </div>
                                    @endif
                                    @if($review->score_references)
                                    <div class="text-center bg-white rounded-lg p-3">
                                        <div class="text-lg font-bold text-red-600">{{ $review->score_references }}/10</div>
                                        <div class="text-xs text-gray-600">Tài liệu tham khảo</div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Recommendation -->
                                @if($review->recommendation_code)
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="text-sm font-medium text-gray-700">Đề xuất:</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if(strpos($review->recommendation_code, 'ACCEPT') !== false) bg-green-100 text-green-800
                                        @elseif(strpos($review->recommendation_code, 'REJECT') !== false) bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($review->recommendation_name)
                                            {{ $review->recommendation_name }}
                                        @else
                                            {{ $review->recommendation_code }}
                                        @endif
                                    </span>
                                </div>
                                @endif

                                <!-- Comments -->
                                @if($review->comment_author)
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Nhận xét cho tác giả:</p>
                                    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $review->comment_author }}</div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Conference Info -->
                <div class="card p-6">
                    <h3 class="font-bold text-gray-900 mb-4 pb-3 border-b">Thông tin hội thảo</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Tên hội thảo:</p>
                            <p class="font-medium text-gray-900">{{ $paper->conference_title }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Deadline nộp bài:</p>
                            <p class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($paper->deadline_submission)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Review Status -->
                <div class="card p-6">
                    <h3 class="font-bold text-gray-900 mb-4 pb-3 border-b">Trạng thái phản biện</h3>
                    <div class="space-y-3">
                        @if($assignments->count() > 0)
                            @php
                                $totalAssignments = $assignments->count();
                                $completedReviews = $assignments->where('review_submitted_at', '!=', null)->count();
                                $acceptedAssignments = $assignments->where('status', 'ACCEPTED')->count();
                                $pendingAssignments = $assignments->where('status', 'PENDING')->count();
                            @endphp
                            
                            <!-- Summary Stats -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">{{ $totalAssignments }}</div>
                                        <div class="text-xs text-gray-600">Tổng số</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-green-600">{{ $completedReviews }}</div>
                                        <div class="text-xs text-gray-600">Hoàn thành</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold 
                                            @if($pendingAssignments > 0) text-yellow-600
                                            @else text-blue-600 
                                            @endif">{{ $acceptedAssignments }}</div>
                                        <div class="text-xs text-gray-600">Đang phản biện</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Assignments -->
                            @foreach($assignments as $index => $assignment)
                            <div class="flex items-center space-x-3 p-3 bg-white border border-gray-200 rounded-lg">
                                @if($assignment->review_submitted_at)
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($assignment->status === 'ACCEPTED')
                                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($assignment->status === 'PENDING')
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm4.293-5.293a1 1 0 010 1.414L11.414 12l2.879 2.879a1 1 0 01-1.414 1.414L10 13.414l-2.879 2.879a1 1 0 01-1.414-1.414L8.586 12 5.707 9.121a1 1 0 011.414-1.414L10 10.586l2.879-2.879a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        @if($assignment->reviewer_name)
                                            {{ $assignment->reviewer_name }}
                                        @else
                                            Reviewer {{ $index + 1 }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        @if($assignment->review_submitted_at)
                                            <span class="text-green-600 font-medium">Hoàn thành</span> 
                                            - {{ \Carbon\Carbon::parse($assignment->review_submitted_at)->format('d/m/Y') }}
                                        @elseif($assignment->status === 'ACCEPTED')
                                            <span class="text-yellow-600 font-medium">Đang phản biện</span>
                                            - Nhận {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}
                                        @elseif($assignment->status === 'PENDING')
                                            <span class="text-gray-500 font-medium">Chờ xác nhận</span>
                                            - Gửi {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') }}
                                        @elseif($assignment->status === 'DECLINED')
                                            <span class="text-red-500 font-medium">Đã từ chối</span>
                                        @else
                                            <span class="text-gray-500 font-medium">{{ $assignment->status }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 mb-2">Chưa có phân công phân biện</p>
                            <p class="text-xs text-gray-400">Bài báo chưa được gửi cho reviewer nào</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div id="withdrawModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Xác nhận rút bài</h3>
            <p class="text-gray-600 mb-6">Bạn có chắc chắn muốn rút bài báo này? Hành động này không thể hoàn tác.</p>
            
            <form method="POST" action="{{ route('author.papers.withdraw', $paper->paper_id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do rút bài (tùy chọn):</label>
                    <textarea name="reason" rows="3" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:outline-none" placeholder="Nhập lý do..."></textarea>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="document.getElementById('withdrawModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        Xác nhận rút bài
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
