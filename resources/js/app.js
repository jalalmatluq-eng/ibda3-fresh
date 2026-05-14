import './bootstrap';

// Global utility functions
window.utils = {
    // Format date to Arabic locale
    formatDate: function(dateString, options = {}) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            ...options
        });
    },

    // Format file size
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Show loading state
    showLoading: function(element, text = 'جاري التحميل...') {
        const originalContent = element.innerHTML;
        element.dataset.originalContent = originalContent;
        element.innerHTML = `
            <div class="flex items-center justify-center">
                <i data-lucide="loader-2" class="animate-spin w-5 h-5 ml-2"></i>
                <span>${text}</span>
            </div>
        `;
        element.disabled = true;
        lucide.createIcons();
    },

    // Hide loading state
    hideLoading: function(element) {
        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }
        element.disabled = false;
        lucide.createIcons();
    },

    // Copy to clipboard
    copyToClipboard: async function(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showNotification('تم النسخ إلى الحافظة', 'success');
        } catch (err) {
            console.error('Failed to copy text: ', err);
            this.showNotification('فشل النسخ إلى الحافظة', 'error');
        }
    },

    // Show notification (global)
    showNotification: function(message, type = 'info', duration = 5000) {
        if (typeof notificationManager !== 'undefined') {
            notificationManager.show(message, type, duration);
        } else {
            // Fallback notification
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    },

    // Validate email
    isValidEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Validate phone number (Saudi format)
    isValidPhone: function(phone) {
        const re = /^05[0-9]{8}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    // Generate random color
    generateColor: function() {
        const colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    },

    // Get user initials
    getInitials: function(name) {
        if (!name) return '?';
        return name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase();
    },

    // Escape HTML
    escapeHtml: function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    // Truncate text
    truncateText: function(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
};

// API helper
window.api = {
    // Base URL for API requests
    baseUrl: '/api',

    // Default headers
    defaultHeaders: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    },

    // Get auth token
    getAuthHeader: function() {
        const token = localStorage.getItem('token');
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    },

    // Make API request
    request: async function(method, endpoint, data = null, options = {}) {
        const url = this.baseUrl + endpoint;
        const headers = {
            ...this.defaultHeaders,
            ...this.getAuthHeader(),
            ...options.headers
        };

        const config = {
            method,
            headers,
            ...options
        };

        if (data) {
            if (data instanceof FormData) {
                config.body = data;
                delete headers['Content-Type']; // Let browser set it for FormData
            } else {
                config.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(url, config);
            
            // Handle 401 Unauthorized
            if (response.status === 401) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/login';
                return;
            }

            // Handle other HTTP errors
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    },

    // Convenience methods
    get: function(endpoint, options = {}) {
        return this.request('GET', endpoint, null, options);
    },

    post: function(endpoint, data, options = {}) {
        return this.request('POST', endpoint, data, options);
    },

    put: function(endpoint, data, options = {}) {
        return this.request('PUT', endpoint, data, options);
    },

    delete: function(endpoint, options = {}) {
        return this.request('DELETE', endpoint, null, options);
    }
};

// Form validation helper
window.formValidator = {
    // Validate form fields
    validateForm: function(formElement, rules = {}) {
        const errors = {};
        const formData = new FormData(formElement);

        for (const [field, rule] of Object.entries(rules)) {
            const value = formData.get(field);
            const fieldErrors = this.validateField(value, rule, field);
            if (fieldErrors.length > 0) {
                errors[field] = fieldErrors;
            }
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    },

    // Validate single field
    validateField: function(value, rule, fieldName) {
        const errors = [];

        if (rule.required && (!value || value.trim() === '')) {
            errors.push('هذا الحقل مطلوب');
        }

        if (rule.minLength && value && value.length < rule.minLength) {
            errors.push(`يجب أن يكون الحقل على الأقل ${rule.minLength} أحرف`);
        }

        if (rule.maxLength && value && value.length > rule.maxLength) {
            errors.push(`يجب أن يكون الحقل على الأكثر ${rule.maxLength} أحرف`);
        }

        if (rule.email && value && !utils.isValidEmail(value)) {
            errors.push('البريد الإلكتروني غير صالح');
        }

        if (rule.phone && value && !utils.isValidPhone(value)) {
            errors.push('رقم الهاتف غير صالح');
        }

        if (rule.pattern && value && !rule.pattern.test(value)) {
            errors.push(rule.message || 'القيمة المدخلة غير صالحة');
        }

        return errors;
    },

    // Display validation errors
    displayErrors: function(formElement, errors) {
        // Clear previous errors
        formElement.querySelectorAll('.error-message').forEach(el => el.remove());
        formElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });

        // Display new errors
        for (const [field, messages] of Object.entries(errors)) {
            const fieldElement = formElement.querySelector(`[name="${field}"]`);
            if (fieldElement) {
                fieldElement.classList.remove('border-gray-300');
                fieldElement.classList.add('border-red-500');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-red-600 text-sm mt-1';
                errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
                fieldElement.parentNode.appendChild(errorDiv);
            }
        }
    }
};

// Security helper
window.security = {
    // Sanitize input
    sanitize: function(input) {
        return utils.escapeHtml(input);
    },

    // Check if user has permission
    hasPermission: function(permission) {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return user.permissions && user.permissions.includes(permission);
    },

    // Check if user has role
    hasRole: function(role) {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return user.role === role;
    },

    // Generate CSRF token
    getCsrfToken: function() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
};

// Performance monitoring
window.performance = {
    // Track page load time
    trackPageLoad: function() {
        window.addEventListener('load', function() {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`Page load time: ${loadTime}ms`);
        });
    },

    // Track API response time
    trackApiResponse: function(url, startTime) {
        const endTime = Date.now();
        const responseTime = endTime - startTime;
        console.log(`API response time for ${url}: ${responseTime}ms`);
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Initialize performance tracking
    performance.trackPageLoad();

    // Add global error handler
    window.addEventListener('error', function(event) {
        console.error('Global error:', event.error);
        utils.showNotification('حدث خطأ غير متوقع', 'error');
    });

    // Add unhandled promise rejection handler
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        utils.showNotification('حدث خطأ في معالجة الطلب', 'error');
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});
