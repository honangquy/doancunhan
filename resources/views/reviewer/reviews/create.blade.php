<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.favicon')
    <title>Submit Review - Conference Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-800 via-purple-700 to-purple-600 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('reviewer.dashboard') }}" class="flex items-center space-x-3 hover:opacity-90 transition">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-purple-700 font-bold text-xl">H</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">HUIT Conferences</div>
                        <div class="text-xs text-purple-200">Reviewer Dashboard</div>
                    </div>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('reviewer.dashboard') }}" class="hover:text-purple-100">Dashboard</a>
                    <a href="{{ route('reviewer.assignments') }}" class="hover:text-purple-100">Assignments</a>
                    <a href="{{ route('reviewer.reviews') }}" class="hover:text-purple-100">My Reviews</a>
                    <div class="h-6 w-px bg-purple-400"></div>
                    <span class="font-medium">{{ Auth::user()->full_name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-200">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('reviewer.assignments') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Assignments
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Submit Review</h1>
            <p class="mt-2 text-gray-600">Review this paper and provide your recommendation</p>
        </div>

        <!-- Alerts -->
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            {{ session('warning') }}
        </div>
        @endif

        <!-- Paper Information -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Paper Information</h2>
            </div>
            <div class="px-6 py-6">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $assignment->title }}</h3>
                    <p class="text-sm text-gray-600">Conference: {{ $assignment->conference_name }}</p>
                    @php
                        $deadline = \Carbon\Carbon::parse($assignment->deadline);
                        $daysLeft = \Carbon\Carbon::now()->diffInDays($deadline, false);
                    @endphp
                    <p class="text-sm mt-1">
                        <span class="text-gray-600">Deadline:</span>
                        <span class="font-medium {{ $daysLeft <= 3 ? 'text-red-600' : ($daysLeft <= 7 ? 'text-yellow-600' : 'text-gray-900') }}">
                            {{ $deadline->format('M d, Y') }} ({{ abs($daysLeft) }} days {{ $daysLeft >= 0 ? 'left' : 'overdue' }})
                        </span>
                    </p>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Abstract</h4>
                    <p class="text-gray-700 leading-relaxed">{{ $assignment->abstract }}</p>
                </div>

                @if($assignment->keywords)
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Keywords</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $assignment->keywords) as $keyword)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ trim($keyword) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Authors</h4>
                    <div class="space-y-2">
                        @foreach($authors as $author)
                        <div class="flex items-center">
                            <span class="text-gray-700">
                                {{ $author->author_order }}. {{ $author->full_name }}
                                @if($author->organization)
                                    <span class="text-gray-500 text-sm">({{ $author->organization }})</span>
                                @endif
                                @if($author->is_contact)
                                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded">Contact Author</span>
                                @endif
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($assignment->file_path)
                <div>
                    <a href="{{ route('reviewer.papers.download', $assignment->assignment_id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Full Paper (PDF)
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Review Form -->
        <form action="{{ route('reviewer.reviews.store') }}" method="POST" class="bg-white rounded-lg shadow">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $assignment->assignment_id }}">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Your Review</h2>
            </div>

            <div class="px-6 py-6 space-y-6">
                <!-- Overall Score -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Overall Score <span class="text-red-600">*</span>
                    </label>
                    <p class="text-sm text-gray-600 mb-3">Rate this paper on a scale of 1-10 (1 = Poor, 10 = Excellent)</p>
                    <div class="flex items-center space-x-4" x-data="{ score: {{ old('score', 5) }} }">
                        <input type="range" name="score" min="1" max="10" x-model="score" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex items-center justify-center w-16 h-16 rounded-lg font-bold text-2xl"
                             :class="{
                                 'bg-red-100 text-red-800': score <= 3,
                                 'bg-yellow-100 text-yellow-800': score > 3 && score <= 6,
                                 'bg-green-100 text-green-800': score > 6
                             }">
                            <span x-text="score"></span>
                        </div>
                    </div>
                    @error('score')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recommendation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Recommendation <span class="text-red-600">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition hover:border-blue-500 {{ old('recommendation_code') === 'ACCEPT' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                            <input type="radio" name="recommendation_code" value="ACCEPT" class="mt-1 mr-3" {{ old('recommendation_code') === 'ACCEPT' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Accept</div>
                                <div class="text-sm text-gray-600">Paper is ready for publication as is</div>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition hover:border-blue-500 {{ old('recommendation_code') === 'MINOR_REVISION' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="recommendation_code" value="MINOR_REVISION" class="mt-1 mr-3" {{ old('recommendation_code') === 'MINOR_REVISION' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Minor Revision</div>
                                <div class="text-sm text-gray-600">Small changes needed before acceptance</div>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition hover:border-blue-500 {{ old('recommendation_code') === 'MAJOR_REVISION' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' }}">
                            <input type="radio" name="recommendation_code" value="MAJOR_REVISION" class="mt-1 mr-3" {{ old('recommendation_code') === 'MAJOR_REVISION' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Major Revision</div>
                                <div class="text-sm text-gray-600">Significant changes required</div>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition hover:border-blue-500 {{ old('recommendation_code') === 'REJECT' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" name="recommendation_code" value="REJECT" class="mt-1 mr-3" {{ old('recommendation_code') === 'REJECT' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Reject</div>
                                <div class="text-sm text-gray-600">Paper not suitable for publication</div>
                            </div>
                        </label>
                    </div>
                    @error('recommendation_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comments for Authors -->
                <div>
                    <label for="comment_author" class="block text-sm font-medium text-gray-700 mb-2">
                        Comments for Authors <span class="text-red-600">*</span>
                    </label>
                    <p class="text-sm text-gray-600 mb-3">
                        Provide detailed feedback to help authors improve their paper. These comments will be shared with the authors.
                        Minimum 50 characters required.
                    </p>
                    <textarea 
                        id="comment_author" 
                        name="comment_author" 
                        rows="8" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Discuss the paper's strengths, weaknesses, technical quality, originality, clarity, and relevance to the conference..."
                    >{{ old('comment_author') }}</textarea>
                    <div class="mt-2 text-sm text-gray-500" x-data="{ count: {{ strlen(old('comment_author', '')) }} }">
                        <span x-text="count"></span> characters (minimum 50 required)
                    </div>
                    @error('comment_author')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confidential Comments for Chair -->
                <div>
                    <label for="comment_chair" class="block text-sm font-medium text-gray-700 mb-2">
                        Confidential Comments for Conference Chair (Optional)
                    </label>
                    <p class="text-sm text-gray-600 mb-3">
                        These comments will only be visible to the conference chair and will not be shared with authors.
                    </p>
                    <textarea 
                        id="comment_chair" 
                        name="comment_chair" 
                        rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Any additional concerns, conflicts of interest, or recommendations for the chair..."
                    >{{ old('comment_chair') }}</textarea>
                    @error('comment_chair')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Guidelines -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">Review Guidelines</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Evaluate technical quality, originality, and relevance</li>
                        <li>Be constructive and provide specific feedback</li>
                        <li>Support your recommendation with clear reasoning</li>
                        <li>Maintain confidentiality and avoid conflicts of interest</li>
                        <li>Complete your review before the deadline</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('reviewer.assignments') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Submit Review
                </button>
            </div>
        </form>
    </div>

    <script>
        // Character counter for comments
        document.getElementById('comment_author').addEventListener('input', function(e) {
            const count = e.target.value.length;
            const countEl = e.target.parentElement.querySelector('[x-data] span');
            if (countEl) {
                countEl.textContent = count;
            }
        });
    </script>
</body>
</html>
