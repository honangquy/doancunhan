@extends('layouts.chair')

@section('title', 'Debug Paper Data')

@section('content')
<div class="container">
    <h1>Debug Paper #{{ $paper->paper_id ?? 'N/A' }}</h1>
    
    <h2>Review Stats:</h2>
    <pre>{{ json_encode($reviewStats ?? [], JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Average Scores:</h2>
    <pre>{{ json_encode($averageScores ?? [], JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Assignments Count:</h2>
    <pre>{{ $assignments->count() ?? 0 }}</pre>
    
    <h2>Completed Reviews Count:</h2>
    <pre>{{ $completedReviews->count() ?? 0 }}</pre>
    
    <h2>Reviews Count:</h2>
    <pre>{{ $reviews->count() ?? 0 }}</pre>
</div>
@endsection