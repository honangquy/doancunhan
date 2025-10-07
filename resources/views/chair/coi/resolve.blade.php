<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giải quyết COI - Chair Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-orange-800 via-orange-700 to-orange-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    <span class="text-xl font-bold">HUIT Conferences</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ Auth::user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:text-orange-200 transition">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <a href="{{ route('chair.dashboard') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Quản lý reviewer</span>
                </button>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Phân công phản biện</span>
                </button>

                <a href="{{ route('chair.coi.index') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 text-orange-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Kiểm tra COI</span>
                </a>

                <button onclick="alert('Chức năng đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="container mx-auto">
        <div class="max-w-4xl mx-auto">
            <!-- Messages -->
            @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- COI Summary -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h2 class="text-lg font-bold text-red-800">⚠️ Tóm tắt COI Case</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Bài báo:</label>
                        <p class="mt-1 text-gray-900 font-semibold">{{ $coi->paper_title }}</p>
                        <p class="text-sm text-gray-500">Paper #{{ $coi->paper_id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Reviewer:</label>
                        <p class="mt-1 text-gray-900 font-semibold">{{ $coi->reviewer_name }}</p>
                        <p class="text-sm text-gray-500">Reviewer #{{ $coi->reviewer_id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Loại COI:</label>
                        <span class="inline-block mt-1 px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full font-medium">
                            {{ $coi->coi_name }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nguồn:</label>
                        <span class="inline-block mt-1 px-3 py-1 
                            @if($coi->source_type === 'DECLARED') bg-purple-100 text-purple-800
                            @else bg-yellow-100 text-yellow-800
                            @endif
                            text-sm rounded-full font-medium">
                            {{ $coi->source_type === 'DECLARED' ? 'Tự khai báo' : 'Tự động phát hiện' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Resolution Form -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="{ 
                selectedResolution: '',
                showConfirm: false,
                formData: {}
            }">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-orange-100 border-b">
                    <h2 class="text-lg font-bold text-gray-800">⚖️ Chọn phương án giải quyết</h2>
                    <p class="text-sm text-gray-600 mt-1">Vui lòng chọn cách giải quyết COI case này</p>
                </div>

                <form action="{{ route('chair.coi.resolve', $coi->coi_id) }}" method="POST" 
                      @submit.prevent="showConfirm = true; formData = Object.fromEntries(new FormData($event.target))">
                    @csrf
                    
                    <div class="p-6 space-y-6">
                        <!-- Resolution Options -->
                        <div class="space-y-4">
                            <label class="text-sm font-medium text-gray-700">Phương án giải quyết: *</label>
                            
                            @foreach($resolutionTypes as $type)
                            <div>
                                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition
                                    hover:bg-gray-50"
                                    :class="selectedResolution === '{{ $type->resolution_code }}' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                    <input type="radio" 
                                           name="resolution_code" 
                                           value="{{ $type->resolution_code }}"
                                           x-model="selectedResolution"
                                           required
                                           class="mt-1 text-orange-600 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <p class="font-semibold text-gray-900">{{ $type->resolution_name }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $type->description }}</p>
                                        
                                        @if($type->resolution_code === 'REMOVE_ASSIGNMENT')
                                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                                            ⚠️ <strong>Cảnh báo:</strong> Sẽ xóa phân công reviewer cho bài báo này
                                        </div>
                                        @elseif($type->resolution_code === 'ALLOW_WITH_DISCLOSURE')
                                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                                            ℹ️ <strong>Lưu ý:</strong> Reviewer vẫn được review nhưng phải công khai xung đột lợi ích
                                        </div>
                                        @elseif($type->resolution_code === 'REASSIGN')
                                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-xs text-green-700">
                                            ✓ <strong>Khuyến nghị:</strong> Phân công reviewer khác không có COI
                                        </div>
                                        @endif
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Note -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ghi chú (tùy chọn):
                            </label>
                            <textarea name="note" 
                                      rows="4"
                                      placeholder="Thêm ghi chú về quyết định của bạn..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Tối đa 500 ký tự</p>
                        </div>

                        <!-- Warning -->
                        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">Lưu ý quan trọng</p>
                                    <p class="text-xs text-yellow-700 mt-1">
                                        Quyết định này không thể hoàn tác. Vui lòng xem xét kỹ trước khi xác nhận.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between pt-4 border-t">
                            <a href="{{ route('chair.coi.show', $coi->coi_id) }}" 
                               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">
                                ← Hủy
                            </a>
                            <button type="submit"
                                    class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition
                                    disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    :disabled="!selectedResolution">
                                Xác nhận giải quyết →
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Confirmation Modal -->
                <div x-show="showConfirm" 
                     x-transition
                     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                     @click.self="showConfirm = false">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
                        <div class="px-6 py-4 bg-orange-600 text-white">
                            <h3 class="text-lg font-bold">⚠️ Xác nhận giải quyết COI</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 mb-4">
                                Bạn có chắc chắn muốn giải quyết COI case này với phương án đã chọn?
                            </p>
                            <p class="text-sm text-gray-600 mb-4">
                                <strong>Paper:</strong> {{ $coi->paper_title }}<br>
                                <strong>Reviewer:</strong> {{ $coi->reviewer_name }}<br>
                                <strong>Loại COI:</strong> {{ $coi->coi_name }}
                            </p>
                            <p class="text-xs text-red-600 font-medium">
                                ⚠️ Hành động này không thể hoàn tác!
                            </p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button @click="showConfirm = false"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                                Hủy
                            </button>
                            <button @click="$el.closest('form').submit()"
                                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                                Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </main>
    </div>
</body>
</html>
