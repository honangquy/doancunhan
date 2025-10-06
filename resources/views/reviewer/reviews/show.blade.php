@extends('layouts.reviewer')

@section('title', 'View Review')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('reviewer.reviews') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Back to My Reviews
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Review Details</h1>
        <p class="mt-2 text-gray-600">View your submitted review</p>
    </div>

    <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Paper Information -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Paper Information</h2>
            </div>
            <div class="px-6 py-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $review->paper_title }}</h3>
                
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Conference</div>
                        <div class="font-medium text-gray-900">{{ $review->conference_name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Review Deadline</div>
                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($review->deadline)->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Assigned Date</div>
                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($review->assigned_at)->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Submitted Date</div>
                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($review->submitted_at)->format('M d, Y H:i') }}</div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="text-sm text-gray-600 mb-2">Abstract</div>
                    <p class="text-gray-700 leading-relaxed">{{ $review->abstract }}</p>
                </div>

                @if($review->keywords)
                <div class="mb-6">
                    <div class="text-sm text-gray-600 mb-2">Keywords</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $review->keywords) as $keyword)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ trim($keyword) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($review->file_path)
                <div>
                    <a href="{{ route('reviewer.papers.download', $review->assignment_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Paper PDF
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Review Details -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Your Review</h2>
            </div>
            <div class="px-6 py-6">
                <!-- Score and Recommendation -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <div class="text-sm text-gray-600 mb-2">Overall Score</div>
                        <div class="flex items-center">
                            <div class="text-4xl font-bold {{ $review->score <= 3 ? 'text-red-600' : ($review->score <= 6 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $review->score }}
                            </div>
                            <div class="text-xl text-gray-500 ml-2">/10</div>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $review->score <= 3 ? 'bg-red-600' : ($review->score <= 6 ? 'bg-yellow-600' : 'bg-green-600') }}" 
                                 style="width: {{ $review->score * 10 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-2">Recommendation</div>
                        <div class="mt-2">
                            @if($review->recommendation_code === 'ACCEPT')
                                <span class="inline-flex px-4 py-2 text-base font-semibold rounded-lg bg-green-100 text-green-800">
                                    ✓ Accept
                                </span>
                            @elseif($review->recommendation_code === 'MINOR_REVISION')
                                <span class="inline-flex px-4 py-2 text-base font-semibold rounded-lg bg-blue-100 text-blue-800">
                                    Minor Revision Required
                                </span>
                            @elseif($review->recommendation_code === 'MAJOR_REVISION')
                                <span class="inline-flex px-4 py-2 text-base font-semibold rounded-lg bg-yellow-100 text-yellow-800">
                                    Major Revision Required
                                </span>
                            @elseif($review->recommendation_code === 'REJECT')
                                <span class="inline-flex px-4 py-2 text-base font-semibold rounded-lg bg-red-100 text-red-800">
                                    ✗ Reject
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comments for Authors -->
                <div class="mb-6">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900">Comments for Authors</h3>
                        <span class="ml-2 text-xs text-gray-500">(Visible to authors)</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $review->comment_author }}</p>
                    </div>
                </div>

                <!-- Confidential Comments -->
                @if($review->comment_chair)
                <div>
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900">Confidential Comments for Chair</h3>
                        <span class="ml-2 text-xs text-red-600">(Not visible to authors)</span>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <p class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $review->comment_chair }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
            <a href="{{ route('reviewer.reviews') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                ← Back to My Reviews
            </a>
        </div>
@endsection
