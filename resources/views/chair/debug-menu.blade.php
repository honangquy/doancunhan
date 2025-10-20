<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Chair Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Debug Chair Menu</h1>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Menu Items Test:</h2>
            
            <div class="space-y-2">
                <a href="{{ route('chair.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-100 hover:bg-orange-200 transition">
                    <span>✅ Dashboard (route: chair.dashboard)</span>
                </a>

                <a href="{{ route('chair.conferences.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-100 hover:bg-orange-200 transition">
                    <span>🎯 Quản lý hội thảo (route: chair.conferences.index)</span>
                </a>

                <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-100">
                    <span>📄 Quản lý bài báo</span>
                </div>

                <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-100">
                    <span>👥 Quản lý reviewer</span>
                </div>

                <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-100">
                    <span>⚖️ Kiểm tra COI</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-100 p-4 mt-6 rounded-lg">
            <h3 class="font-semibold">Route Check:</h3>
            <p>Current Route: {{ Route::currentRouteName() ?? 'N/A' }}</p>
            <p>User: {{ auth()->user()->ho_ten ?? 'Not logged in' }}</p>
            <p>User ID: {{ auth()->user()->nguoi_dung_id ?? 'N/A' }}</p>
        </div>

        <div class="bg-blue-100 p-4 mt-4 rounded-lg">
            <h3 class="font-semibold">Role Check:</h3>
            @if(auth()->check())
                @php
                    $user = auth()->user();
                    $roles = $user->vaiTroNguoiDung;
                @endphp
                @if($roles->count() > 0)
                    @foreach($roles as $role)
                        <p>Role: {{ $role->loaiVaiTro->ten_vai_tro ?? 'N/A' }} (Code: {{ $role->role_code ?? 'N/A' }})</p>
                    @endforeach
                @else
                    <p class="text-red-600">No roles found!</p>
                @endif
            @else
                <p class="text-red-600">User not authenticated!</p>
            @endif
        </div>
    </div>
</body>
</html>