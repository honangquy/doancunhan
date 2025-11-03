@extends('layouts.reviewer')

@section('title', 'Phản biện bài báo')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('reviewer.assignments.show', $assignment->id) }}" class="inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors duration-200 font-medium">
                    <!-- Heroicons: arrow-left -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Quay lại chi tiết phân công</span>
                </a>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-100 shadow-sm">
            <div class="p-2 bg-indigo-100 rounded-xl shadow-md">
                <!-- Review form icon -->
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Form đánh giá phản biện</h1>
                <p class="text-gray-600 text-sm">Đánh giá chi tiết bài báo và đưa ra khuyến nghị chuyên môn</p>
                <div class="mt-1 flex items-center space-x-2 text-sm text-indigo-600">
                    <span class="bg-indigo-100 px-2 py-1 rounded-full font-medium">Thang điểm: 1-10</span>
                    <span class="bg-purple-100 px-2 py-1 rounded-full font-medium">Bắt buộc: Tất cả tiêu chí</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Paper Information Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-lg mb-8">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center space-x-3">
                <!-- Paper info icon (Heroicons: document-text) -->
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12h10" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16h8" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3v6h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707L13.293 3.293A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Thông tin bài báo</span>
            </h2>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <label class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Tiêu đề:</label>
                    <p class="text-gray-900 font-semibold mt-2 text-sm leading-relaxed">{{ $paper->title }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <label class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Hội thảo:</label>
                        <p class="text-gray-900 font-medium mt-2 text-sm">{{ $paper->conference_name ?? 'Chưa xác định' }}</p>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                        <label class="text-sm font-semibold text-emerald-600 uppercase tracking-wide">Tác giả:</label>
                        <p class="text-gray-900 font-medium mt-2 text-sm">{{ $paper->author_names ?? $paper->author_name }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <label class="text-sm font-semibold text-orange-600 uppercase tracking-wide">Lĩnh vực:</label>
                        <p class="text-gray-900 font-medium mt-2 text-sm">{{ $paper->track_name ?? $paper->field ?? 'Chưa xác định' }}</p>
                    </div>
                </div>
            </div>

            @if($paper->abstract)
            <div class="mt-6">
                <label class="text-sm font-medium text-gray-600">Tóm tắt:</label>
                <div class="mt-2 p-4 bg-gray-50 rounded-lg border">
                    <p class="text-gray-800 leading-relaxed text-sm">{{ $paper->abstract }}</p>
                </div>
            </div>
            @endif

            @if($paper->file_path)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- File icon -->
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-blue-900">File bài báo</p>
                            <p class="text-sm text-blue-700">{{ $paper->title }}.pdf</p>
                        </div>
                    </div>
                    <a href="{{ route('reviewer.papers.download', $paper->paper_id) }}" 
                       class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                        <!-- Download icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Tải xuống</span>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Review Form -->
    <form id="reviewForm" action="{{ route('reviewer.reviews.store', $assignment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @if($existingReview)
        <!-- Draft Status Indicator -->
        <div class="bg-{{ $existingReview->is_draft ? 'amber' : 'green' }}-50 border border-{{ $existingReview->is_draft ? 'amber' : 'green' }}-200 rounded-lg p-4 mb-6">
            <div class="flex items-center space-x-3">
                @if($existingReview->is_draft)
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
                <div>
                    <h3 class="text-sm font-semibold text-{{ $existingReview->is_draft ? 'amber' : 'green' }}-800">
                        @if($existingReview->is_draft)
                            📝 Bản nháp đã lưu
                        @else
                            ✅ Phản biện đã gửi chính thức
                        @endif
                    </h3>
                    <p class="text-xs text-{{ $existingReview->is_draft ? 'amber' : 'green' }}-700">
                        @if($existingReview->is_draft)
                            @if($existingReview->submitted_at)
                                Cập nhật lần cuối: {{ \Carbon\Carbon::parse($existingReview->submitted_at)->format('d/m/Y H:i') }}. Bạn có thể tiếp tục chỉnh sửa và lưu nháp.
                            @else
                                Bản nháp chưa lưu. Bạn có thể tiếp tục chỉnh sửa và lưu nháp.
                            @endif
                        @else
                            Đã gửi vào: {{ \Carbon\Carbon::parse($existingReview->submitted_at)->format('d/m/Y H:i') }}. 
                            ⚠️ Phản biện đã được gửi chính thức và không thể chỉnh sửa.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Scoring Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg mb-8">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center space-x-3">
                    <!-- Chart icon -->
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>A. Phần cho điểm</span>
                </h2>
                <p class="text-sm text-blue-700 mt-2 font-medium">Chấm điểm từng tiêu chí theo thang 1–10 (1: Kém nhất, 10: Xuất sắc)</p>
            </div>

            <div class="p-6">
                <!-- Scoring Table -->
                <div class="overflow-hidden bg-white border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu chí đánh giá</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm (1-10)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-emerald-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold" aria-hidden="true">1</span>
                                            <span class="sr-only">Tiêu chí 1</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Tính mới của đề tài</h4>
                                            <p class="text-xs text-gray-600 leading-relaxed">Bài có đưa ra đóng góp mới về học thuật, không trùng lặp với nghiên cứu trước</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select name="score_novelty" class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-16 h-9 mx-auto text-center font-semibold" required>
                                        <option value="" {{ old('score_novelty', $existingReview->score_novelty ?? '') == '' ? 'selected' : '' }}>--</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('score_novelty', $existingReview->score_novelty ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold" aria-hidden="true">2</span>
                                            <span class="sr-only">Tiêu chí 2</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Tính phù hợp với chủ đề hội thảo</h4>
                                            <p class="text-xs text-gray-600 leading-relaxed">Mức độ liên quan đến lĩnh vực, tiểu ban và mục tiêu của hội thảo</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select name="score_relevance" class="bg-blue-50 border border-blue-300 text-blue-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-16 h-9 mx-auto text-center font-semibold" required>
                                        <option value="" {{ old('score_relevance', $existingReview->score_relevance ?? '') == '' ? 'selected' : '' }}>--</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('score_relevance', $existingReview->score_relevance ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr class="hover:bg-purple-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold" aria-hidden="true">3</span>
                                            <span class="sr-only">Tiêu chí 3</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Chất lượng kỹ thuật / độ tin cậy</h4>
                                            <p class="text-xs text-gray-600 leading-relaxed">Phương pháp nghiên cứu, thiết kế thí nghiệm, độ tin cậy dữ liệu</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select name="score_technical_quality" class="bg-purple-50 border border-purple-300 text-purple-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-16 h-9 mx-auto text-center font-semibold" required>
                                        <option value="" {{ old('score_technical_quality', $existingReview->score_technical_quality ?? '') == '' ? 'selected' : '' }}>--</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('score_technical_quality', $existingReview->score_technical_quality ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr class="hover:bg-orange-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs font-semibold" aria-hidden="true">4</span>
                                            <span class="sr-only">Tiêu chí 4</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Cách trình bày</h4>
                                            <p class="text-xs text-gray-600 leading-relaxed">Bố cục, biểu đồ, hình ảnh, ngôn ngữ khoa học và tính dễ hiểu</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select name="score_presentation" class="bg-orange-50 border border-orange-300 text-orange-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-16 h-9 mx-auto text-center font-semibold" required>
                                        <option value="" {{ old('score_presentation', $existingReview->score_presentation ?? '') == '' ? 'selected' : '' }}>--</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('score_presentation', $existingReview->score_presentation ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr class="hover:bg-rose-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold" aria-hidden="true">5</span>
                                            <span class="sr-only">Tiêu chí 5</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Tài liệu tham khảo</h4>
                                            <p class="text-xs text-gray-600 leading-relaxed">Tính cập nhật, đúng chuẩn trích dẫn IEEE/APA, độ uy tín nguồn</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select name="score_references" class="bg-rose-50 border border-rose-300 text-rose-900 text-sm rounded-lg focus:ring-rose-500 focus:border-rose-500 block w-16 h-9 mx-auto text-center font-semibold" required>
                                        <option value="" {{ old('score_references', $existingReview->score_references ?? '') == '' ? 'selected' : '' }}>--</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('score_references', $existingReview->score_references ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg mb-8">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center space-x-3">
                    <!-- Comments icon -->
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    <span>B. Phần nhận xét chi tiết</span>
                </h2>
                <p class="text-sm text-green-700 mt-2 font-medium">Nhận xét chuyên môn chi tiết về bài báo</p>
            </div>

            <div class="p-6">
                <div class="relative">
                    <textarea name="detailed_comments"
                              rows="10"
                              class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500 resize-none"
                              required>{{ old('detailed_comments', $existingReview->detailed_comments ?? '') }}</textarea>
                    
                    <div class="mt-2 flex items-center space-x-2 text-xs text-gray-400">
                        <!-- Edit icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Nhận xét chuyên môn</span>
                    </div>
                </div>
                <p class="mt-3 text-sm text-gray-600 flex items-center space-x-1">
                    <!-- Info icon -->
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Nhập phản hồi chi tiết, xây dựng về nội dung, phương pháp và đóng góp của bài báo</span>
                </p>
            </div>
        </div>

        <!-- Recommendation Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg mb-8">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-yellow-50">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center space-x-3">
                    <!-- Recommendation icon -->
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                    <span>C. Khuyến nghị kết quả</span>
                </h2>
                <p class="text-sm text-orange-700 mt-2 font-medium">Quyết định cuối cùng về việc chấp nhận bài báo</p>
            </div>

            <div class="p-6">
                <!-- Recommendation Grid (2 columns x 4 rows) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Strong Accept -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="STRONG_ACCEPT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'STRONG_ACCEPT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-green-200 bg-green-50 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-100 hover:bg-green-100 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-green-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-green-900">Chấp nhận mạnh</h4>
                            </div>
                            <p class="text-xs text-green-700 leading-relaxed">Bài báo xuất sắc, đóng góp khoa học quan trọng</p>
                        </div>
                    </label>

                    <!-- Accept -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="ACCEPT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'ACCEPT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-blue-200 bg-blue-50 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-100 hover:bg-blue-100 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-blue-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-blue-900">Chấp nhận</h4>
                            </div>
                            <p class="text-xs text-blue-700 leading-relaxed">Bài báo đạt chất lượng tốt, đóng góp khoa học rõ ràng</p>
                        </div>
                    </label>

                    <!-- Weak Accept -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="WEAK_ACCEPT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'WEAK_ACCEPT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-yellow-200 bg-yellow-50 rounded-lg peer-checked:border-yellow-500 peer-checked:bg-yellow-100 hover:bg-yellow-100 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-yellow-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-yellow-900">Chấp nhận yếu</h4>
                            </div>
                            <p class="text-xs text-yellow-700 leading-relaxed">Bài báo có thể chấp nhận nhưng còn một số hạn chế</p>
                        </div>
                    </label>

                    <!-- Borderline -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="BORDERLINE" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'BORDERLINE' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-orange-200 bg-orange-50 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-100 hover:bg-orange-100 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-orange-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-orange-900">Biên giới</h4>
                            </div>
                            <p class="text-xs text-orange-700 leading-relaxed">Bài báo nằm ở ranh giới giữa chấp nhận và từ chối</p>
                        </div>
                    </label>

                    <!-- Weak Reject -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="WEAK_REJECT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'WEAK_REJECT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-red-200 bg-red-50 rounded-lg peer-checked:border-red-400 peer-checked:bg-red-100 hover:bg-red-100 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-red-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-red-800">Từ chối yếu</h4>
                            </div>
                            <p class="text-xs text-red-600 leading-relaxed">Bài báo có một số vấn đề cần khắc phục</p>
                        </div>
                    </label>

                    <!-- Reject -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="REJECT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'REJECT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-red-300 bg-red-100 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-200 hover:bg-red-200 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-red-200">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-red-900">Từ chối</h4>
                            </div>
                            <p class="text-xs text-red-700 leading-relaxed">Bài báo không phù hợp hoặc chất lượng chưa đạt yêu cầu</p>
                        </div>
                    </label>

                    <!-- Strong Reject -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="recommendation_code" value="STRONG_REJECT" class="sr-only peer" required {{ old('recommendation_code', $existingReview->recommendation_code ?? '') == 'STRONG_REJECT' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-700 bg-gray-100 rounded-lg peer-checked:border-gray-900 peer-checked:bg-gray-200 hover:bg-gray-200 transition-all duration-200 peer-checked:ring-2 peer-checked:ring-gray-300">
                            <div class="flex items-center space-x-3 mb-2">
                                <svg class="w-5 h-5 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-gray-900">Từ chối mạnh</h4>
                            </div>
                            <p class="text-xs text-gray-700 leading-relaxed">Bài báo có nhiều vấn đề nghiêm trọng, không phù hợp</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg mb-8">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-violet-50">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center space-x-3">
                    <!-- Upload icon -->
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <span>D. File phản biện bổ sung (tùy chọn)</span>
                </h2>
                <p class="text-sm text-purple-700 mt-2 font-medium">Tải lên file đánh giá chi tiết bằng Word hoặc PDF</p>
            </div>

            <div class="p-6">
                <div class="border-2 border-dashed border-purple-300 rounded-xl p-8 text-center bg-purple-25 hover:bg-purple-50 transition-colors duration-200">
                    <!-- Cloud upload icon -->
                    <svg class="mx-auto h-12 w-12 text-purple-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <div class="mb-4">
                        <label for="review_file" class="cursor-pointer">
                            <span class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200 shadow-md">
                                <!-- Upload button icon -->
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v12"></path>
                                </svg>
                                Chọn file để tải lên
                            </span>
                            <input id="review_file" name="review_file" type="file" accept=".doc,.docx,.pdf" class="sr-only">
                        </label>
                    </div>
                    <div class="text-xs text-gray-600">
                        <p class="font-medium">Định dạng hỗ trợ: Word (.doc, .docx) hoặc PDF</p>
                        <p class="text-xs text-gray-500 mt-1">Kích thước tối đa: 10MB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg">
            <div class="p-6">
                @if(!$existingReview || $existingReview->is_draft)
                <div class="flex justify-between items-center space-x-4">
                    <button type="button" onclick="saveDraft()" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">
                        <!-- Heroicons: Bookmark icon -->
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-3 7 3V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" />
                        </svg>
                        Lưu nháp
                    </button>
                    
                    <button type="button" onclick="submitFinal()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 me-2 mb-2 focus:outline-none">
                        <!-- Submit icon -->
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Gửi phản biện chính thức
                    </button>
                </div>
                @else
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Phản biện đã được gửi chính thức
                    </div>
                </div>
                @endif
                
                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <!-- Warning icon -->
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs text-amber-800">
                            <p class="font-medium mb-1">Lưu ý quan trọng:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-700">
                                <li><strong>Lưu nháp:</strong> Có thể chỉnh sửa và hoàn thiện sau</li>
                                <li><strong>Gửi chính thức:</strong> Không thể thay đổi sau khi gửi</li>
                                <li>Vui lòng kiểm tra kỹ trước khi gửi phản biện chính thức</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .score-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        transition: all 0.2s ease-in-out;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
    }
    
    .score-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: scale(1.02);
    }
    
    .bg-blue-25 { background-color: #eff6ff; }
    .bg-green-25 { background-color: #f0fdf4; }
    .bg-yellow-25 { background-color: #fefce8; }
    .bg-purple-25 { background-color: #faf5ff; }
    .bg-red-25 { background-color: #fef2f2; }
    
    /* Custom radio button styling */
    .recommendation-card input[type="radio"] {
        display: none;
    }
    
    .recommendation-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    .recommendation-card:hover {
        transform: translateY(-2px);
        shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .recommendation-card input[type="radio"]:checked + .card-content {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
    }
</style>

<script>
    // Hidden form field for draft/final submission
    const isDraftField = document.createElement('input');
    isDraftField.type = 'hidden';
    isDraftField.name = 'is_draft';
    isDraftField.value = '1';
    isDraftField.id = 'isDraftField';
    document.getElementById('reviewForm').appendChild(isDraftField);

    // File upload functionality with preview
    document.getElementById('review_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const uploadArea = this.closest('.border-dashed');
        
        if (file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileName = file.name;
            
            // Update upload area with file info
            uploadArea.innerHTML = `
                <div class="flex items-center space-x-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 text-left">
                        <p class="font-semibold text-green-800">${fileName}</p>
                        <p class="text-sm text-green-600">Kích thước: ${fileSize} MB</p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        }
    });

    // Clear file selection
    function clearFile() {
        document.getElementById('review_file').value = '';
        location.reload(); // Simple reload to restore original upload area
    }

    // Save draft function
    function saveDraft() {
        document.getElementById('isDraftField').value = '1';
        document.getElementById('reviewForm').submit();
    }

    // Submit final review function
    function submitFinal() {
        console.log('🔍 DEBUG: submitFinal() called');
        
        // Enhanced validation before submission
        const scores = ['score_relevance', 'score_novelty', 'score_technical_quality', 'score_presentation', 'score_references'];
        let hasEmptyScore = false;
        
        scores.forEach(score => {
            const select = document.querySelector(`select[name="${score}"]`);
            const value = select ? select.value : 'NOT_FOUND';
            console.log(`🔍 DEBUG: Checking ${score} = "${value}"`);
            
            if (value === '' || value < 1 || value > 10) {
                hasEmptyScore = true;
                if (select) {
                    select.style.borderColor = '#ef4444';
                    select.style.backgroundColor = '#fef2f2';
                }
            } else {
                if (select) {
                    select.style.borderColor = '#e5e7eb';
                    select.style.backgroundColor = 'white';
                }
            }
        });

        const recommendation = document.querySelector('input[name="recommendation_code"]:checked');
        const comments = document.querySelector('textarea[name="detailed_comments"]').value.trim();
        
        console.log(`🔍 DEBUG: hasEmptyScore = ${hasEmptyScore}`);
        console.log(`🔍 DEBUG: recommendation = ${recommendation ? recommendation.value : 'null'}`);
        console.log(`🔍 DEBUG: comments length = ${comments.length}`);
        
        if (hasEmptyScore) {
            console.log('🔍 DEBUG: Validation failed - empty scores');
            alert('⚠️ Vui lòng điền đầy đủ điểm số (1-10) cho tất cả các tiêu chí đánh giá.');
            return false;
        }
        
        if (!recommendation) {
            console.log('🔍 DEBUG: Validation failed - no recommendation');
            alert('⚠️ Vui lòng chọn khuyến nghị của bạn cho bài báo này.');
            return false;
        }
        
        if (comments.length < 50) {
            console.log('🔍 DEBUG: Validation failed - comments too short');
            alert('⚠️ Vui lòng nhập ít nhất 50 ký tự cho phần nhận xét chi tiết.');
            return false;
        }
        
        // Final confirmation
        const confirmMessage = `🔒 Xác nhận gửi phản biện chính thức\n\n` +
                              `Bạn có chắc chắn muốn gửi phản biện này không?\n\n` +
                              `⚠️ LưU Ý: Sau khi gửi, bạn sẽ KHÔNG THỂ chỉnh sửa nội dung phản biện.\n\n` +
                              `Vui lòng kiểm tra kỹ tất cả thông tin trước khi xác nhận.`;
        
        if (confirm(confirmMessage)) {
            console.log('🔍 DEBUG: User confirmed, submitting form...');
            document.getElementById('isDraftField').value = '0';
            console.log('🔍 DEBUG: Set is_draft to 0');
            console.log('🔍 DEBUG: About to submit form with ID: reviewForm');
            
            const form = document.getElementById('reviewForm');
            if (form) {
                console.log('🔍 DEBUG: Form found, submitting...');
                form.submit();
            } else {
                console.log('🔍 DEBUG: ERROR - Form not found!');
            }
        } else {
            console.log('🔍 DEBUG: User cancelled submission');
        }
    }

    // Real-time character counter for comments
    const commentsTextarea = document.querySelector('textarea[name="detailed_comments"]');
    if (commentsTextarea) {
        const charCounter = document.createElement('div');
        charCounter.className = 'text-xs text-gray-500 mt-2 text-right';
        commentsTextarea.parentNode.appendChild(charCounter);

        commentsTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = `${length} ký tự${length < 50 ? ' (tối thiểu 50 ký tự)' : ''}`;
            
            if (length < 50) {
                charCounter.className = 'text-xs text-red-500 mt-2 text-right font-medium';
            } else {
                charCounter.className = 'text-xs text-green-600 mt-2 text-right';
            }
        });

        // Trigger initial character count
        commentsTextarea.dispatchEvent(new Event('input'));
    }

    // Visual feedback for score selection
    document.querySelectorAll('select[name^="score_"]').forEach(select => {
        select.addEventListener('change', function() {
            const value = parseInt(this.value);
            if (value >= 1 && value <= 10) {
                this.style.backgroundColor = getScoreColor(value);
                this.style.borderColor = getScoreBorderColor(value);
                this.style.color = value <= 4 ? '#dc2626' : value <= 7 ? '#f59e0b' : '#059669';
            }
        });
    });

    function getScoreColor(score) {
        if (score <= 4) return '#fef2f2'; // Red background
        if (score <= 7) return '#fffbeb'; // Yellow background
        return '#f0fdf4'; // Green background
    }

    function getScoreBorderColor(score) {
        if (score <= 4) return '#ef4444'; // Red border
        if (score <= 7) return '#f59e0b'; // Yellow border  
        return '#10b981'; // Green border
    }
</script>

@endsection