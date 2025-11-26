<div class="row">
    <!-- Left Column - Main Content -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Thông tin cơ bản
                </h6>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">
                        Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $news->title ?? '') }}"
                           placeholder="Nhập tiêu đề bài viết..." required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-link"></i></span>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $news->slug ?? '') }}"
                               placeholder="Auto-generate từ tiêu đề">
                    </div>
                    <small class="text-muted">URL thân thiện sẽ tự động tạo từ tiêu đề</small>
                    @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Summary -->
                <div class="mb-3">
                    <label for="summary" class="form-label fw-bold">Tóm tắt</label>
                    <textarea class="form-control @error('summary') is-invalid @enderror"
                              id="summary" name="summary" rows="3"
                              placeholder="Tóm tắt ngắn gọn về nội dung (hiển thị trong danh sách)...">{{ old('summary', $news->summary ?? '') }}</textarea>
                    <small class="text-muted"><span id="summary-count">0</span> / 200 ký tự</small>
                    @error('summary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea class="form-control @error('content') is-invalid @enderror"
                              id="content" name="content" rows="15">{{ old('content', $news->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Cover Image Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image"></i> Ảnh bìa
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="cover_image" class="form-label">Upload ảnh bìa</label>
                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                           id="cover_image" name="cover_image" accept="image/*" onchange="previewImage(event)">
                    <small class="text-muted">Định dạng: JPG, PNG, GIF. Kích thước khuyến nghị: 1200x630px</small>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Preview -->
                <div id="image-preview" class="text-center">
                    @if(isset($news) && $news->cover_image)
                        <img src="{{ asset('storage/' . $news->cover_image) }}" alt="Cover"
                             class="img-fluid rounded border" style="max-height: 300px;">
                        <p class="small text-muted mt-2"><i class="fas fa-check-circle text-success"></i> Ảnh hiện tại</p>
                    @else
                        <div class="border border-2 border-dashed rounded p-5 bg-light">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có ảnh bìa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Metadata -->
    <div class="col-lg-4">
        <!-- Publish Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cog"></i> Cài đặt xuất bản
                </h6>
            </div>
            <div class="card-body">
                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">
                        Trạng thái <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="DRAFT" {{ old('status', $news->status ?? '') == 'DRAFT' ? 'selected' : '' }}>
                            📝 Bản nháp
                        </option>
                        <option value="PENDING" {{ old('status', $news->status ?? '') == 'PENDING' ? 'selected' : '' }}>
                            ⏳ Chờ duyệt
                        </option>
                        <option value="PUBLISHED" {{ old('status', $news->status ?? 'PUBLISHED') == 'PUBLISHED' ? 'selected' : '' }}>
                            ✅ Đã xuất bản
                        </option>
                        <option value="ARCHIVED" {{ old('status', $news->status ?? '') == 'ARCHIVED' ? 'selected' : '' }}>
                            📦 Lưu trữ
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Published Date -->
                <div class="mb-3">
                    <label for="published_at" class="form-label">Ngày xuất bản</label>
                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                           id="published_at" name="published_at"
                           value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                    <small class="text-muted">Để trống = thời gian hiện tại</small>
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Featured Checkbox -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                            {{ old('is_featured', $news->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning"></i> <strong>Tin nổi bật</strong>
                        </label>
                    </div>
                    <small class="text-muted">Hiển thị ở vị trí ưu tiên trên trang chủ</small>
                </div>
            </div>
        </div>

        <!-- Category & Conference -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-folder"></i> Phân loại
                </h6>
            </div>
            <div class="card-body">
                <!-- Category -->
                <div class="mb-3">
                    <label for="category" class="form-label fw-bold">
                        Danh mục <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                        <option value="">-- Chọn danh mục --</option>
                        <option value="NEWS" {{ old('category', $news->category ?? '') == 'NEWS' ? 'selected' : '' }}>
                            📰 Tin tức
                        </option>
                        <option value="ANNOUNCEMENT" {{ old('category', $news->category ?? '') == 'ANNOUNCEMENT' ? 'selected' : '' }}>
                            📢 Thông báo
                        </option>
                        <option value="EVENT" {{ old('category', $news->category ?? '') == 'EVENT' ? 'selected' : '' }}>
                            🎉 Sự kiện
                        </option>
                        <option value="GUIDE" {{ old('category', $news->category ?? '') == 'GUIDE' ? 'selected' : '' }}>
                            📚 Hướng dẫn
                        </option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Conference -->
                <div class="mb-3">
                    <label for="conference_id" class="form-label">Hội thảo liên quan</label>
                    <select class="form-select @error('conference_id') is-invalid @enderror" id="conference_id" name="conference_id">
                        <option value="">-- Không thuộc hội thảo --</option>
                        @foreach($conferences as $conf)
                            <option value="{{ $conf->conference_id }}"
                                {{ old('conference_id', $news->conference_id ?? '') == $conf->conference_id ? 'selected' : '' }}>
                                {{ $conf->title }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Liên kết bài viết với hội thảo cụ thể</small>
                    @error('conference_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Lưu bài viết
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.card {
    border: none;
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
}
.form-label.fw-bold {
    color: #2d3748;
}
#image-preview img {
    transition: all 0.3s ease;
}
#image-preview img:hover {
    transform: scale(1.02);
}
.border-dashed {
    border-style: dashed !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.auto !== 'false') {
        const title = this.value;
        const slug = title
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'd')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
        slugInput.dataset.auto = 'true';
    }
});

// Mark slug as manually edited
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.auto = 'false';
});

// Summary character counter
const summaryTextarea = document.getElementById('summary');
const summaryCount = document.getElementById('summary-count');
summaryTextarea.addEventListener('input', function() {
    summaryCount.textContent = this.value.length;
    if (this.value.length > 200) {
        summaryCount.classList.add('text-danger');
    } else {
        summaryCount.classList.remove('text-danger');
    }
});
// Initialize counter
summaryCount.textContent = summaryTextarea.value.length;

// Image preview function
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = `
            <img src="${reader.result}" alt="Preview"
                 class="img-fluid rounded border" style="max-height: 300px;">
            <p class="small text-success mt-2">
                <i class="fas fa-check-circle"></i> Ảnh mới sẽ được upload
            </p>
        `;
    };
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Initialize TinyMCE editor
tinymce.init({
    selector: '#content',
    height: 500,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | link image | code | help',
    content_style: 'body { font-family:Inter,sans-serif; font-size:14px }',
    placeholder: 'Nhập nội dung chi tiết bài viết tại đây...',
    language: 'vi'
});
</script>
@endpush
