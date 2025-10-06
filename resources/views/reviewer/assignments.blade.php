@extends('layouts.reviewer')

@section('title', 'Review Assignments')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Review Assignments</h1>
        <p class="mt-2 text-gray-600">Manage your assigned paper reviews</p>
    </div>

    <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

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

        @if(session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
            {{ session('info') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Total Assignments</div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Pending Response</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Accepted</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['accepted'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Completed</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Assignments</h2>
            </div>

            @if($assignments->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-4 text-lg text-gray-600">No assignments yet</p>
                <p class="mt-2 text-sm text-gray-500">You will see your review assignments here once they are assigned by the conference chair.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paper</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                        <tr class="hover:bg-gray-50" x-data="{ showDetails: false }">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $assignment->paper_title }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <button @click="showDetails = !showDetails" class="text-blue-600 hover:text-blue-800">
                                        <span x-show="!showDetails">Show abstract ▼</span>
                                        <span x-show="showDetails">Hide abstract ▲</span>
                                    </button>
                                </div>
                                <div x-show="showDetails" class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                    {{ Str::limit($assignment->abstract, 200) }}
                                    @if($assignment->keywords)
                                    <div class="mt-2">
                                        <span class="font-medium">Keywords:</span> {{ $assignment->keywords }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $assignment->conference_name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($assignment->status_code === 'INVITED')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending Response
                                    </span>
                                @elseif($assignment->status_code === 'ACCEPTED')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Accepted
                                    </span>
                                @elseif($assignment->status_code === 'COMPLETED')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($assignment->status_code === 'DECLINED')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Declined
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $deadline = \Carbon\Carbon::parse($assignment->deadline);
                                    $now = \Carbon\Carbon::now();
                                    $daysLeft = $now->diffInDays($deadline, false);
                                @endphp
                                <div class="text-gray-900 font-medium">{{ $deadline->format('M d, Y') }}</div>
                                @if($assignment->status_code !== 'COMPLETED' && $assignment->status_code !== 'DECLINED')
                                    @if($daysLeft < 0)
                                        <div class="text-xs text-red-600 font-medium">Overdue</div>
                                    @elseif($daysLeft <= 3)
                                        <div class="text-xs text-red-600 font-medium">{{ abs($daysLeft) }} days left</div>
                                    @elseif($daysLeft <= 7)
                                        <div class="text-xs text-yellow-600">{{ $daysLeft }} days left</div>
                                    @else
                                        <div class="text-xs text-gray-500">{{ $daysLeft }} days left</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-2">
                                    @if($assignment->status_code === 'INVITED')
                                        <form action="{{ route('reviewer.assignments.accept', $assignment->assignment_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-full text-center px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                                Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('reviewer.assignments.decline', $assignment->assignment_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure you want to decline this assignment?')" class="w-full text-center px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                                Decline
                                            </button>
                                        </form>
                                    @elseif($assignment->status_code === 'ACCEPTED')
                                        @if($assignment->review_id)
                                            <a href="{{ route('reviewer.reviews.show', $assignment->review_id) }}" class="text-center px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                                View Review
                                            </a>
                                        @else
                                            <a href="{{ route('reviewer.reviews.create', $assignment->assignment_id) }}" class="text-center px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                                Start Review
                                            </a>
                                        @endif
                                        <a href="{{ route('reviewer.papers.download', $assignment->assignment_id) }}" class="text-center px-3 py-1.5 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                                            Download PDF
                                        </a>
                                    @elseif($assignment->status_code === 'COMPLETED')
                                        <a href="{{ route('reviewer.reviews.show', $assignment->review_id) }}" class="text-center px-3 py-1.5 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                                            View Review
                                        </a>
                                        <a href="{{ route('reviewer.papers.download', $assignment->assignment_id) }}" class="text-center px-3 py-1.5 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                                            Download PDF
                                        </a>
                                    @endif
                                </div>
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
