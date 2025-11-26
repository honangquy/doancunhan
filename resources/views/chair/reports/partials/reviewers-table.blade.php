<!-- Tab Reviewers -->
<div x-show="activeTab === 'reviewers'" x-cloak>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Danh sách Reviewers ({{ $reviewers->count() }})</h3>
        <a href="{{ route('chair.reports.export', ['conferenceId' => $conference->conference_id, 'type' => 'reviewers', 'format' => 'csv']) }}"
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng assigned</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đã hoàn thành</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đang chờ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COI đã khai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max muốn nhận</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workload</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviewers as $reviewer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    @if($reviewer->avatar_url)
                                        <img class="h-8 w-8 rounded-full" src="{{ $reviewer->avatar_url }}" alt="{{ $reviewer->full_name }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xs">
                                            {{ strtoupper(substr($reviewer->full_name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $reviewer->full_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $reviewer->email }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded font-semibold">
                                {{ $reviewer->total_assigned }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded font-semibold">
                                {{ $reviewer->completed_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @if($reviewer->pending_count > 0)
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-semibold">
                                    {{ $reviewer->pending_count }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @if($reviewer->coi_declared > 3)
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-semibold">
                                    {{ $reviewer->coi_declared }}
                                </span>
                            @elseif($reviewer->coi_declared > 0)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-semibold">
                                    {{ $reviewer->coi_declared }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-700">
                            {{ $reviewer->max_papers ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @php
                                $workload = $reviewer->workload_percent;
                                $workloadColor = 'bg-green-100 text-green-800';
                                if ($workload >= 100) {
                                    $workloadColor = 'bg-red-100 text-red-800';
                                } elseif ($workload >= 80) {
                                    $workloadColor = 'bg-yellow-100 text-yellow-800';
                                }
                            @endphp
                            <span class="px-2 py-1 rounded font-semibold {{ $workloadColor }}">
                                {{ number_format($workload, 0) }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Không có reviewer nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Workload Legend -->
    <div class="mt-4 flex items-center justify-end space-x-4 text-xs">
        <div class="flex items-center">
            <span class="w-3 h-3 bg-green-100 rounded mr-1"></span>
            <span class="text-gray-600">Workload &lt; 80%</span>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-yellow-100 rounded mr-1"></span>
            <span class="text-gray-600">80% - 100%</span>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-red-100 rounded mr-1"></span>
            <span class="text-gray-600">Workload ≥ 100%</span>
        </div>
    </div>
</div>
