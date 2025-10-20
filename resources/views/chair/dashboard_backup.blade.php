<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chair Dashboard - HUIT Conference</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#ea580c',
                        accent: '#f97316',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3);
        }
    </style>
    
    <script>
        // Define routes for Alpine.js
        window.appRoutes = {
            chairPapers: '{{ route("chair.papers") }}'
        };
        
        // Alpine.js component data
        function chairDashboard() {
            return {
                currentView: 'dashboard',
                papersData: null,
                selectedPaperId: null,
                paperDetailData: null,
                assignReviewerData: null,
                reviewsData: null,
                decisionData: null,
                reviewersData: null,
                loading: false,
                
                switchView(view) {
                    this.currentView = view;
                    if (view === 'papers' && !this.papersData) {
                        this.loadPapers();
                    }
                },
                
                async loadPapers() {
                    this.loading = true;
                    try {
                        const response = await fetch(window.appRoutes.chairPapers, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const html = await response.text();
            this.papersData = html;
        } catch (error) {
            console.error('Error loading papers:', error);
        } finally {
            this.loading = false;
        }
    },
    
    async viewPaperDetail(paperId) {
        this.selectedPaperId = paperId;
        this.currentView = 'paper-detail';
        this.loading = true;
        this.paperDetailData = null;
        
        try {
            const response = await fetch(`/qly_hthao/qlyhoithao/public/chair/papers/${paperId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            
            // Parse HTML and extract main content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try different selectors
            let content = null;
            const selectors = ['main', '.main-content', 'body > *', 'body'];
            
            for (const selector of selectors) {
                const element = doc.querySelector(selector);
                if (element && element.innerHTML.trim()) {
                    content = element.innerHTML;
                    break;
                }
            }
            
            if (content) {
                this.paperDetailData = content;
            } else {
                throw new Error('Cannot extract content from response');
            }
        } catch (error) {
            console.error('Error loading paper detail:', error);
            this.paperDetailData = `<div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'><p class='text-red-600 font-medium text-lg mb-2'>❌ Không thể tải chi tiết bài báo</p><p class='text-red-500 text-sm mt-2'>Lỗi: ${error.message}</p><p class='text-red-500 text-sm mt-1'>Vui lòng thử lại hoặc <a href='/qly_hthao/qlyhoithao/public/chair/papers/${paperId}' class='underline font-medium'>mở trực tiếp</a></p></div>`;
        } finally {
            this.loading = false;
        }
    },
    
    async viewAssignReviewer(paperId) {
        this.selectedPaperId = paperId;
        this.currentView = 'assign-reviewer';
        this.loading = true;
        this.assignReviewerData = null;
        
        try {
            const response = await fetch(`/qly_hthao/qlyhoithao/public/chair/papers/${paperId}/assign`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try multiple selectors to find content
            let content = null;
            const selectors = ['main', '.main-content', '.container', 'body > *'];
            for (const selector of selectors) {
                const element = doc.querySelector(selector);
                if (element && element.innerHTML.trim()) {
                    content = element.innerHTML;
                    break;
                }
            }
            
            if (content) {
                this.assignReviewerData = content;
            } else {
                throw new Error('No content found');
            }
        } catch (error) {
            console.error('Error loading assign reviewer:', error);
            this.assignReviewerData = `<div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'><p class='text-red-600 font-medium'>Không thể tải trang phân công</p><p class='text-red-500 text-sm mt-2'>Lỗi: ${error.message}</p></div>`;
        } finally {
            this.loading = false;
        }
    },
    
    async viewReviews(paperId) {
        this.selectedPaperId = paperId;
        this.currentView = 'reviews';
        this.loading = true;
        this.reviewsData = null;
        
        try {
            const response = await fetch(`/qly_hthao/qlyhoithao/public/chair/papers/${paperId}/reviews`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try multiple selectors
            let content = null;
            const selectors = ['.main-content', 'main', '.container', 'body > *'];
            for (const selector of selectors) {
                const element = doc.querySelector(selector);
                if (element && element.innerHTML.trim()) {
                    content = element.innerHTML;
                    break;
                }
            }
            
            if (content) {
                this.reviewsData = content;
            } else {
                throw new Error('No content found');
            }
        } catch (error) {
            console.error('Error loading reviews:', error);
            this.reviewsData = `<div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'><p class='text-red-600 font-medium'>Không thể tải danh sách nhận xét</p><p class='text-red-500 text-sm mt-2'>Lỗi: ${error.message}</p></div>`;
        } finally {
            this.loading = false;
        }
    },
    
    async viewDecision(paperId) {
        this.selectedPaperId = paperId;
        this.currentView = 'decision';
        this.loading = true;
        this.decisionData = null;
        
        try {
            const response = await fetch(`/qly_hthao/qlyhoithao/public/chair/papers/${paperId}/decision`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try multiple selectors
            let content = null;
            const selectors = ['.main-content', 'main', '.container', 'body > *'];
            for (const selector of selectors) {
                const element = doc.querySelector(selector);
                if (element && element.innerHTML.trim()) {
                    content = element.innerHTML;
                    break;
                }
            }
            
            if (content) {
                this.decisionData = content;
            } else {
                throw new Error('No content found');
            }
        } catch (error) {
            console.error('Error loading decision:', error);
            this.decisionData = `<div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'><p class='text-red-600 font-medium'>Không thể tải trang quyết định</p><p class='text-red-500 text-sm mt-2'>Lỗi: ${error.message}</p></div>`;
        } finally {
            this.loading = false;
        }
    },
    
    async loadReviewers() {
        this.loading = true;
        this.reviewersData = null;
        
        try {
            const response = await fetch('/qly_hthao/qlyhoithao/public/chair/reviewers');
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try multiple selectors
            let content = null;
            const selectors = ['.main-content', 'main', '.container', 'body > div'];
            for (const selector of selectors) {
                const element = doc.querySelector(selector);
                if (element && element.innerHTML.trim()) {
                    content = element.innerHTML;
                    break;
                }
            }
            
            if (content) {
                this.reviewersData = content;
            } else {
                throw new Error('No content found');
            }
        } catch (error) {
            console.error('Error loading reviewers:', error);
            this.reviewersData = `<div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'><p class='text-red-600 font-medium text-lg mb-2'>❌ Không thể tải danh sách phản biện</p><p class='text-red-500 text-sm mt-2'>Lỗi: ${error.message}</p><p class='text-red-500 text-sm mt-1'>Vui lòng thử lại hoặc <a href="/qly_hthao/qlyhoithao/public/chair/reviewers" class="underline font-medium">mở trực tiếp</a></p></div>`;
        } finally {
            this.loading = false;
        }
    },
                
                async loadReviewersView() {
                    this.currentView = 'reviewers';
                    if (!this.reviewersData) {
                        await this.loadReviewers();
                    }
                }
            };
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="chairDashboard()">
    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-orange-800 via-orange-700 to-orange-600 text-white shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <a href="{{ route('home') }}" class="flex items-center space-x-4 hover:opacity-90 transition">
                    <div class="flex-shrink-0 bg-white rounded-full p-2">
                        <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-8 h-8 rounded-full object-cover" />
                    </div>
                    <div>
                        <div class="text-xl font-bold">HUIT Conferences</div>
                        <div class="text-xs text-orange-100">Chair Dashboard</div>
                    </div>
                </a>

                <!-- Right Side Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 hover:bg-orange-700 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 hover:bg-orange-700 px-3 py-2 rounded-lg transition">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name ?? 'C') }}&background=ea580c&color=fff&bold=true" 
                                 alt="User" 
                                 class="w-8 h-8 rounded-full border-2 border-orange-300">
                            <span class="font-medium hidden md:block">{{ Auth::user()->full_name ?? 'Chair User' }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl z-50 overflow-hidden"
                             style="display: none;">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 transition">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Hồ sơ</span>
                                </div>
                            </a>
                            <a href="{{ route('home') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 transition border-t">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span>Trang chủ</span>
                                </div>
                            </a>
                            <div class="border-t">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Đăng xuất</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <button @click="switchView('dashboard')" 
                        :class="currentView === 'dashboard' ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </button>
                
                <button @click="switchView('papers')" 
                        :class="currentView === 'papers' || currentView === 'paper-detail' || currentView === 'assign-reviewer' || currentView === 'reviews' || currentView === 'decision' ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Quản lý bài báo</span>
                </button>
                
                <button @click="loadReviewersView()" 
                        :class="currentView === 'reviewers' ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Quản lý reviewer</span>
                </button>
                
                <button @click="switchView('papers')" 
                        :class="currentView === 'papers' || currentView === 'paper-detail' || currentView === 'assign-reviewer' || currentView === 'reviews' || currentView === 'decision' ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Phân công phản biện</span>
                </button>
                
                <button @click="window.location.href='{{ route('chair.coi.index') }}'" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Kiểm tra COI</span>
                </button>
                
                <button @click="alert('Chức năng Trợ giúp đang phát triển')" 
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Trợ giúp</span>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'dashboard'" x-cloak>
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Quản lý hội thảo và phản biện</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Papers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Tổng bài báo</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_papers'] ?? 0 }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Từ {{ $conferences->count() }} hội thảo</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Accepted Papers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đã chấp nhận</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['accepted'] ?? 0 }}</h3>
                            <p class="text-xs text-green-600 mt-2">✓ Đã duyệt</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Under Review -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Đang review</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['under_review'] ?? 0 }}</h3>
                            <p class="text-xs text-blue-600 mt-2">⏳ Đang xử lý</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Need Reviewers -->
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Cần reviewer</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['needs_reviewers'] ?? 0 }}</h3>
                            <p class="text-xs text-orange-600 mt-2">⚠ Cần phân công</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Papers Requiring Action -->
            <div class="bg-white rounded-xl shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Bài báo gần đây</h2>
                        <a href="{{ route('chair.papers') }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Xem tất cả →</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tiêu đề</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tác giả</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reviewers</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPapers as $paper)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $paper->paper_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($paper->title, 50) }}</div>
                                    <div class="text-xs text-gray-500">Nộp: {{ \Carbon\Carbon::parse($paper->created_at)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->author_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if($paper->reviews_total > 0)
                                            <span class="text-blue-600 font-medium">{{ $paper->reviews_total }} reviewer(s)</span>
                                            @if($paper->reviews_completed > 0)
                                                <div class="text-xs text-green-600 mt-1">
                                                    {{ $paper->reviews_completed }}/{{ $paper->reviews_total }} hoàn thành
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-orange-600 text-xs">⚠ Chưa phân công</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'SUBMITTED' => ['label' => 'Đã nộp', 'class' => 'bg-blue-100 text-blue-800'],
                                            'UNDER_REVIEW' => ['label' => 'Đang xét', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'REVIEWED' => ['label' => 'Đã xét', 'class' => 'bg-purple-100 text-purple-800'],
                                            'ACCEPTED' => ['label' => 'Chấp nhận', 'class' => 'bg-green-100 text-green-800'],
                                            'REJECTED' => ['label' => 'Từ chối', 'class' => 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusConfig[$paper->status_code] ?? ['label' => $paper->status_code, 'class' => 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button @click="viewPaperDetail({{ $paper->paper_id }})" class="text-orange-600 hover:text-orange-700 font-medium hover:underline">Chi tiết →</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Chưa có bài báo nào</p>
                                    <p class="text-sm mt-1">Bài báo mới nộp sẽ xuất hiện ở đây</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Grid: Conferences & Pending Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Conferences -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hội thảo của tôi
                    </h3>
                    <div class="space-y-3">
                        @forelse($conferences as $conf)
                        <div class="border-l-4 border-orange-500 pl-4 py-3 bg-orange-50 rounded-r-lg hover:bg-orange-100 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $conf->title }}</p>
                                    <div class="flex items-center mt-2 text-xs text-gray-600 space-x-4">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            @if($conf->deadline_submission)
                                                Deadline: {{ \Carbon\Carbon::parse($conf->deadline_submission)->format('d/m/Y') }}
                                            @else
                                                Chưa có deadline
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="text-sm">Chưa có hội thảo nào</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Actions -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Công việc cần xử lý
                    </h3>
                    <div class="space-y-3">
                        @forelse($pendingActions as $action)
                        <div class="flex items-start space-x-3 p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition border-l-4 border-orange-500">
                            <div class="flex-shrink-0 mt-1">
                                @if($action['type'] === 'assign_reviewers')
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                @else
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $action['message'] }}</p>
                                <div class="mt-1 flex items-center text-xs">
                                    @if($action['type'] === 'assign_reviewers')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-medium">
                                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1"></span>
                                            Ưu tiên cao
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-medium">
                                            <span class="w-1.5 h-1.5 bg-yellow-600 rounded-full mr-1"></span>
                                            Ưu tiên trung bình
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium">Tuyệt vời! Không có công việc cần xử lý</p>
                            <p class="text-xs mt-1">Tất cả đã được hoàn thành</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>

        <!-- Papers Management View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'papers'" x-cloak>
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Quản lý bài báo</h1>
                <p class="text-gray-600 mt-1">Xem và quản lý tất cả bài báo trong hội thảo</p>
            </div>

            <!-- Papers Content Loaded Dynamically -->
            <div id="papers-content">
                <div class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải dữ liệu bài báo...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
            </div>

            <script>
                // Load papers content when view changes
                document.addEventListener('alpine:initialized', () => {
                    // Watch for view changes
                    let papersLoaded = false;
                    
                    setInterval(() => {
                        const currentView = Alpine.$data(document.body).currentView;
                        if (currentView === 'papers' && !papersLoaded) {
                            papersLoaded = true;
                            loadPapersContent();
                        }
                    }, 100);
                });

                async function loadPapersContent() {
                    try {
                        const response = await fetch(window.appRoutes.chairPapers, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        
                        const html = await response.text();
                        
                        // Extract just the content part (remove sidebar and navigation)
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Get the main content area
                        const mainContent = doc.querySelector('main');
                        
                        if (mainContent) {
                            document.getElementById('papers-content').innerHTML = mainContent.innerHTML;
                        } else {
                            document.getElementById('papers-content').innerHTML = '<div class="text-center py-12"><p class="text-red-600">Lỗi tải dữ liệu</p></div>';
                        }
                    } catch (error) {
                        console.error('Error loading papers:', error);
                        document.getElementById('papers-content').innerHTML = '<div class="text-center py-12"><p class="text-red-600">Lỗi kết nối. Vui lòng thử lại.</p></div>';
                    }
                }
            </script>
        </main>

        <!-- Paper Detail View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'paper-detail'" x-cloak>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <button @click="currentView = 'dashboard'" class="text-sm text-gray-600 hover:text-gray-900 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Quay lại Dashboard
                    </button>
                    <h1 class="text-3xl font-bold text-gray-900">Chi tiết bài báo</h1>
                    <p class="text-gray-600 mt-1">Thông tin chi tiết và quản lý bài báo</p>
                </div>
            </div>

            <!-- Paper Detail Content Loaded Dynamically -->
            <div id="paper-detail-content">
                <div class="flex items-center justify-center h-96" x-show="loading">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải chi tiết bài báo...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
                <div x-show="!loading && paperDetailData" x-html="paperDetailData"></div>
            </div>
        </main>

        <!-- Assign Reviewer View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'assign-reviewer'" x-cloak>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <button @click="currentView = 'paper-detail'; viewPaperDetail(selectedPaperId)" class="text-sm text-gray-600 hover:text-gray-900 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Quay lại chi tiết bài báo
                    </button>
                    <h1 class="text-3xl font-bold text-gray-900">Phân công phản biện</h1>
                    <p class="text-gray-600 mt-1">Chọn reviewer để phân công phản biện bài báo</p>
                </div>
            </div>

            <!-- Assign Reviewer Content Loaded Dynamically -->
            <div id="assign-reviewer-content">
                <div class="flex items-center justify-center h-96" x-show="loading">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải trang phân công...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
                <div x-show="!loading && assignReviewerData" x-html="assignReviewerData"></div>
            </div>
        </main>

        <!-- Reviews View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'reviews'" x-cloak>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <button @click="viewPaperDetail(selectedPaperId)" class="text-sm text-gray-600 hover:text-gray-900 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Quay lại chi tiết bài báo
                    </button>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Tất cả nhận xét</h1>
                    <p class="text-gray-600 mt-1">Xem và quản lý các nhận xét của reviewer</p>
                </div>
            </div>

            <!-- Reviews Content Loaded Dynamically -->
            <div id="reviews-content">
                <div class="flex items-center justify-center h-96" x-show="loading">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải danh sách nhận xét...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
                <div x-show="!loading && reviewsData" x-html="reviewsData"></div>
            </div>
        </main>

        <!-- Final Decision View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'decision'" x-cloak>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <button @click="viewPaperDetail(selectedPaperId)" class="text-sm text-gray-600 hover:text-gray-900 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Quay lại chi tiết bài báo
                    </button>
                    <h1 class="text-3xl font-bold text-gray-900">⚖️ Quyết định cuối cùng</h1>
                    <p class="text-gray-600 mt-1">Đưa ra quyết định chấp nhận, từ chối hoặc yêu cầu sửa lại bài báo</p>
                </div>
            </div>

            <!-- Decision Content Loaded Dynamically -->
            <div id="decision-content">
                <div class="flex items-center justify-center h-96" x-show="loading">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải trang quyết định...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
                <div x-show="!loading && decisionData" x-html="decisionData"></div>
            </div>
        </main>

        <!-- Reviewers Management View -->
        <main class="flex-1 p-6 lg:p-8" x-show="currentView === 'reviewers'" x-cloak>
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">👥 Quản lý Reviewer</h1>
                <p class="text-gray-600 mt-1">Xem thông tin, thống kê và hiệu suất của các reviewer</p>
            </div>

            <!-- Reviewers Content Loaded Dynamically -->
            <div id="reviewers-content">
                <div class="flex items-center justify-center h-96" x-show="loading">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
                        <p class="text-gray-600">Đang tải danh sách reviewer...</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chờ...</p>
                    </div>
                </div>
                <div x-show="!loading && reviewersData" x-html="reviewersData"></div>
            </div>
        </main>
    </div>
</body>
</html>

