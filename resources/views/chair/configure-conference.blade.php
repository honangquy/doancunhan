@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Cấu hình Hội thảo</h1>
            <p class="text-gray-600">Hoàn thành các thông tin dưới đây để công khai hội thảo của bạn</p>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800" id="successText"></p>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800" id="errorText"></p>
        </div>

        <!-- Loading State -->
        <div id="loadingSpinner" class="hidden mb-4 flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Đang xử lý...</span>
        </div>

        <form id="configureForm" class="bg-white rounded-lg shadow-md p-8" x-data="conferenceForm()">
            @csrf

            <!-- Basic Information Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b-2 border-blue-200">
                    Thông tin Cơ bản
                </h2>

                <!-- Conference Title (Read-only) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tên Hội thảo
                    </label>
                    <input type="text" 
                           id="conferenceTitle"
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                           placeholder="Sẽ tự động điền từ yêu cầu">
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả Chi tiết <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" 
                              name="description"
                              required
                              rows="5"
                              placeholder="Nhập mô tả chi tiết về hội thảo (tối đa 2000 ký tự)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              maxlength="2000"></textarea>
                    <p id="descriptionCount" class="text-sm text-gray-500 mt-1">0 / 2000</p>
                    <p class="text-red-500 text-sm mt-1 hidden" id="descriptionError"></p>
                </div>

                <!-- Keywords -->
                <div class="mb-6">
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                        Từ khóa <span class="text-gray-500">(phân cách bằng dấu phẩy)</span>
                    </label>
                    <input type="text" 
                           id="keywords"
                           name="keywords"
                           placeholder="VD: AI, Machine Learning, Deep Learning"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="1000">
                    <p class="text-gray-500 text-sm mt-1">Nhập các từ khóa chính của hội thảo</p>
                </div>
            </div>

            <!-- Location & Contact Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b-2 border-blue-200">
                    Địa điểm & Liên hệ
                </h2>

                <!-- Location -->
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Địa điểm Tổ chức <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="location"
                           name="location"
                           required
                           placeholder="VD: Trường Đại học Khoa học Tự nhiên, Hà Nội"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="255">
                    <p class="text-red-500 text-sm mt-1 hidden" id="locationError"></p>
                </div>

                <!-- Contact Email -->
                <div class="mb-6">
                    <label for="contactEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Liên hệ <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="contactEmail"
                           name="contact_email"
                           required
                           placeholder="contact@conference.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="255">
                    <p class="text-red-500 text-sm mt-1 hidden" id="contactEmailError"></p>
                </div>

                <!-- Contact Phone -->
                <div class="mb-6">
                    <label for="contactPhone" class="block text-sm font-medium text-gray-700 mb-2">
                        Số điện thoại Liên hệ
                    </label>
                    <input type="tel" 
                           id="contactPhone"
                           name="contact_phone"
                           placeholder="+84 9 1234 5678"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="20">
                    <p class="text-gray-500 text-sm mt-1">Số điện thoại liên hệ (tùy chọn)</p>
                </div>
            </div>

            <!-- Chair Information Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b-2 border-blue-200">
                    Thông tin Chủ tịch
                </h2>

                <!-- Chair Name -->
                <div class="mb-6">
                    <label for="chairName" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên Chủ tịch <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="chairName"
                           name="chair_name"
                           required
                           placeholder="Nhập tên đầy đủ"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="255">
                    <p class="text-red-500 text-sm mt-1 hidden" id="chairNameError"></p>
                </div>

                <!-- Chair Email -->
                <div class="mb-6">
                    <label for="chairEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Chủ tịch <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="chairEmail"
                           name="chair_email"
                           required
                           placeholder="chair@email.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="255">
                    <p class="text-red-500 text-sm mt-1 hidden" id="chairEmailError"></p>
                </div>
            </div>

            <!-- Call for Papers Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b-2 border-blue-200">
                    Gọi Bài viết & Hướng dẫn
                </h2>

                <!-- CFP URL -->
                <div class="mb-6">
                    <label for="cfpUrl" class="block text-sm font-medium text-gray-700 mb-2">
                        Link CFP (Call for Papers)
                    </label>
                    <input type="url" 
                           id="cfpUrl"
                           name="cfp_url"
                           placeholder="https://example.com/cfp.pdf"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           maxlength="500">
                    <p class="text-gray-500 text-sm mt-1">Link đến file PDF gọi bài viết (tùy chọn)</p>
                </div>

                <!-- Submission Guidelines -->
                <div class="mb-6">
                    <label for="submissionGuidelines" class="block text-sm font-medium text-gray-700 mb-2">
                        Hướng dẫn Nộp bài
                    </label>
                    <textarea id="submissionGuidelines" 
                              name="submission_guidelines"
                              rows="5"
                              placeholder="Nhập hướng dẫn chi tiết cho quá trình nộp bài viết (tối đa 5000 ký tự)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              maxlength="5000"></textarea>
                    <p id="guidelinesCount" class="text-sm text-gray-500 mt-1">0 / 5000</p>
                    <p class="text-gray-500 text-sm mt-1">Hướng dẫn chi tiết cho người nộp bài (tùy chọn)</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <button type="submit" 
                        id="submitBtn"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <span id="submitBtnText">Cấu hình Hội thảo</span>
                </button>
                <a href="{{ route('home') }}" 
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function conferenceForm() {
    return {
        requestId: null,
        conferenceTitle: '',

        init() {
            // Get request ID from URL
            const urlPath = window.location.pathname;
            const matches = urlPath.match(/\/chair\/configure-conference\/(\d+)/);
            if (matches) {
                this.requestId = matches[1];
                this.loadRequestDetails();
            }
        },

        loadRequestDetails() {
            if (!this.requestId) return;

            fetch(`/api/conference-requests/${this.requestId}`, {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const request = data.data;
                    document.getElementById('conferenceTitle').value = request.title || '';
                    this.conferenceTitle = request.title || '';
                    
                    // Pre-fill chair information if available
                    if (request.chair_fullname) {
                        document.getElementById('chairName').value = request.chair_fullname;
                    }
                    if (request.chair_email) {
                        document.getElementById('chairEmail').value = request.chair_email;
                    }
                }
            })
            .catch(error => {
                console.error('Error loading request details:', error);
                this.showError('Lỗi khi tải thông tin yêu cầu');
            });
        },

        async submitForm(e) {
            e.preventDefault();

            if (!this.requestId) {
                this.showError('Không tìm thấy ID yêu cầu');
                return;
            }

            const formData = new FormData(document.getElementById('configureForm'));
            const data = Object.fromEntries(formData);

            // Show loading state
            document.getElementById('loadingSpinner').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;

            try {
                const response = await fetch(`/api/conference-requests/${this.requestId}/configure`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccess('Cấu hình hội thảo thành công!');
                    setTimeout(() => {
                        window.location.href = '/chair/my-conferences';
                    }, 1500);
                } else {
                    if (result.errors) {
                        this.displayValidationErrors(result.errors);
                    }
                    this.showError(result.message || 'Lỗi khi cấu hình hội thảo');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showError('Lỗi khi gửi dữ liệu');
            } finally {
                document.getElementById('loadingSpinner').classList.add('hidden');
                document.getElementById('submitBtn').disabled = false;
            }
        },

        displayValidationErrors(errors) {
            // Clear previous errors
            document.querySelectorAll('[id$="Error"]').forEach(el => el.classList.add('hidden'));

            // Display field-specific errors
            Object.keys(errors).forEach(field => {
                const errorElement = document.getElementById(`${field}Error`);
                if (errorElement) {
                    errorElement.textContent = errors[field][0];
                    errorElement.classList.remove('hidden');
                }
            });
        },

        showSuccess(message) {
            const msgDiv = document.getElementById('successMessage');
            document.getElementById('successText').textContent = message;
            msgDiv.classList.remove('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        },

        showError(message) {
            const msgDiv = document.getElementById('errorMessage');
            document.getElementById('errorText').textContent = message;
            msgDiv.classList.remove('hidden');
            document.getElementById('successMessage').classList.add('hidden');
        },

        getAuthToken() {
            return document.querySelector('meta[name="auth-token"]')?.content || 
                   localStorage.getItem('auth_token') || '';
        }
    }
}

// Attach form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('configureForm');
    if (form) {
        const controller = conferenceForm();
        controller.init();
        form.addEventListener('submit', (e) => controller.submitForm(e));
    }

    // Description character counter
    const descArea = document.getElementById('description');
    if (descArea) {
        descArea.addEventListener('input', function() {
            document.getElementById('descriptionCount').textContent = `${this.value.length} / 2000`;
        });
    }

    // Guidelines character counter
    const guideArea = document.getElementById('submissionGuidelines');
    if (guideArea) {
        guideArea.addEventListener('input', function() {
            document.getElementById('guidelinesCount').textContent = `${this.value.length} / 5000`;
        });
    }
});
</script>
@endsection
