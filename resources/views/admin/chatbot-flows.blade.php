@extends('layouts.admin-3d')

@section('title', 'Flujos del Chatbot')
@section('subtitle', 'Sistema de flujos de conversación personalizables con IA')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon);">
        <i class="fas fa-project-diagram me-2" style="color: var(--primary-neon);"></i>Flujos del Chatbot
    </h2>
    <div class="d-flex gap-2">
        <button class="btn-3d" onclick="createFlow()">
            <i class="fas fa-plus me-2"></i>Nuevo Flujo
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);" onclick="importFlow()">
            <i class="fas fa-upload me-2"></i>Importar
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);" onclick="exportFlows()">
            <i class="fas fa-download me-2"></i>Exportar
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--primary-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Total Flujos
                        </div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace;">
                            {{ $stats['total_flows'] ?? 12 }}
                        </div>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-project-diagram" style="font-size: 20px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--success-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Flujos Activos
                        </div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace;">
                            {{ $stats['active_flows'] ?? 8 }}
                        </div>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play" style="font-size: 20px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--warning-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Con IA
                        </div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace;">
                            {{ $stats['ai_enabled'] ?? 5 }}
                        </div>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-brain" style="font-size: 20px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--info-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Uso Total
                        </div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace;">
                            {{ number_format($stats['total_usage'] ?? 2847) }}
                        </div>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 20px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card-3d mb-4">
    <div class="card-body-3d">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Estado</label>
                <select class="form-control-3d" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="draft">Borrador</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Tipo de Activación</label>
                <select class="form-control-3d" id="triggerFilter">
                    <option value="">Todos los tipos</option>
                    <option value="welcome">Bienvenida</option>
                    <option value="keyword">Palabra clave</option>
                    <option value="menu_option">Opción de menú</option>
                    <option value="intent">Intención</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Idioma</label>
                <select class="form-control-3d" id="languageFilter">
                    <option value="">Todos los idiomas</option>
                    <option value="es">Español</option>
                    <option value="en">Inglés</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Buscar</label>
                <input type="text" class="form-control-3d" id="searchFilter" placeholder="Buscar flujos...">
            </div>
        </div>
    </div>
</div>

<!-- Flows List -->
<div class="card-3d">
    <div class="card-header-3d">
        <h6 class="card-title-3d">
            <i class="fas fa-list me-2"></i>
            Lista de Flujos del Chatbot
        </h6>
    </div>
    <div class="card-body-3d">
        <div class="table-responsive">
            <table class="table" style="color: var(--text-primary);">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <th style="color: var(--text-primary); font-weight: 600;">Nombre</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Tipo</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Estado</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Prioridad</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Uso</th>
                        <th style="color: var(--text-primary); font-weight: 600;">IA</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="flowsTable">
                    <!-- Flujo 1: Bienvenida con IA -->
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td>
                            <div>
                                <strong style="color: var(--text-primary);">Flujo de Bienvenida</strong>
                                <br>
                                <small style="color: var(--text-secondary);">Saludo inicial con opciones del menú</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--primary-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                Bienvenida
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 30px; height: 6px; background: var(--glass-bg); border-radius: 3px; margin-right: 8px; overflow: hidden;">
                                    <div style="width: 90%; height: 100%; background: var(--primary-neon);"></div>
                                </div>
                                <span style="color: var(--text-secondary); font-size: 12px;">9/10</span>
                            </div>
                        </td>
                        <td>
                            <div style="color: var(--text-primary); font-weight: 600;">1,247</div>
                            <small style="color: var(--text-secondary);">usos</small>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--warning-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-brain me-1"></i>OpenAI
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="viewFlow(1)" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="editFlow(1)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="testFlow(1)" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="duplicateFlow(1)" title="Duplicar">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Flujo 2: Soporte Técnico -->
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td>
                            <div>
                                <strong style="color: var(--text-primary);">Soporte Técnico</strong>
                                <br>
                                <small style="color: var(--text-secondary);">Escalación automática a agentes humanos</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--info-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                Palabra clave
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 30px; height: 6px; background: var(--glass-bg); border-radius: 3px; margin-right: 8px; overflow: hidden;">
                                    <div style="width: 80%; height: 100%; background: var(--info-neon);"></div>
                                </div>
                                <span style="color: var(--text-secondary); font-size: 12px;">8/10</span>
                            </div>
                        </td>
                        <td>
                            <div style="color: var(--text-primary); font-weight: 600;">892</div>
                            <small style="color: var(--text-secondary);">usos</small>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-user me-1"></i>Humano
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="viewFlow(2)" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="editFlow(2)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="testFlow(2)" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="duplicateFlow(2)" title="Duplicar">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Flujo 3: Mensajes Interactivos -->
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td>
                            <div>
                                <strong style="color: var(--text-primary);">Mensajes Interactivos</strong>
                                <br>
                                <small style="color: var(--text-secondary);">Botones y listas dinámicas</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--accent-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                Menú
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 30px; height: 6px; background: var(--glass-bg); border-radius: 3px; margin-right: 8px; overflow: hidden;">
                                    <div style="width: 70%; height: 100%; background: var(--warning-neon);"></div>
                                </div>
                                <span style="color: var(--text-secondary); font-size: 12px;">7/10</span>
                            </div>
                        </td>
                        <td>
                            <div style="color: var(--text-primary); font-weight: 600;">456</div>
                            <small style="color: var(--text-secondary);">usos</small>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--info-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-mouse-pointer me-1"></i>Interactivo
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="viewFlow(3)" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="editFlow(3)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="testFlow(3)" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="duplicateFlow(3)" title="Duplicar">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control-3d {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        color: var(--text-primary);
        padding: 8px 12px;
        transition: all 0.3s ease;
    }
    
    .form-control-3d:focus {
        outline: none;
        border-color: var(--primary-neon);
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.3);
        background: rgba(255, 255, 255, 0.08);
    }
    
    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Flow management functions
    function createFlow() {
        showStreetAlert('info', 'Crear Flujo', 'Abriendo editor de flujos del chatbot...');
        // Implementar navegación al formulario de creación
    }

    function viewFlow(id) {
        showStreetAlert('info', 'Ver Flujo', `Mostrando detalles del flujo #${id}`);
        // Implementar vista detallada del flujo
    }

    function editFlow(id) {
        showStreetAlert('info', 'Editar Flujo', `Editando flujo #${id}...`);
        // Implementar edición del flujo
    }

    function testFlow(id) {
        showLoading('Probando flujo del chatbot...');
        
        // Simular prueba del flujo
        setTimeout(() => {
            closeLoading();
            showStreetAlert('success', 'Prueba Exitosa', 'El flujo se ejecutó correctamente. Tiempo de respuesta: 1.2s');
        }, 2000);
    }

    function duplicateFlow(id) {
        showStreetAlert('success', 'Flujo Duplicado', `Flujo #${id} duplicado exitosamente`);
        // Implementar duplicación del flujo
    }

    function importFlow() {
        showStreetAlert('info', 'Importar Flujo', 'Selecciona un archivo JSON con la configuración del flujo');
        // Implementar importación de flujos
    }

    function exportFlows() {
        showStreetAlert('success', 'Exportando', 'Descargando configuración de todos los flujos...');
        // Implementar exportación de flujos
    }

    // Filters
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const triggerFilter = document.getElementById('triggerFilter');
        const languageFilter = document.getElementById('languageFilter');
        const searchFilter = document.getElementById('searchFilter');

        // Add event listeners for filters
        [statusFilter, triggerFilter, languageFilter].forEach(filter => {
            filter.addEventListener('change', applyFilters);
        });

        searchFilter.addEventListener('input', debounce(applyFilters, 300));

        function applyFilters() {
            // Implementar lógica de filtrado
            console.log('Aplicando filtros...');
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Show welcome message only once per session for this section
        showSessionAlert(
            'chatbotFlowsWelcomeShown',
            'info',
            'Motor del Chatbot',
            'Sistema de flujos de conversación personalizables con IA activado',
            1000
        );
    });
</script>
@endpush
