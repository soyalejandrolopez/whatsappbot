/**
 * Street Alerts 3D - Sistema de notificaciones avanzado
 * Con efectos glassmorphism y animaciones 3D
 */

class StreetAlerts3D {
    constructor() {
        this.init();
        this.createContainer();
    }

    init() {
        // Configuración de SweetAlert2 personalizada
        this.swalConfig = {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            background: 'rgba(0, 0, 0, 0.8)',
            color: '#ffffff',
            backdrop: false,
            customClass: {
                popup: 'street-alert-popup',
                title: 'street-alert-title',
                content: 'street-alert-content',
                timerProgressBar: 'street-alert-progress'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
                this.addGlowEffect(toast);
            }
        };

        // Inyectar estilos CSS
        this.injectStyles();
    }

    createContainer() {
        // Crear contenedor para alertas personalizadas
        if (!document.getElementById('street-alerts-container')) {
            const container = document.createElement('div');
            container.id = 'street-alerts-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }
    }

    injectStyles() {
        const styles = `
            <style id="street-alerts-styles">
                .street-alert-popup {
                    background: linear-gradient(135deg, 
                        rgba(0, 0, 0, 0.9) 0%, 
                        rgba(20, 20, 20, 0.95) 50%, 
                        rgba(0, 0, 0, 0.9) 100%) !important;
                    backdrop-filter: blur(20px) !important;
                    border: 1px solid rgba(255, 255, 255, 0.1) !important;
                    border-radius: 15px !important;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(0, 255, 136, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
                    transform: translateX(100%);
                    animation: slideInRight 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
                    font-family: 'Rajdhani', sans-serif !important;
                    min-width: 350px !important;
                    max-width: 400px !important;
                }

                .street-alert-popup.swal2-show {
                    transform: translateX(0) !important;
                }

                .street-alert-title {
                    font-family: 'Orbitron', monospace !important;
                    font-weight: 700 !important;
                    font-size: 16px !important;
                    text-transform: uppercase !important;
                    letter-spacing: 1px !important;
                    text-shadow: 0 0 10px currentColor !important;
                    margin-bottom: 8px !important;
                }

                .street-alert-content {
                    font-size: 14px !important;
                    line-height: 1.5 !important;
                    opacity: 0.9 !important;
                }

                .street-alert-progress {
                    background: linear-gradient(90deg, #00ff88, #00d4ff) !important;
                    height: 4px !important;
                    border-radius: 2px !important;
                    box-shadow: 0 0 10px rgba(0, 255, 136, 0.5) !important;
                }

                .street-alert-success {
                    border-left: 4px solid #00ff88 !important;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(0, 255, 136, 0.4) !important;
                }

                .street-alert-error {
                    border-left: 4px solid #ff3366 !important;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(255, 51, 102, 0.4) !important;
                }

                .street-alert-warning {
                    border-left: 4px solid #ffaa00 !important;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(255, 170, 0, 0.4) !important;
                }

                .street-alert-info {
                    border-left: 4px solid #00d4ff !important;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(0, 212, 255, 0.4) !important;
                }

                @keyframes slideInRight {
                    from {
                        transform: translateX(100%) rotateY(-30deg);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0) rotateY(0deg);
                        opacity: 1;
                    }
                }

                @keyframes glowPulse {
                    0%, 100% {
                        box-shadow: 
                            0 8px 32px rgba(0, 0, 0, 0.4),
                            0 0 20px rgba(0, 255, 136, 0.3);
                    }
                    50% {
                        box-shadow: 
                            0 8px 32px rgba(0, 0, 0, 0.4),
                            0 0 30px rgba(0, 255, 136, 0.6);
                    }
                }

                .street-alert-glow {
                    animation: glowPulse 2s ease-in-out infinite !important;
                }

                /* Custom alert container */
                .custom-street-alert {
                    background: linear-gradient(135deg, 
                        rgba(0, 0, 0, 0.9) 0%, 
                        rgba(20, 20, 20, 0.95) 50%, 
                        rgba(0, 0, 0, 0.9) 100%);
                    backdrop-filter: blur(20px);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    border-radius: 15px;
                    padding: 20px;
                    margin-bottom: 15px;
                    color: white;
                    font-family: 'Rajdhani', sans-serif;
                    transform: translateX(100%);
                    animation: slideInRight 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                    pointer-events: auto;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    min-width: 350px;
                    max-width: 400px;
                }

                .custom-street-alert:hover {
                    transform: translateX(-5px) scale(1.02);
                }

                .custom-street-alert .alert-header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 10px;
                }

                .custom-street-alert .alert-icon {
                    font-size: 24px;
                    margin-right: 15px;
                    text-shadow: 0 0 10px currentColor;
                }

                .custom-street-alert .alert-title {
                    font-family: 'Orbitron', monospace;
                    font-weight: 700;
                    font-size: 16px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    text-shadow: 0 0 10px currentColor;
                }

                .custom-street-alert .alert-message {
                    font-size: 14px;
                    line-height: 1.5;
                    opacity: 0.9;
                    margin-left: 39px;
                }

                .custom-street-alert .alert-close {
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    background: none;
                    border: none;
                    color: rgba(255, 255, 255, 0.6);
                    font-size: 18px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .custom-street-alert .alert-close:hover {
                    color: white;
                    transform: scale(1.2);
                }

                .custom-street-alert.success {
                    border-left: 4px solid #00ff88;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(0, 255, 136, 0.4);
                }

                .custom-street-alert.error {
                    border-left: 4px solid #ff3366;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(255, 51, 102, 0.4);
                }

                .custom-street-alert.warning {
                    border-left: 4px solid #ffaa00;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(255, 170, 0, 0.4);
                }

                .custom-street-alert.info {
                    border-left: 4px solid #00d4ff;
                    box-shadow: 
                        0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(0, 212, 255, 0.4);
                }
            </style>
        `;

        if (!document.getElementById('street-alerts-styles')) {
            document.head.insertAdjacentHTML('beforeend', styles);
        }
    }

    addGlowEffect(element) {
        element.classList.add('street-alert-glow');
        setTimeout(() => {
            element.classList.remove('street-alert-glow');
        }, 3000);
    }

    // Método principal para mostrar alertas
    show(type, title, message, options = {}) {
        const icons = {
            success: '🎉',
            error: '⚠️',
            warning: '🚨',
            info: 'ℹ️'
        };

        const colors = {
            success: '#00ff88',
            error: '#ff3366',
            warning: '#ffaa00',
            info: '#00d4ff'
        };

        const config = {
            ...this.swalConfig,
            icon: type,
            title: `${icons[type]} ${title}`,
            text: message,
            iconColor: colors[type],
            customClass: {
                ...this.swalConfig.customClass,
                popup: `${this.swalConfig.customClass.popup} street-alert-${type}`
            },
            ...options
        };

        return Swal.fire(config);
    }

    // Método para alertas personalizadas
    showCustom(type, title, message, duration = 5000) {
        const container = document.getElementById('street-alerts-container');
        
        const icons = {
            success: '🎉',
            error: '⚠️',
            warning: '🚨',
            info: 'ℹ️'
        };

        const colors = {
            success: '#00ff88',
            error: '#ff3366',
            warning: '#ffaa00',
            info: '#00d4ff'
        };

        const alertElement = document.createElement('div');
        alertElement.className = `custom-street-alert ${type}`;
        alertElement.innerHTML = `
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            <div class="alert-header">
                <div class="alert-icon" style="color: ${colors[type]}">${icons[type]}</div>
                <div class="alert-title" style="color: ${colors[type]}">${title}</div>
            </div>
            <div class="alert-message">${message}</div>
        `;

        // Auto remove
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.style.animation = 'slideInRight 0.3s reverse';
                setTimeout(() => alertElement.remove(), 300);
            }
        }, duration);

        // Click to remove
        alertElement.addEventListener('click', () => {
            alertElement.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => alertElement.remove(), 300);
        });

        container.appendChild(alertElement);
        return alertElement;
    }

    // Métodos de conveniencia
    success(title, message, options = {}) {
        return this.show('success', title, message, options);
    }

    error(title, message, options = {}) {
        return this.show('error', title, message, options);
    }

    warning(title, message, options = {}) {
        return this.show('warning', title, message, options);
    }

    info(title, message, options = {}) {
        return this.show('info', title, message, options);
    }

    // Alertas de validación específicas
    validationError(field, message) {
        const fieldRect = field.getBoundingClientRect();
        const alertElement = this.showCustom('error', 'Error de Validación', message, 3000);
        
        // Posicionar cerca del campo
        alertElement.style.position = 'fixed';
        alertElement.style.top = `${fieldRect.top}px`;
        alertElement.style.right = '20px';
        alertElement.style.zIndex = '10001';
        
        return alertElement;
    }

    // Alerta de bienvenida
    welcome(username = 'Usuario') {
        return this.success(
            '¡Bienvenido de vuelta!', 
            `Hola ${username}, es genial verte de nuevo 🚀`,
            { timer: 4000 }
        );
    }

    // Alerta de carga
    loading(message = 'Procesando...') {
        return Swal.fire({
            title: message,
            html: `
                <div style="display: flex; align-items: center; justify-content: center; color: #00ff88;">
                    <div style="width: 30px; height: 30px; border: 3px solid rgba(0, 255, 136, 0.3); border-radius: 50%; border-top-color: #00ff88; animation: spin 1s linear infinite; margin-right: 15px;"></div>
                    <span style="font-family: 'Orbitron', monospace;">Cargando datos...</span>
                </div>
            `,
            background: 'rgba(0, 0, 0, 0.9)',
            color: '#ffffff',
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'street-alert-popup'
            }
        });
    }

    // Cerrar alerta de carga
    closeLoading() {
        Swal.close();
    }
}

// Inicializar sistema de alertas
const streetAlerts = new StreetAlerts3D();

// Funciones globales para compatibilidad
window.showStreetAlert = function(type, title, message, options = {}) {
    return streetAlerts.show(type, title, message, options);
};

window.showCustomAlert = function(type, title, message, duration = 5000) {
    return streetAlerts.showCustom(type, title, message, duration);
};

window.showValidationError = function(field, message) {
    return streetAlerts.validationError(field, message);
};

window.showWelcome = function(username) {
    return streetAlerts.welcome(username);
};

window.showLoading = function(message) {
    return streetAlerts.loading(message);
};

window.closeLoading = function() {
    return streetAlerts.closeLoading();
};

// Session-based welcome messages
window.showSessionAlert = function(key, type, title, message, delay = 1000) {
    if (!sessionStorage.getItem(key)) {
        setTimeout(() => {
            showStreetAlert(type, title, message);
            sessionStorage.setItem(key, 'true');
        }, delay);
    }
};

// Clear all session alerts (useful for testing or manual reset)
window.clearSessionAlerts = function() {
    const keys = [
        'dashboardWelcomeShown',
        'chatbotFlowsWelcomeShown',
        'analyticsWelcomeShown',
        'testPanelWelcomeShown',
        'conversationsWelcomeShown',
        'contactsWelcomeShown',
        'usersWelcomeShown'
    ];

    keys.forEach(key => sessionStorage.removeItem(key));
    console.log('🔄 Session alerts cleared - Welcome messages will show again');
    showStreetAlert('info', 'Alertas Reiniciadas', 'Los mensajes de bienvenida se mostrarán nuevamente');
};

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StreetAlerts3D;
}
