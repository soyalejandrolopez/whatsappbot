@extends('layouts.auth-3d')

@section('title', 'Demo Sistema 3D')

@section('content')
<div class="auth-card-3d">
    <!-- Header -->
    <div class="auth-header-3d">
        <div class="logo-3d">
            <i class="fas fa-rocket"></i>
        </div>
        <h1 class="auth-title-3d">Sistema 3D Demo</h1>
        <p class="auth-subtitle-3d">Prueba todas las características avanzadas</p>
    </div>

    <!-- Body -->
    <div class="auth-body-3d">
        <h6 style="color: var(--text-primary); margin-bottom: 20px; font-family: 'Orbitron', monospace; text-align: center;">
            🎮 Prueba las Street Alerts 3D
        </h6>

        <!-- Alert Buttons -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
            <button class="btn-3d" onclick="testSuccess()" style="background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); padding: 12px;">
                <i class="fas fa-check me-2"></i>Éxito
            </button>
            <button class="btn-3d" onclick="testError()" style="background: linear-gradient(135deg, var(--error-neon) 0%, #cc2244 100%); padding: 12px;">
                <i class="fas fa-times me-2"></i>Error
            </button>
            <button class="btn-3d" onclick="testWarning()" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); padding: 12px;">
                <i class="fas fa-exclamation me-2"></i>Advertencia
            </button>
            <button class="btn-3d" onclick="testInfo()" style="background: linear-gradient(135deg, var(--secondary-neon) 0%, #0099cc 100%); padding: 12px;">
                <i class="fas fa-info me-2"></i>Información
            </button>
        </div>

        <!-- Advanced Features -->
        <div style="margin-bottom: 30px;">
            <h6 style="color: var(--text-primary); margin-bottom: 15px; font-family: 'Orbitron', monospace;">
                ⚡ Características Avanzadas
            </h6>
            <div style="display: grid; gap: 10px;">
                <button class="btn-3d" onclick="testCustomAlert()" style="background: linear-gradient(135deg, #ff0080 0%, #8000ff 100%); padding: 10px; font-size: 14px;">
                    <i class="fas fa-magic me-2"></i>Alerta Personalizada
                </button>
                <button class="btn-3d" onclick="testValidation()" style="background: linear-gradient(135deg, #00ffff 0%, #0080ff 100%); padding: 10px; font-size: 14px;">
                    <i class="fas fa-check-double me-2"></i>Validación en Tiempo Real
                </button>
                <button class="btn-3d" onclick="testLoading()" style="background: linear-gradient(135deg, #ffff00 0%, #ff8000 100%); padding: 10px; font-size: 14px;">
                    <i class="fas fa-spinner me-2"></i>Indicador de Carga
                </button>
                <button class="btn-3d" onclick="testWelcome()" style="background: linear-gradient(135deg, #80ff00 0%, #00ff80 100%); padding: 10px; font-size: 14px;">
                    <i class="fas fa-user-check me-2"></i>Mensaje de Bienvenida
                </button>
            </div>
        </div>

        <!-- Test Form -->
        <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 15px; padding: 20px; margin-bottom: 20px;">
            <h6 style="color: var(--text-primary); margin-bottom: 15px; font-family: 'Orbitron', monospace;">
                🧪 Formulario de Prueba
            </h6>
            <form id="testForm">
                <div class="form-group-3d" style="margin-bottom: 20px;">
                    <label class="form-label-3d">Email de Prueba</label>
                    <div class="input-container-3d">
                        <input 
                            type="email" 
                            class="form-control-3d" 
                            id="testEmail"
                            placeholder="test@ejemplo.com"
                        >
                        <i class="input-icon-3d fas fa-envelope"></i>
                    </div>
                </div>
                <div class="form-group-3d" style="margin-bottom: 20px;">
                    <label class="form-label-3d">Contraseña de Prueba</label>
                    <div class="input-container-3d">
                        <input 
                            type="password" 
                            class="form-control-3d" 
                            id="testPassword"
                            placeholder="Mínimo 6 caracteres"
                        >
                        <i class="input-icon-3d fas fa-lock"></i>
                    </div>
                </div>
                <button type="button" class="btn-3d" onclick="validateTestForm()" style="width: 100%; padding: 12px;">
                    <i class="fas fa-check-circle me-2"></i>Validar Formulario
                </button>
            </form>
        </div>

        <!-- Navigation -->
        <div style="text-align: center;">
            <a href="{{ route('login') }}" style="color: var(--primary-neon); text-decoration: none; font-weight: 600; margin-right: 20px;">
                <i class="fas fa-arrow-left me-1"></i>Login Normal
            </a>
            <a href="{{ route('login.3d') }}" style="color: var(--secondary-neon); text-decoration: none; font-weight: 600;">
                <i class="fas fa-cube me-1"></i>Login 3D
            </a>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="demo-card-3d" style="margin-top: 30px;">
    <h6 class="demo-title-3d">
        <i class="fas fa-star me-2"></i>
        Sistema de Autenticación 3D
    </h6>
    <div class="demo-info-3d">
        ✨ Efectos glassmorphism avanzados<br>
        🎯 Validaciones en tiempo real<br>
        🚨 Street alerts con animaciones 3D<br>
        🎮 Interfaz interactiva y moderna<br>
        🌟 Completamente en español
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Test functions for street alerts
    function testSuccess() {
        showStreetAlert('success', '¡Operación Exitosa!', 'Todo ha funcionado perfectamente. El sistema está operativo y listo para usar. 🎉');
    }

    function testError() {
        showStreetAlert('error', 'Error Detectado', 'Se ha producido un error en el sistema. Por favor revisa los datos ingresados e intenta nuevamente. ⚠️');
    }

    function testWarning() {
        showStreetAlert('warning', 'Advertencia Importante', 'Atención: Esta acción requiere confirmación. Asegúrate de que los datos sean correctos antes de continuar. 🚨');
    }

    function testInfo() {
        showStreetAlert('info', 'Información del Sistema', 'El sistema está funcionando correctamente. Todas las funciones están disponibles y operativas. ℹ️');
    }

    function testCustomAlert() {
        showCustomAlert('success', 'Alerta Personalizada', 'Esta es una alerta personalizada con efectos glassmorphism y animaciones 3D avanzadas. ✨', 6000);
    }

    function testValidation() {
        const email = document.getElementById('testEmail');
        const password = document.getElementById('testPassword');
        
        // Simulate validation
        email.value = 'test@ejemplo.com';
        password.value = '123456';
        
        email.classList.add('valid');
        password.classList.add('valid');
        
        showStreetAlert('success', 'Validación Exitosa', 'Los campos han sido validados correctamente en tiempo real. ✅');
        
        setTimeout(() => {
            email.classList.remove('valid');
            password.classList.remove('valid');
        }, 3000);
    }

    function testLoading() {
        showLoading('Procesando datos del sistema...');
        
        setTimeout(() => {
            closeLoading();
            showStreetAlert('success', 'Carga Completada', 'Los datos se han procesado exitosamente. ⚡');
        }, 3000);
    }

    function testWelcome() {
        showWelcome('Administrador');
    }

    function validateTestForm() {
        const email = document.getElementById('testEmail');
        const password = document.getElementById('testPassword');
        
        let isValid = true;
        
        // Clear previous validations
        email.classList.remove('valid', 'invalid');
        password.classList.remove('valid', 'invalid');
        
        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            email.classList.add('invalid');
            showValidationError(email, 'El correo electrónico es obligatorio 📧');
            isValid = false;
        } else if (!emailRegex.test(email.value)) {
            email.classList.add('invalid');
            showValidationError(email, 'Por favor ingresa un correo válido 🔍');
            isValid = false;
        } else {
            email.classList.add('valid');
        }
        
        // Validate password
        if (!password.value) {
            password.classList.add('invalid');
            showValidationError(password, 'La contraseña es obligatoria 🔐');
            isValid = false;
        } else if (password.value.length < 6) {
            password.classList.add('invalid');
            showValidationError(password, 'Mínimo 6 caracteres requeridos 📏');
            isValid = false;
        } else {
            password.classList.add('valid');
        }
        
        if (isValid) {
            showStreetAlert('success', 'Formulario Válido', 'Todos los campos han sido validados correctamente. ✨');
        }
    }

    // Auto-demo on load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            showStreetAlert('info', 'Demo Iniciada', 'Bienvenido al sistema de autenticación 3D. Prueba todas las características disponibles. 🚀');
        }, 1000);
        
        // Add real-time validation to test form
        const testEmail = document.getElementById('testEmail');
        const testPassword = document.getElementById('testPassword');
        
        testEmail.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.classList.remove('valid', 'invalid');
            
            if (this.value.trim() && emailRegex.test(this.value)) {
                this.classList.add('valid');
            } else if (this.value.trim()) {
                this.classList.add('invalid');
            }
        });
        
        testPassword.addEventListener('input', function() {
            this.classList.remove('valid', 'invalid');
            
            if (this.value.length >= 6) {
                this.classList.add('valid');
            } else if (this.value.length > 0) {
                this.classList.add('invalid');
            }
        });
    });
</script>
@endpush
