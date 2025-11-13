@extends('layouts.chair')

@section('title', 'Logs Reminder - ' . $conference->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('chair.reminders.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại danh sách
        </a>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Logs Reminder: {{ $conference->title }}
            </h1>
            <p class="text-gray-600 mt-2">
                Tổng số: <strong>{{ $logs->count() }}</strong> email reminder đã được gửi
            </p>
        </div>
    </div>

    <!-- Stats Summary -->
    @if($logs->isNotEmpty())
        @php
            $groupedByType = $logs->groupBy('event_type');
            $groupedByTemplate = $logs->groupBy('template_code');
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- By Event Type -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Theo loại deadline
                </h3>
                <div class="space-y-2">
                    @foreach($groupedByType as $type => $items)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $type }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold">{{ $items->count() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- By Template -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Theo template
                </h3>
                <div class="space-y-2">
                    @foreach($groupedByTemplate as $template => $items)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">{{ $template }}</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold">{{ $items->count() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Chi Tiết Logs</h2>
        
        @if($logs->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 mb-2">Chưa có email reminder nào được gửi cho hội thảo này</p>
                <p class="text-sm text-gray-400">Hệ thống sẽ tự động gửi khi đến đúng thời điểm (7 ngày / 3 ngày trước deadline)</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người Nhận</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log['timestamp'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $log['recipient'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-semibold">
                                        {{ $log['event_type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $log['template_code'] }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="showLogDetail({{ json_encode($log['data']) }})" 
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Xem JSON
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal for JSON detail -->
<div id="jsonModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Chi Tiết Log</h3>
            <button onclick="closeJsonModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <pre id="jsonContent" class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"></pre>
        </div>
    </div>
</div>

<script>
function showLogDetail(data) {
    document.getElementById('jsonContent').textContent = JSON.stringify(data, null, 2);
    document.getElementById('jsonModal').classList.remove('hidden');
}

function closeJsonModal() {
    document.getElementById('jsonModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('jsonModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeJsonModal();
    }
});
</script>
@endsection
