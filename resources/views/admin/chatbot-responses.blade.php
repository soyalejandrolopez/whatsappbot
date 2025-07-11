@extends('layouts.admin-3d')

@section('title', 'Respuestas del Bot')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-reply-all me-2"></i>Respuestas del Chatbot</h2>
    <div>
        <button class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Respuesta
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select class="form-select">
                    <option value="">Todas las categorías</option>
                    <option value="greeting">Saludo</option>
                    <option value="menu">Menú</option>
                    <option value="error">Error</option>
                    <option value="goodbye">Despedida</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Idioma</label>
                <select class="form-select">
                    <option value="">Todos los idiomas</option>
                    <option value="es">Español</option>
                    <option value="en">Inglés</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" placeholder="Clave o contenido...">
            </div>
        </div>
    </div>
</div>

<!-- Lista de Respuestas -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Categoría</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Uso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <code>welcome</code>
                        </td>
                        <td>
                            <span class="badge bg-primary">Saludo</span>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                ¡Hola! 👋 Bienvenido a *TechSolutions*. Soy tu asistente virtual...
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label text-success">Activo</label>
                            </div>
                        </td>
                        <td>
                            <span class="text-success">1,245 veces</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code>main_menu</code>
                        </td>
                        <td>
                            <span class="badge bg-info">Menú</span>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                ¿En qué puedo ayudarte hoy? Selecciona una opción del menú...
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label text-success">Activo</label>
                            </div>
                        </td>
                        <td>
                            <span class="text-success">892 veces</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code>not_understood</code>
                        </td>
                        <td>
                            <span class="badge bg-warning">Error</span>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                Lo siento, no entendí tu mensaje. ¿Podrías reformularlo?
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label text-success">Activo</label>
                            </div>
                        </td>
                        <td>
                            <span class="text-warning">234 veces</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" title="Probar">
                                    <i class="fas fa-play"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code>goodbye</code>
                        </td>
                        <td>
                            <span class="badge bg-success">Despedida</span>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                ¡Gracias por contactarnos! Que tengas un excelente día 😊
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                                <label class="form-check-label text-muted">Inactivo</label>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">156 veces</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">Activas</h5>
                <h2 class="text-success">24</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Inactivas</h5>
                <h2 class="text-muted">6</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Usos Totales</h5>
                <h2 class="text-primary">2,527</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning">Más Usada</h5>
                <h2 class="text-warning">welcome</h2>
            </div>
        </div>
    </div>
</div>
@endsection
