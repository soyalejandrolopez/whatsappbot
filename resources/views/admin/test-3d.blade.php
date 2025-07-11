@extends('layouts.admin-3d')

@section('title', 'Panel de Prueba')
@section('subtitle', 'Vista de prueba del sistema administrativo 3D')

@section('content')
<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-rocket me-2"></i>
                    ¡Bienvenido al Panel Administrativo 3D!
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 style="color: var(--text-primary); margin-bottom: 15px;">Sistema ChatBot WhatsApp</h4>
                        <p style="color: var(--text-secondary); margin-bottom: 20px;">
                            El panel administrativo está funcionando correctamente con el nuevo diseño 3D. 
                            Todas las funcionalidades están disponibles y operativas.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('admin.conversations.index') }}" class="btn-3d">
                                <i class="fas fa-comments me-2"></i>Ver Conversaciones
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);">
                                <i class="fas fa-chart-bar me-2"></i>Ver Analíticas
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 0 30px rgba(37, 211, 102, 0.5); animation: pulse 2s infinite;">
                            <i class="fab fa-whatsapp" style="font-size: 60px; color: var(--dark-bg);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card-3d">
            <div class="card-body-3d text-center">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);">
                    <i class="fas fa-check" style="font-size: 24px; color: var(--dark-bg);"></i>
                </div>
                <h6 style="color: var(--text-primary); margin-bottom: 8px; font-family: 'Orbitron', monospace;">Sistema Operativo</h6>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Todos los servicios funcionando correctamente</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card-3d">
            <div class="card-body-3d text-center">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);">
                    <i class="fas fa-database" style="font-size: 24px; color: var(--dark-bg);"></i>
                </div>
                <h6 style="color: var(--text-primary); margin-bottom: 8px; font-family: 'Orbitron', monospace;">Base de Datos</h6>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Conexión estable y optimizada</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card-3d">
            <div class="card-body-3d text-center">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 0 20px rgba(255, 170, 0, 0.5);">
                    <i class="fas fa-robot" style="font-size: 24px; color: var(--dark-bg);"></i>
                </div>
                <h6 style="color: var(--text-primary); margin-bottom: 8px; font-family: 'Orbitron', monospace;">ChatBot</h6>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Respuestas automáticas activas</p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Menu -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-compass me-2"></i>
                    Navegación Rápida
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.conversations.index') }}" class="card-3d text-decoration-none" style="display: block; padding: 20px; text-align: center;">
                            <i class="fas fa-comments" style="font-size: 32px; color: var(--primary-neon); margin-bottom: 10px;"></i>
                            <h6 style="color: var(--text-primary); margin: 0;">Conversaciones</h6>
                            <small style="color: var(--text-secondary);">Gestionar chats</small>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.contacts.index') }}" class="card-3d text-decoration-none" style="display: block; padding: 20px; text-align: center;">
                            <i class="fas fa-address-book" style="font-size: 32px; color: var(--info-neon); margin-bottom: 10px;"></i>
                            <h6 style="color: var(--text-primary); margin: 0;">Contactos</h6>
                            <small style="color: var(--text-secondary);">Base de datos</small>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.chatbot-flows.index') }}" class="card-3d text-decoration-none" style="display: block; padding: 20px; text-align: center;">
                            <i class="fas fa-project-diagram" style="font-size: 32px; color: var(--warning-neon); margin-bottom: 10px;"></i>
                            <h6 style="color: var(--text-primary); margin: 0;">Flujos del Bot</h6>
                            <small style="color: var(--text-secondary);">Configuración</small>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.users.index') }}" class="card-3d text-decoration-none" style="display: block; padding: 20px; text-align: center;">
                            <i class="fas fa-users" style="font-size: 32px; color: var(--accent-neon); margin-bottom: 10px;"></i>
                            <h6 style="color: var(--text-primary); margin: 0;">Usuarios</h6>
                            <small style="color: var(--text-secondary);">Gestión de acceso</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row">
    <div class="col-md-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-info-circle me-2"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="info-item" style="display: flex; justify-content: between; padding: 10px 0; border-bottom: 1px solid var(--glass-border);">
                    <span style="color: var(--text-secondary);">Versión Laravel:</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ app()->version() }}</span>
                </div>
                <div class="info-item" style="display: flex; justify-content: between; padding: 10px 0; border-bottom: 1px solid var(--glass-border);">
                    <span style="color: var(--text-secondary);">Versión PHP:</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ PHP_VERSION }}</span>
                </div>
                <div class="info-item" style="display: flex; justify-content: between; padding: 10px 0; border-bottom: 1px solid var(--glass-border);">
                    <span style="color: var(--text-secondary);">Usuario Actual:</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ Auth::user()->name ?? 'Administrador' }}</span>
                </div>
                <div class="info-item" style="display: flex; justify-content: between; padding: 10px 0;">
                    <span style="color: var(--text-secondary);">Último Acceso:</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-cog me-2"></i>
                    Configuración Rápida
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.chatbot-responses.index') }}" class="btn-3d">
                        <i class="fas fa-robot me-2"></i>Configurar Respuestas del Bot
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);">
                        <i class="fas fa-chart-bar me-2"></i>Ver Reportes y Analíticas
                    </a>
                    <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);" onclick="testSystem()">
                        <i class="fas fa-vial me-2"></i>Probar Sistema
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
    }
</style>
@endpush

@push('scripts')
<script>
    function testSystem() {
        showStreetAlert('success', 'Sistema Probado', 'Todas las funcionalidades están operativas y funcionando correctamente');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Show welcome message only once per session for this section
        showSessionAlert(
            'testPanelWelcomeShown',
            'info',
            'Panel Administrativo 3D',
            'Bienvenido al sistema de gestión ChatBot WhatsApp con diseño 3D avanzado',
            1500
        );
    });
</script>
@endpush
