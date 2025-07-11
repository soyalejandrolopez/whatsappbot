<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Iniciar Sesión') - {{ config('app.name', 'ChatBot WhatsApp') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom 3D CSS -->
    <style>
        :root {
            --primary-neon: #00ff88;
            --secondary-neon: #00d4ff;
            --accent-neon: #ff0080;
            --warning-neon: #ffaa00;
            --error-neon: #ff3366;
            --success-neon: #00ff88;
            --dark-bg: #0a0a0a;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --shadow-neon: 0 0 20px var(--primary-neon);
            --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #0a0a0a 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 136, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 0, 128, 0.05) 0%, transparent 50%);
            animation: backgroundPulse 8s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.7; }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--primary-neon);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            box-shadow: 0 0 6px var(--primary-neon);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
        }

        /* 3D Container */
        .auth-container-3d {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            padding: 20px;
        }

        /* Glassmorphism Card */
        .auth-card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 0;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow-glass), var(--shadow-neon);
            transform-style: preserve-3d;
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: cardEntrance 1.2s ease-out;
            overflow: hidden;
            position: relative;
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(100px) rotateX(-30deg) scale(0.8);
            }
            100% {
                opacity: 1;
                transform: translateY(0) rotateX(0deg) scale(1);
            }
        }

        .auth-card-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 
                var(--shadow-glass), 
                0 0 40px var(--primary-neon),
                0 20px 60px rgba(0, 0, 0, 0.4);
        }

        /* Neon Header */
        .auth-header-3d {
            background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 20px 20px 0 0;
        }

        .auth-header-3d::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        /* 3D Logo */
        .logo-3d {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            position: relative;
            transform-style: preserve-3d;
            animation: logoRotate 4s ease-in-out infinite;
        }

        @keyframes logoRotate {
            0%, 100% { transform: rotateY(0deg) rotateX(0deg); }
            25% { transform: rotateY(90deg) rotateX(10deg); }
            50% { transform: rotateY(180deg) rotateX(0deg); }
            75% { transform: rotateY(270deg) rotateX(-10deg); }
        }

        .logo-3d i {
            font-size: 50px;
            color: var(--dark-bg);
            text-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            display: block;
            transform: translateZ(20px);
        }

        /* Neon Typography */
        .auth-title-3d {
            font-family: 'Orbitron', monospace;
            font-size: 32px;
            font-weight: 800;
            color: var(--dark-bg);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .auth-subtitle-3d {
            font-family: 'Rajdhani', sans-serif;
            font-size: 18px;
            font-weight: 500;
            color: rgba(10, 10, 10, 0.8);
            position: relative;
            z-index: 2;
        }

        /* Form Styles */
        .auth-body-3d {
            padding: 40px 30px;
            background: var(--glass-bg);
        }

        .form-group-3d {
            margin-bottom: 30px;
            position: relative;
        }

        .form-label-3d {
            display: block;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Orbitron', monospace;
        }

        .input-container-3d {
            position: relative;
            transform-style: preserve-3d;
        }

        .form-control-3d {
            width: 100%;
            padding: 15px 20px 15px 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--glass-border);
            border-radius: 15px;
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 500;
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .form-control-3d:focus {
            outline: none;
            border-color: var(--primary-neon);
            box-shadow: 
                inset 0 2px 10px rgba(0, 0, 0, 0.2),
                0 0 20px var(--primary-neon),
                0 0 40px rgba(0, 255, 136, 0.3);
            transform: translateY(-2px) translateZ(10px);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-control-3d::placeholder {
            color: var(--text-secondary);
            font-style: italic;
        }

        /* Input Icons */
        .input-icon-3d {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 20px;
            transition: all 0.4s ease;
            z-index: 3;
        }

        .form-control-3d:focus + .input-icon-3d {
            color: var(--primary-neon);
            text-shadow: 0 0 10px var(--primary-neon);
            transform: translateY(-50%) scale(1.1);
        }

        /* Neon Button */
        .btn-3d {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
            border: none;
            border-radius: 15px;
            color: var(--dark-bg);
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Orbitron', monospace;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
            transform-style: preserve-3d;
        }

        .btn-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
        }

        .btn-3d:hover {
            transform: translateY(-5px) translateZ(20px);
            box-shadow: 
                0 15px 35px rgba(0, 255, 136, 0.4),
                0 0 30px var(--primary-neon);
        }

        .btn-3d:hover::before {
            left: 100%;
        }

        .btn-3d:active {
            transform: translateY(-2px) translateZ(10px);
        }

        /* Loading Animation */
        .loading-3d {
            display: none;
            width: 25px;
            height: 25px;
            border: 3px solid rgba(10, 10, 10, 0.3);
            border-radius: 50%;
            border-top-color: var(--dark-bg);
            animation: spin3d 1s ease-in-out infinite;
            margin-right: 15px;
        }

        @keyframes spin3d {
            to { transform: rotate(360deg); }
        }

        /* Validation Styles */
        .form-control-3d.valid {
            border-color: var(--success-neon);
            box-shadow: 
                inset 0 2px 10px rgba(0, 0, 0, 0.2),
                0 0 15px var(--success-neon);
        }

        .form-control-3d.invalid {
            border-color: var(--error-neon);
            box-shadow: 
                inset 0 2px 10px rgba(0, 0, 0, 0.2),
                0 0 15px var(--error-neon);
            animation: shake3d 0.5s ease-in-out;
        }

        @keyframes shake3d {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px) rotateZ(-2deg); }
            75% { transform: translateX(10px) rotateZ(2deg); }
        }

        /* Remember Me */
        .form-check-3d {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check-input-3d {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            appearance: none;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--glass-border);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-check-input-3d:checked {
            background: var(--primary-neon);
            border-color: var(--primary-neon);
            box-shadow: 0 0 15px var(--primary-neon);
        }

        .form-check-input-3d:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--dark-bg);
            font-weight: bold;
            font-size: 12px;
        }

        .form-check-label-3d {
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        /* Links */
        .forgot-password-3d {
            text-align: center;
            margin-top: 25px;
        }

        .forgot-password-3d a {
            color: var(--primary-neon);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px var(--primary-neon);
        }

        .forgot-password-3d a:hover {
            color: var(--secondary-neon);
            text-shadow: 0 0 10px var(--secondary-neon);
        }

        /* Demo Credentials */
        .demo-card-3d {
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .demo-card-3d:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary-neon);
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.2);
            transform: translateY(-5px);
        }

        .demo-title-3d {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 15px;
            font-family: 'Orbitron', monospace;
        }

        .demo-info-3d {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-card-3d {
                margin: 10px;
                max-width: none;
            }
            
            .auth-header-3d {
                padding: 30px 20px;
            }
            
            .auth-body-3d {
                padding: 30px 20px;
            }
            
            .auth-title-3d {
                font-size: 24px;
            }
            
            .logo-3d {
                width: 80px;
                height: 80px;
            }
            
            .logo-3d i {
                font-size: 40px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <div class="auth-container-3d">
        @yield('content')
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Street Alerts 3D -->
    <script src="{{ asset('js/street-alerts-3d.js') }}"></script>

    <!-- Custom 3D JavaScript -->
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                
                // Random colors
                const colors = ['#00ff88', '#00d4ff', '#ff0080'];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                particle.style.boxShadow = `0 0 6px ${particle.style.background}`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize particles
        document.addEventListener('DOMContentLoaded', createParticles);

        // Custom SweetAlert2 configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: 'rgba(255, 255, 255, 0.05)',
            color: '#ffffff',
            backdrop: 'rgba(0, 0, 0, 0.1)',
            customClass: {
                popup: 'swal-3d-popup',
                title: 'swal-3d-title',
                content: 'swal-3d-content'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Street Alert Functions
        window.showStreetAlert = function(type, title, message) {
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

            Toast.fire({
                icon: type,
                title: `${icons[type]} ${title}`,
                text: message,
                iconColor: colors[type],
                background: `linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.9) 100%)`,
                backdrop: 'rgba(0, 0, 0, 0.3)'
            });
        };

        // Enhanced validation messages in Spanish
        window.validationMessages = {
            email: {
                required: 'El correo electrónico es obligatorio 📧',
                invalid: 'Por favor ingresa un correo válido 🔍',
                example: 'Ejemplo: usuario@empresa.com'
            },
            password: {
                required: 'La contraseña es obligatoria 🔐',
                minLength: 'Mínimo 6 caracteres requeridos 📏',
                weak: 'Considera una contraseña más segura 💪'
            },
            general: {
                success: '¡Perfecto! ✨',
                error: '¡Oops! Algo salió mal 😅',
                processing: 'Procesando... ⚡',
                welcome: '¡Bienvenido de vuelta! 🚀'
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
