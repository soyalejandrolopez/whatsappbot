@extends('layouts.admin-3d')

@section('title', 'Analíticas y Reportes')
@section('subtitle', 'Métricas avanzadas, satisfacción del cliente y reportes en tiempo real')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon);">
        <i class="fas fa-chart-bar me-2" style="color: var(--primary-neon);"></i>Analíticas y Reportes
    </h2>
    <div class="d-flex gap-2">
        <button class="btn-3d" onclick="exportCSV()">
            <i class="fas fa-download me-2"></i>Exportar CSV
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%);" onclick="exportExcel()">
            <i class="fas fa-file-excel me-2"></i>Exportar Excel
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);" onclick="generateDailyReport()">
            <i class="fas fa-magic me-2"></i>Reporte Diario
        </button>
    </div>
</div>

<!-- Date Filters -->
<div class="card-3d mb-4">
    <div class="card-body-3d">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Fecha Inicio</label>
                <input type="date" class="form-control-3d" id="startDate" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Fecha Fin</label>
                <input type="date" class="form-control-3d" id="endDate" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Período</label>
                <select class="form-control-3d" id="periodFilter">
                    <option value="today">Hoy</option>
                    <option value="week" selected>Esta semana</option>
                    <option value="month">Este mes</option>
                    <option value="quarter">Este trimestre</option>
                    <option value="year">Este año</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color: var(--text-primary); font-weight: 600;">Agente</label>
                <select class="form-control-3d" id="agentFilter">
                    <option value="">Todos los agentes</option>
                    <option value="chatbot">ChatBot</option>
                    <option value="human">Agentes humanos</option>
                    <option value="1">Juan Pérez</option>
                    <option value="2">María García</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card-3d">
            <div class="card-body-3d">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; font-weight: 600; color: var(--success-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Satisfacción Cliente
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--success-neon);">
                            4.8<span style="font-size: 16px; color: var(--text-secondary);">/5.0</span>
                        </div>
                        <div style="font-size: 12px; color: var(--success-neon); margin-top: 5px;">
                            <i class="fas fa-arrow-up me-1"></i>+0.3 vs mes anterior
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--success-neon) 0%, #00cc77 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);">
                        <i class="fas fa-smile" style="font-size: 24px; color: var(--dark-bg);"></i>
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
                            Tiempo Respuesta
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--info-neon);">
                            1.2<span style="font-size: 16px; color: var(--text-secondary);">min</span>
                        </div>
                        <div style="font-size: 12px; color: var(--info-neon); margin-top: 5px;">
                            <i class="fas fa-arrow-down me-1"></i>-15% más rápido
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);">
                        <i class="fas fa-clock" style="font-size: 24px; color: var(--dark-bg);"></i>
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
                        <div style="font-size: 12px; font-weight: 600; color: var(--primary-neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-family: 'Orbitron', monospace;">
                            Tasa Resolución
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon);">
                            94<span style="font-size: 16px; color: var(--text-secondary);">%</span>
                        </div>
                        <div style="font-size: 12px; color: var(--primary-neon); margin-top: 5px;">
                            <i class="fas fa-arrow-up me-1"></i>+5% vs semana anterior
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(37, 211, 102, 0.5);">
                        <i class="fas fa-check-circle" style="font-size: 24px; color: var(--dark-bg);"></i>
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
                            Actividad por Hora
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--warning-neon);">
                            156<span style="font-size: 16px; color: var(--text-secondary);">/h</span>
                        </div>
                        <div style="font-size: 12px; color: var(--warning-neon); margin-top: 5px;">
                            <i class="fas fa-arrow-up me-1"></i>Pico: 14:00-16:00
                        </div>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(255, 170, 0, 0.5);">
                        <i class="fas fa-chart-line" style="font-size: 24px; color: var(--dark-bg);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-chart-area me-2"></i>
                    Actividad por Horas (Últimos 7 días)
                </h6>
            </div>
            <div class="card-body-3d">
                <canvas id="hourlyActivityChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-chart-pie me-2"></i>
                    Rendimiento por Agente
                </h6>
            </div>
            <div class="card-body-3d">
                <canvas id="agentPerformanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Response Time Analysis -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-stopwatch me-2"></i>
                    Análisis de Tiempos de Respuesta
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="response-time-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--glass-border);">
                    <div>
                        <div style="color: var(--text-primary); font-weight: 600;">ChatBot Automático</div>
                        <small style="color: var(--text-secondary);">Respuestas instantáneas</small>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--success-neon); font-weight: 700; font-size: 18px;">0.3s</div>
                        <div style="width: 100px; height: 6px; background: var(--glass-bg); border-radius: 3px; overflow: hidden; margin-top: 5px;">
                            <div style="width: 15%; height: 100%; background: var(--success-neon);"></div>
                        </div>
                    </div>
                </div>

                <div class="response-time-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--glass-border);">
                    <div>
                        <div style="color: var(--text-primary); font-weight: 600;">Agentes Humanos</div>
                        <small style="color: var(--text-secondary);">Respuestas personalizadas</small>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--info-neon); font-weight: 700; font-size: 18px;">2.1min</div>
                        <div style="width: 100px; height: 6px; background: var(--glass-bg); border-radius: 3px; overflow: hidden; margin-top: 5px;">
                            <div style="width: 70%; height: 100%; background: var(--info-neon);"></div>
                        </div>
                    </div>
                </div>

                <div class="response-time-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
                    <div>
                        <div style="color: var(--text-primary); font-weight: 600;">Escalación a Supervisor</div>
                        <small style="color: var(--text-secondary);">Casos complejos</small>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--warning-neon); font-weight: 700; font-size: 18px;">5.7min</div>
                        <div style="width: 100px; height: 6px; background: var(--glass-bg); border-radius: 3px; overflow: hidden; margin-top: 5px;">
                            <div style="width: 95%; height: 100%; background: var(--warning-neon);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-star me-2"></i>
                    Estadísticas de Satisfacción
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="satisfaction-item" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: var(--text-primary); font-weight: 600;">Excelente (5 estrellas)</span>
                        <span style="color: var(--success-neon); font-weight: 600;">68%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--glass-bg); border-radius: 4px; overflow: hidden;">
                        <div style="width: 68%; height: 100%; background: var(--success-neon); border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="satisfaction-item" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: var(--text-primary); font-weight: 600;">Bueno (4 estrellas)</span>
                        <span style="color: var(--primary-neon); font-weight: 600;">22%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--glass-bg); border-radius: 4px; overflow: hidden;">
                        <div style="width: 22%; height: 100%; background: var(--primary-neon); border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="satisfaction-item" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: var(--text-primary); font-weight: 600;">Regular (3 estrellas)</span>
                        <span style="color: var(--warning-neon); font-weight: 600;">7%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--glass-bg); border-radius: 4px; overflow: hidden;">
                        <div style="width: 7%; height: 100%; background: var(--warning-neon); border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="satisfaction-item" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: var(--text-primary); font-weight: 600;">Malo (1-2 estrellas)</span>
                        <span style="color: var(--error-neon); font-weight: 600;">3%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--glass-bg); border-radius: 4px; overflow: hidden;">
                        <div style="width: 3%; height: 100%; background: var(--error-neon); border-radius: 4px;"></div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--glass-border);">
                    <div style="color: var(--text-primary); font-size: 24px; font-weight: 700; font-family: 'Orbitron', monospace;">4.8/5.0</div>
                    <div style="color: var(--text-secondary); font-size: 14px;">Promedio general</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reports -->
<div class="card-3d">
    <div class="card-header-3d">
        <h6 class="card-title-3d">
            <i class="fas fa-file-alt me-2"></i>
            Reportes Generados Automáticamente
        </h6>
    </div>
    <div class="card-body-3d">
        <div class="table-responsive">
            <table class="table" style="color: var(--text-primary);">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <th style="color: var(--text-primary); font-weight: 600;">Fecha</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Tipo</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Período</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Estado</th>
                        <th style="color: var(--text-primary); font-weight: 600;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="color: var(--text-primary);">{{ date('d/m/Y') }}</td>
                        <td>
                            <span class="badge" style="background: var(--primary-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                Reporte Diario
                            </span>
                        </td>
                        <td style="color: var(--text-secondary);">{{ date('d/m/Y', strtotime('-1 day')) }} - {{ date('d/m/Y') }}</td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-check me-1"></i>Generado
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="downloadReport('daily', '{{ date('Y-m-d') }}')" title="Descargar">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="color: var(--text-primary);">{{ date('d/m/Y', strtotime('-1 day')) }}</td>
                        <td>
                            <span class="badge" style="background: var(--info-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                Reporte Semanal
                            </span>
                        </td>
                        <td style="color: var(--text-secondary);">{{ date('d/m/Y', strtotime('-7 days')) }} - {{ date('d/m/Y', strtotime('-1 day')) }}</td>
                        <td>
                            <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 4px 8px; border-radius: 8px; font-size: 11px;">
                                <i class="fas fa-check me-1"></i>Generado
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="downloadReport('weekly', '{{ date('Y-m-d', strtotime('-1 day')) }}')" title="Descargar">
                                <i class="fas fa-download"></i>
                            </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Hourly Activity Chart
        const hourlyCtx = document.getElementById('hourlyActivityChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
                datasets: [{
                    label: 'Conversaciones',
                    data: [12, 8, 5, 15, 45, 89, 156, 234, 198, 145, 89, 34],
                    borderColor: '#25D366',
                    backgroundColor: 'rgba(37, 211, 102, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Resoluciones',
                    data: [10, 7, 4, 14, 42, 84, 147, 220, 186, 136, 83, 31],
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
                        labels: { color: '#ffffff' }
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

        // Agent Performance Chart
        const agentCtx = document.getElementById('agentPerformanceChart').getContext('2d');
        new Chart(agentCtx, {
            type: 'doughnut',
            data: {
                labels: ['ChatBot', 'Juan Pérez', 'María García', 'Otros'],
                datasets: [{
                    data: [65, 15, 12, 8],
                    backgroundColor: ['#25D366', '#00d4ff', '#ffaa00', '#ff3366'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: { color: '#ffffff' }
                    }
                }
            }
        });

        // Show welcome message only once per session for this section
        showSessionAlert(
            'analyticsWelcomeShown',
            'info',
            'Analíticas Avanzadas',
            'Sistema de métricas y reportes en tiempo real activado',
            1000
        );
    });

    // Export functions
    function exportCSV() {
        showLoading('Generando archivo CSV...');
        setTimeout(() => {
            closeLoading();
            showStreetAlert('success', 'CSV Exportado', 'Archivo descargado exitosamente');
        }, 2000);
    }

    function exportExcel() {
        showLoading('Generando archivo Excel...');
        setTimeout(() => {
            closeLoading();
            showStreetAlert('success', 'Excel Exportado', 'Archivo descargado exitosamente');
        }, 2500);
    }

    function generateDailyReport() {
        showLoading('Generando reporte diario automático...');
        setTimeout(() => {
            closeLoading();
            showStreetAlert('success', 'Reporte Generado', 'Reporte diario creado y enviado por email');
        }, 3000);
    }

    function downloadReport(type, date) {
        showStreetAlert('success', 'Descargando', `Reporte ${type} del ${date} descargado`);
    }
</script>
@endpush
