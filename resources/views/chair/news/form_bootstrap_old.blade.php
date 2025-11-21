<div class="row">
    <!-- Left Column - Main Content -->
    <div class="col-lg-8">
        <!-- Info Alert -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <strong>Lưu ý:</strong> Tin tức của bạn sẽ được gửi để Admin duyệt trước khi xuất bản.
                    <br><small class="text-muted">Trạng thái sẽ là "Chờ duyệt" cho đến khi Admin phê duyệt.</small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gradient-orange text-white py-3">
                <h6 class="m-0 font-weight-bold">
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
            <div class="card-header bg-gradient-orange text-white py-3">
                <h6 class="m-0 font-weight-bold">
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
        <!-- Conference Info -->
        <div class="card shadow-sm mb-4 border-warning">
            <div class="card-header bg-gradient-orange text-white py-3">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-building"></i> Hội thảo
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center py-2">
                    <i class="fas fa-university fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold">{{ $conference->title }}</h6>
                    <small class="text-muted">Tin tức thuộc hội thảo của bạn</small>
                </div>
            </div>
        </div>

        <!-- Category -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gradient-orange text-white py-3">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-folder"></i> Phân loại
                </h6>
            </div>
            <div class="card-body">
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

                <!-- Featured Checkbox -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                            {{ old('is_featured', $news->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning"></i> <strong>Tin nổi bật</strong>
                        </label>
                    </div>
                    <small class="text-muted">Đề xuất hiển thị ở vị trí ưu tiên</small>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg text-white">
                        <i class="fas fa-paper-plane"></i> Gửi bài viết
                    </button>
                    <a href="{{ route('chair.news.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> Bài viết sẽ ở trạng thái <strong>"Chờ duyệt"</strong> cho đến khi Admin phê duyệt.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.bg-gradient-orange {
    background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
}
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

            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if(isset($news) && $news->cover_image)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $news->cover_image) }}" alt="Current cover" class="img-thumbnail" style="max-width: 200px;">
                <p class="small text-muted mt-1">Ảnh hiện tại</p>
            </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="col-md-12 mb-3">
        <label for="summary" class="form-label">Tóm tắt</label>
        <textarea class="form-control @error('summary') is-invalid @enderror"
                  id="summary" name="summary" rows="3"
                  placeholder="Tóm tắt ngắn gọn về nội dung tin tức...">{{ old('summary', $news->summary ?? '') }}</textarea>
        @error('summary')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Content -->
    <div class="col-md-12 mb-3">
        <label for="content" class="form-label">Nội dung chi tiết</label>
        <textarea class="form-control @error('content') is-invalid @enderror"
                  id="content" name="content" rows="10">{{ old('content', $news->content ?? '') }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Featured -->
    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                {{ old('is_featured', $news->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">
                Tin nổi bật (hiển thị ở vị trí ưu tiên)
            </label>
        </div>
    </div>

    <!-- Info Alert for Chair -->
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Lưu ý:</strong> Tin tức sẽ được gửi đến Admin để duyệt trước khi xuất bản công khai.
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('blur', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        const title = this.value;
        const slug = title
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});
</script>
@endpush
