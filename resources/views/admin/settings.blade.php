@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    activeTab: 'backup',
    showPasswordModal: false,
    modalAction: '',
    modalTitle: '',
    modalDescription: '',
    password: ''
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
        <p class="mt-2 text-gray-600">Quản lý sao lưu, cấu hình hệ thống và xem cấu trúc dữ liệu</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'backup'" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'backup', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'backup' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Sao lưu & Phục hồi
            </button>
            <button @click="activeTab = 'config'" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'config', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'config' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Cấu hình tự động
            </button>
            <button @click="activeTab = 'database'" 
                :class="{ 'border-blue-500 text-blue-600': activeTab === 'database', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'database' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Cấu trúc Database
            </button>
        </nav>
    </div>

    <!-- Tab Content: Backup & Restore -->
    <div x-show="activeTab === 'backup'" class="space-y-6">
        <div class="flex justify-end">
            <button @click="showPasswordModal = true; modalAction = '{{ route('admin.settings.backup.create') }}'; modalTitle = 'Xác nhận tạo bản sao lưu'; modalDescription = 'Vui lòng nhập mật khẩu để xác nhận tạo bản sao lưu mới.';" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tạo bản sao lưu mới
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Danh sách bản sao lưu</h3>
            </div>
            
            @if(count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên file</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kích thước</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($backups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup['created_at'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <a href="{{ route('admin.settings.backup.download', $backup['filename']) }}" class="text-blue-600 hover:text-blue-900">Tải xuống</a>
                                        
                                        <button @click="showPasswordModal = true; modalAction = '{{ route('admin.settings.backup.restore', $backup['filename']) }}'; modalTitle = 'Xác nhận phục hồi dữ liệu'; modalDescription = 'CẢNH BÁO: Hành động này sẽ ghi đè toàn bộ dữ liệu hiện tại bằng dữ liệu từ bản sao lưu này. Vui lòng nhập mật khẩu để tiếp tục.';" 
                                            class="text-yellow-600 hover:text-yellow-900">Phục hồi</button>

                                        <form action="{{ route('admin.settings.backup.delete', $backup['filename']) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản sao lưu này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    Chưa có bản sao lưu nào.
                </div>
            @endif
        </div>
        
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-blue-800 font-semibold mb-2">Lưu ý quan trọng:</h4>
            <ul class="list-disc list-inside text-blue-700 text-sm space-y-1">
                <li>Chức năng sao lưu sẽ tạo ra một file .sql chứa toàn bộ cấu trúc và dữ liệu của cơ sở dữ liệu hiện tại.</li>
                <li>File sao lưu được lưu trữ trong thư mục <code>storage/app/backups</code> của server.</li>
                <li>Khi phục hồi dữ liệu, toàn bộ dữ liệu hiện tại sẽ bị thay thế bởi dữ liệu trong file sao lưu. Hãy cân nhắc kỹ trước khi thực hiện.</li>
            </ul>
        </div>
    </div>

    <!-- Password Confirmation Modal -->
    <div x-show="showPasswordModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showPasswordModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="modalAction" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="modalTitle">
                                    Xác nhận hành động
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" x-text="modalDescription">
                                        Vui lòng nhập mật khẩu để tiếp tục.
                                    </p>
                                    <div class="mt-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu xác nhận</label>
                                        <input type="password" name="password" id="password" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xác nhận
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showPasswordModal = false">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Content: Auto Config -->
    <div x-show="activeTab === 'config'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Cấu hình sao lưu tự động</h3>
            
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="auto_backup" name="auto_backup" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                            {{ isset($settings['auto_backup']) && $settings['auto_backup'] == '1' ? 'checked' : '' }}>
                        <label for="auto_backup" class="ml-2 block text-sm text-gray-900">
                            Bật sao lưu tự động
                        </label>
                    </div>

                    <div>
                        <label for="backup_frequency" class="block text-sm font-medium text-gray-700">Tần suất</label>
                        <select id="backup_frequency" name="backup_frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="daily" {{ (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'daily') ? 'selected' : '' }}>Hàng ngày</option>
                            <option value="weekly" {{ (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'weekly') ? 'selected' : '' }}>Hàng tuần</option>
                            <option value="monthly" {{ (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'monthly') ? 'selected' : '' }}>Hàng tháng</option>
                        </select>
                    </div>

                    <div>
                        <label for="backup_time" class="block text-sm font-medium text-gray-700">Thời gian thực hiện</label>
                        <input type="time" name="backup_time" id="backup_time" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            value="{{ $settings['backup_time'] ?? '00:00' }}">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            Lưu cấu hình
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Content: Database Structure -->
    <div x-show="activeTab === 'database'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cấu trúc Database ({{ count($dbStructure) }} bảng)</h3>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($dbStructure as $tableName => $info)
                    <div x-data="{ expanded: false }" class="bg-white">
                        <div class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50" @click="expanded = !expanded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2 transform transition-transform duration-200" :class="{ 'rotate-90': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ $tableName }}</span>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $info['rows'] }} rows
                                </span>
                            </div>
                            <span class="text-sm text-gray-500">{{ count($info['columns']) }} columns</span>
                        </div>
                        
                        <div x-show="expanded" class="px-6 pb-4 bg-gray-50 border-t border-gray-100" style="display: none;">
                            <table class="min-w-full mt-2">
                                <thead>
                                    <tr>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2">Field</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2">Type</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2">Null</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2">Key</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2">Default</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($info['columns'] as $col)
                                        <tr>
                                            <td class="py-2 text-sm font-medium text-gray-900">{{ $col->Field }}</td>
                                            <td class="py-2 text-sm text-gray-500">{{ $col->Type }}</td>
                                            <td class="py-2 text-sm text-gray-500">{{ $col->Null }}</td>
                                            <td class="py-2 text-sm text-gray-500">{{ $col->Key }}</td>
                                            <td class="py-2 text-sm text-gray-500">{{ $col->Default ?? 'NULL' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Fallback if not loaded globally -->
<script>
    if (typeof Alpine === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer><\/script>');
    }
</script>
@endsection
