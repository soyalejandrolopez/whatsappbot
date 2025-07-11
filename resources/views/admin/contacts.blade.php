@extends('layouts.admin-3d')

@section('title', 'Contactos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-address-book me-2"></i>Gestión de Contactos</h2>
    <div>
        <button class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Contacto
        </button>
        <button class="btn btn-success ms-2">
            <i class="fas fa-file-excel me-2"></i>Exportar
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="blocked">Bloqueado</option>
                    <option value="inactive">Inactivo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Etiquetas</label>
                <select class="form-select">
                    <option value="">Todas las etiquetas</option>
                    <option value="cliente">Cliente</option>
                    <option value="prospecto">Prospecto</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha de registro</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" placeholder="Nombre, teléfono...">
            </div>
        </div>
    </div>
</div>

<!-- Lista de Contactos -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Etiquetas</th>
                        <th>Última Interacción</th>
                        <th>Conversaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <strong>Carlos Rodríguez</strong><br>
                                    <small class="text-muted">carlos@ejemplo.com</small>
                                </div>
                            </div>
                        </td>
                        <td>+52 55 1234 5678</td>
                        <td>
                            <span class="badge bg-success">Activo</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">Cliente</span>
                            <span class="badge bg-info">VIP</span>
                        </td>
                        <td>
                            <small>Hace 5 min</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">12</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" title="Mensaje">
                                    <i class="fas fa-comment"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="Bloquear">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <strong>Ana López</strong><br>
                                    <small class="text-muted">ana@ejemplo.com</small>
                                </div>
                            </div>
                        </td>
                        <td>+52 55 9876 5432</td>
                        <td>
                            <span class="badge bg-success">Activo</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">Prospecto</span>
                        </td>
                        <td>
                            <small>Hace 2 horas</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">5</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" title="Mensaje">
                                    <i class="fas fa-comment"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="Bloquear">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <strong>Miguel Torres</strong><br>
                                    <small class="text-muted">miguel@ejemplo.com</small>
                                </div>
                            </div>
                        </td>
                        <td>+52 55 5555 1234</td>
                        <td>
                            <span class="badge bg-danger">Bloqueado</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Inactivo</span>
                        </td>
                        <td>
                            <small>Hace 5 días</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">3</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" title="Desbloquear">
                                    <i class="fas fa-check"></i>
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

        <!-- Paginación -->
        <nav aria-label="Paginación de contactos">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
                <li class="page-item active">
                    <span class="page-link">1</span>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">Activos</h5>
                <h2 class="text-success">245</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning">Prospectos</h5>
                <h2 class="text-warning">78</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-danger">Bloqueados</h5>
                <h2 class="text-danger">12</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Total</h5>
                <h2 class="text-primary">335</h2>
            </div>
        </div>
    </div>
</div>
@endsection
