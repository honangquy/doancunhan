<!-- Tab Papers -->
<div x-show="activeTab === 'papers'" x-cloak>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Danh sách Bài báo ({{ $papers->count() }})</h3>
        <a href="{{ route('chair.reports.export', ['conferenceId' => $conference->conference_id, 'type' => 'papers', 'format' => 'csv']) }}"
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên bài</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiểu ban</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày nộp</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewers</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COI</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quyết định</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($papers as $paper)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $paper->paper_id }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $paper->title }}">
                                {{ $paper->title }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $paper->submitter->full_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                {{ $paper->tieuBan->title ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @php
                                $statusColors = [
                                    'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                    'UNDER_REVIEW' => 'bg-yellow-100 text-yellow-800',
                                    'REVIEWED' => 'bg-green-100 text-green-800',
                                    'ACCEPTED' => 'bg-green-100 text-green-800',
                                    'REJECTED' => 'bg-red-100 text-red-800',
                                ];
                                $color = $statusColors[$paper->status_code] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ $paper->status_code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @php
                                $required = $conference->reviewers_per_paper ?? 3;
                                $assigned = $paper->reviewer_assignments_count;
                                $isComplete = $assigned >= $required;
                            @endphp
                            <span class="px-2 py-1 rounded {{ $isComplete ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }} font-semibold">
                                {{ $assigned }} / {{ $required }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @if($paper->coi_count > 0)
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-semibold">
                                    {{ $paper->coi_count }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($paper->decision)
                                @php
                                    $decisionColors = [
                                        'ACCEPT' => 'bg-green-100 text-green-800',
                                        'REJECT' => 'bg-red-100 text-red-800',
                                        'REVISION' => 'bg-yellow-100 text-yellow-800',
                                        'REVISE' => 'bg-yellow-100 text-yellow-800',
                                        'PUBLISHED' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $dColor = $decisionColors[$paper->decision] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dColor }}">
                                    @if($paper->decision === 'ACCEPT') Chấp nhận
                                    @elseif($paper->decision === 'REJECT') Từ chối  
                                    @elseif($paper->decision === 'REVISE') Yêu cầu sửa
                                    @elseif($paper->decision === 'PUBLISHED') Đã xuất bản
                                    @else {{ $paper->decision }}
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">Chưa có</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            Không có dữ liệu
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
