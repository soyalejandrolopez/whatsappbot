<!-- Modal de Transferencia -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary" id="transferModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Transferir Conversación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    <input type="hidden" id="transferConversationId" name="conversation_id">
                    
                    <div class="mb-3">
                        <label for="transferAgentSelect" class="form-label">Transferir a Agente</label>
                        <select class="form-control-3d" id="transferAgentSelect" name="new_agent_id" required>
                            <option value="">Selecciona el nuevo agente...</option>
                            @foreach($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->name }} ({{ $agent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transferPriority" class="form-label">Actualizar Prioridad</label>
                        <select class="form-control-3d" id="transferPriority" name="priority">
                            <option value="low">Baja</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transferReason" class="form-label">Motivo de Transferencia</label>
                        <select class="form-control-3d" id="transferReason" name="reason" required>
                            <option value="">Selecciona un motivo...</option>
                            <option value="expertise">Especialización requerida</option>
                            <option value="workload">Balanceamiento de carga</option>
                            <option value="availability">Disponibilidad del agente</option>
                            <option value="escalation">Escalamiento</option>
                            <option value="client_request">Solicitud del cliente</option>
                            <option value="other">Otro motivo</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transferNotes" class="form-label">Notas de Transferencia</label>
                        <textarea class="form-control-3d" id="transferNotes" name="notes" rows="3" 
                                  placeholder="Agregar información importante para el nuevo agente..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifyClient" name="notify_client" checked>
                            <label class="form-check-label" for="notifyClient">
                                Notificar al cliente sobre la transferencia
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn-3d-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn-3d" onclick="confirmTransfer()">
                    <i class="fas fa-exchange-alt me-2"></i>Transferir Conversación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmTransfer() {
    const form = document.getElementById('transferForm');
    const conversationId = document.getElementById('transferConversationId').value;
    const newAgentId = document.getElementById('transferAgentSelect').value;
    const reason = document.getElementById('transferReason').value;
    const notes = document.getElementById('transferNotes').value;
    
    if (!newAgentId) {
        showStreetAlert('warning', 'Agente Requerido', 'Selecciona el agente al que transferir la conversación');
        return;
    }
    
    if (!reason) {
        showStreetAlert('warning', 'Motivo Requerido', 'Selecciona el motivo de la transferencia');
        return;
    }
    
    if (!notes.trim()) {
        showStreetAlert('warning', 'Notas Requeridas', 'Agregar notas de transferencia es obligatorio');
        return;
    }
    
    showLoading('Transfiriendo conversación...');
    
    fetch(`{{ route('admin.conversations.index') }}/${conversationId}/transfer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            new_agent_id: newAgentId,
            priority: document.getElementById('transferPriority').value,
            reason: reason,
            notes: notes,
            notify_client: document.getElementById('notifyClient').checked
        })
    })
    .then(response => response.json())
    .then(data => {
        closeLoading();
        $('#transferModal').modal('hide');
        
        if (data.success) {
            showStreetAlert('success', 'Conversación Transferida', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showStreetAlert('error', 'Error', data.message || 'No se pudo transferir la conversación');
        }
    })
    .catch(error => {
        closeLoading();
        $('#transferModal').modal('hide');
        console.error('Error:', error);
        showStreetAlert('error', 'Error de Red', 'Ocurrió un error al contactar al servidor');
    });
}
</script> 