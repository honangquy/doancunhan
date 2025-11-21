@extends('layouts.admin')

@section('title', 'Tạo tin tức mới')

@section('content')
<!-- Hero Header -->
<div class="relative bg-gradient-to-br from-green-600 via-teal-600 to-cyan-600 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tạo tin tức mới
                    </h1>
                    <p class="text-white text-opacity-90 text-sm">Thêm tin tức, thông báo hoặc sự kiện mới</p>
                </div>
                <a href="{{ route('admin.news.index') }}"
                   class="bg-white hover:bg-gray-100 text-teal-600 px-5 py-2.5 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-6 py-8">
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold mb-2">Có lỗi xảy ra:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.news.form')

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <a href="{{ route('admin.news.index') }}"
               class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hủy
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Tạo tin tức
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-generate slug from title
document.getElementById('title')?.addEventListener('input', function(e) {
    const slug = document.getElementById('slug');
    if (slug && !slug.dataset.manual) {
        slug.value = e.target.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd').replace(/Đ/g, 'D')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
});

document.getElementById('slug')?.addEventListener('input', function() {
    this.dataset.manual = 'true';
});

// Character counter for summary
document.getElementById('summary')?.addEventListener('input', function(e) {
    const counter = document.getElementById('summary-count');
    if (counter) {
        counter.textContent = e.target.value.length;
    }
});

// Image preview
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('image-preview');

    if (file && preview) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="max-h-80 rounded-lg shadow-md mx-auto border-2 border-gray-200">
                <p class="text-sm text-gray-500 mt-3">
                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Ảnh mới đã chọn
                </p>
            `;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
