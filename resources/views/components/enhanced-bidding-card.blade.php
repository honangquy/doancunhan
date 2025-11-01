<!-- Enhanced Bidding Component with Animations -->
<div class="bg-white rounded-xl shadow-lg hover-lift animate-fade-in-up p-6" x-data="{
    bidValue: 0,
    coi: false,
    submitting: false,
    success: false,
    error: false,
    
    getBidColor(value) {
        const colors = {
            0: { bg: 'bg-gray-100', text: 'text-gray-600', border: 'border-gray-200' },
            1: { bg: 'bg-red-100', text: 'text-red-700', border: 'border-red-200' },
            2: { bg: 'bg-yellow-100', text: 'text-yellow-700', border: 'border-yellow-200' },
            3: { bg: 'bg-green-100', text: 'text-green-700', border: 'border-green-200' }
        };
        return colors[value] || colors[0];
    },
    
    getBidIcon(value) {
        const icons = {
            0: '😐',
            1: '😕',
            2: '🙂',
            3: '😍'
        };
        return icons[value] || icons[0];
    },
    
    async submitBid(paperId) {
        this.submitting = true;
        this.error = false;
        
        try {
            const response = await fetch('/api/reviewer/bidding', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    paper_id: paperId,
                    bidding_value: this.bidValue,
                    coi: this.coi
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.success = true;
                setTimeout(() => this.success = false, 3000);
            } else {
                this.error = true;
                setTimeout(() => this.error = false, 3000);
            }
        } catch (error) {
            this.error = true;
            setTimeout(() => this.error = false, 3000);
        } finally {
            this.submitting = false;
        }
    }
}">
    <!-- Paper Header with Animation -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1 animate-fade-in-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $paper->title ?? 'AI in Healthcare Research' }}</h3>
            <div class="flex items-center space-x-3 text-sm text-gray-500">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $paper->author_name ?? 'Dr. John Smith' }}
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v1H6V9a2 2 0 012-2h3z"></path>
                    </svg>
                    {{ $paper->conference_name ?? 'HUIT Tech Conference 2025' }}
                </span>
            </div>
        </div>
        
        <!-- Status Badge with Animation -->
        <div class="animate-bounce-in">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse-slow">
                🆕 Mới nộp
            </span>
        </div>
    </div>

    <!-- Paper Abstract -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4 animate-fade-in-up">
        <p class="text-sm text-gray-700 leading-relaxed">
            {{ Str::limit($paper->abstract ?? 'This research explores the application of artificial intelligence in modern healthcare systems, focusing on diagnostic accuracy, patient outcomes, and system efficiency improvements.', 200) }}
        </p>
    </div>

    <!-- Bidding Interface -->
    <div class="border-t pt-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            Mức độ quan tâm
        </h4>
        
        <!-- Bidding Scale -->
        <div class="space-y-3">
            <div class="grid grid-cols-4 gap-2">
                <template x-for="value in [0, 1, 2, 3]" :key="value">
                    <button 
                        @click="bidValue = value"
                        :class="`
                            relative overflow-hidden btn-ripple transition-all duration-300 p-3 rounded-lg border-2 text-center hover-glow
                            ${bidValue === value ? 'ring-2 ring-purple-500 ring-opacity-50 transform scale-105' : ''}
                            ${getBidColor(value).bg} ${getBidColor(value).text} ${getBidColor(value).border}
                        `"
                        class="group"
                    >
                        <div class="text-xl mb-1" x-text="getBidIcon(value)"></div>
                        <div class="text-xs font-medium" x-text="['Không quan tâm', 'Ít quan tâm', 'Quan tâm', 'Rất quan tâm'][value]"></div>
                        <div class="text-xs opacity-75" x-text="`${value}/3`"></div>
                        
                        <!-- Animated selection indicator -->
                        <div x-show="bidValue === value" x-transition class="absolute inset-0 bg-white bg-opacity-20 animate-pulse"></div>
                    </button>
                </template>
            </div>
            
            <!-- Selected Bid Display -->
            <div x-show="bidValue > 0" x-transition class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-3 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg" x-text="getBidIcon(bidValue)"></span>
                        <span class="text-sm font-medium text-gray-800">
                            Bạn đã chọn: <span x-text="['', 'Ít quan tâm', 'Quan tâm', 'Rất quan tâm'][bidValue]" class="text-purple-700"></span>
                        </span>
                    </div>
                    <span :class="getBidColor(bidValue).bg + ' ' + getBidColor(bidValue).text" class="px-2 py-1 rounded-full text-xs font-bold badge-bounce" x-text="`${bidValue}/3`"></span>
                </div>
            </div>
        </div>

        <!-- COI Declaration -->
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg animate-fade-in-up" style="animation-delay: 0.4s;">
            <label class="flex items-start space-x-3 cursor-pointer group">
                <input 
                    type="checkbox" 
                    x-model="coi"
                    class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500 focus:ring-2"
                >
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-red-800">⚠️ Conflict of Interest (COI)</span>
                        <div class="group relative">
                            <svg class="w-4 h-4 text-red-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="invisible group-hover:visible absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap">
                                Xung đột lợi ích với tác giả
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-red-600 mt-1">
                        Tôi xác nhận có xung đột lợi ích với bài báo này
                    </p>
                </div>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mt-4 animate-fade-in-up" style="animation-delay: 0.6s;">
            <button 
                @click="submitBid({{ $paper->paper_id ?? 1 }})"
                :disabled="submitting"
                :class="`
                    w-full btn-ripple transition-all duration-300 px-4 py-3 rounded-lg font-medium text-sm
                    ${submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 hover-glow'}
                    text-white relative overflow-hidden
                `"
            >
                <span x-show="!submitting">🚀 Gửi Bidding</span>
                <span x-show="submitting" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang gửi...
                </span>
            </button>
        </div>

        <!-- Success/Error Messages -->
        <div x-show="success" x-transition class="mt-3 p-3 bg-green-100 border border-green-200 rounded-lg success-flash">
            <div class="flex items-center space-x-2 text-green-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">✅ Bidding đã được gửi thành công!</span>
            </div>
        </div>

        <div x-show="error" x-transition class="mt-3 p-3 bg-red-100 border border-red-200 rounded-lg error-flash animate-shake">
            <div class="flex items-center space-x-2 text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">❌ Có lỗi xảy ra. Vui lòng thử lại!</span>
            </div>
        </div>
    </div>
</div>