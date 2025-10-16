<footer class="bg-gray-800 text-gray-300 py-12 mt-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-lg font-semibold text-white mb-4">HUIT Conference Management</h3>
                <p class="text-gray-400 mb-4">Hệ thống quản lý hội thảo khoa học của Đại học Công nghiệp TP.HCM</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22 12.07C22 6.49 17.52 2 11.93 2S2 6.49 2 12.07c0 4.99 3.66 9.12 8.44 9.93v-7.04H8.08v-2.89h2.36V9.41c0-2.33 1.39-3.61 3.52-3.61.99 0 2.03.18 2.03.18v2.23h-1.14c-1.12 0-1.47.7-1.47 1.42v1.7h2.5l-.4 2.89h-2.1v7.04C18.34 21.19 22 17.06 22 12.07z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="LinkedIn">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065z" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-medium mb-4">Liên kết nhanh</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Trang chủ</a></li>
                    <li><a href="{{ route('conferences.index') }}" class="text-gray-400 hover:text-white transition-colors">Hội thảo</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Hướng dẫn</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Liên hệ</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-medium mb-4">Hỗ trợ</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tài liệu</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Báo lỗi</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-400">© {{ date('Y') }} HUIT - Đại học Công nghiệp TP.HCM. All rights reserved.</p>
        </div>
    </div>
</footer>