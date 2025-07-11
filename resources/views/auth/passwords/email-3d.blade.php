@extends('layouts.auth-3d')

@section('title', 'Recuperar Acceso')

@section('content')
<div class="auth-card-3d">
    <!-- Header -->
    <div class="auth-header-3d">
        <div class="logo-3d">
            <i class="fas fa-key"></i>
        </div>
        <h1 class="auth-title-3d">Recuperar Acceso</h1>
        <p class="auth-subtitle-3d">Restablece tu contraseña de forma segura</p>
    </div>

    <!-- Body -->
    <div class="auth-body-3d">
        <!-- Success Message -->
        @if (session('status'))
            <div class="alert alert-success-3d" style="background: rgba(0, 255, 136, 0.1); border: 1px solid var(--success-neon); border-radius: 10px; padding: 15px; margin-bottom: 25px; color: var(--success-neon);">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Perfecto!</strong> {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger-3d" style="background: rgba(255, 51, 102, 0.1); border: 1px solid var(--error-neon); border-radius: 10px; padding: 15px; margin-bottom: 25px; color: var(--error-neon);">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¡Oops!</strong> Hay algunos problemas:
                <ul style="margin: 10px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Instructions -->
        <div style="background: rgba(0, 212, 255, 0.1); border: 1px solid var(--secondary-neon); border-radius: 10px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; align-items: flex-start;">
                <i class="fas fa-info-circle" style="color: var(--secondary-neon); margin-right: 15px; margin-top: 5px; font-size: 20px; text-shadow: 0 0 10px var(--secondary-neon);"></i>
                <div>
                    <h6 style="color: var(--text-primary); margin-bottom: 10px; font-weight: 600; font-family: 'Orbitron', monospace;">¿Olvidaste tu contraseña?</h6>
                    <p style="color: var(--text-secondary); margin: 0; font-size: 14px; line-height: 1.6;">
                        No te preocupes, es completamente normal. Ingresa tu correo electrónico y te enviaremos un enlace seguro para crear una nueva contraseña.
                    </p>
                </div>
            </div>
        </div>

        <!-- Reset Form -->
        <form method="POST" action="{{ route('password.email') }}" id="resetForm3D" novalidate>
            @csrf

            <!-- Email Field -->
            <div class="form-group-3d">
                <label for="email" class="form-label-3d">
                    <i class="fas fa-envelope me-1"></i>
                    Correo Electrónico
                </label>
                <div class="input-container-3d">
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control-3d @error('email') invalid @enderror" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                        placeholder="Ingresa tu correo electrónico"
                    >
                    <i class="input-icon-3d fas fa-envelope"></i>
                    @error('email')
                        <div class="error-message" style="color: var(--error-neon); margin-top: 8px; font-size: 14px; text-shadow: 0 0 5px var(--error-neon);">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <small style="color: var(--text-secondary); margin-top: 8px; display: block; font-size: 12px;">
                    <i class="fas fa-shield-alt me-1"></i>
                    Usaremos este correo para enviarte el enlace de recuperación
                </small>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-3d" id="submitBtn3D">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="loading-3d" id="loading3D"></div>
                    <span class="btn-text-3d">
                        <i class="fas fa-paper-plane me-2"></i>
                        Enviar Enlace de Recuperación
                    </span>
                </div>
            </button>

            <!-- Back to Login -->
            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ route('login') }}" style="color: var(--text-secondary); text-decoration: none; font-weight: 500; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Additional Info -->
<div class="demo-card-3d" style="margin-top: 30px;">
    <h6 class="demo-title-3d">
        <i class="fas fa-clock me-2"></i>
        Tiempo de Respuesta
    </h6>
    <div class="demo-info-3d">
        El enlace de recuperación llegará a tu correo en menos de 5 minutos.<br>
        Si no lo recibes, revisa tu carpeta de spam o correo no deseado.
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetForm3D');
        const emailField = document.getElementById('email');
        const submitBtn = document.getElementById('submitBtn3D');
        const loading = document.getElementById('loading3D');
        const btnText = document.querySelector('.btn-text-3d');

        // Real-time validation
        emailField.addEventListener('input', function() {
            validateEmail3D(this);
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const isEmailValid = validateEmail3D(emailField);

            if (!isEmailValid) {
                e.preventDefault();
                
                // Show street alert
                showStreetAlert('error', 'Email Inválido', 'Por favor ingresa un correo electrónico válido');
                
                // Shake effect
                emailField.classList.add('shake3d');
                setTimeout(() => emailField.classList.remove('shake3d'), 500);
                
                emailField.focus();
            } else {
                // Show loading state
                submitBtn.disabled = true;
                loading.style.display = 'inline-block';
                btnText.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
                
                // Show success message
                showStreetAlert('info', 'Enviando Enlace', 'Procesando tu solicitud de recuperación');
            }
        });

        // Validation function
        function validateEmail3D(field) {
            const value = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            clearValidation3D(field);

            if (!value) {
                showError3D(field, 'El correo electrónico es obligatorio 📧');
                return false;
            } else if (!emailRegex.test(value)) {
                showError3D(field, 'Por favor ingresa un correo válido 🔍');
                return false;
            } else {
                showSuccess3D(field);
                return true;
            }
        }

        function clearValidation3D(field) {
            field.classList.remove('valid', 'invalid');
            const existingError = field.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
        }

        function showError3D(field, message) {
            field.classList.add('invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;
            errorDiv.style.color = 'var(--error-neon)';
            errorDiv.style.marginTop = '8px';
            errorDiv.style.fontSize = '14px';
            errorDiv.style.textShadow = '0 0 5px var(--error-neon)';
            field.parentNode.appendChild(errorDiv);
        }

        function showSuccess3D(field) {
            field.classList.add('valid');
        }

        // Show welcome message
        setTimeout(() => {
            showStreetAlert('info', 'Recuperación Segura', 'Ingresa tu email para recibir el enlace de recuperación');
        }, 1000);

        // Add CSS for shake animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake3d {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px) rotateZ(-2deg); }
                75% { transform: translateX(10px) rotateZ(2deg); }
            }
            .shake3d {
                animation: shake3d 0.5s ease-in-out !important;
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush
