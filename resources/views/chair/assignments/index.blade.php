@extends('layouts.chair')

@section('title', 'Phân công phản biện')

@section('page-title', 'Phân công phản biện')

@section('page-subtitle', 'Quản lý việc phân công phản biện viên cho các bài báo')

@section('content')
<div x-data="reviewerAssignment()" class="space-y-6">
    <!-- Bộ lọc -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hội thảo</label>
                <select x-model="filters.conference_id" @change="filterPapers()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">-- Tất cả hội thảo --</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->conference_id }}">{{ $conference->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái phân công</label>
                <select x-model="filters.assignment_status" @change="filterPapers()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">-- Tất cả --</option>
                    <option value="unassigned">Chưa phân công</option>
                    <option value="partial">Phân công một phần</option>
                    <option value="complete">Đã phân công đủ</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" x-model="filters.search" @input="filterPapers()" 
                       placeholder="Tìm theo tiêu đề bài báo..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tổng bài báo</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.total"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Chưa phân công</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.unassigned"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Phân công một phần</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.partial"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Đã phân công đủ</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.complete"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách bài báo -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Danh sách bài báo cần phân công</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bài báo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tác giả
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Phân công
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="paper in filteredPapers" :key="paper.paper_id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="paper.title"></div>
                                <div class="text-sm text-gray-500" x-text="'ID: ' + paper.paper_id"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="paper.author_name"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="paper.reviewer_count + '/3 reviewers'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="getStatusClass(paper.reviewer_count)"
                                      x-text="getStatusText(paper.reviewer_count)">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a :href="'/chair/assignments/' + paper.paper_id + '/assign'" 
                                   class="text-orange-600 hover:text-orange-900">
                                    Phân công
                                </a>
                                <template x-if="paper.reviewer_count > 0">
                                    <a :href="'/chair/assignments/' + paper.paper_id + '/view'" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Xem chi tiết
                                    </a>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredPapers.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Không có bài báo nào cần phân công
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function reviewerAssignment() {
    return {
        papers: @json($unassignedPapers),
        filteredPapers: [],
        filters: {
            conference_id: '',
            assignment_status: '',
            search: ''
        },
        stats: {
            total: 0,
            unassigned: 0,
            partial: 0,
            complete: 0
        },

        init() {
            this.filteredPapers = this.papers;
            this.updateStats();
        },

        filterPapers() {
            let filtered = this.papers;

            // Filter by conference
            if (this.filters.conference_id) {
                filtered = filtered.filter(paper => 
                    paper.conference_id == this.filters.conference_id
                );
            }

            // Filter by assignment status
            if (this.filters.assignment_status) {
                filtered = filtered.filter(paper => {
                    const count = parseInt(paper.reviewer_count);
                    switch (this.filters.assignment_status) {
                        case 'unassigned':
                            return count === 0;
                        case 'partial':
                            return count > 0 && count < 3;
                        case 'complete':
                            return count >= 3;
                        default:
                            return true;
                    }
                });
            }

            // Filter by search
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(paper => 
                    paper.title.toLowerCase().includes(search) ||
                    paper.author_name.toLowerCase().includes(search)
                );
            }

            this.filteredPapers = filtered;
            this.updateStats();
        },

        updateStats() {
            this.stats.total = this.filteredPapers.length;
            this.stats.unassigned = this.filteredPapers.filter(p => parseInt(p.reviewer_count) === 0).length;
            this.stats.partial = this.filteredPapers.filter(p => {
                const count = parseInt(p.reviewer_count);
                return count > 0 && count < 3;
            }).length;
            this.stats.complete = this.filteredPapers.filter(p => parseInt(p.reviewer_count) >= 3).length;
        },

        getStatusClass(reviewerCount) {
            const count = parseInt(reviewerCount);
            if (count === 0) return 'bg-red-100 text-red-800';
            if (count < 3) return 'bg-yellow-100 text-yellow-800';
            return 'bg-green-100 text-green-800';
        },

        getStatusText(reviewerCount) {
            const count = parseInt(reviewerCount);
            if (count === 0) return 'Chưa phân công';
            if (count < 3) return 'Phân công một phần';
            return 'Đã phân công đủ';
        }
    };
}
</script>
@endsection