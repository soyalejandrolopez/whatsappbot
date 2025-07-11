@extends('layouts.admin-3d')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general del sistema ChatBot WhatsApp')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--primary-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Total Conversaciones
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon);">
                            {{ number_format($stats['total_conversations']) }}
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(37, 211, 102, 0.5); animation: pulse 2s infinite;">
                        <i class="fas fa-comments" style="font-size: 24px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--success-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Conversaciones Activas
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--success-neon);">
                            {{ number_format($stats['active_conversations']) }}
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(0, 255, 136, 0.5); animation: pulse 2s infinite 0.5s;">
                        <i class="fas fa-comment-dots" style="font-size: 24px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--info-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Total Contactos
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--info-neon);">
                            {{ number_format($stats['total_contacts']) }}
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(0, 212, 255, 0.5); animation: pulse 2s infinite 1s;">
                        <i class="fas fa-address-book" style="font-size: 24px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--warning-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Mensajes Hoy
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--warning-neon);">
                            {{ number_format($stats['messages_today']) }}
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(255, 170, 0, 0.5); animation: pulse 2s infinite 1.5s;">
                        <i class="fas fa-envelope" style="font-size: 24px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-chart-area me-2"></i>
                    Actividad Semanal
                </h6>
            </div>
            <div class="card-body-3d">
                <canvas id="weeklyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-chart-pie me-2"></i>
                    Tipos de Conversación
                </h6>
            </div>
            <div class="card-body-3d">
                <canvas id="conversationTypesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Top Agents -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-history me-2"></i>
                    Actividad Reciente
                </h6>
            </div>
            <div class="card-body-3d" style="max-height: 400px; overflow-y: auto;">
                @forelse($recentConversations as $conversation)
                    <div class="activity-item" style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--glass-border);">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-comment" style="color: var(--dark-bg); font-size: 16px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div style="color: var(--text-primary); font-weight: 600; margin-bottom: 2px;">
                                Conversación con {{ $conversation->contact->name ?? $conversation->contact->phone_number }}
                            </div>
                            <div style="color: var(--text-secondary); font-size: 12px;">
                                {{ $conversation->last_message_at->diffForHumans() }}
                                @if($conversation->assignedUser)
                                    - Asignada a {{ $conversation->assignedUser->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4">
                        <p style="color: var(--text-secondary);">No hay actividad reciente.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-star me-2"></i>
                    Top Agentes (Mes Actual)
                </h6>
            </div>
            <div class="card-body-3d" style="max-height: 400px; overflow-y: auto;">
                 @forelse($topAgents as $agent)
                    <div class="agent-item" style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--glass-border);">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: var(--dark-bg); font-weight: bold;">
                            {{ strtoupper(substr($agent->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div style="color: var(--text-primary); font-weight: 600; margin-bottom: 2px;">{{ $agent->name }}</div>
                            <div style="color: var(--text-secondary); font-size: 12px;">{{ $agent->assigned_conversations_count }} conversaciones resueltas</div>
                        </div>
                        <div style="color: var(--success-neon); font-weight: 600;">
                            <i class="fas fa-check-circle me-1"></i> #{{ $loop->iteration }}
                        </div>
                    </div>
                 @empty
                    <div class="text-center p-4">
                        <p style="color: var(--text-secondary);">No hay datos de agentes para este mes.</p>
                    </div>
                 @endforelse

                <div class="text-center mt-3">
                    <a href="{{ route('admin.users.index') }}" class="btn-3d" style="padding: 8px 20px; font-size: 14px;">
                        <i class="fas fa-users me-2"></i>Ver Todos los Agentes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.conversations.index') }}" class="btn-3d w-100" style="padding: 15px;">
                            <i class="fas fa-comments me-2"></i>
                            Ver Conversaciones
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.contacts.index') }}" class="btn-3d w-100" style="padding: 15px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);">
                            <i class="fas fa-address-book me-2"></i>
                            Gestionar Contactos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.chatbot-flows.index') }}" class="btn-3d w-100" style="padding: 15px; background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);">
                            <i class="fas fa-project-diagram me-2"></i>
                            Configurar Bot
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.analytics') }}" class="btn-3d w-100" style="padding: 15px; background: linear-gradient(135deg, var(--accent-neon) 0%, #8000ff 100%);">
                            <i class="fas fa-chart-bar me-2"></i>
                            Ver Analíticas
                        </a>
                    </div>
                </div>

                <!-- Segunda fila de acciones -->
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <button onclick="clearSessionAlerts()" class="btn-3d w-100" style="padding: 12px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);">
                            <i class="fas fa-redo me-2"></i>
                            Reiniciar Notificaciones de Bienvenida
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button onclick="testSystemNotifications()" class="btn-3d w-100" style="padding: 12px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%);">
                            <i class="fas fa-bell me-2"></i>
                            Probar Sistema de Notificaciones
                        </button>
                    </div>
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const weeklyMetrics = @json($weeklyMetrics);
        const conversationTypes = @json($conversationTypes);

        // Gráfico de actividad semanal
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyMetrics.days,
                datasets: [{
                    label: 'Conversaciones',
                    data: weeklyMetrics.conversations,
                    borderColor: '#25D366',
                    backgroundColor: 'rgba(37, 211, 102, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Mensajes',
                    data: weeklyMetrics.messages,
                    borderColor: '#00d4ff',
                    backgroundColor: 'rgba(0, 212, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#b0b0b0' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    y: {
                        ticks: { color: '#b0b0b0' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                }
            }
        });

        // Gráfico de tipos de conversación
        const typesCtx = document.getElementById('conversationTypesChart').getContext('2d');
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(conversationTypes),
                datasets: [{
                    data: Object.values(conversationTypes),
                    backgroundColor: ['#25D366', '#00d4ff', '#ffaa00', '#f14e4e', '#9d4edd'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });
    });

    function clearSessionAlerts() {
        showStreetAlert('info', 'Procesando...', 'Reiniciando las notificaciones de bienvenida.');

        fetch("{{ route('admin.notifications.clear') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStreetAlert('success', 'Éxito', data.message);
            } else {
                showStreetAlert('error', 'Error', 'No se pudo completar la operación.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStreetAlert('error', 'Error de Red', 'Ocurrió un error al contactar al servidor.');
        });
    }

    // Function to test system notifications
    function testSystemNotifications() {
        showStreetAlert('info', 'Probando Sistema', 'Iniciando prueba de notificaciones...');

        setTimeout(() => {
            showStreetAlert('success', 'Prueba Exitosa', 'Sistema de notificaciones funcionando correctamente');
        }, 1500);

        setTimeout(() => {
            showStreetAlert('warning', 'Advertencia de Prueba', 'Esta es una notificación de advertencia');
        }, 3000);

        setTimeout(() => {
            showStreetAlert('error', 'Error de Prueba', 'Esta es una notificación de error (solo prueba)');
        }, 4500);
    }
</script>
@endpush
