<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Hồ sơ cá nhân</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Card -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-gray-800">Thông tin cá nhân</h3>
                </div>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" id="name" name="name" 
                                   value="{{ Auth::user()->name }}" 
                                   class="form-input">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="{{ Auth::user()->email }}" 
                                   class="form-input" readonly>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="form-label">Vai trò</label>
                            <div class="mt-2">
                                <span class="badge badge-info">{{ ucfirst(Auth::user()->role ?? 'Author') }}</span>
                            </div>
                        </div>

                        <!-- Member Since -->
                        <div>
                            <label class="form-label">Thành viên từ</label>
                            <div class="mt-2 text-gray-700">
                                {{ Auth::user()->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-gray-800">Đổi mật khẩu</h3>
                </div>
                
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-input" required>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-input" required>
                            <p class="mt-1 text-xs text-gray-500">Tối thiểu 6 ký tự</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                   class="form-input" required>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-danger">
                            Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
