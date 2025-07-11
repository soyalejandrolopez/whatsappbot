<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'ChatBot WhatsApp') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom 3D Admin CSS -->
    <style>
        :root {
            --primary-neon: #25D366;
            --secondary-neon: #128C7E;
            --accent-neon: #00d4ff;
            --warning-neon: #ffaa00;
            --error-neon: #ff3366;
            --success-neon: #25D366;
            --info-neon: #00d4ff;
            --dark-bg: #0a0a0a;
            --dark-secondary: #1a1a2e;
            --dark-tertiary: #16213e;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-muted: #808080;
            --shadow-neon: 0 0 20px var(--primary-neon);
            --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.3);
            --sidebar-width: 280px;
            
            /* RGB values for rgba usage */
            --primary-rgb: 37, 211, 102;
            --secondary-rgb: 18, 140, 126;
            --accent-rgb: 0, 212, 255;
            --warning-rgb: 255, 170, 0;
            --error-rgb: 255, 51, 102;
            --success-rgb: 37, 211, 102;
            --info-rgb: 0, 212, 255;
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
            color: var(--text-primary);
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
                radial-gradient(circle at 20% 80%, rgba(37, 211, 102, 0.1) 0%, transparent 50%),
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

        /* Main Layout */
        .admin-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar 3D */
        .sidebar-3d {
            width: var(--sidebar-width);
            background: linear-gradient(180deg,
                rgba(37, 211, 102, 0.1) 0%,
                rgba(18, 140, 126, 0.1) 50%,
                rgba(0, 0, 0, 0.2) 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-glass);
        }

        .sidebar-3d::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-3d::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-3d::-webkit-scrollbar-thumb {
            background: var(--primary-neon);
            border-radius: 3px;
            box-shadow: 0 0 10px var(--primary-neon);
        }

        /* Sidebar Header */
        .sidebar-header-3d {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.03);
        }

        .sidebar-logo-3d {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: var(--dark-bg);
            box-shadow: 0 0 20px rgba(37, 211, 102, 0.5);
            animation: logoFloat 3s ease-in-out infinite;
            transform-style: preserve-3d;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            50% { transform: translateY(-10px) rotateY(180deg); }
        }

        .sidebar-title-3d {
            font-family: 'Orbitron', monospace;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
            text-shadow: 0 0 10px var(--primary-neon);
        }

        .sidebar-subtitle-3d {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Navigation Menu */
        .nav-menu-3d {
            padding: 20px 0;
        }

        .nav-item-3d {
            margin-bottom: 5px;
        }

        .nav-link-3d {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            border-radius: 0 25px 25px 0;
            margin-right: 20px;
            font-weight: 500;
            transform-style: preserve-3d;
        }

        .nav-link-3d::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: transparent;
            transition: all 0.4s ease;
        }

        .nav-link-3d:hover,
        .nav-link-3d.active {
            color: var(--text-primary);
            background: rgba(37, 211, 102, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(37, 211, 102, 0.2);
            box-shadow: 0 0 20px rgba(37, 211, 102, 0.3);
            transform: translateX(10px) translateZ(10px);
        }

        .nav-link-3d:hover::before,
        .nav-link-3d.active::before {
            background: var(--primary-neon);
            box-shadow: 0 0 10px var(--primary-neon);
        }

        .nav-icon-3d {
            width: 20px;
            margin-right: 15px;
            font-size: 18px;
            text-align: center;
            transition: all 0.4s ease;
        }

        .nav-link-3d:hover .nav-icon-3d {
            color: var(--primary-neon);
            text-shadow: 0 0 10px var(--primary-neon);
            transform: scale(1.2) rotateY(360deg);
        }

        .nav-text-3d {
            font-size: 14px;
            font-weight: 500;
        }

        /* Main Content */
        .main-content-3d {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            position: relative;
            z-index: 5;
        }

        /* Top Header */
        .top-header-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 20px 30px;
            display: flex;
            justify-content: between;
            align-items: center;
            box-shadow: var(--shadow-glass);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left-3d {
            flex: 1;
        }

        .page-title-3d {
            font-family: 'Orbitron', monospace;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            text-shadow: 0 0 10px var(--primary-neon);
        }

        .page-subtitle-3d {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 5px 0 0 0;
        }

        .header-right-3d {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* User Menu */
        .user-menu-3d {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .user-menu-3d:hover {
            background: rgba(37, 211, 102, 0.1);
            border-color: var(--primary-neon);
            box-shadow: 0 0 20px rgba(37, 211, 102, 0.3);
            transform: translateY(-2px);
        }

        .user-avatar-3d {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-bg);
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 0 15px rgba(37, 211, 102, 0.5);
        }

        .user-info-3d {
            text-align: left;
        }

        .user-name-3d {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .user-role-3d {
            font-size: 12px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* Content Area */
        .content-area-3d {
            padding: 30px;
            min-height: calc(100vh - 80px);
        }

        /* Cards 3D */
        .card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-glass);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
            transform-style: preserve-3d;
        }

        .card-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow:
                var(--shadow-glass),
                0 0 30px rgba(37, 211, 102, 0.3),
                0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .card-header-3d {
            background: linear-gradient(135deg,
                rgba(37, 211, 102, 0.1) 0%,
                rgba(18, 140, 126, 0.1) 100%);
            border-bottom: 1px solid var(--glass-border);
            padding: 20px 25px;
            position: relative;
            overflow: hidden;
        }

        .card-header-3d::before {
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

        .card-title-3d {
            font-family: 'Orbitron', monospace;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            position: relative;
            z-index: 2;
        }

        .card-body-3d {
            padding: 25px;
            position: relative;
            z-index: 2;
        }

        /* Buttons 3D */
        .btn-3d {
            background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
            border: none;
            border-radius: 12px;
            color: var(--dark-bg);
            font-weight: 600;
            padding: 12px 24px;
            font-family: 'Rajdhani', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
            transform-style: preserve-3d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            transform: translateY(-3px) translateZ(10px);
            box-shadow:
                0 15px 35px rgba(37, 211, 102, 0.4),
                0 0 30px var(--primary-neon);
            color: var(--dark-bg);
            text-decoration: none;
        }

        .btn-3d:hover::before {
            left: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar-3d {
                transform: translateX(-100%);
            }

            .sidebar-3d.show {
                transform: translateX(0);
            }

            .main-content-3d {
                margin-left: 0;
            }

            .top-header-3d {
                padding: 15px 20px;
            }

            .content-area-3d {
                padding: 20px 15px;
            }

            .page-title-3d {
                font-size: 20px;
            }
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 8px 12px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(37, 211, 102, 0.1);
            border-color: var(--primary-neon);
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar-3d" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header-3d">
                <div class="sidebar-logo-3d">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h4 class="sidebar-title-3d">ChatBot WhatsApp</h4>
                <p class="sidebar-subtitle-3d">Panel de Control</p>
            </div>

            <!-- Navigation Menu -->
            <div class="nav-menu-3d">
                <div class="nav-item-3d">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link-3d {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-tachometer-alt"></i>
                        <span class="nav-text-3d">Dashboard</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.conversations.index') }}" class="nav-link-3d {{ request()->routeIs('admin.conversations.*') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-comments"></i>
                        <span class="nav-text-3d">Conversaciones</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.contacts.index') }}" class="nav-link-3d {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-address-book"></i>
                        <span class="nav-text-3d">Contactos</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.chatbot-flows.index') }}" class="nav-link-3d {{ request()->routeIs('admin.chatbot-flows.*') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-project-diagram"></i>
                        <span class="nav-text-3d">Flujos del Bot</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.chatbot-responses.index') }}" class="nav-link-3d {{ request()->routeIs('admin.chatbot-responses.*') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-robot"></i>
                        <span class="nav-text-3d">Respuestas del Bot</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.users.index') }}" class="nav-link-3d {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-users"></i>
                        <span class="nav-text-3d">Usuarios</span>
                    </a>
                </div>

                <div class="nav-item-3d">
                    <a href="{{ route('admin.analytics') }}" class="nav-link-3d {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                        <i class="nav-icon-3d fas fa-chart-bar"></i>
                        <span class="nav-text-3d">Analíticas</span>
                    </a>
                </div>

                <!-- Divider -->
                <hr style="border-color: var(--glass-border); margin: 20px 25px;">

                <!-- Logout -->
                <div class="nav-item-3d">
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="nav-link-3d" style="width: 100%; background: none; border: none; text-align: left;">
                            <i class="nav-icon-3d fas fa-sign-out-alt"></i>
                            <span class="nav-text-3d">Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content-3d">
            <!-- Top Header -->
            <header class="top-header-3d">
                <div class="header-left-3d">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title-3d">@yield('title', 'Dashboard')</h1>
                    <p class="page-subtitle-3d">@yield('subtitle', 'Panel de administración avanzado')</p>
                </div>

                <div class="header-right-3d">
                    <!-- User Menu -->
                    <div class="user-menu-3d" onclick="toggleUserMenu()">
                        <div class="user-avatar-3d">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-info-3d">
                            <p class="user-name-3d">{{ Auth::user()->name ?? 'Administrador' }}</p>
                            <p class="user-role-3d">{{ ucfirst(Auth::user()->role ?? 'admin') }}</p>
                        </div>
                        <i class="fas fa-chevron-down" style="color: var(--text-secondary); margin-left: 10px;"></i>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area-3d">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Street Alerts 3D -->
    <script src="{{ asset('js/street-alerts-3d.js') }}"></script>

    <!-- Custom Admin 3D JavaScript -->
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30; // Menos partículas para el dashboard

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 4) + 's';

                // Random colors
                const colors = ['#25D366', '#128C7E', '#00d4ff'];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                particle.style.boxShadow = `0 0 6px ${particle.style.background}`;

                particlesContainer.appendChild(particle);
            }
        }

        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Toggle user menu
        function toggleUserMenu() {
            // Implementar dropdown del usuario si es necesario
            console.log('User menu clicked');
        }

        // Initialize particles
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();

            // Show welcome message only once per session
            showSessionAlert(
                'dashboardWelcomeShown',
                'success',
                'Bienvenido al Dashboard',
                'Sistema de gestión ChatBot WhatsApp cargado exitosamente',
                1000
            );
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>