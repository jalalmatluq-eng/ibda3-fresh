<!-- Notification System -->
<div id="notificationContainer" class="fixed top-4 left-4 z-50 space-y-2">
    <!-- Notifications will be dynamically added here -->
</div>

<script>
// Notification Manager
class NotificationManager {
    constructor() {
        this.container = document.getElementById('notificationContainer');
        this.notifications = [];
        this.maxNotifications = 5;
    }

    show(message, type = 'info', duration = 5000) {
        const notification = this.createNotification(message, type);
        this.container.appendChild(notification);
        this.notifications.push(notification);

        // Remove old notifications if too many
        if (this.notifications.length > this.maxNotifications) {
            const oldNotification = this.notifications.shift();
            this.removeNotification(oldNotification);
        }

        // Auto remove after duration
        setTimeout(() => {
            this.removeNotification(notification);
        }, duration);

        // Initialize Lucide icons
        lucide.createIcons();
    }

    createNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
            this.getNotificationClasses(type)
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i data-lucide="${this.getNotificationIcon(type)}" class="w-5 h-5 ml-3 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="notificationManager.remove(this.parentElement.parentElement)" class="mr-2 text-current opacity-70 hover:opacity-100">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        
        // Trigger animation
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);
        
        return notification;
    }

    removeNotification(notification) {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
            const index = this.notifications.indexOf(notification);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }, 300);
    }

    getNotificationClasses(type) {
        const classes = {
            'success': 'bg-green-100 border border-green-200 text-green-800',
            'error': 'bg-red-100 border border-red-200 text-red-800',
            'warning': 'bg-yellow-100 border border-yellow-200 text-yellow-800',
            'info': 'bg-blue-100 border border-blue-200 text-blue-800'
        };
        return classes[type] || classes.info;
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'alert-circle',
            'warning': 'alert-triangle',
            'info': 'info'
        };
        return icons[type] || icons.info;
    }

    // Real-time notification checking
    async function checkNotifications() {
        try {
            const response = await fetch('/api/notifications', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const notifications = await response.json();
                notifications.forEach(notification => {
                    if (!notification.read) {
                        this.show(notification.message, notification.type);
                        this.markAsRead(notification.id);
                    }
                });
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    async function markAsRead(notificationId) {
        try {
            await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Start checking for new notifications
    startRealTimeChecking() {
        // Check immediately
        this.checkNotifications();
        
        // Then check every 30 seconds
        setInterval(() => {
            this.checkNotifications();
        }, 30000);
    }
}

// Global notification manager
const notificationManager = new NotificationManager();

// Override the showNotification function
function showNotification(message, type = 'info', duration = 5000) {
    notificationManager.show(message, type, duration);
}

// Start real-time notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('token')) {
        notificationManager.startRealTimeChecking();
    }
});
</script>
