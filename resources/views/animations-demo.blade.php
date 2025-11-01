<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Animations Demo - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/animations.css') }}" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white/10 backdrop-blur-md border-b border-white/20 animate-fade-in-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <img src="https://foodtech.huit.edu.vn/images_new/logo_en.png" alt="HUIT logo" class="w-10 h-10 bg-white rounded-full object-cover shadow-md animate-bounce-in" />
                    <div class="animate-fade-in-left">
                        <div class="font-bold text-lg text-white">HUIT Conferences</div>
                        <div class="text-xs text-purple-200">UI Animations Demo</div>
                    </div>
                </div>
                
                <div class="animate-fade-in-right">
                    <button onclick="showSuccess('Demo Started!', 'Welcome to the animations showcase')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 hover-glow">
                        🚀 Test Animations
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Page Title -->
        <div class="text-center mb-12 animate-fade-in-up">
            <h1 class="text-5xl font-bold text-white mb-4 animate-gradient">🎨 UI Animations Showcase</h1>
            <p class="text-purple-200 text-lg">Demonstrating all animations and polish features</p>
        </div>

        <!-- Animation Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            
            <!-- Card Animations -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 animate-fade-in-left hover-lift">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-3">🃏</span> Card Animations
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 p-4 rounded-lg animate-scale-in hover-glow">
                        <div class="text-white text-sm font-medium">Scale In</div>
                        <div class="text-white/80 text-xs mt-1">Smooth entrance</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-blue-500 p-4 rounded-lg animate-bounce-in hover-glow">
                        <div class="text-white text-sm font-medium">Bounce In</div>
                        <div class="text-white/80 text-xs mt-1">Playful entrance</div>
                    </div>
                </div>
            </div>

            <!-- Status Badges -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 animate-fade-in-right hover-lift">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-3">🏷️</span> Status Badges
                </h2>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium animate-pulse-slow">✅ Completed</span>
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium animate-pulse">⏳ Pending</span>
                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm font-medium animate-pulse">❌ Declined</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm font-medium badge-bounce">🎯 Assigned</span>
                        <span class="px-3 py-1 bg-purple-500/20 text-purple-300 rounded-full text-sm font-medium animate-glow">🔥 Hot</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Interactive Demo Section -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-8 border border-white/20 animate-fade-in-up hover-lift" x-data="{
            bidValue: 0,
            showSuccess: false,
            showError: false,
            loading: false,
            
            testAnimation(type) {
                if (type === 'success') {
                    this.showSuccess = true;
                    showSuccess('Success Animation!', 'This is a test success message');
                    setTimeout(() => this.showSuccess = false, 3000);
                } else if (type === 'error') {
                    this.showError = true;
                    showError('Error Animation!', 'This is a test error message');
                    setTimeout(() => this.showError = false, 3000);
                } else if (type === 'loading') {
                    this.loading = true;
                    showInfo('Loading...', 'Testing loading animation');
                    setTimeout(() => { 
                        this.loading = false; 
                        showSuccess('Loaded!', 'Loading completed successfully');
                    }, 3000);
                }
            },
            
            getBidColor(value) {
                const colors = {
                    0: 'bg-gray-100 text-gray-600 border-gray-200',
                    1: 'bg-red-100 text-red-700 border-red-200', 
                    2: 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    3: 'bg-green-100 text-green-700 border-green-200'
                };
                return colors[value] || colors[0];
            }
        }">
            
            <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                <span class="text-3xl mr-3">🎮</span> Interactive Animations Demo
            </h2>

            <!-- Bidding Interface Demo -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-white mb-4">Color-coded Bidding System</h3>
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <template x-for="value in [0, 1, 2, 3]" :key="value">
                        <button 
                            @click="bidValue = value"
                            :class="`
                                p-3 rounded-lg border-2 text-center transition-all duration-300 btn-ripple hover-glow
                                ${bidValue === value ? 'ring-2 ring-purple-500 ring-opacity-50 transform scale-105 animate-glow' : ''}
                                ${getBidColor(value)}
                            `"
                        >
                            <div class="text-xl mb-1" x-text="['😐', '😕', '🙂', '😍'][value]"></div>
                            <div class="text-xs font-medium" x-text="['No Interest', 'Low', 'Medium', 'High'][value]"></div>
                            <div class="text-xs opacity-75" x-text="`${value}/3`"></div>
                        </button>
                    </template>
                </div>
                
                <div x-show="bidValue > 0" x-transition class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-3 border border-purple-200">
                    <span class="text-sm font-medium text-gray-800">Selected: </span>
                    <span x-text="['', 'Low Interest', 'Medium Interest', 'High Interest'][bidValue]" class="text-purple-700 font-semibold"></span>
                    <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold badge-bounce" x-text="`${bidValue}/3`"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 mb-8">
                <button @click="testAnimation('success')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover-lift btn-ripple">
                    ✅ Test Success
                </button>
                <button @click="testAnimation('error')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover-lift btn-ripple animate-shake">
                    ❌ Test Error
                </button>
                <button @click="testAnimation('loading')" :disabled="loading" class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover-lift btn-ripple">
                    <span x-show="!loading">🔄 Test Loading</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </span>
                </button>
                <button onclick="showWarning('Warning!', 'This is a test warning message')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover-lift btn-ripple">
                    ⚠️ Test Warning
                </button>
            </div>

            <!-- Animation Status -->
            <div class="space-y-3">
                <div x-show="showSuccess" x-transition class="p-4 bg-green-100 border border-green-200 rounded-lg success-flash">
                    <div class="flex items-center text-green-800">
                        <span class="text-lg mr-2 animate-bounce-in">✅</span>
                        <span class="font-medium">Success animation triggered!</span>
                    </div>
                </div>
                
                <div x-show="showError" x-transition class="p-4 bg-red-100 border border-red-200 rounded-lg error-flash animate-shake">
                    <div class="flex items-center text-red-800">
                        <span class="text-lg mr-2 animate-pulse">❌</span>
                        <span class="font-medium">Error animation triggered!</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Features Showcase -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 stagger-item hover-lift">
                <div class="text-3xl mb-3 animate-bounce-in">🎨</div>
                <h3 class="text-lg font-semibold text-white mb-2">Rich Animations</h3>
                <p class="text-purple-200 text-sm">Smooth entrance, hover effects, and interactive feedback</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 stagger-item hover-lift">
                <div class="text-3xl mb-3 animate-pulse-slow">🏷️</div>
                <h3 class="text-lg font-semibold text-white mb-2">Color-coded Badges</h3>
                <p class="text-purple-200 text-sm">Intuitive status indicators with visual feedback</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 stagger-item hover-lift">
                <div class="text-3xl mb-3 animate-glow">✨</div>
                <h3 class="text-lg font-semibold text-white mb-2">Enhanced UX</h3>
                <p class="text-purple-200 text-sm">Professional polish with accessibility support</p>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="text-center mt-12 animate-fade-in-up">
            <a href="{{ route('reviewer.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-300 hover-lift animate-glow">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

    </div>

    <!-- Enhanced Toast Component -->
    @include('components.enhanced-toast')

    <!-- Stagger Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger stagger animations
            setTimeout(() => {
                document.querySelectorAll('.stagger-item').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('animate');
                    }, index * 100);
                });
            }, 500);
        });
    </script>

</body>
</html>