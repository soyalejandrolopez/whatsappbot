@extends('layouts.auth-3d')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="auth-card slide-up">
    <!-- Header -->
    <div class="auth-header">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-lock-open"></i>
            </div>
        </div>
        <h1 class="auth-title">Nueva Contraseña</h1>
        <p class="auth-subtitle">Crea una contraseña segura para tu cuenta</p>
    </div>

    <!-- Body -->
    <div class="auth-body">
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¡Oops!</strong> Hay algunos problemas:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Password Requirements -->
        <div class="mb-4 p-3" style="background: rgba(37, 211, 102, 0.1); border-radius: 12px; border-left: 4px solid var(--primary-color);">
            <div class="d-flex align-items-start">
                <i class="fas fa-shield-alt me-3 mt-1" style="color: var(--primary-color);"></i>
                <div>
                    <h6 style="color: var(--dark-color); margin-bottom: 8px; font-weight: 600;">Requisitos de seguridad</h6>
                    <ul style="color: var(--medium-gray); margin: 0; font-size: 14px; line-height: 1.5; padding-left: 20px;">
                        <li>Mínimo 8 caracteres</li>
                        <li>Al menos una letra mayúscula</li>
                        <li>Al menos un número</li>
                        <li>Evita contraseñas comunes</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Reset Form -->
        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1"></i>
                    Correo Electrónico
                </label>
                <div class="input-group">
                    <input
                        id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ $email ?? old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        {{ $email ? 'readonly' : '' }}
                    >
                    <i class="input-icon fas fa-envelope"></i>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-1"></i>
                    Nueva Contraseña
                </label>
                <div class="input-group">
                    <input
                        id="password"
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Ingresa tu nueva contraseña"
                    >
                    <i class="input-icon fas fa-lock"></i>
                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password', 'toggleIcon1')"
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--medium-gray); cursor: pointer; z-index: 4;"
                    >
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <!-- Password Strength Meter -->
                <div class="password-strength mt-2" id="passwordStrength">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Seguridad:</small>
                        <small id="strengthText" class="text-muted">No ingresada</small>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 3px;">
                        <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%; border-radius: 3px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
                <label for="password-confirm" class="form-label">
                    <i class="fas fa-check-circle me-1"></i>
                    Confirmar Contraseña
                </label>
                <div class="input-group">
                    <input
                        id="password-confirm"
                        type="password"
                        class="form-control"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirma tu nueva contraseña"
                    >
                    <i class="input-icon fas fa-check-circle"></i>
                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password-confirm', 'toggleIcon2')"
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--medium-gray); cursor: pointer; z-index: 4;"
                    >
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                    <div class="invalid-feedback" id="password-match-feedback">
                        Las contraseñas no coinciden
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <div class="loading-spinner"></div>
                <span class="btn-text">
                    <i class="fas fa-check-circle me-2"></i>
                    Restablecer Contraseña
                </span>
            </button>

            <!-- Back to Login -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--medium-gray); font-weight: 500; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(iconId);

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

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetPasswordForm');
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password-confirm');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const matchFeedback = document.getElementById('password-match-feedback');

        // Password strength checker
        passwordField.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;
            let status = '';
            let color = '';

            // Clear validation
            clearValidation(this);

            if (value.length > 0) {
                // Length check
                if (value.length >= 8) {
                    strength += 25;
                }

                // Uppercase check
                if (/[A-Z]/.test(value)) {
                    strength += 25;
                }

                // Lowercase check
                if (/[a-z]/.test(value)) {
                    strength += 25;
                }

                // Number check
                if (/[0-9]/.test(value)) {
                    strength += 25;
                }

                // Special character check
                if (/[^A-Za-z0-9]/.test(value)) {
                    strength += 25;
                }

                // Cap at 100%
                strength = Math.min(strength, 100);

                // Set status text and color
                if (strength < 25) {
                    status = 'Muy débil';
                    color = '#dc3545'; // Red
                } else if (strength < 50) {
                    status = 'Débil';
                    color = '#ffc107'; // Yellow
                } else if (strength < 75) {
                    status = 'Buena';
                    color = '#17a2b8'; // Teal
                } else {
                    status = 'Fuerte';
                    color = '#28a745'; // Green
                }
            } else {
                status = 'No ingresada';
                color = '#6c757d'; // Gray
            }

            // Update UI
            strengthBar.style.width = strength + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = status;
            strengthText.style.color = color;

            // Check if passwords match
            checkPasswordsMatch();
        });

        // Password match checker
        confirmField.addEventListener('input', function() {
            clearValidation(this);
            checkPasswordsMatch();
        });

        function checkPasswordsMatch() {
            const password = passwordField.value;
            const confirm = confirmField.value;

            if (confirm.length > 0) {
                if (password === confirm) {
                    confirmField.classList.add('is-valid');
                    confirmField.classList.remove('is-invalid');
                    matchFeedback.style.display = 'none';
                } else {
                    confirmField.classList.add('is-invalid');
                    confirmField.classList.remove('is-valid');
                    matchFeedback.style.display = 'block';
                }
            }
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            const password = passwordField.value;
            const confirm = confirmField.value;
            let isValid = true;

            // Validate password
            if (!password) {
                showError(passwordField, 'La contraseña es obligatoria.');
                isValid = false;
            } else if (password.length < 8) {
                showError(passwordField, 'La contraseña debe tener al menos 8 caracteres.');
                isValid = false;
            }

            // Validate confirmation
            if (!confirm) {
                showError(confirmField, 'Por favor confirma tu contraseña.');
                isValid = false;
            } else if (password !== confirm) {
                showError(confirmField, 'Las contraseñas no coinciden.');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();

                // Focus on first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        function clearValidation(field) {
            field.classList.remove('is-invalid', 'is-valid');
            const existingError = field.parentNode.querySelector('.invalid-feedback:not(#password-match-feedback)');
            if (existingError) {
                existingError.remove();
            }
        }

        function showError(field, message) {
            field.classList.add('is-invalid');

            // Don't add error message for confirm field (we already have a static one)
            if (field.id !== 'password-confirm') {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;
                field.parentNode.appendChild(errorDiv);
            } else {
                matchFeedback.style.display = 'block';
            }
        }
    });
</script>
@endpush
