<!-- Tab Bidding & COI -->
<div x-show="activeTab === 'bidding'" x-cloak>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bidding Distribution -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Phân bố Bidding</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Interested (Muốn review)</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-green-600">{{ $biddingStats['interested'] ?? 0 }}</span>
                        <span class="text-sm text-gray-500 ml-1">
                            ({{ $biddingStats['total'] > 0 ? number_format(($biddingStats['interested'] / $biddingStats['total']) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Not Interested (Không muốn)</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-red-600">{{ $biddingStats['not_interested'] ?? 0 }}</span>
                        <span class="text-sm text-gray-500 ml-1">
                            ({{ $biddingStats['total'] > 0 ? number_format(($biddingStats['not_interested'] / $biddingStats['total']) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t">
                    <span class="text-gray-700 font-semibold">Tổng số bidding</span>
                    <span class="text-2xl font-bold text-gray-800">{{ $biddingStats['total'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            @if($biddingStats['total'] > 0)
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-green-500 h-3 float-left" style="width: {{ ($biddingStats['interested'] / $biddingStats['total']) * 100 }}%"></div>
                        <div class="bg-red-500 h-3 float-left" style="width: {{ ($biddingStats['not_interested'] / $biddingStats['total']) * 100 }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- COI Summary -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tổng quan COI</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-gray-700">Tổng số COI</span>
                    </div>
                    <span class="text-2xl font-bold text-orange-600">{{ $biddingStats['total_coi'] ?? 0 }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-gray-700">Bài báo có COI</span>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">{{ $biddingStats['papers_with_coi'] ?? 0 }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="text-gray-700">Reviewers có COI</span>
                    </div>
                    <span class="text-2xl font-bold text-purple-600">{{ $biddingStats['reviewers_with_coi'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top COI Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Papers with Most COI -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Bài báo có nhiều COI nhất</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bài báo</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">COI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($biddingStats['top_papers_with_coi'] ?? [] as $index => $paper)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $paper->title }}">
                                        {{ $paper->title }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-semibold">
                                        {{ $paper->coi_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reviewers with Most COI -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reviewers khai báo nhiều COI nhất</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">COI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($biddingStats['top_reviewers_with_coi'] ?? [] as $index => $reviewer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-6 w-6">
                                            @if($reviewer->avatar_url)
                                                <img class="h-6 w-6 rounded-full" src="{{ $reviewer->avatar_url }}" alt="{{ $reviewer->full_name }}">
                                            @else
                                                <div class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xs">
                                                    {{ strtoupper(substr($reviewer->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $reviewer->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-semibold">
                                        {{ $reviewer->coi_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
