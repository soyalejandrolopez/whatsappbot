@extends('layouts.admin-3d')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Estadísticas principales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Total Conversaciones
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($stats['total_conversations']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Conversaciones Activas
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($stats['active_conversations']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comment-dots fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-info">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Total Contactos
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($stats['total_contacts']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            Mensajes Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($stats['messages_today']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area me-2"></i>
                    Actividad de los Últimos 7 Días
                </h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>
                    Tipos de Conversación
                </h6>
            </div>
            <div class="card-body">
                <canvas id="conversationTypesChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Conversaciones recientes y Top agentes -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock me-2"></i>
                    Conversaciones Recientes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th>Agente</th>
                                <th>Último Mensaje</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentConversations as $conversation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">
                                                {{ $conversation->contact->name ?? $conversation->contact->phone_number }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $conversation->contact->phone_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $conversation->status === 'active' ? 'success' : ($conversation->status === 'closed' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($conversation->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($conversation->assignedUser)
                                        <span class="text-primary">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $conversation->assignedUser->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-robot me-1"></i>
                                            Bot
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'Sin mensajes' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.conversations.show', $conversation) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No hay conversaciones recientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy me-2"></i>
                    Top Agentes del Mes
                </h6>
            </div>
            <div class="card-body">
                @forelse($topAgents as $agent)
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-3">
                        @if($agent->avatar)
                            <img src="{{ $agent->avatar }}" class="rounded-circle" width="40" height="40">
                        @else
                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold">{{ $agent->name }}</div>
                        <div class="text-muted small">
                            {{ $agent->assigned_conversations_count }} conversaciones resueltas
                        </div>
                    </div>
                    <div class="text-success font-weight-bold">
                        #{{ $loop->iteration }}
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gráfico de actividad semanal
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: @json($weeklyMetrics['days']),
        datasets: [{
            label: 'Conversaciones',
            data: @json($weeklyMetrics['conversations']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Mensajes',
            data: @json($weeklyMetrics['messages']),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de tipos de conversación
const typesCtx = document.getElementById('conversationTypesChart').getContext('2d');
new Chart(typesCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($conversationTypes->toArray())),
        datasets: [{
            data: @json(array_values($conversationTypes->toArray())),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
