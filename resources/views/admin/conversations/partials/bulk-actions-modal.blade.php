<!-- Modal de Acciones Masivas - Asignación -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary" id="bulkAssignModalLabel">
                    <i class="fas fa-users me-2"></i>Asignación Masiva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info glass-alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Se asignarán <strong id="bulkAssignCount">0</strong> conversaciones seleccionadas.
                </div>
                
                <form id="bulkAssignForm">
                    <div class="mb-3">
                        <label for="bulkAgentSelect" class="form-label">Seleccionar Agente</label>
                        <select class="form-control-3d" id="bulkAgentSelect" name="agent_id" required>
                            <option value="">Selecciona un agente...</option>
                            @foreach($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->name }} ({{ $agent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulkPrioritySelect" class="form-label">Actualizar Prioridad</label>
                        <select class="form-control-3d" id="bulkPrioritySelect" name="priority">
                            <option value="">Mantener prioridad actual</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulkAssignNotes" class="form-label">Notas de Asignación</label>
                        <textarea class="form-control-3d" id="bulkAssignNotes" name="notes" rows="3" 
                                  placeholder="Notas que se aplicarán a todas las conversaciones..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifyClients" name="notify_clients" checked>
                            <label class="form-check-label" for="notifyClients">
                                Notificar a los clientes sobre la asignación
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn-3d-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn-3d" onclick="confirmBulkAssign()">
                    <i class="fas fa-users me-2"></i>Asignar Conversaciones
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminación Masiva -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="bulkDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación Masiva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger glass-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                </div>
                
                <p class="text-muted">
                    Estás a punto de eliminar <strong id="bulkDeleteCount">0</strong> conversaciones seleccionadas.
                    Solo se eliminarán las conversaciones cerradas.
                </p>
                
                <div class="mb-3">
                    <label for="deleteConfirmation" class="form-label">
                        Escribe <strong>"ELIMINAR"</strong> para confirmar:
                    </label>
                    <input type="text" class="form-control-3d" id="deleteConfirmation" 
                           placeholder="ELIMINAR" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn-3d-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn-danger" onclick="confirmBulkDelete()" id="confirmDeleteBtn" disabled>
                    <i class="fas fa-trash me-2"></i>Eliminar Conversaciones
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.glass-modal {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.glass-alert {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
}

.modal-header {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
}

.modal-footer {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
}
</style>

<script>
// Actualizar contador cuando se abra el modal
$('#bulkAssignModal').on('show.bs.modal', function() {
    const selectedCount = document.querySelectorAll('.conversation-checkbox:checked').length;
    document.getElementById('bulkAssignCount').textContent = selectedCount;
});

$('#bulkDeleteModal').on('show.bs.modal', function() {
    const selectedCount = document.querySelectorAll('.conversation-checkbox:checked').length;
    document.getElementById('bulkDeleteCount').textContent = selectedCount;
});

// Validar confirmación de eliminación
document.getElementById('deleteConfirmation').addEventListener('input', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (this.value === 'ELIMINAR') {
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('btn-secondary');
        confirmBtn.classList.add('btn-danger');
    } else {
        confirmBtn.disabled = true;
        confirmBtn.classList.remove('btn-danger');
        confirmBtn.classList.add('btn-secondary');
    }
});

function confirmBulkAssign() {
    const agentId = document.getElementById('bulkAgentSelect').value;
    const selectedIds = getSelectedIds();
    
    if (!agentId) {
        showStreetAlert('warning', 'Agente Requerido', 'Selecciona un agente para la asignación masiva');
        return;
    }
    
    if (selectedIds.length === 0) {
        showStreetAlert('warning', 'Sin Selección', 'No hay conversaciones seleccionadas');
        return;
    }
    
    showLoading(`Asignando ${selectedIds.length} conversaciones...`);
    
    const requestData = {
        conversation_ids: selectedIds,
        agent_id: agentId,
        priority: document.getElementById('bulkPrioritySelect').value,
        notes: document.getElementById('bulkAssignNotes').value,
        notify_clients: document.getElementById('notifyClients').checked
    };
    
    // Simular procesamiento (reemplazar con llamada real a la API)
    setTimeout(() => {
        closeLoading();
        $('#bulkAssignModal').modal('hide');
        showStreetAlert('success', 'Asignación Completada', `${selectedIds.length} conversaciones asignadas exitosamente`);
        setTimeout(() => window.location.reload(), 1500);
    }, 2000);
}

function confirmBulkDelete() {
    const confirmation = document.getElementById('deleteConfirmation').value;
    const selectedIds = getSelectedIds();
    
    if (confirmation !== 'ELIMINAR') {
        showStreetAlert('warning', 'Confirmación Requerida', 'Escribe "ELIMINAR" para confirmar');
        return;
    }
    
    if (selectedIds.length === 0) {
        showStreetAlert('warning', 'Sin Selección', 'No hay conversaciones seleccionadas');
        return;
    }
    
    showLoading(`Eliminando ${selectedIds.length} conversaciones...`);
    
    // Simular procesamiento (reemplazar con llamada real a la API)
    setTimeout(() => {
        closeLoading();
        $('#bulkDeleteModal').modal('hide');
        showStreetAlert('success', 'Eliminación Completada', `${selectedIds.length} conversaciones eliminadas exitosamente`);
        setTimeout(() => window.location.reload(), 1500);
    }, 3000);
}
</script> 