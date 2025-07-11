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

        // Custom validation messages in Spanish
        const validationMessages = {
            email: {
                required: 'El correo electrónico es obligatorio.',
                invalid: 'Por favor ingresa un correo electrónico válido.',
                example: 'Ejemplo: usuario@empresa.com'
            },
            password: {
                required: 'La contraseña es obligatoria.',
                minLength: 'La contraseña debe tener al menos 6 caracteres.',
                weak: 'Considera usar una contraseña más segura.'
            }
        };

        // Real-time validation
        emailField.addEventListener('input', function() {
            validateEmail(this);
        });

        passwordField.addEventListener('input', function() {
            validatePassword(this);
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const isEmailValid = validateEmail(emailField);
            const isPasswordValid = validatePassword(passwordField);

            if (!isEmailValid || !isPasswordValid) {
                e.preventDefault();

                // Focus on first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        function validateEmail(field) {
            const value = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            clearValidation(field);

            if (!value) {
                showError(field, validationMessages.email.required);
                return false;
            } else if (!emailRegex.test(value)) {
                showError(field, validationMessages.email.invalid);
                return false;
            } else {
                showSuccess(field);
                return true;
            }
        }

        function validatePassword(field) {
            const value = field.value;

            clearValidation(field);

            if (!value) {
                showError(field, validationMessages.password.required);
                return false;
            } else if (value.length < 6) {
                showError(field, validationMessages.password.minLength);
                return false;
            } else {
                showSuccess(field);
                return true;
            }
        }

        function clearValidation(field) {
            field.classList.remove('is-invalid', 'is-valid');
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
        }

        function showError(field, message) {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;
            field.parentNode.appendChild(errorDiv);
        }

        function showSuccess(field) {
            field.classList.add('is-valid');
        }

        // Auto-fill demo credentials
        const demoCard = document.querySelector('.card-body');
        if (demoCard) {
            demoCard.addEventListener('click', function() {
                emailField.value = 'admin@chatbot.com';
                passwordField.value = 'admin123';
                validateEmail(emailField);
                validatePassword(passwordField);

                // Add visual feedback
                emailField.style.background = 'rgba(37, 211, 102, 0.1)';
                passwordField.style.background = 'rgba(37, 211, 102, 0.1)';

                setTimeout(() => {
                    emailField.style.background = '';
                    passwordField.style.background = '';
                }, 1000);
            });
        }
    });
</script>
@endpush
