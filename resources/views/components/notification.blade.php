{{-- Modern Notification Component with SVG Icons and Smooth Animations --}}
<div id="notification-container"></div>

{{-- Modern Notification Styles --}}
<style>
/* Container Styles */
#notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    pointer-events: none;
    max-width: 400px;
    width: 100%;
}

/* Base Notification Styles */
.notification {
    position: relative;
    margin-bottom: 12px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    pointer-events: auto;
    overflow: hidden;
    transform: translateX(100%) scale(0.8);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.notification.show {
    transform: translateX(0) scale(1);
    opacity: 1;
}

.notification.hide {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
}

/* Content Layout */
.notification-content {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    gap: 12px;
}

.notification-icon-wrapper {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-icon {
    width: 20px;
    height: 20px;
    stroke-width: 2.5;
}

.notification-text {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #1f2937;
}

.notification-message {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.4;
}

.notification-close {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
    color: #9ca3af;
}

.notification-close:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #6b7280;
}

.notification-close svg {
    width: 14px;
    height: 14px;
}

/* Progress Bar */
.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    opacity: 0.3;
    animation: progress linear;
    transform-origin: left;
}

@keyframes progress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}

/* Type-specific Styles */
.notification-success {
    border-left: 4px solid #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(255, 255, 255, 0.95));
}

.notification-success .notification-icon-wrapper {
    color: #10b981;
}

.notification-success .notification-progress {
    background: #10b981;
}

.notification-error {
    border-left: 4px solid #ef4444;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(255, 255, 255, 0.95));
}

.notification-error .notification-icon-wrapper {
    color: #ef4444;
}

.notification-error .notification-progress {
    background: #ef4444;
}

.notification-warning {
    border-left: 4px solid #f59e0b;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(255, 255, 255, 0.95));
}

.notification-warning .notification-icon-wrapper {
    color: #f59e0b;
}

.notification-warning .notification-progress {
    background: #f59e0b;
}

.notification-info {
    border-left: 4px solid #3b82f6;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(255, 255, 255, 0.95));
}

.notification-info .notification-icon-wrapper {
    color: #3b82f6;
}

.notification-info .notification-progress {
    background: #3b82f6;
}

/* Hover Effects */
.notification:hover {
    transform: translateX(-4px) scale(1.02);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.notification:hover .notification-progress {
    animation-play-state: paused;
}

/* Mobile Responsiveness */
@media (max-width: 640px) {
    #notification-container {
        left: 20px;
        right: 20px;
        top: 20px;
        max-width: none;
    }
    
    .notification {
        margin-bottom: 8px;
    }
    
    .notification-content {
        padding: 12px;
    }
}
</style>

{{-- Modern Notification System with Smooth Animations --}}
<script>

// Advanced notification system with beautiful animations
window.NotificationManager = {
    notifications: [],
    nextId: 1,
    container: null,

    init() {
        if (!this.container) {
            this.container = document.getElementById('notification-container');
        }
        return this.container !== null;
    },

    show(type, title, message, duration = 5000) {
        if (!this.init()) {
            console.warn('Notification container not found, falling back to alert');
            alert(`${title}\n\n${message}`);
            return null;
        }

        const id = this.nextId++;
        const notification = this.createNotificationElement(id, type, title, message, duration);
        
        this.container.appendChild(notification);
        this.notifications.push({ id, element: notification, type, title, message });

        // Trigger enter animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, duration);
        }

        return id;
    },

    createNotificationElement(id, type, title, message, duration) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.dataset.id = id;

        const icons = {
            'success': `<svg class="notification-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>`,
            'error': `<svg class="notification-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>`,
            'warning': `<svg class="notification-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>`,
            'info': `<svg class="notification-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`
        };

        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon-wrapper">
                    ${icons[type] || icons.info}
                </div>
                <div class="notification-text">
                    <div class="notification-title">${title}</div>
                    <div class="notification-message">${message}</div>
                </div>
                <button class="notification-close" onclick="NotificationManager.remove(${id})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            ${duration > 0 ? `<div class="notification-progress" style="animation-duration: ${duration}ms;"></div>` : ''}
        `;

        return notification;
    },

    remove(id) {
        const notification = this.container?.querySelector(`[data-id="${id}"]`);
        if (notification) {
            notification.classList.add('hide');
            
            setTimeout(() => {
                notification.remove();
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 300);
        }
    },

    clear() {
        this.notifications.forEach(n => this.remove(n.id));
    }
};

window.showNotification = function(type, title, message, duration = 5000) {
    return window.NotificationManager.show(type, title, message, duration);
};

window.showSuccess = function(title, message, duration = 4000) {
    return window.showNotification('success', title, message, duration);
};

window.showError = function(title, message, duration = 6000) {
    return window.showNotification('error', title, message, duration);
};

window.showWarning = function(title, message, duration = 5000) {
    return window.showNotification('warning', title, message, duration);
};

window.showInfo = function(title, message, duration = 4000) {
    return window.showNotification('info', title, message, duration);
};

// Initialize notification system when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.NotificationManager.init();
    console.log('✨ Notification system initialized');
});
</script>