@extends('layouts.chair')

@section('title', 'Quản lý phân công phản biện')

@section('content')
<div x-data="chairAssignmentApp()" class="space-y-8 animate-fadeIn">
    
    <!-- Conference Selection -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Chọn hội thảo</h2>
                <p class="text-sm text-gray-600">Chọn hội thảo để xem danh sách bài báo và quản lý phân công</p>
            </div>
            <div class="flex items-center space-x-4">
                <select x-model="selectedConference" @change="onConferenceChange()" 
                        class="w-80 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">-- Chọn hội thảo --</option>
                    <template x-for="conf in conferences" :key="conf.conference_id">
                        <option :value="conf.conference_id" x-text="conf.title"></option>
                    </template>
                </select>
                <div x-show="selectedConference" class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Đã chọn hội thảo</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Header -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quản lý phân công phản biện</h1>
                <p class="text-gray-600 mt-1">Xem thống kê bidding và phân công reviewer cho các bài báo đã được nộp</p>
            </div>
            <div class="flex space-x-4">
                <button @click="refreshData()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <!-- Refresh Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <button @click="openAutoAssignModal()" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-colors duration-200">
                    Tự động phân công
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tổng bài báo</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistics.total_papers || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Đã phân công</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistics.papers_with_assignments || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tổng reviewer</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistics.total_bidders || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">COI declarations</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="statistics.coi_declarations || 0"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold mb-4">Bộ lọc</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái phân công</label>
                <select x-model="filters.assignment_status" @change="applyFilters()" 
                        class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="assigned">Đã phân công</option>
                    <option value="unassigned">Chưa phân công</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số bidder tối thiểu</label>
                <input type="number" x-model="filters.min_bidders" @input="applyFilters()" min="0" 
                       class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm bài báo</label>
                <input type="text" x-model="filters.search" @input="applyFilters()" placeholder="Tên bài báo..." 
                       class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Papers List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Danh sách bài báo</h3>
        </div>
        
        <div x-show="conferences.length === 0" class="p-8 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có quyền quản lý hội thảo</h3>
            <p class="text-gray-500">Bạn chưa được phân quyền Chair cho hội thảo nào. Vui lòng liên hệ quản trị viên để được cấp quyền.</p>
        </div>
        
        <div x-show="conferences.length > 0 && !selectedConference" class="p-8 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa chọn hội thảo</h3>
            <p class="text-gray-500">Vui lòng chọn hội thảo ở phía trên để xem danh sách bài báo và quản lý phân công phản biện</p>
        </div>
        
        <div x-show="conferences.length > 0 && selectedConference && loading" class="p-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
            <p class="mt-2 text-gray-600">Đang tải dữ liệu...</p>
        </div>

        <div x-show="conferences.length > 0 && selectedConference && !loading && filteredPapers.length === 0" class="p-8 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có bài báo nào</h3>
            <p class="text-gray-500">Chưa có bài báo nào được nộp cho hội thảo này hoặc không có bài nào phù hợp với bộ lọc</p>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <strong>Debug Info:</strong><br>
                    Conference ID: <span x-text="selectedConference"></span><br>
                    Total Papers: <span x-text="papers.length"></span><br>
                    Filtered Papers: <span x-text="filteredPapers.length"></span><br>
                    <strong>Quick Test:</strong><br>
                    <button @click="selectedConference = 8; onConferenceChange()" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 mr-2">
                        Test Conference ID 8
                    </button>
                    <button @click="console.log('Current state:', {conferences, selectedConference, papers, filteredPapers})" class="mt-2 px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                        Log State
                    </button>
                </p>
            </div>
        </div>

        <div x-show="conferences.length > 0 && selectedConference && !loading && filteredPapers.length > 0" class="divide-y divide-gray-200">
            <template x-for="paper in paginatedPapers" :key="paper.paper_id">
                <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-lg font-medium text-gray-900 truncate" x-text="paper.title"></h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Tác giả: <span x-text="paper.submitted_by_name"></span>
                            </p>
                            
                            <!-- Bidding Statistics -->
                            <div class="flex flex-wrap items-center gap-4 mt-3">
                                <div class="flex items-center text-sm">
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    <span x-text="paper.total_bidders + ' bidders'"></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    <span x-text="'Avg bid: ' + (paper.avg_bid ? parseFloat(paper.avg_bid).toFixed(1) : '0')"></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="inline-block w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                    <span x-text="paper.assigned_reviewers + ' assigned'"></span>
                                </div>
                                <div x-show="paper.coi_count > 0" class="flex items-center text-sm">
                                    <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    <span x-text="paper.coi_count + ' COI'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 ml-4">
                            <button @click="viewPaperDetails(paper.paper_id)" 
                                    class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                Chi tiết
                            </button>
                            <button @click="openManualAssignModal(paper.paper_id)" 
                                    class="px-4 py-2 text-sm bg-primary text-white rounded-lg hover:bg-accent transition-colors duration-200">
                                Phân công
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div x-show="conferences.length > 0 && selectedConference && !loading && filteredPapers.length > pageSize" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Hiển thị <span x-text="((currentPage - 1) * pageSize) + 1"></span> đến 
                    <span x-text="Math.min(currentPage * pageSize, filteredPapers.length)"></span> 
                    trong tổng số <span x-text="filteredPapers.length"></span> bài báo
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="currentPage--" :disabled="currentPage <= 1" 
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded disabled:opacity-50">
                        Trước
                    </button>
                    <span x-text="'Trang ' + currentPage + ' / ' + totalPages" class="text-sm text-gray-700"></span>
                    <button @click="currentPage++" :disabled="currentPage >= totalPages" 
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded disabled:opacity-50">
                        Sau
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Paper Details Modal -->
    <div x-show="showPaperModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Chi tiết bidding - <span x-text="selectedPaper?.title"></span></h3>
                    <button @click="closePaperModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div x-show="loadingBiddings" class="p-8 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                    <p class="mt-2 text-gray-600">Đang tải dữ liệu bidding...</p>
                </div>

                <div x-show="!loadingBiddings" class="space-y-4">
                    <!-- Bidding List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bid Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="bidding in paperBiddings" :key="bidding.id">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900" x-text="bidding.full_name"></div>
                                                <div class="text-sm text-gray-500" x-text="bidding.email"></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getBidColorClass(bidding.bidding_value)" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  x-text="getBidLabel(bidding.bidding_value)">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span x-show="bidding.coi" class="text-red-600 text-sm">
                                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                COI
                                            </span>
                                            <span x-show="!bidding.coi" class="text-green-600 text-sm">OK</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span x-show="bidding.is_assigned" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Đã phân công
                                            </span>
                                            <span x-show="!bidding.is_assigned" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Chưa phân công
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button x-show="!bidding.is_assigned && !bidding.coi" 
                                                    @click="assignSingleReviewer(selectedPaper.paper_id, bidding.user_id)"
                                                    class="text-primary hover:text-accent">
                                                Phân công
                                            </button>
                                            <button x-show="bidding.is_assigned" 
                                                    @click="removeAssignment(bidding.assignment_id)"
                                                    class="text-red-600 hover:text-red-900">
                                                Xóa
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Assignment Modal -->
    <div x-show="showManualAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Phân công thủ công</h3>
                    <button @click="closeManualAssignModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitManualAssignment()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn reviewers</label>
                            <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                <template x-for="bidding in availableReviewers" :key="bidding.user_id">
                                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                                        <input type="checkbox" :value="bidding.user_id" 
                                               x-model="manualAssignment.reviewer_ids"
                                               :disabled="bidding.coi"
                                               class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900" x-text="bidding.full_name"></div>
                                            <div class="text-xs text-gray-500">
                                                Bid: <span :class="getBidColorClass(bidding.bidding_value)" 
                                                          x-text="getBidLabel(bidding.bidding_value)"></span>
                                                <span x-show="bidding.coi" class="text-red-600 ml-2">(COI)</span>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeManualAssignModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Hủy
                        </button>
                        <button type="submit" :disabled="manualAssignment.reviewer_ids.length === 0"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-accent disabled:opacity-50">
                            Phân công
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Auto Assignment Modal -->
    <div x-show="showAutoAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Tự động phân công</h3>
                    <button @click="closeAutoAssignModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitAutoAssignment()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số reviewer mỗi bài</label>
                            <input type="number" x-model="autoAssignment.reviewer_count" min="1" max="5" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bid tối thiểu</label>
                            <select x-model="autoAssignment.min_bid" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="0">Không ưu tiên (0)</option>
                                <option value="1">Sẵn sàng (1)</option>
                                <option value="2">Có thể (2)</option>
                                <option value="3">Rất muốn (3)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeAutoAssignModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Hủy
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-accent">
                            Bắt đầu tự động phân công
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function chairAssignmentApp() {
    return {
        // Conference Data
        conferences: @json($conferences ?? []),
        selectedConference: @json($selectedConference ?? ''),
        
        // Data
        papers: [],
        filteredPapers: [],
        statistics: {},
        selectedPaper: null,
        paperBiddings: [],
        availableReviewers: [],
        
        // UI State
        loading: true,
        loadingBiddings: false,
        showPaperModal: false,
        showManualAssignModal: false,
        showAutoAssignModal: false,
        
        // Pagination
        currentPage: 1,
        pageSize: 10,
        
        // Filters
        filters: {
            assignment_status: '',
            min_bidders: '',
            search: ''
        },
        
        // Forms
        manualAssignment: {
            paper_id: null,
            reviewer_ids: []
        },
        
        autoAssignment: {
            reviewer_count: 3,
            min_bid: 1
        },
        
        // Computed
        get totalPages() {
            return Math.ceil(this.filteredPapers.length / this.pageSize);
        },
        
        get paginatedPapers() {
            const start = (this.currentPage - 1) * this.pageSize;
            const end = start + this.pageSize;
            return this.filteredPapers.slice(start, end);
        },
        
        // Methods
        async init() {
            console.log('Chair Assignment App Initialized');
            console.log('Conferences available:', this.conferences);
            console.log('Selected conference:', this.selectedConference);
            
            // If no conference selected but conferences available, select first one
            if (!this.selectedConference && this.conferences.length > 0) {
                this.selectedConference = this.conferences[0].conference_id;
                console.log('Auto-selected first conference:', this.selectedConference);
            }
            
            if (this.selectedConference) {
                await this.loadStatistics();
                await this.loadPapers();
            }
            this.loading = false;
        },
        
        async onConferenceChange() {
            if (!this.selectedConference) {
                this.papers = [];
                this.filteredPapers = [];
                this.statistics = {};
                return;
            }
            
            this.loading = true;
            await this.loadStatistics();
            await this.loadPapers();
            this.loading = false;
        },
        
        async loadStatistics() {
            if (!this.selectedConference) return;
            
            console.log('Loading statistics for conference:', this.selectedConference);
            
            try {
                const response = await fetch(`/chair/assignments/statistics/${this.selectedConference}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                console.log('Statistics response status:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Statistics data received:', data);
                    this.statistics = data.statistics || {};
                } else {
                    const errorText = await response.text();
                    console.error('Statistics error response:', errorText);
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        },
        
        async loadPapers() {
            if (!this.selectedConference) return;
            
            console.log('Loading papers for conference:', this.selectedConference);
            
            try {
                const response = await fetch(`/chair/assignments/papers/${this.selectedConference}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                console.log('Papers response status:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Papers API Response:', data);
                    console.log('Papers received:', data.papers);
                    console.log('Papers count:', data.papers ? data.papers.length : 0);
                    
                    this.papers = data.papers || [];
                    console.log('Papers set to:', this.papers);
                    console.log('Papers length after set:', this.papers.length);
                    
                    this.applyFilters();
                    
                    console.log('Filtered papers after applyFilters:', this.filteredPapers);
                } else {
                    const errorText = await response.text();
                    console.error('Error response status:', response.status);
                    console.error('Error response text:', errorText);
                }
            } catch (error) {
                console.error('Error loading papers:', error);
                console.error('Error stack:', error.stack);
            }
        },
        
        async refreshData() {
            this.loading = true;
            await this.loadStatistics();
            await this.loadPapers();
            this.loading = false;
        },
        
        applyFilters() {
            let filtered = [...this.papers];
            
            // Filter by assignment status
            if (this.filters.assignment_status === 'assigned') {
                filtered = filtered.filter(p => p.assigned_reviewers > 0);
            } else if (this.filters.assignment_status === 'unassigned') {
                filtered = filtered.filter(p => p.assigned_reviewers === 0);
            }
            
            // Filter by minimum bidders
            if (this.filters.min_bidders) {
                const minBidders = parseInt(this.filters.min_bidders);
                filtered = filtered.filter(p => p.total_bidders >= minBidders);
            }
            
            // Filter by search term
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(p => 
                    p.title.toLowerCase().includes(search) ||
                    p.submitted_by_name.toLowerCase().includes(search)
                );
            }
            
            this.filteredPapers = filtered;
            this.currentPage = 1;
        },
        
        async viewPaperDetails(paperId) {
            this.selectedPaper = this.papers.find(p => p.paper_id === paperId);
            this.showPaperModal = true;
            this.loadingBiddings = true;
            
            try {
                const response = await fetch(`/chair/assignments/paper/${paperId}/biddings`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.paperBiddings = data.biddings;
                }
            } catch (error) {
                console.error('Error loading paper biddings:', error);
            } finally {
                this.loadingBiddings = false;
            }
        },
        
        closePaperModal() {
            this.showPaperModal = false;
            this.selectedPaper = null;
            this.paperBiddings = [];
        },
        
        openManualAssignModal(paperId) {
            this.manualAssignment.paper_id = paperId;
            this.manualAssignment.reviewer_ids = [];
            
            // Get available reviewers for this paper
            this.availableReviewers = this.paperBiddings.filter(b => !b.is_assigned);
            this.showManualAssignModal = true;
        },
        
        closeManualAssignModal() {
            this.showManualAssignModal = false;
            this.manualAssignment = { paper_id: null, reviewer_ids: [] };
        },
        
        async submitManualAssignment() {
            try {
                const response = await fetch('/chair/assignments/manual-assign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.manualAssignment)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.closeManualAssignModal();
                    await this.refreshData();
                    this.showNotification('Phân công thành công!', 'success');
                } else {
                    this.showNotification(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error in manual assignment:', error);
                this.showNotification('Có lỗi xảy ra khi phân công', 'error');
            }
        },
        
        openAutoAssignModal() {
            this.showAutoAssignModal = true;
        },
        
        closeAutoAssignModal() {
            this.showAutoAssignModal = false;
        },
        
        async submitAutoAssignment() {
            try {
                // Process each unassigned paper
                const unassignedPapers = this.papers.filter(p => p.assigned_reviewers === 0);
                
                for (const paper of unassignedPapers) {
                    const assignmentData = {
                        paper_id: paper.paper_id,
                        reviewer_count: this.autoAssignment.reviewer_count,
                        min_bid: this.autoAssignment.min_bid
                    };
                    
                    const response = await fetch('/chair/assignments/auto-assign', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(assignmentData)
                    });
                    
                    const data = await response.json();
                    if (!data.success) {
                        console.warn(`Failed to auto-assign paper ${paper.paper_id}:`, data.message);
                    }
                }
                
                this.closeAutoAssignModal();
                await this.refreshData();
                this.showNotification('Hoàn thành tự động phân công!', 'success');
            } catch (error) {
                console.error('Error in auto assignment:', error);
                this.showNotification('Có lỗi xảy ra khi tự động phân công', 'error');
            }
        },
        
        async assignSingleReviewer(paperId, reviewerId) {
            try {
                const response = await fetch('/chair/assignments/manual-assign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        paper_id: paperId,
                        reviewer_ids: [reviewerId]
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await this.viewPaperDetails(paperId); // Refresh modal data
                    await this.refreshData(); // Refresh main data
                    this.showNotification('Phân công thành công!', 'success');
                } else {
                    this.showNotification(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error assigning single reviewer:', error);
                this.showNotification('Có lỗi xảy ra', 'error');
            }
        },
        
        async removeAssignment(assignmentId) {
            if (!confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
                return;
            }
            
            try {
                const response = await fetch(`/chair/assignments/${assignmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await this.viewPaperDetails(this.selectedPaper.paper_id); // Refresh modal data
                    await this.refreshData(); // Refresh main data
                    this.showNotification('Đã xóa phân công thành công!', 'success');
                } else {
                    this.showNotification(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error removing assignment:', error);
                this.showNotification('Có lỗi xảy ra', 'error');
            }
        },
        
        // Helper methods
        getBidLabel(value) {
            const labels = {
                0: 'Không ưu tiên',
                1: 'Sẵn sàng',
                2: 'Có thể',
                3: 'Rất muốn'
            };
            return labels[value] || 'Unknown';
        },
        
        getBidColorClass(value) {
            const classes = {
                0: 'bg-gray-100 text-gray-800',
                1: 'bg-yellow-100 text-yellow-800',
                2: 'bg-blue-100 text-blue-800',
                3: 'bg-green-100 text-green-800'
            };
            return classes[value] || 'bg-gray-100 text-gray-800';
        },
        
        showNotification(message, type) {
            // Simple notification - you can replace with a proper notification system
            if (type === 'success') {
                alert('✅ ' + message);
            } else {
                alert('❌ ' + message);
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
