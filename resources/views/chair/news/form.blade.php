<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content - Left Side -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>

            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="title"
                       id="title"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('title') border-red-500 @enderror"
                       value="{{ old('title', $news->title ?? '') }}"
                       placeholder="Nhập tiêu đề tin tức..."
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                    Slug (URL thân thiện)
                </label>
                <input type="text"
                       name="slug"
                       id="slug"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('slug') border-red-500 @enderror"
                       value="{{ old('slug', $news->slug ?? '') }}"
                       placeholder="auto-generate-tu-tieu-de">
                <p class="mt-1 text-xs text-gray-500">Để trống để tự động tạo từ tiêu đề</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Summary -->
            <div class="mb-4">
                <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">
                    Tóm tắt
                </label>
                <textarea name="summary"
                          id="summary"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('summary') border-red-500 @enderror"
                          placeholder="Tóm tắt ngắn gọn về nội dung...">{{ old('summary', $news->summary ?? '') }}</textarea>
                @error('summary')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                    Nội dung chi tiết
                </label>
                <textarea name="content"
                          id="content"
                          rows="12"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('content') border-red-500 @enderror"
                          placeholder="Nhập nội dung chi tiết...">{{ old('content', $news->content ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Cover Image -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ảnh bìa</h3>

            <div class="mb-4">
                <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload ảnh bìa
                </label>
                <input type="file"
                       name="cover_image"
                       id="cover_image"
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('cover_image') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF. Kích thước tối đa: 2MB</p>
                @error('cover_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(isset($news) && $news->cover_image)
                <div class="mt-4">
                    <p class="text-sm text-gray-700 mb-2">Ảnh hiện tại:</p>
                    <img src="{{ asset('storage/' . $news->cover_image) }}"
                         alt="Cover"
                         class="w-full max-w-md rounded-lg border border-gray-200">
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar - Right Side -->
    <div class="space-y-6">
        <!-- Publish Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cài đặt xuất bản</h3>

            <!-- Category -->
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Danh mục <span class="text-red-500">*</span>
                </label>
                <select name="category"
                        id="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('category') border-red-500 @enderror"
                        required>
                    <option value="">-- Chọn danh mục --</option>
                    <option value="NEWS" {{ old('category', $news->category ?? '') == 'NEWS' ? 'selected' : '' }}>Tin tức</option>
                    <option value="ANNOUNCEMENT" {{ old('category', $news->category ?? '') == 'ANNOUNCEMENT' ? 'selected' : '' }}>Thông báo</option>
                    <option value="EVENT" {{ old('category', $news->category ?? '') == 'EVENT' ? 'selected' : '' }}>Sự kiện</option>
                    <option value="GUIDE" {{ old('category', $news->category ?? '') == 'GUIDE' ? 'selected' : '' }}>Hướng dẫn</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Trạng thái <span class="text-red-500">*</span>
                </label>
                <select name="status"
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('status') border-red-500 @enderror"
                        required>
                    <option value="DRAFT" {{ old('status', $news->status ?? 'DRAFT') == 'DRAFT' ? 'selected' : '' }}>Bản nháp</option>
                    <option value="PENDING" {{ old('status', $news->status ?? '') == 'PENDING' ? 'selected' : '' }}>Chờ duyệt (Gửi Admin)</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    <svg class="w-4 h-4 inline-block text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Chọn "Chờ duyệt" để gửi tin tức đến Admin phê duyệt. Sau khi được duyệt, tin sẽ tự động xuất bản.
                </p>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Published Date -->
            <div class="mb-4">
                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">
                    Ngày xuất bản
                </label>
                <input type="datetime-local"
                       name="published_at"
                       id="published_at"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 @error('published_at') border-red-500 @enderror"
                       value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                <p class="mt-1 text-xs text-gray-500">Để trống để tự động đặt thời gian hiện tại khi xuất bản</p>
                @error('published_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_featured"
                           value="1"
                           {{ old('is_featured', $news->is_featured ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <span class="ml-2 text-sm text-gray-700">Đánh dấu tin nổi bật</span>
                </label>
            </div>
        </div>

        <!-- Conference (Hidden - set by controller) -->
        <input type="hidden" name="conference_id" value="{{ $conferenceId ?? ($news->conference_id ?? '') }}">
    </div>
</div>
