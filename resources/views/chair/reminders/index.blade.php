@extends('layouts.chair')

@section('title', 'Quản Lý Reminder Tự Động')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Quản Lý Reminder Tự Động
                </h1>
                <p class="text-gray-600 mt-2">
                    Hệ thống tự động gửi email nhắc nhở cho tác giả và phản biện về các deadline quan trọng
                </p>
            </div>
            
            <button onclick="runTestReminder()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Test Gửi Ngay
            </button>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Lịch gửi reminder tự động (mỗi ngày lúc 07:00)
                </p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><strong>7 ngày trước deadline nộp bài:</strong> Nhắc tác giả nộp bài</li>
                    <li><strong>3 ngày trước deadline nộp bài:</strong> Nhắc KHẨN tác giả</li>
                    <li><strong>7 ngày trước deadline phản biện:</strong> Nhắc phản biện hoàn thành review</li>
                    <li><strong>3 ngày trước deadline phản biện:</strong> Nhắc KHẨN phản biện</li>
                    <li><strong>3 ngày trước deadline Camera-Ready:</strong> Nhắc tác giả nộp bản cuối</li>
                    <li><strong>7 ngày trước ngày hội thảo:</strong> Nhắc tất cả thành viên chuẩn bị</li>
                    <li><strong>1 ngày trước hội thảo kết thúc:</strong> Thông báo sắp kết thúc</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Reminder Templates Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Email Templates Đã Cấu Hình
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                'SUBMISSION_REMINDER_7D' => ['icon' => 'document', 'color' => 'blue', 'label' => 'Nhắc nộp bài (7 ngày)'],
                'SUBMISSION_REMINDER_3D' => ['icon' => 'exclamation', 'color' => 'orange', 'label' => 'KHẨN nộp bài (3 ngày)'],
                'REVIEW_REMINDER_7D' => ['icon' => 'clipboard', 'color' => 'green', 'label' => 'Nhắc phản biện (7 ngày)'],
                'REVIEW_REMINDER_3D' => ['icon' => 'bell', 'color' => 'red', 'label' => 'KHẨN phản biện (3 ngày)'],
                'CAMERA_READY_REMINDER_3D' => ['icon' => 'camera', 'color' => 'purple', 'label' => 'Camera-Ready (3 ngày)'],
                'CONFERENCE_START_7D' => ['icon' => 'sparkles', 'color' => 'indigo', 'label' => 'Sắp diễn ra (7 ngày)'],
                'CONFERENCE_END_1D' => ['icon' => 'hand', 'color' => 'gray', 'label' => 'Sắp kết thúc (1 ngày)']
            ] as $code => $info)
                @php
                    $template = $templates[$code] ?? null;
                    $isActive = $template !== null;
                    
                    // SVG icons mapping
                    $icons = [
                        'document' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                        'exclamation' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                        'clipboard' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
                        'bell' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
                        'camera' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                        'sparkles' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                        'hand' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>',
                    ];
                @endphp
                <div class="border border-{{ $info['color'] }}-200 rounded-lg p-4 {{ $isActive ? 'bg-'.$info['color'].'-50' : 'bg-gray-100' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-{{ $info['color'] }}-600">
                            {!! $icons[$info['icon']] !!}
                        </div>
                        @if($isActive)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-300 text-gray-600 text-xs rounded-full font-semibold">Chưa cấu hình</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $info['label'] }}</h3>
                    @if($isActive)
                        <p class="text-xs text-gray-600 truncate">{{ $template->title }}</p>
                    @else
                        <p class="text-xs text-gray-500">Template chưa được tạo</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Conferences List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Hội Thảo Của Bạn ({{ count($conferences) }})
        </h2>

        @if(empty($conferences) || count($conferences) === 0)
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500">Bạn chưa là Chair của hội thảo nào</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hội Thảo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline Nộp Bài</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline Review</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Diễn Ra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reminder Đã Gửi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($conferences as $conf)
                            @php
                                $stats = $reminderStats[$conf->conference_id] ?? ['total' => 0, 'by_type' => []];
                                $statusColors = [
                                    'APPROVED' => 'green',
                                    'PENDING' => 'yellow',
                                    'COMPLETED' => 'blue',
                                    'CANCELLED' => 'red'
                                ];
                                $statusColor = $statusColors[$conf->status] ?? 'gray';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $conf->title }}</div>
                                    <span class="px-2 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-xs rounded-full">
                                        {{ $conf->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $conf->deadline_submission ? \Carbon\Carbon::parse($conf->deadline_submission)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $conf->deadline_review ? \Carbon\Carbon::parse($conf->deadline_review)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $conf->start_date ? \Carbon\Carbon::parse($conf->start_date)->format('d/m/Y') : '-' }}
                                    →
                                    {{ $conf->end_date ? \Carbon\Carbon::parse($conf->end_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold">
                                            {{ $stats['total'] }}
                                        </span>
                                        @if($stats['total'] > 0)
                                            <button onclick="showStatsModal({{ $conf->conference_id }})" class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('chair.reminders.logs', $conf->conference_id) }}" 
                                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition inline-flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Xem Logs
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
function runTestReminder() {
    if (!confirm('Bạn có chắc muốn chạy lệnh gửi reminder ngay lập tức?\n\nLưu ý: Chỉ gửi cho các deadline đúng hôm nay.')) {
        return;
    }
    
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang chạy...';
    
    fetch('/chair/reminders/test-send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ Đã chạy lệnh thành công!\n\n' + data.output);
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.error);
        }
    })
    .catch(err => {
        alert('❌ Lỗi kết nối: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function showStatsModal(conferenceId) {
    alert('Chi tiết thống kê sẽ được hiển thị ở trang Logs');
    window.location.href = '/chair/reminders/' + conferenceId + '/logs';
}
</script>
@endsection
