<!-- Modal de Asignación -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary" id="assignModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Asignar Conversación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <input type="hidden" id="assignConversationId" name="conversation_id">
                    
                    <div class="mb-3">
                        <label for="agentSelect" class="form-label">Seleccionar Agente</label>
                        <select class="form-control-3d" id="agentSelect" name="agent_id" required>
                            <option value="">Selecciona un agente...</option>
                            @foreach($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->name }} ({{ $agent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prioritySelect" class="form-label">Prioridad</label>
                        <select class="form-control-3d" id="prioritySelect" name="priority">
                            <option value="low">Baja</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignNotes" class="form-label">Notas (Opcional)</label>
                        <textarea class="form-control-3d" id="assignNotes" name="notes" rows="3" 
                                  placeholder="Agregar notas sobre la asignación..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn-3d-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn-3d" onclick="confirmAssignment()">
                    <i class="fas fa-check me-2"></i>Asignar Conversación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAssignment() {
    const form = document.getElementById('assignForm');
    const formData = new FormData(form);
    const conversationId = document.getElementById('assignConversationId').value;
    const agentId = document.getElementById('agentSelect').value;
    
    if (!agentId) {
        showStreetAlert('warning', 'Agente Requerido', 'Selecciona un agente para asignar la conversación');
        return;
    }
    
    showLoading('Asignando conversación...');
    
    fetch(`{{ route('admin.conversations.index') }}/${conversationId}/assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            agent_id: agentId,
            priority: document.getElementById('prioritySelect').value,
            notes: document.getElementById('assignNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        closeLoading();
        $('#assignModal').modal('hide');
        
        if (data.success) {
            showStreetAlert('success', 'Conversación Asignada', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showStreetAlert('error', 'Error', data.message || 'No se pudo asignar la conversación');
        }
    })
    .catch(error => {
        closeLoading();
        $('#assignModal').modal('hide');
        console.error('Error:', error);
        showStreetAlert('error', 'Error de Red', 'Ocurrió un error al contactar al servidor');
    });
}
</script> 