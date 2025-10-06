<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $paper->title }} - HUIT Conferences</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .section { border-left: 4px solid; padding-left: 16px; margin-bottom: 24px; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 text-white shadow-xl">
        <div class="px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('author.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                        <span class="text-blue-700 font-bold text-xl">H</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-blue-200">Author Portal</div>
                    </div>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('author.dashboard') }}" class="px-4 py-2 hover:bg-blue-700 rounded-lg transition">Dashboard</a>
                    <a href="{{ route('author.papers.index') }}" class="px-4 py-2 hover:bg-blue-700 rounded-lg transition">Bài báo</a>
                    <span class="text-white">{{ Auth::user()->full_name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-8">
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
                    
                    @if(in_array($paper->status_code, ['DRAFT', 'SUBMITTED']))
                    <a href="{{ route('author.papers.edit', $paper->paper_id) }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Chỉnh sửa</span>
                    </a>
                    @endif
                    
                    @if($paper->status_code !== 'ACCEPTED' && $paper->status_code !== 'WITHDRAWN')
                    <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Rút bài</span>
                    </button>
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
                        <div class="space-y-4">
                            @foreach($reviews as $index => $review)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-900">Phản biện #{{ $index + 1 }}</h3>
                                    <span class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($review->submitted_at)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Điểm đánh giá:</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ $review->score }}/10</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Đề xuất:</p>
                                        <p class="font-semibold">
                                            @if($review->recommendation === 'ACCEPT')
                                                <span class="text-green-600">Chấp nhận</span>
                                            @elseif($review->recommendation === 'REJECT')
                                                <span class="text-red-600">Từ chối</span>
                                            @else
                                                <span class="text-yellow-600">Yêu cầu sửa</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="border-t border-yellow-300 pt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Nhận xét:</p>
                                    <p class="text-gray-700 whitespace-pre-line">{{ $review->review_content }}</p>
                                </div>
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
                            @foreach($assignments as $index => $assignment)
                            <div class="flex items-center space-x-3">
                                @if($assignment->status_code === 'COMPLETED')
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($assignment->status_code === 'ACCEPTED')
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Phản biện {{ $index + 1 }}</p>
                                    <p class="text-xs text-gray-600">
                                        @if($assignment->status_code === 'COMPLETED')
                                            Hoàn thành
                                        @elseif($assignment->status_code === 'ACCEPTED')
                                            Đang phản biện
                                        @elseif($assignment->status_code === 'PENDING')
                                            Chờ xác nhận
                                        @else
                                            {{ $assignment->status_code }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <p class="text-sm text-gray-600">Chưa có phân công phản biện</p>
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

    <footer class="mt-12 py-6 text-center text-gray-600 text-sm">
        <p>&copy; 2025 HUIT Conferences. All rights reserved.</p>
    </footer>
</body>
</html>
