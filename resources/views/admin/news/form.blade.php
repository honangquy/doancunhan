<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content Column (2/3 width) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Thông tin cơ bản
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tiêu đề <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $news->title ?? '') }}"
                           class="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all @error('title') border-red-500 @enderror"
                           placeholder="Nhập tiêu đề bài viết..."
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug (URL)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug', $news->slug ?? '') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all @error('slug') border-red-500 @enderror"
                               placeholder="auto-generate-tu-tieu-de">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">URL thân thiện sẽ tự động tạo từ tiêu đề</p>
                    @error('slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Summary -->
                <div>
                    <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tóm tắt
                    </label>
                    <textarea id="summary"
                              name="summary"
                              rows="3"
                              maxlength="200"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none @error('summary') border-red-500 @enderror"
                              placeholder="Tóm tắt ngắn gọn về nội dung (hiển thị trong danh sách)...">{{ old('summary', $news->summary ?? '') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">Tóm tắt ngắn gọn hiển thị trong danh sách tin</p>
                        <p class="text-xs text-gray-500">
                            <span id="summary-count">0</span> / 200
                        </p>
                    </div>
                    @error('summary')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nội dung chi tiết
                    </label>
                    <textarea id="content"
                              name="content"
                              rows="15"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-mono text-sm @error('content') border-red-500 @enderror"
                              placeholder="Nhập nội dung chi tiết bài viết tại đây...">{{ old('content', $news->content ?? '') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Cover Image Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Ảnh bìa
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload ảnh bìa
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="cover_image" class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click để upload</span> hoặc kéo thả</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 2MB, 1200x630px)</p>
                            </div>
                            <input id="cover_image"
                                   name="cover_image"
                                   type="file"
                                   class="hidden"
                                   accept="image/*"
                                   onchange="previewImage(event)" />
                        </label>
                    </div>
                    @error('cover_image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Preview -->
                <div id="image-preview" class="text-center">
                    @if(isset($news) && $news->cover_image)
                        <img src="{{ asset('storage/' . $news->cover_image) }}"
                             alt="Cover"
                             class="max-h-80 rounded-lg shadow-md mx-auto border-2 border-gray-200">
                        <p class="text-sm text-green-600 mt-3 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Ảnh hiện tại
                        </p>
                    @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-gray-50">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-400 text-sm">Chưa có ảnh bìa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attachments & Gallery Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    Tệp đính kèm & Thư viện ảnh
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- PDF Attachment -->
                <div>
                    <label for="attachment_path" class="block text-sm font-medium text-gray-700 mb-2">
                        Tài liệu đính kèm (PDF)
                    </label>
                    <input type="file"
                           id="attachment_path"
                           name="attachment_path"
                           accept=".pdf"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition-all">
                    <p class="mt-1 text-xs text-gray-500">Chỉ chấp nhận file PDF (Max: 10MB)</p>

                    @if(isset($news) && $news->attachment_path)
                        <div class="mt-3 flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ basename($news->attachment_path) }}
                                </p>
                                <a href="{{ asset('storage/' . $news->attachment_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                    Xem tài liệu hiện tại
                                </a>
                            </div>
                        </div>
                    @endif
                    @error('attachment_path')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Image Gallery -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Thư viện ảnh (Nhiều ảnh)
                    </label>
                    <input type="file"
                           id="images"
                           name="images[]"
                           multiple
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="mt-1 text-xs text-gray-500">Chọn nhiều ảnh cùng lúc (Max: 2MB/ảnh)</p>

                    @if(isset($news) && !empty($news->images))
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Ảnh đã tải lên:</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($news->images as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image) }}" class="h-24 w-full object-cover rounded-lg border border-gray-200">
                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                            <label class="flex items-center space-x-1 cursor-pointer text-white">
                                                <input type="checkbox" name="remove_images[]" value="{{ $image }}" class="form-checkbox h-4 w-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                                                <span class="text-xs font-medium">Xóa</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-500 italic">* Chọn "Xóa" để loại bỏ ảnh khi cập nhật</p>
                        </div>
                    @endif
                    @error('images.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column (1/3 width) -->
    <div class="space-y-6">
        <!-- Publish Settings Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Cài đặt xuất bản
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                        Danh mục <span class="text-red-500">*</span>
                    </label>
                    <select id="category"
                            name="category"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('category') border-red-500 @enderror"
                            required>
                        <option value="">-- Chọn danh mục --</option>
                        <option value="NEWS" {{ old('category', $news->category ?? '') == 'NEWS' ? 'selected' : '' }}>📰 Tin tức</option>
                        <option value="ANNOUNCEMENT" {{ old('category', $news->category ?? '') == 'ANNOUNCEMENT' ? 'selected' : '' }}>📢 Thông báo</option>
                        <option value="EVENT" {{ old('category', $news->category ?? '') == 'EVENT' ? 'selected' : '' }}>🎉 Sự kiện</option>
                        <option value="GUIDE" {{ old('category', $news->category ?? '') == 'GUIDE' ? 'selected' : '' }}>📚 Hướng dẫn</option>
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('status') border-red-500 @enderror"
                            required>
                        <option value="DRAFT" {{ old('status', $news->status ?? '') == 'DRAFT' ? 'selected' : '' }}>📝 Bản nháp</option>
                        <option value="PENDING" {{ old('status', $news->status ?? '') == 'PENDING' ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                        <option value="PUBLISHED" {{ old('status', $news->status ?? 'PUBLISHED') == 'PUBLISHED' ? 'selected' : '' }}>✅ Đã xuất bản</option>
                        <option value="ARCHIVED" {{ old('status', $news->status ?? '') == 'ARCHIVED' ? 'selected' : '' }}>📦 Lưu trữ</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conference -->
                <div>
                    <label for="conference_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Hội thảo liên quan
                    </label>
                    <select id="conference_id"
                            name="conference_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('conference_id') border-red-500 @enderror">
                        <option value="">-- Không thuộc hội thảo --</option>
                        @foreach($conferences as $conf)
                            <option value="{{ $conf->conference_id }}" {{ old('conference_id', $news->conference_id ?? '') == $conf->conference_id ? 'selected' : '' }}>
                                {{ $conf->title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Liên kết bài viết với hội thảo cụ thể</p>
                    @error('conference_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Published Date -->
                <div>
                    <label for="published_at" class="block text-sm font-semibold text-gray-700 mb-2">
                        Ngày xuất bản
                    </label>
                    <input type="datetime-local"
                           id="published_at"
                           name="published_at"
                           value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('published_at') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Để trống = thời gian hiện tại</p>
                    @error('published_at')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Featured Toggle -->
                <div class="pt-3 border-t border-gray-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               id="is_featured"
                               name="is_featured"
                               value="1"
                               {{ old('is_featured', $news->is_featured ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-yellow-500 border-gray-300 rounded focus:ring-2 focus:ring-yellow-400 transition-all">
                        <span class="ml-3 flex items-center">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-semibold text-gray-700">Tin nổi bật</span>
                        </span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500 ml-8">Hiển thị ở vị trí ưu tiên trên trang chủ</p>
                </div>
            </div>
        </div>
    </div>
</div>
