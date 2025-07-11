@extends('layouts.admin-3d')

@section('title', 'Gestión de Conversaciones')
@section('subtitle', 'Sistema avanzado de gestión de conversaciones con WhatsApp')

@section('content')
<!-- Header con acciones principales -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon);">
        <i class="fas fa-comments me-2" style="color: var(--primary-neon);"></i>Gestión de Conversaciones
    </h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.conversations.create') }}" class="btn-3d">
            <i class="fas fa-plus me-2"></i>Nueva Conversación
        </a>
        <button class="btn-3d" onclick="refreshConversations()">
            <i class="fas fa-sync me-2"></i>Actualizar
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);" onclick="exportConversations()">
            <i class="fas fa-download me-2"></i>Exportar
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);" onclick="toggleRealTimeUpdates()">
            <i class="fas fa-broadcast-tower me-2"></i><span id="realTimeStatus">Tiempo Real</span>
        </button>
    </div>
</div>

<!-- Estadísticas con diseño glass -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="glass-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-label">Conversaciones Activas</div>
                        <div class="stat-value text-primary">{{ $stats['active'] ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-arrow-up me-1"></i>En tiempo real
                        </div>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="glass-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-label">Pendientes</div>
                        <div class="stat-value text-warning">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="stat-change text-warning">
                            <i class="fas fa-clock me-1"></i>Requieren atención
                        </div>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="glass-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-label">Tiempo Respuesta</div>
                        <div class="stat-value text-info">{{ $stats['avg_response_time'] ?? 0 }}<span class="stat-unit">min</span></div>
                        <div class="stat-change text-info">
                            <i class="fas fa-arrow-down me-1"></i>Optimizado
                        </div>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="glass-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-label">Resueltas Hoy</div>
                        <div class="stat-value text-success">{{ $stats['closed_today'] ?? 0 }}</div>
                        <div class="stat-change text-success">
                            <i class="fas fa-check me-1"></i>100% satisfacción
                        </div>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros avanzados con diseño glass -->
<div class="glass-card mb-4">
    <div class="card-body p-4">
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros Avanzados
                </h5>
            </div>
        </div>
        
        <form id="filtersForm" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select class="form-control-3d" name="status" id="statusFilter">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activa</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Esperando</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resuelta</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Agente</label>
                <select class="form-control-3d" name="agent_id" id="agentFilter">
                    <option value="">Todos los agentes</option>
                    @foreach($agents ?? [] as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Prioridad</label>
                <select class="form-control-3d" name="priority" id="priorityFilter">
                    <option value="">Todas</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select class="form-control-3d" name="type" id="typeFilter">
                    <option value="">Todos</option>
                    <option value="chatbot" {{ request('type') == 'chatbot' ? 'selected' : '' }}>Chatbot</option>
                    <option value="human" {{ request('type') == 'human' ? 'selected' : '' }}>Humano</option>
                    <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixto</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" class="form-control-3d" name="date_from" id="dateFromFilter" 
                       value="{{ request('date_from', date('Y-m-d', strtotime('-7 days'))) }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" class="form-control-3d" name="date_to" id="dateToFilter" 
                       value="{{ request('date_to', date('Y-m-d')) }}">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control-3d" name="search" id="searchFilter" 
                       placeholder="Buscar por nombre, teléfono o notas..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-3d">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <button type="button" class="btn-3d-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Acciones masivas -->
<div class="glass-card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-primary fw-bold" id="selectedCount">0</span> conversaciones seleccionadas
            </div>
            <div class="d-flex gap-2">
                <button class="btn-3d-sm" onclick="bulkAssign()">
                    <i class="fas fa-user-plus me-1"></i>Asignar
                </button>
                <button class="btn-3d-sm" onclick="bulkClose()">
                    <i class="fas fa-times-circle me-1"></i>Cerrar
                </button>
                <button class="btn-3d-sm btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </button>
                <button class="btn-3d-sm btn-secondary" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lista de conversaciones con diseño glass -->
<div class="glass-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-glass">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                        </th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Tipo</th>
                        <th>Agente</th>
                        <th>Último Mensaje</th>
                        <th>Tiempo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="conversationsTable">
                    @forelse($conversations ?? [] as $conversation)
                    <tr class="conversation-row" data-id="{{ $conversation->id }}">
                        <td>
                            <input type="checkbox" class="conversation-checkbox" value="{{ $conversation->id }}" 
                                   onchange="updateSelectionCount()">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="contact-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $conversation->contact->name ?? 'Sin nombre' }}</div>
                                    <div class="text-muted small">{{ $conversation->contact->phone_number ?? 'Sin teléfono' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'active' => ['class' => 'status-active', 'icon' => 'fa-circle', 'text' => 'Activa'],
                                    'pending' => ['class' => 'status-pending', 'icon' => 'fa-clock', 'text' => 'Pendiente'],
                                    'waiting' => ['class' => 'status-waiting', 'icon' => 'fa-pause', 'text' => 'Esperando'],
                                    'closed' => ['class' => 'status-closed', 'icon' => 'fa-check-circle', 'text' => 'Cerrada'],
                                    'resolved' => ['class' => 'status-resolved', 'icon' => 'fa-check-circle', 'text' => 'Resuelta'],
                                ];
                                $currentStatus = $statusConfig[$conversation->status] ?? $statusConfig['pending'];
                            @endphp
                            <span class="status-badge {{ $currentStatus['class'] }}">
                                <i class="fas {{ $currentStatus['icon'] }} me-1"></i>{{ $currentStatus['text'] }}
                            </span>
                        </td>
                        <td>
                            @php
                                $priorityConfig = [
                                    'low' => ['class' => 'priority-low', 'icon' => 'fa-minus', 'text' => 'Baja'],
                                    'medium' => ['class' => 'priority-medium', 'icon' => 'fa-equals', 'text' => 'Media'],
                                    'high' => ['class' => 'priority-high', 'icon' => 'fa-exclamation', 'text' => 'Alta'],
                                    'urgent' => ['class' => 'priority-urgent', 'icon' => 'fa-exclamation-triangle', 'text' => 'Urgente'],
                                ];
                                $currentPriority = $priorityConfig[$conversation->priority] ?? $priorityConfig['medium'];
                            @endphp
                            <span class="priority-badge {{ $currentPriority['class'] }}">
                                <i class="fas {{ $currentPriority['icon'] }} me-1"></i>{{ $currentPriority['text'] }}
                            </span>
                        </td>
                        <td>
                            @php
                                $typeConfig = [
                                    'chatbot' => ['class' => 'type-chatbot', 'icon' => 'fa-robot', 'text' => 'Bot'],
                                    'human' => ['class' => 'type-human', 'icon' => 'fa-user', 'text' => 'Humano'],
                                    'mixed' => ['class' => 'type-mixed', 'icon' => 'fa-users', 'text' => 'Mixto'],
                                ];
                                $currentType = $typeConfig[$conversation->type] ?? $typeConfig['chatbot'];
                            @endphp
                            <span class="type-badge {{ $currentType['class'] }}">
                                <i class="fas {{ $currentType['icon'] }} me-1"></i>{{ $currentType['text'] }}
                            </span>
                        </td>
                        <td>
                            @if($conversation->assignedAgent)
                                <div class="d-flex align-items-center">
                                    <div class="agent-avatar me-2">
                                        {{ strtoupper(substr($conversation->assignedAgent->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold small">{{ $conversation->assignedAgent->name }}</div>
                                        <div class="text-muted tiny">{{ $conversation->assignedAgent->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </td>
                        <td>
                            @if($conversation->messages && $conversation->messages->count() > 0)
                                @php $lastMessage = $conversation->messages->first(); @endphp
                                <div class="last-message">
                                    <div class="message-preview">
                                        {{ Str::limit($lastMessage->content ?? 'Mensaje multimedia', 30) }}
                                    </div>
                                    <div class="message-meta">
                                        <span class="badge bg-{{ $lastMessage->direction == 'inbound' ? 'primary' : 'secondary' }}">
                                            {{ $lastMessage->direction == 'inbound' ? 'Entrante' : 'Saliente' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Sin mensajes</span>
                            @endif
                        </td>
                        <td>
                            <div class="time-info">
                                <div class="time-relative text-primary fw-bold">
                                    {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}
                                </div>
                                <div class="time-absolute text-muted small">
                                    {{ $conversation->last_message_at ? $conversation->last_message_at->format('d/m/y H:i') : 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <div class="dropdown">
                                    <button class="action-btn dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false" 
                                            title="Acciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.conversations.show', $conversation) }}">
                                                <i class="fas fa-eye me-2"></i>Ver Detalle
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.conversations.edit', $conversation) }}">
                                                <i class="fas fa-edit me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        @if($conversation->status !== 'closed')
                                            <li>
                                                <button class="dropdown-item" onclick="assignConversation({{ $conversation->id }})">
                                                    <i class="fas fa-user-plus me-2"></i>Asignar
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="transferConversation({{ $conversation->id }})">
                                                    <i class="fas fa-exchange-alt me-2"></i>Transferir
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="closeConversation({{ $conversation->id }})">
                                                    <i class="fas fa-times-circle me-2"></i>Cerrar
                                                </button>
                                            </li>
                                        @else
                                            <li>
                                                <button class="dropdown-item" onclick="reopenConversation({{ $conversation->id }})">
                                                    <i class="fas fa-undo me-2"></i>Reabrir
                                                </button>
                                            </li>
                                        @endif
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" onclick="deleteConversation({{ $conversation->id }})">
                                                <i class="fas fa-trash me-2"></i>Eliminar
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center p-5">
                            <div class="empty-state">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay conversaciones disponibles</h5>
                                <p class="text-muted">Prueba ajustando los filtros o crea una nueva conversación.</p>
                                <a href="{{ route('admin.conversations.create') }}" class="btn-3d">
                                    <i class="fas fa-plus me-2"></i>Nueva Conversación
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($conversations) && $conversations->hasPages())
        <div class="d-flex justify-content-between align-items-center p-4 border-top">
            <div class="text-muted">
                Mostrando {{ $conversations->firstItem() }}-{{ $conversations->lastItem() }} de {{ $conversations->total() }} conversaciones
            </div>
            <div>
                {{ $conversations->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modales para acciones -->
@include('admin.conversations.partials.assign-modal')
@include('admin.conversations.partials.transfer-modal')
@include('admin.conversations.partials.bulk-actions-modal')

@endsection

@push('styles')
<style>
/* Diseño Glassmorphism */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

/* Estadísticas */
.stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    font-family: 'Orbitron', monospace;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    font-family: 'Orbitron', monospace;
    text-shadow: 0 0 10px currentColor;
    line-height: 1;
}

.stat-unit {
    font-size: 16px;
    color: var(--text-secondary);
}

.stat-change {
    font-size: 12px;
    margin-top: 5px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
}

/* Tabla glass */
.table-glass {
    background: transparent;
    color: var(--text-primary);
}

.table-glass thead th {
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    padding: 1rem;
}

.table-glass tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.table-glass tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(5px);
    transform: translateY(-1px);
}

.table-glass td {
    vertical-align: middle;
    padding: 1rem;
    border: none;
}

/* Avatares y badges */
.contact-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-neon), var(--secondary-neon));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.agent-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.status-badge, .priority-badge, .type-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Estados */
.status-active { 
    background: rgba(40, 167, 69, 0.2); 
    color: #28a745; 
    border: 1px solid rgba(40, 167, 69, 0.3);
}
.status-pending { 
    background: rgba(255, 193, 7, 0.2); 
    color: #ffc107; 
    border: 1px solid rgba(255, 193, 7, 0.3);
}
.status-waiting { 
    background: rgba(23, 162, 184, 0.2); 
    color: #17a2b8; 
    border: 1px solid rgba(23, 162, 184, 0.3);
}
.status-closed { 
    background: rgba(108, 117, 125, 0.2); 
    color: #6c757d; 
    border: 1px solid rgba(108, 117, 125, 0.3);
}
.status-resolved { 
    background: rgba(40, 167, 69, 0.2); 
    color: #28a745; 
    border: 1px solid rgba(40, 167, 69, 0.3);
}

/* Prioridades */
.priority-low { 
    background: rgba(23, 162, 184, 0.2); 
    color: #17a2b8; 
    border: 1px solid rgba(23, 162, 184, 0.3);
}
.priority-medium { 
    background: rgba(255, 193, 7, 0.2); 
    color: #ffc107; 
    border: 1px solid rgba(255, 193, 7, 0.3);
}
.priority-high { 
    background: rgba(255, 69, 58, 0.2); 
    color: #ff453a; 
    border: 1px solid rgba(255, 69, 58, 0.3);
}
.priority-urgent { 
    background: rgba(220, 53, 69, 0.2); 
    color: #dc3545; 
    border: 1px solid rgba(220, 53, 69, 0.3);
}

/* Tipos */
.type-chatbot { 
    background: rgba(111, 66, 193, 0.2); 
    color: #6f42c1; 
    border: 1px solid rgba(111, 66, 193, 0.3);
}
.type-human { 
    background: rgba(40, 167, 69, 0.2); 
    color: #28a745; 
    border: 1px solid rgba(40, 167, 69, 0.3);
}
.type-mixed { 
    background: rgba(253, 126, 20, 0.2); 
    color: #fd7e14; 
    border: 1px solid rgba(253, 126, 20, 0.3);
}

/* Mensajes */
.last-message .message-preview {
    font-size: 13px;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.last-message .message-meta .badge {
    font-size: 10px;
}

/* Tiempo */
.time-info .time-relative {
    font-size: 13px;
    margin-bottom: 2px;
}

.time-info .time-absolute {
    font-size: 11px;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
}

.action-buttons .dropdown {
    position: relative;
}

.action-btn {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: var(--text-primary);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    outline: none;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: var(--primary-neon);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transform: translateY(-1px);
    color: var(--primary-neon);
}

.action-btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    border-color: var(--primary-neon);
    outline: none;
}

.action-btn::after {
    display: none;
}

.action-buttons .dropdown-toggle {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: var(--text-primary);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-buttons .dropdown-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: var(--primary-neon);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transform: translateY(-1px);
}

.action-buttons .dropdown-toggle:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    border-color: var(--primary-neon);
}

.action-buttons .dropdown-toggle::after {
    display: none;
}

.dropdown-menu {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin-top: 0.5rem;
    min-width: 200px;
    padding: 0.5rem 0;
}

.dropdown-item {
    transition: all 0.3s ease;
    padding: 0.75rem 1rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
}

.dropdown-item:hover {
    background: rgba(var(--primary-rgb), 0.1);
    color: var(--primary-neon);
    transform: translateX(5px);
}

.dropdown-item.text-danger:hover {
    background: rgba(var(--error-rgb), 0.1);
    color: var(--error-neon);
}

.dropdown-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 0.5rem 0;
}

/* Estado vacío */
.empty-state {
    padding: 3rem;
}

/* Botones adicionales */
.btn-3d-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-3d-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.btn-3d-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .stat-value {
        font-size: 24px;
    }
    
    .table-responsive {
        font-size: 14px;
    }
    
    .contact-avatar {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
    let selectedConversations = [];

    document.addEventListener('DOMContentLoaded', function () {
        // Initial update of selection count and bulk actions display
        updateSelectionCount();

        // Filter form submission
        document.getElementById('filtersForm').addEventListener('submit', function (e) {
            e.preventDefault();
            applyFilters();
        });
    });

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.conversation-checkbox');
        selectedConversations = [];

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            if (checkbox.checked) {
                selectedConversations.push(checkbox.value);
            }
        });
        updateSelectionCount();
    }

    function updateSelectionCount() {
        selectedConversations = Array.from(document.querySelectorAll('.conversation-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        const selectedCountSpan = document.getElementById('selectedCount');
        const bulkActionsCard = document.getElementById('bulkActionsCard');

        selectedCountSpan.textContent = selectedConversations.length;

        if (selectedConversations.length > 0) {
            bulkActionsCard.style.display = 'block';
        } else {
            bulkActionsCard.style.display = 'none';
        }
    }

    function clearSelection() {
        selectedConversations = [];
        document.getElementById('selectAllCheckbox').checked = false;
        document.querySelectorAll('.conversation-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectionCount();
    }

    function applyFilters() {
        const form = document.getElementById('filtersForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.search = params.toString();
    }

    function clearFilters() {
        window.location.href = '{{ route('admin.conversations.index') }}';
    }

    function refreshConversations() {
        applyFilters();
    }

    function exportConversations() {
        const form = document.getElementById('filtersForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.href = `{{ route('admin.conversations.export') }}?${params.toString()}`;
    }

    function toggleRealTimeUpdates() {
        const statusSpan = document.getElementById('realTimeStatus');
        if (statusSpan.textContent === 'Tiempo Real ON') {
            // Disable real-time
            statusSpan.textContent = 'Tiempo Real';
            // You would typically unsubscribe from channels here
            // Echo.leave(`conversations`);
            showStreetAlert('info', 'Tiempo Real', 'Actualizaciones en tiempo real desactivadas.');
        } else {
            // Enable real-time
            statusSpan.textContent = 'Tiempo Real ON';
            // You would typically subscribe to channels here
            // Echo.channel(`conversations`)
            //     .listen('NewConversation', (e) => {
            //         console.log('New conversation:', e.conversation);
            //         refreshConversations();
            //     });
            showStreetAlert('success', 'Tiempo Real', 'Actualizaciones en tiempo real activadas.');
        }
    }

    // Bulk Actions Implementation
    function performBulkAction(action, agentId = null) {
        if (selectedConversations.length === 0) {
            showStreetAlert('warning', 'Sin Selección', 'Selecciona al menos una conversación.');
            return;
        }

        let confirmText = '';
        if (action === 'delete') {
            confirmText = '¿Estás seguro de que quieres ELIMINAR las conversaciones seleccionadas? Esta acción es irreversible.';
        } else if (action === 'close') {
            confirmText = '¿Estás seguro de que quieres CERRAR las conversaciones seleccionadas?';
        } else if (action === 'assign') {
            confirmText = '¿Estás seguro de que quieres ASIGNAR las conversaciones seleccionadas?';
        }

        Swal.fire({
            title: 'Confirmar Acción Masiva',
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-neon)',
            cancelButtonColor: 'var(--error-neon)',
            confirmButtonText: 'Sí, proceder',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Realizando acción masiva...');

                axios.post('{{ route('admin.conversations.bulk-action') }}', {
                    action: action,
                    conversation_ids: selectedConversations,
                    agent_id: agentId,
                    _token: '{{ csrf_token() }}'
                })
                .then(response => {
                    closeLoading();
                    if (response.data.success) {
                        showStreetAlert('success', 'Éxito', response.data.message);
                        clearSelection();
                        refreshConversations();
                    } else {
                        showStreetAlert('error', 'Error', response.data.message || 'Hubo un error al procesar la solicitud.');
                    }
                })
                .catch(error => {
                    closeLoading();
                    console.error('Error en acción masiva:', error);
                    showStreetAlert('error', 'Error', 'Error de red o servidor.');
                });
            }
        });
    }

    function bulkAssign() {
        Swal.fire({
            title: 'Asignar Conversaciones',
            html: `
                <select id="assignAgentSelect" class="form-control-3d" style="width: 100%;">
                    <option value="">Seleccionar Agente</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-neon)',
            cancelButtonColor: 'var(--error-neon)',
            confirmButtonText: 'Asignar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const agentId = document.getElementById('assignAgentSelect').value;
                if (!agentId) {
                    Swal.showValidationMessage('Por favor, selecciona un agente.');
                }
                return agentId;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction('assign', result.value);
            }
        });
    }

    function bulkClose() {
        performBulkAction('close');
    }

    function bulkDelete() {
        performBulkAction('delete');
    }
</script>
@endpush
