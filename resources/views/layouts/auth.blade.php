<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de ChatBot WhatsApp - Panel de Administración">
    <meta name="keywords" content="chatbot, whatsapp, administración, panel">
    <meta name="author" content="ChatBot WhatsApp">

    <title>@yield('title', 'Iniciar Sesión') - {{ config('app.name', 'ChatBot WhatsApp') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/auth-custom.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: #25D366;
            --primary-dark: #128C7E;
            --secondary-color: #34B7F1;
            --accent-color: #DCF8C6;
            --dark-color: #075E54;
            --light-gray: #F8F9FA;
            --medium-gray: #6C757D;
            --border-color: #E9ECEF;
            --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            z-index: 1;
        }

        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: var(--shadow-heavy);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm-30 0c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10-10-4.477-10-10z'/%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .logo i {
            font-size: 36px;
            color: white;
        }

        .auth-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }

        .auth-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }

        .auth-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            font-size: 18px;
            z-index: 3;
            transition: all 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 16px;
            font-weight: 400;
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
            transform: translateY(-2px);
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 14px;
            margin-top: 8px;
            font-weight: 500;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--medium-gray);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .forgot-password {
            text-align: center;
            margin-top: 25px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-container {
                padding: 15px;
            }
            
            .auth-header {
                padding: 30px 20px;
            }
            
            .auth-body {
                padding: 30px 20px;
            }
            
            .auth-title {
                font-size: 24px;
            }
            
            .logo {
                width: 70px;
                height: 70px;
            }
            
            .logo i {
                font-size: 30px;
            }
        }

        @media (max-width: 480px) {
            .auth-header {
                padding: 25px 15px;
            }
            
            .auth-body {
                padding: 25px 15px;
            }
            
            .form-control {
                padding: 12px 12px 12px 45px;
                font-size: 14px;
            }
            
            .input-icon {
                font-size: 16px;
                left: 12px;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enhanced Auth JS -->
    <script src="{{ asset('js/auth-enhanced.js') }}"></script>

    <!-- Custom JS -->
    <script>
        // Add loading animation to forms
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const spinner = submitBtn.querySelector('.loading-spinner');
                    const btnText = submitBtn.querySelector('.btn-text');
                    
                    if (submitBtn && spinner && btnText) {
                        submitBtn.disabled = true;
                        spinner.style.display = 'inline-block';
                        btnText.textContent = 'Procesando...';
                    }
                });
            });

            // Add animation classes
            const authCard = document.querySelector('.auth-card');
            if (authCard) {
                authCard.classList.add('fade-in');
            }

            // Real-time validation
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });
        });

        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.name;
            let isValid = true;
            let errorMessage = '';

            // Remove existing validation classes
            field.classList.remove('is-invalid', 'is-valid');
            
            // Remove existing error message
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }

            // Validation rules
            if (fieldName === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!value) {
                    isValid = false;
                    errorMessage = 'El correo electrónico es obligatorio.';
                } else if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Por favor ingresa un correo electrónico válido.';
                }
            } else if (fieldName === 'password') {
                if (!value) {
                    isValid = false;
                    errorMessage = 'La contraseña es obligatoria.';
                } else if (value.length < 6) {
                    isValid = false;
                    errorMessage = 'La contraseña debe tener al menos 6 caracteres.';
                }
            }

            // Apply validation result
            if (isValid) {
                field.classList.add('is-valid');
            } else {
                field.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errorMessage;
                field.parentNode.appendChild(errorDiv);
            }

            return isValid;
        }
    </script>

    @stack('scripts')
</body>
</html>
