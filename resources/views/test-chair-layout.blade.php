@extends('layouts.chair')

@section('title', 'Test Chair Layout')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">🎉 Chair Layout Test</h1>
    
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <strong>Success!</strong> Bạn đang sử dụng Chair Layout
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">User Info:</h2>
        <ul class="space-y-2">
            <li><strong>Name:</strong> {{ Auth::user()->name }}</li>
            <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
            <li><strong>User ID:</strong> {{ Auth::user()->user_id }}</li>
            <li><strong>Roles:</strong> 
                @foreach(Auth::user()->vaiTros as $role)
                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-1">{{ $role->role_code }}</span>
                @endforeach
            </li>
        </ul>
    </div>

    <div class="mt-6">
        <a href="{{ route('chair.conferences.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Go to Conferences Management
        </a>
    </div>
</div>
@endsection