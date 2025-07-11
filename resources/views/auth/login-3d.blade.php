@extends('layouts.auth-3d')

@section('title', 'Acceso Seguro')

@section('content')
<div class="auth-card-3d">
    <!-- Header -->
    <div class="auth-header-3d">
        <div class="logo-3d">
            <i class="fab fa-whatsapp"></i>
        </div>
        <h1 class="auth-title-3d">ChatBot WhatsApp</h1>
        <p class="auth-subtitle-3d">Panel de Control Avanzado</p>
    </div>

    <!-- Body -->
    <div class="auth-body-3d">
        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" id="loginForm3D" novalidate>
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
            </div>

            <!-- Password Field -->
            <div class="form-group-3d">
                <label for="password" class="form-label-3d">
                    <i class="fas fa-lock me-1"></i>
                    Contraseña
                </label>
                <div class="input-container-3d">
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control-3d @error('password') invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    >
                    <i class="input-icon-3d fas fa-lock"></i>
                    <button 
                        type="button" 
                        class="password-toggle" 
                        onclick="togglePassword3D()"
                        style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; z-index: 4;"
                    >
                        <i class="fas fa-eye" id="toggleIcon3D"></i>
                    </button>
                    @error('password')
                        <div class="error-message" style="color: var(--error-neon); margin-top: 8px; font-size: 14px; text-shadow: 0 0 5px var(--error-neon);">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-check-3d">
                <input 
                    class="form-check-input-3d" 
                    type="checkbox" 
                    name="remember" 
                    id="remember" 
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label class="form-check-label-3d" for="remember">
                    Recordar mi sesión
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-3d" id="submitBtn3D">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="loading-3d" id="loading3D"></div>
                    <span class="btn-text-3d">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </span>
                </div>
            </button>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <div class="forgot-password-3d">
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-question-circle me-1"></i>
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Demo Credentials Info -->
<div class="demo-card-3d" id="demoCard3D">
    <h6 class="demo-title-3d">
        <i class="fas fa-info-circle me-2"></i>
        Credenciales de Demostración
    </h6>
    <div class="row text-center">
        <div class="col-6">
            <div class="demo-info-3d">
                <strong>Email:</strong><br>
                admin@chatbot.com
            </div>
        </div>
        <div class="col-6">
            <div class="demo-info-3d">
                <strong>Contraseña:</strong><br>
                admin123
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword3D() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon3D');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Enhanced 3D form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm3D');
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn3D');
        const loading = document.getElementById('loading3D');
        const btnText = document.querySelector('.btn-text-3d');
        const demoCard = document.getElementById('demoCard3D');

        // Real-time validation
        emailField.addEventListener('input', function() {
            validateEmail3D(this);
        });

        passwordField.addEventListener('input', function() {
            validatePassword3D(this);
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const isEmailValid = validateEmail3D(emailField);
            const isPasswordValid = validatePassword3D(passwordField);

            if (!isEmailValid || !isPasswordValid) {
                e.preventDefault();
                
                // Show street alert
                showStreetAlert('error', 'Validación Fallida', 'Por favor corrige los errores en el formulario');
                
                // Shake effect
                form.classList.add('shake3d');
                setTimeout(() => form.classList.remove('shake3d'), 500);
                
                // Focus on first invalid field
                const firstInvalid = form.querySelector('.invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            } else {
                // Show loading state
                submitBtn.disabled = true;
                loading.style.display = 'inline-block';
                btnText.textContent = 'Accediendo...';
                
                // Show success message
                showStreetAlert('success', 'Acceso Exitoso', 'Iniciando sesión en el sistema');
            }
        });

        // Demo credentials click
        demoCard.addEventListener('click', function() {
            fillDemoCredentials3D();
        });

        // Validation functions
        function validateEmail3D(field) {
            const value = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            clearValidation3D(field);

            if (!value) {
                showError3D(field, window.validationMessages.email.required);
                return false;
            } else if (!emailRegex.test(value)) {
                showError3D(field, window.validationMessages.email.invalid);
                return false;
            } else {
                showSuccess3D(field);
                return true;
            }
        }

        function validatePassword3D(field) {
            const value = field.value;
            
            clearValidation3D(field);

            if (!value) {
                showError3D(field, window.validationMessages.password.required);
                return false;
            } else if (value.length < 6) {
                showError3D(field, window.validationMessages.password.minLength);
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

        function fillDemoCredentials3D() {
            // Animated typing effect
            const typeText = (element, text, callback) => {
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
            };
            
            // Type email then password
            typeText(emailField, 'admin@chatbot.com', () => {
                typeText(passwordField, 'admin123', () => {
                    validateEmail3D(emailField);
                    validatePassword3D(passwordField);
                    
                    // Show success message
                    showStreetAlert('success', 'Credenciales Cargadas', 'Listo para iniciar sesión');
                    
                    // Highlight button
                    submitBtn.style.animation = 'pulse 1s infinite';
                    setTimeout(() => submitBtn.style.animation = '', 3000);
                });
            });
        }

        // Show welcome message
        setTimeout(() => {
            showStreetAlert('info', 'Bienvenido', 'Ingresa tus credenciales para acceder al sistema');
        }, 1000);
    });
</script>
@endpush
