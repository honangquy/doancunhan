@extends('layouts.reviewer')

@section('title', 'My Reviews')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Reviews</h1>
        <p class="mt-2 text-gray-600">View all reviews you have submitted</p>
    </div>

    <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Total Reviews</div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Average Score</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['average_score'] }}</div>
                <div class="text-xs text-gray-500 mt-1">out of 10</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Accepted</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['accept'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Rejected</div>
                <div class="text-3xl font-bold text-red-600">{{ $stats['reject'] }}</div>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Reviews</h2>
            </div>

            @if($reviews->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-4 text-lg text-gray-600">No reviews yet</p>
                <p class="mt-2 text-sm text-gray-500">Reviews you submit will appear here.</p>
                <a href="{{ route('reviewer.assignments.index') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    View Assignments
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommendation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $review->paper_title }}</div>
                                <div class="text-xs text-gray-500 mt-1">Paper Status: 
                                    @if($review->paper_status === 'ACCEPTED')
                                        <span class="text-green-600 font-medium">Accepted</span>
                                    @elseif($review->paper_status === 'REJECTED')
                                        <span class="text-red-600 font-medium">Rejected</span>
                                    @elseif($review->paper_status === 'REVISION')
                                        <span class="text-yellow-600 font-medium">Revision</span>
                                    @else
                                        <span class="text-blue-600 font-medium">{{ $review->paper_status }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $review->conference_name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold {{ ($review->total_score ?? 0) <= 3 ? 'text-red-600' : (($review->total_score ?? 0) <= 6 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($review->total_score ?? 0, 1) }}
                                    </div>
                                    <div class="text-sm text-gray-500 ml-1">/10</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($review->recommendation_code, ['ACCEPT', 'STRONG_ACCEPT']))
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $review->recommendation_code === 'STRONG_ACCEPT' ? 'Chấp nhận mạnh' : 'Chấp nhận' }}
                                    </span>
                                @elseif($review->recommendation_code === 'WEAK_ACCEPT')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700">
                                        Chấp nhận yếu
                                    </span>
                                @elseif($review->recommendation_code === 'BORDERLINE')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Biên giới
                                    </span>
                                @elseif($review->recommendation_code === 'WEAK_REJECT')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-700">
                                        Từ chối yếu
                                    </span>
                                @elseif(in_array($review->recommendation_code, ['REJECT', 'STRONG_REJECT']))
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $review->recommendation_code === 'STRONG_REJECT' ? 'Từ chối mạnh' : 'Từ chối' }}
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $review->recommendation_code ?? 'Chưa xác định' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($review->submitted_at)->format('M d, Y') }}
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($review->submitted_at)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('reviewer.reviews.show', $review->review_id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
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
@endsection
