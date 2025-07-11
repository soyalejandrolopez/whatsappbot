@extends('layouts.admin-3d')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
    <div>
        <button class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Usuario
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select class="form-select">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administrador</option>
                    <option value="agent">Agente</option>
                    <option value="user">Usuario</option>
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
                <label class="form-label">Último acceso</label>
                <select class="form-select">
                    <option value="">Cualquier fecha</option>
                    <option value="today">Hoy</option>
                    <option value="week">Esta semana</option>
                    <option value="month">Este mes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" placeholder="Nombre o email...">
            </div>
        </div>
    </div>
</div>

<!-- Lista de Usuarios -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Conversaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <strong>Administrador</strong><br>
                                    <small class="text-muted">Cuenta principal</small>
                                </div>
                            </div>
                        </td>
                        <td>admin@chatbot.com</td>
                        <td>
                            <span class="badge bg-danger">Administrador</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Activo</span>
                        </td>
                        <td>
                            <small>Conectado ahora</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">-</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-info"></i>
                                </div>
                                <div>
                                    <strong>Juan Pérez</strong><br>
                                    <small class="text-muted">Agente de soporte</small>
                                </div>
                            </div>
                        </td>
                        <td>juan@chatbot.com</td>
                        <td>
                            <span class="badge bg-info">Agente</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Activo</span>
                        </td>
                        <td>
                            <small>Hace 15 min</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">12</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="Desactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-info"></i>
                                </div>
                                <div>
                                    <strong>María García</strong><br>
                                    <small class="text-muted">Agente de ventas</small>
                                </div>
                            </div>
                        </td>
                        <td>maria@chatbot.com</td>
                        <td>
                            <span class="badge bg-info">Agente</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Activo</span>
                        </td>
                        <td>
                            <small>Hace 1 hora</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">8</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="Desactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                </div>
                                <div>
                                    <strong>Carlos López</strong><br>
                                    <small class="text-muted">Usuario de prueba</small>
                                </div>
                            </div>
                        </td>
                        <td>carlos@ejemplo.com</td>
                        <td>
                            <span class="badge bg-secondary">Usuario</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">Inactivo</span>
                        </td>
                        <td>
                            <small>Hace 5 días</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">2</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" title="Activar">
                                    <i class="fas fa-check"></i>
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
                <h5 class="card-title text-danger">Administradores</h5>
                <h2 class="text-danger">1</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-info">Agentes</h5>
                <h2 class="text-info">5</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">Activos</h5>
                <h2 class="text-success">4</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Total</h5>
                <h2 class="text-primary">6</h2>
            </div>
        </div>
    </div>
</div>
@endsection
