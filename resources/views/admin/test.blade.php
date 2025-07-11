<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #25D366;
            min-height: 100vh;
            color: white;
        }
        .nav-link {
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.2);
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar p-4">
                <h4 class="mb-4">ChatBot WhatsApp</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/conversations">
                            <i class="fas fa-comments me-2"></i> Conversaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/contacts">
                            <i class="fas fa-address-book me-2"></i> Contactos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/chatbot-flows">
                            <i class="fas fa-project-diagram me-2"></i> Flujos del Bot
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/chatbot-responses">
                            <i class="fas fa-reply-all me-2"></i> Respuestas del Bot
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/users">
                            <i class="fas fa-users me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/analytics-view">
                            <i class="fas fa-chart-bar me-2"></i> Analíticas
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel de Administración</h2>
                    <div>
                        <span class="me-3">Bienvenido, {{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Conversaciones</h5>
                                <p class="card-text display-4">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Contactos</h5>
                                <p class="card-text display-4">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Mensajes</h5>
                                <p class="card-text display-4">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Información del Sistema</h5>
                        <p>Esta es una página de prueba para verificar que el panel de administración funciona correctamente.</p>
                        <p>Si puedes ver esta página, significa que la autenticación y el acceso al panel de administración están funcionando.</p>
                        <p>Ahora puedes navegar a las diferentes secciones del panel usando el menú lateral.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
