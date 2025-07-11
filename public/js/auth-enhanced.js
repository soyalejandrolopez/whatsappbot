/**
 * ChatBot WhatsApp - Enhanced Authentication JavaScript
 * Provides advanced functionality for the authentication system
 */

class AuthEnhancer {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupAnimations();
        this.setupValidation();
        this.setupToasts();
        this.setupKeyboardShortcuts();
        this.setupAccessibility();
    }

    setupEventListeners() {
        // Form submission with enhanced loading
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    this.handleFormSubmission(form, e);
                });
            });

            // Auto-focus first input
            const firstInput = document.querySelector('.form-control');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 500);
            }

            // Demo credentials click handler
            const demoCard = document.querySelector('.card-body');
            if (demoCard) {
                demoCard.style.cursor = 'pointer';
                demoCard.addEventListener('click', () => {
                    this.fillDemoCredentials();
                });
            }
        });
    }

    setupAnimations() {
        // Stagger animation for form elements
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group, index) => {
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                group.style.transition = 'all 0.6s ease';
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, 200 + (index * 100));
        });

        // Logo pulse animation
        const logo = document.querySelector('.logo');
        if (logo) {
            logo.addEventListener('mouseenter', () => {
                logo.classList.add('pulse');
            });
            
            logo.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    logo.classList.remove('pulse');
                }, 2000);
            });
        }
    }

    setupValidation() {
        const inputs = document.querySelectorAll('.form-control');
        
        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('input', () => {
                this.validateField(input);
            });

            // Enhanced focus effects
            input.addEventListener('focus', () => {
                this.addFocusEffect(input);
            });

            input.addEventListener('blur', () => {
                this.removeFocusEffect(input);
                this.validateField(input);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const fieldName = field.name;
        
        // Clear previous validation
        this.clearFieldValidation(field);

        let isValid = true;
        let message = '';

        // Validation rules
        switch (fieldName) {
            case 'email':
                if (!value) {
                    isValid = false;
                    message = 'El correo electrónico es obligatorio.';
                } else if (!this.isValidEmail(value)) {
                    isValid = false;
                    message = 'Por favor ingresa un correo electrónico válido.';
                }
                break;

            case 'password':
                if (!value) {
                    isValid = false;
                    message = 'La contraseña es obligatoria.';
                } else if (value.length < 6) {
                    isValid = false;
                    message = 'La contraseña debe tener al menos 6 caracteres.';
                }
                break;

            case 'password_confirmation':
                const passwordField = document.querySelector('input[name="password"]');
                if (passwordField && value !== passwordField.value) {
                    isValid = false;
                    message = 'Las contraseñas no coinciden.';
                }
                break;
        }

        // Apply validation result
        if (isValid) {
            this.showFieldSuccess(field);
        } else {
            this.showFieldError(field, message);
        }

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    clearFieldValidation(field) {
        field.classList.remove('is-valid', 'is-invalid', 'error-animation');
        const feedback = field.parentNode.querySelector('.invalid-feedback:not(#password-match-feedback)');
        if (feedback) {
            feedback.remove();
        }
    }

    showFieldSuccess(field) {
        field.classList.add('is-valid');
        field.classList.add('success-animation');
        setTimeout(() => field.classList.remove('success-animation'), 1000);
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid', 'error-animation');
        
        if (field.name !== 'password_confirmation') {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;
            field.parentNode.appendChild(errorDiv);
        }

        setTimeout(() => field.classList.remove('error-animation'), 500);
    }

    addFocusEffect(field) {
        const icon = field.parentNode.querySelector('.input-icon');
        if (icon) {
            icon.style.color = 'var(--primary-color)';
            icon.style.transform = 'translateY(-50%) scale(1.1)';
        }
    }

    removeFocusEffect(field) {
        const icon = field.parentNode.querySelector('.input-icon');
        if (icon) {
            icon.style.color = '';
            icon.style.transform = '';
        }
    }

    handleFormSubmission(form, event) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.loading-spinner');
        const btnText = submitBtn.querySelector('.btn-text');

        // Validate all fields
        const inputs = form.querySelectorAll('.form-control');
        let allValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                allValid = false;
            }
        });

        if (!allValid) {
            event.preventDefault();
            this.showToast('Por favor corrige los errores en el formulario.', 'error');
            
            // Focus first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return;
        }

        // Show loading state
        if (submitBtn && spinner && btnText) {
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            spinner.style.display = 'inline-block';
            
            const originalText = btnText.textContent;
            btnText.textContent = 'Procesando...';

            // Show loading overlay
            this.showLoadingOverlay();

            // Simulate processing time (remove in production)
            setTimeout(() => {
                this.hideLoadingOverlay();
                // Form will submit naturally
            }, 1000);
        }
    }

    fillDemoCredentials() {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        if (emailField && passwordField) {
            // Animate filling
            this.typeText(emailField, 'admin@chatbot.com', () => {
                this.typeText(passwordField, 'admin123', () => {
                    this.validateField(emailField);
                    this.validateField(passwordField);
                    this.showToast('Credenciales de demostración cargadas', 'success');
                });
            });
        }
    }

    typeText(element, text, callback) {
        element.value = '';
        element.focus();
        
        let i = 0;
        const typeInterval = setInterval(() => {
            element.value += text.charAt(i);
            i++;
            
            if (i >= text.length) {
                clearInterval(typeInterval);
                element.blur();
                if (callback) callback();
            }
        }, 50);
    }

    setupToasts() {
        // Create toast container if it doesn't exist
        if (!document.querySelector('.toast-container')) {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
    }

    showToast(message, type = 'info', duration = 4000) {
        const container = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        
        const iconMap = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-header">
                <i class="${iconMap[type]} me-2"></i>
                <strong class="me-auto">ChatBot WhatsApp</strong>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;

        container.appendChild(toast);

        // Show toast
        setTimeout(() => toast.classList.add('show'), 100);

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    showLoadingOverlay() {
        let overlay = document.querySelector('.loading-overlay');
        
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(overlay);
        }
        
        overlay.classList.add('show');
    }

    hideLoadingOverlay() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.classList.remove('show');
        }
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const form = document.querySelector('form');
                if (form) {
                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }

            // Escape to clear form
            if (e.key === 'Escape') {
                const inputs = document.querySelectorAll('.form-control');
                inputs.forEach(input => {
                    input.value = '';
                    this.clearFieldValidation(input);
                });
            }
        });
    }

    setupAccessibility() {
        // Add ARIA labels
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            const label = input.parentNode.parentNode.querySelector('.form-label');
            if (label && !input.getAttribute('aria-label')) {
                input.setAttribute('aria-label', label.textContent.trim());
            }
        });

        // Add role attributes
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.setAttribute('role', 'alert');
            alert.setAttribute('aria-live', 'polite');
        });

        // Focus management
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                submitBtn.setAttribute('aria-busy', 'true');
            });
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AuthEnhancer();
});

// Export for use in other scripts
window.AuthEnhancer = AuthEnhancer;
