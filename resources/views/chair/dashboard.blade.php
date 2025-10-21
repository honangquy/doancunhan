@extends('layouts.chair')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('page-subtitle', 'Quản lý hội thảo và phản biện')

@section('content')

<!-- Conference Request Status -->
@if(isset($conferenceRequests) && $conferenceRequests->count() > 0)
<div class="mb-8">
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-xl shadow-lg text-white p-6 animate-slideInUp">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Hội thảo của tôi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($conferenceRequests as $request)
            <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold">{{ $request->title }}</h3>
                    @if($request->status == 'APPROVED')
                        <span class="bg-green-500 text-xs px-2 py-1 rounded-full">✓ Đã duyệt</span>
                    @elseif($request->status == 'PENDING')
                        <span class="bg-yellow-500 text-xs px-2 py-1 rounded-full">⏳ Chờ duyệt</span>
                    @elseif($request->status == 'REJECTED')
                        <span class="bg-red-500 text-xs px-2 py-1 rounded-full">✗ Từ chối</span>
                    @endif
                </div>
                <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</p>
                @if($request->status == 'APPROVED')
                    <div class="mt-2">
                        <a href="{{ route('chair.conferences') }}" class="inline-flex items-center text-xs bg-white text-orange-600 px-3 py-1 rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Quản lý
                        </a>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($conferenceRequests->where('status', 'APPROVED')->count() == 0)
        <div class="mt-4 p-4 bg-white bg-opacity-20 rounded-lg">
            <p class="text-sm">💡 <strong>Mẹo:</strong> Sau khi admin duyệt yêu cầu hội thảo, bạn sẽ có thể quản lý hội thảo đó từ trang "Quản lý hội thảo".</p>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Stats Cards -->

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Papers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 animate-scaleIn hover:shadow-lg transition-all duration-300" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng bài báo</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_papers'] ?? 0 }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Từ {{ $stats['approved_conferences'] ?? 0 }} hội thảo</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Accepted Papers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 animate-scaleIn hover:shadow-lg transition-all duration-300" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đã chấp nhận</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['accepted'] ?? 0 }}</h3>
                            <p class="text-xs text-green-600 mt-2">✓ Đã duyệt</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Under Review -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 animate-scaleIn hover:shadow-lg transition-all duration-300" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đang review</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['under_review'] ?? 0 }}</h3>
                            <p class="text-xs text-blue-600 mt-2">⏳ Đang xử lý</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Need Reviewers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 animate-scaleIn hover:shadow-lg transition-all duration-300" style="animation-delay: 0.4s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Cần reviewer</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['needs_reviewers'] ?? 0 }}</h3>
                            <p class="text-xs text-orange-600 mt-2">⚠ Cần phân công</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Papers Requiring Action -->
            <div class="bg-white rounded-xl shadow-md mb-8 animate-slideInUp" style="animation-delay: 0.5s">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Bài báo gần đây</h2>
                        <a href="{{ route('chair.papers') }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Xem tất cả →</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tiêu đề</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tác giả</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reviewers</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPapers ?? [] as $paper)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $paper->bai_bao_id ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($paper->tieu_de ?? 'N/A', 50) }}</div>
                                    <div class="text-xs text-gray-500">Nộp: {{ $paper->created_at ? $paper->created_at->format('d/m/Y') : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->nguoiDung->ho_ten ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if(isset($paper->reviews_total) && $paper->reviews_total > 0)
                                            <span class="text-blue-600 font-medium">{{ $paper->reviews_total }} reviewer(s)</span>
                                            @if(isset($paper->reviews_completed) && $paper->reviews_completed > 0)
                                                <div class="text-xs text-green-600 mt-1">
                                                    {{ $paper->reviews_completed }}/{{ $paper->reviews_total }} hoàn thành
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-orange-600 text-xs">⚠ Chưa phân công</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'PENDING' => ['label' => 'Đang chờ', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'UNDER_REVIEW' => ['label' => 'Đang xét', 'class' => 'bg-blue-100 text-blue-800'],
                                            'REVIEWED' => ['label' => 'Đã xét', 'class' => 'bg-purple-100 text-purple-800'],
                                            'ACCEPTED' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                            'REJECTED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                        ];
                                        $currentStatus = $paper->trang_thai ?? 'PENDING';
                                        $status = $statusConfig[$currentStatus] ?? ['label' => $currentStatus, 'class' => 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('chair.papers.show', $paper->bai_bao_id) }}" class="text-orange-600 hover:text-orange-700 font-medium hover:underline">Chi tiết →</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Chưa có bài báo nào</p>
                                    <p class="text-sm mt-1">Bài báo mới nộp sẽ xuất hiện ở đây</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Grid: Conferences & Pending Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Conferences -->
                <div class="bg-white rounded-xl shadow-md p-6 animate-slideInUp" style="animation-delay: 0.6s">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hội thảo của tôi
                    </h3>
                    <div class="space-y-3">
                        @forelse($conferences ?? [] as $conference)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $conference->ten_hoi_thao ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">{{ $conference->papers_count ?? 0 }} bài báo</p>
                            </div>
                            <a href="{{ route('chair.conferences.show', $conference->hoi_thao_id) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                                Xem →
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p>Chưa có hội thảo nào</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-md p-6 animate-slideInUp" style="animation-delay: 0.7s">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Hành động nhanh
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('chair.conferences.index') }}" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Quản lý hội thảo</h4>
                                <p class="text-sm text-gray-500">Cấu hình và quản lý hội thảo</p>
                            </div>
                        </a>

                        <a href="{{ route('chair.papers') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Quản lý bài báo</h4>
                                <p class="text-sm text-gray-500">Xem và phê duyệt bài báo</p>
                            </div>
                        </a>

                        <a href="#" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Quản lý reviewer</h4>
                                <p class="text-sm text-gray-500">Phân công và theo dõi reviewer</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3);
    }
</style>
@endsection
