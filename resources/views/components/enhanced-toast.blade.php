<!-- Enhanced Toast Notification Component -->
<div x-data="toastManager()" class="fixed top-4 right-4 z-50 space-y-2" x-init="init()">
    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-show="toast.visible"
            x-transition:enter="animate-slide-in-right"
            x-transition:leave="animate-slide-out-right"
            :class="`
                max-w-sm w-full shadow-lg rounded-xl pointer-events-auto overflow-hidden animate-bounce-in
                ${toast.type === 'success' ? 'bg-green-500' : ''}
                ${toast.type === 'error' ? 'bg-red-500' : ''}
                ${toast.type === 'info' ? 'bg-blue-500' : ''}
                ${toast.type === 'warning' ? 'bg-yellow-500' : ''}
            `"
            class="relative"
        >
            <!-- Progress Bar -->
            <div 
                :class="`absolute top-0 left-0 h-1 bg-white bg-opacity-30 animate-progress`"
                :style="`animation-duration: ${toast.duration}ms`"
            ></div>
            
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Icon with Animation -->
                    <div class="flex-shrink-0">
                        <template x-if="toast.type === 'success'">
                            <div class="animate-bounce-in text-white text-xl">✅</div>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <div class="animate-shake text-white text-xl">❌</div>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <div class="animate-pulse-slow text-white text-xl">ℹ️</div>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <div class="animate-pulse text-white text-xl">⚠️</div>
                        </template>
                    </div>
                    
                    <!-- Content -->
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-white" x-text="toast.title"></p>
                        <p x-show="toast.message" class="mt-1 text-sm text-white text-opacity-90" x-text="toast.message"></p>
                    </div>
                    
                    <!-- Close Button -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button 
                            @click="removeToast(toast.id)"
                            class="bg-white bg-opacity-20 rounded-md p-1 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-white transition-all duration-200 hover-glow"
                        >
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- Toast Manager Script -->
<script>
    function toastManager() {
        return {
            toasts: [],
            
            init() {
                // Listen for custom toast events
                window.addEventListener('show-toast', (event) => {
                    this.addToast(event.detail);
                });
                
                // Global toast functions
                window.showToast = (type, title, message = '', duration = 5000) => {
                    this.addToast({ type, title, message, duration });
                };
                
                window.showSuccess = (title, message = '') => {
                    this.addToast({ type: 'success', title, message, duration: 4000 });
                };
                
                window.showError = (title, message = '') => {
                    this.addToast({ type: 'error', title, message, duration: 6000 });
                };
                
                window.showInfo = (title, message = '') => {
                    this.addToast({ type: 'info', title, message, duration: 5000 });
                };
                
                window.showWarning = (title, message = '') => {
                    this.addToast({ type: 'warning', title, message, duration: 5000 });
                };
            },
            
            addToast(options) {
                const toast = {
                    id: Date.now() + Math.random(),
                    type: options.type || 'info',
                    title: options.title || 'Notification',
                    message: options.message || '',
                    duration: options.duration || 5000,
                    visible: false
                };
                
                this.toasts.push(toast);
                
                // Trigger entrance animation
                this.$nextTick(() => {
                    toast.visible = true;
                });
                
                // Auto remove after duration
                setTimeout(() => {
                    this.removeToast(toast.id);
                }, toast.duration);
            },
            
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    // Remove from array after animation
                    setTimeout(() => {
                        const index = this.toasts.findIndex(t => t.id === id);
                        if (index > -1) {
                            this.toasts.splice(index, 1);
                        }
                    }, 300);
                }
            }
        };
    }

    // Custom animations for toast
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slide-in-right {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slide-out-right {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        .animate-slide-in-right { animation: slide-in-right 0.3s ease-out; }
        .animate-slide-out-right { animation: slide-out-right 0.3s ease-in; }
        .animate-progress { animation: progress linear; }
    `;
    document.head.appendChild(style);
</script>