@extends('layouts.chair')

@section('title', 'Xuất bản Kỷ yếu - ' . $conference->title)

@section('content')
<div class="max-w-7xl mx-auto" x-data="proceedingsManager()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Xuất bản Kỷ yếu</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng số bài chấp nhận</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ count($acceptedPapers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đã có số trang</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="papersWithPagination"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đã xuất bản</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $acceptedPapers->whereNotNull('published_at')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mb-6 flex flex-wrap gap-4">
        <a href="{{ route('chair.proceedings.upload', $conference->conference_id) }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Upload Kỷ yếu PDF
        </a>

        <button @click="showPaginationModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Cập nhật số trang
        </button>

        <button @click="publishSelected()"
                :disabled="selectedPapers.length === 0"
                :class="selectedPapers.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                class="text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Xuất bản (<span x-text="selectedPapers.length"></span>)
        </button>

        <a href="{{ route('chair.conferences.proceedings', $conference->conference_id) }}"
           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Xem Kỷ yếu
        </a>
    </div>

    <!-- Papers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Danh sách bài báo đã chấp nhận</h3>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" @change="toggleAll($event)" class="form-checkbox">
                    <span class="text-sm text-gray-600">Chọn tất cả</span>
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chọn</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số trang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($acceptedPapers as $paper)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(is_null($paper->published_at))
                                    <input type="checkbox"
                                           x-model="selectedPapers"
                                           value="{{ $paper->paper_id }}"
                                           class="form-checkbox">
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $paper->title }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $paper->paper_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @foreach($paper->authors as $author)
                                        <span class="block">
                                            {{ $author->full_name }}
                                            @if($author->is_contact)
                                                <span class="text-blue-600 text-xs">(Contact)</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($paper->page_start) && isset($paper->page_end) && $paper->page_start && $paper->page_end)
                                    {{ $paper->page_start }} - {{ $paper->page_end }}
                                @else
                                    <span class="text-gray-400">Chưa cập nhật</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paper->published_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Đã xuất bản
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Chấp nhận
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($paper->latest_version_path)
                                    <a href="{{ route('chair.proceedings.download', [$conference->conference_id, $paper->paper_id]) }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Tải PDF
                                    </a>
                                @else
                                    <span class="text-gray-400">Chưa có file</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Modal -->
    <div x-show="showPaginationModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPaginationModal = false"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Cập nhật số trang cho bài báo
                    </h3>

                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài báo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trang bắt đầu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trang kết thúc</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($acceptedPapers as $index => $paper)
                                    <tr>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ Str::limit($paper->title, 50) }}</div>
                                            <div class="text-gray-500">ID: {{ $paper->paper_id }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number"
                                                   x-model="paginationData[{{ $paper->paper_id }}].page_start"
                                                   class="w-20 border border-gray-300 rounded px-2 py-1 text-sm"
                                                   min="1">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number"
                                                   x-model="paginationData[{{ $paper->paper_id }}].page_end"
                                                   class="w-20 border border-gray-300 rounded px-2 py-1 text-sm"
                                                   min="1">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="savePagination()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Lưu thay đổi
                    </button>
                    <button @click="showPaginationModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function proceedingsManager() {
    return {
        selectedPapers: [],
        showPaginationModal: false,
        paginationData: @json($acceptedPapers->keyBy('paper_id')->map(function($paper) {
            return [
                'paper_id' => $paper->paper_id,
                'page_start' => $paper->page_start ?? 1,
                'page_end' => $paper->page_end ?? 1
            ];
        })),

        get papersWithPagination() {
            return Object.values(this.paginationData).filter(p => p.page_start && p.page_end).length;
        },

        toggleAll(event) {
            if (event.target.checked) {
                this.selectedPapers = @json($acceptedPapers->whereNull('published_at')->pluck('paper_id'));
            } else {
                this.selectedPapers = [];
            }
        },

        async savePagination() {
            const papers = Object.values(this.paginationData).map(p => ({
                paper_id: p.paper_id,
                page_start: parseInt(p.page_start),
                page_end: parseInt(p.page_end)
            }));

            try {
                const response = await fetch(`{{ route('chair.proceedings.update-pagination', $conference->conference_id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ papers })
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    this.showPaginationModal = false;
                    location.reload();
                } else {
                    alert(result.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                alert('Có lỗi xảy ra: ' + error.message);
            }
        },

        async publishSelected() {
            if (this.selectedPapers.length === 0) {
                alert('Vui lòng chọn ít nhất một bài báo để xuất bản');
                return;
            }

            if (!confirm(`Bạn có chắc chắn muốn xuất bản ${this.selectedPapers.length} bài báo đã chọn?`)) {
                return;
            }

            try {
                const response = await fetch(`{{ route('chair.proceedings.publish', $conference->conference_id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        paper_ids: this.selectedPapers.map(id => parseInt(id))
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.error || 'Có lỗi xảy ra');
                }
            } catch (error) {
                alert('Có lỗi xảy ra: ' + error.message);
            }
        }
    }
}
</script>
@endsection
