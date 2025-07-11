@extends('layouts.admin-3d')

@section('title', 'Conversación Detallada')
@section('subtitle', 'Vista completa de la conversación con herramientas de gestión')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.conversations.index') }}" class="btn-3d me-3" style="padding: 8px 12px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="color: var(--text-primary); font-family: 'Orbitron', monospace; text-shadow: 0 0 10px var(--primary-neon); margin: 0;">
                <i class="fas fa-comment-dots me-2" style="color: var(--primary-neon);"></i>{{ $conversation->contact->name ?? 'Conversación' }}
            </h2>
            <div style="color: var(--text-secondary); font-size: 14px; margin-top: 4px;">
                <i class="fas fa-phone me-1"></i>{{ $conversation->contact->phone ?? '+52 555 123 4567' }}
                <span class="ms-3"><i class="fas fa-clock me-1"></i>Iniciada {{ $conversation->started_at->diffForHumans() ?? 'hace 2 horas' }}</span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn-3d" onclick="assignConversation()">
            <i class="fas fa-user-plus me-2"></i>Asignar
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%);" onclick="transferConversation()">
            <i class="fas fa-exchange-alt me-2"></i>Transferir
        </button>
        <button class="btn-3d" style="background: linear-gradient(135deg, var(--error-neon) 0%, #cc0033 100%);" onclick="closeConversation()">
            <i class="fas fa-times me-2"></i>Cerrar
        </button>
    </div>
</div>

<div class="row">
    <!-- Chat Area -->
    <div class="col-xl-8">
        <!-- Conversation Info -->
        <div class="card-3d mb-4">
            <div class="card-body-3d">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-user" style="color: var(--dark-bg); font-size: 20px;"></i>
                            </div>
                            <div>
                                <div style="color: var(--text-primary); font-weight: 600; font-size: 16px;">{{ $conversation->contact->name ?? 'María González' }}</div>
                                <div style="color: var(--text-secondary); font-size: 12px;">Cliente</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Estado</div>
                        <span class="badge" style="background: var(--success-neon); color: var(--dark-bg); padding: 6px 12px; border-radius: 8px; font-size: 12px;">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ ucfirst($conversation->status ?? 'Activa') }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Prioridad</div>
                        <span class="badge" style="background: var(--error-neon); color: var(--dark-bg); padding: 6px 12px; border-radius: 8px; font-size: 12px;">
                            <i class="fas fa-exclamation me-1"></i>{{ ucfirst($conversation->priority ?? 'Alta') }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Agente Asignado</div>
                        <div class="d-flex align-items-center">
                            <div style="width: 24px; height: 24px; background: var(--info-neon); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                                <span style="color: var(--dark-bg); font-size: 10px; font-weight: 600;">JP</span>
                            </div>
                            <span style="color: var(--text-primary); font-size: 14px;">{{ $conversation->assignedAgent->name ?? 'Juan Pérez' }}</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Mensajes</div>
                        <div style="color: var(--text-primary); font-weight: 600; font-size: 16px;">{{ $conversation->messages->count() ?? 12 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="card-3d" style="height: 600px; display: flex; flex-direction: column;">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-comments me-2"></i>
                    Historial de Mensajes
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="scrollToTop()">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 4px 8px;" onclick="scrollToBottom()">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </div>
            
            <!-- Messages Container -->
            <div class="card-body-3d" style="flex: 1; overflow-y: auto; padding: 20px;" id="messagesContainer">
                <!-- Incoming Message -->
                <div class="message-item incoming" style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="width: 32px; height: 32px; background: var(--primary-neon); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                            <i class="fas fa-user" style="color: var(--dark-bg); font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; max-width: 70%;">
                            <div style="background: rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 12px 16px; border: 1px solid var(--glass-border);">
                                <div style="color: var(--text-primary); font-size: 14px; line-height: 1.4;">
                                    Hola, necesito ayuda urgente con mi pedido. El número de orden es #12345 y no he recibido ninguna actualización.
                                </div>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 11px; margin-top: 4px; margin-left: 16px;">
                                <i class="fas fa-clock me-1"></i>Hace 15 minutos
                                <span class="ms-2"><i class="fas fa-check-double me-1" style="color: var(--success-neon);"></i>Leído</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outgoing Message -->
                <div class="message-item outgoing" style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: flex-start; justify-content: flex-end;">
                        <div style="flex: 1; max-width: 70%; text-align: right;">
                            <div style="background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%); border-radius: 12px; padding: 12px 16px; display: inline-block; text-align: left;">
                                <div style="color: var(--dark-bg); font-size: 14px; line-height: 1.4; font-weight: 500;">
                                    Hola María, gracias por contactarnos. Estoy revisando tu pedido #12345 ahora mismo. Te tendré una respuesta en unos minutos.
                                </div>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 11px; margin-top: 4px; margin-right: 16px;">
                                <span><i class="fas fa-user me-1"></i>Juan Pérez</span>
                                <span class="ms-2"><i class="fas fa-clock me-1"></i>Hace 12 minutos</span>
                                <span class="ms-2"><i class="fas fa-check-double me-1" style="color: var(--success-neon);"></i>Entregado</span>
                            </div>
                        </div>
                        <div style="width: 32px; height: 32px; background: var(--info-neon); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 12px; flex-shrink: 0;">
                            <span style="color: var(--dark-bg); font-size: 10px; font-weight: 600;">JP</span>
                        </div>
                    </div>
                </div>

                <!-- System Message -->
                <div class="message-item system" style="margin-bottom: 20px;">
                    <div style="text-align: center;">
                        <div style="background: rgba(255, 255, 255, 0.05); border-radius: 8px; padding: 8px 16px; display: inline-block; border: 1px solid var(--glass-border);">
                            <div style="color: var(--text-secondary); font-size: 12px;">
                                <i class="fas fa-info-circle me-1"></i>Conversación asignada a Juan Pérez
                            </div>
                        </div>
                        <div style="color: var(--text-secondary); font-size: 10px; margin-top: 4px;">
                            Hace 20 minutos
                        </div>
                    </div>
                </div>

                <!-- Another Incoming Message -->
                <div class="message-item incoming" style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="width: 32px; height: 32px; background: var(--primary-neon); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                            <i class="fas fa-user" style="color: var(--dark-bg); font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; max-width: 70%;">
                            <div style="background: rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 12px 16px; border: 1px solid var(--glass-border);">
                                <div style="color: var(--text-primary); font-size: 14px; line-height: 1.4;">
                                    Perfecto, gracias. Estaré esperando tu respuesta.
                                </div>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 11px; margin-top: 4px; margin-left: 16px;">
                                <i class="fas fa-clock me-1"></i>Hace 10 minutos
                                <span class="ms-2"><i class="fas fa-check-double me-1" style="color: var(--success-neon);"></i>Leído</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div class="message-item typing" id="typingIndicator" style="margin-bottom: 20px; display: none;">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="width: 32px; height: 32px; background: var(--info-neon); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                            <i class="fas fa-keyboard" style="color: var(--dark-bg); font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; max-width: 70%;">
                            <div style="background: rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 12px 16px; border: 1px solid var(--glass-border);">
                                <div id="typingIndicatorText" style="color: var(--text-secondary); font-size: 14px; font-style: italic;">
                                    Someone is typing...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="card-footer-3d" style="border-top: 1px solid var(--glass-border); padding: 20px;">
                <form id="messageForm" class="d-flex gap-3">
                    <div style="flex: 1;">
                        <textarea 
                            class="form-control-3d" 
                            id="messageInput" 
                            placeholder="Escribe tu mensaje aquí..." 
                            rows="2" 
                            style="resize: none; min-height: 50px;"
                        ></textarea>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 8px 12px;" onclick="attachFile()" title="Adjuntar archivo">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="button" class="btn btn-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); padding: 8px 12px;" onclick="showQuickReplies()" title="Respuestas rápidas">
                            <i class="fas fa-bolt"></i>
                        </button>
                        <button type="submit" class="btn-3d" style="padding: 8px 16px;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4">
        <!-- Contact Info -->
        <div class="card-3d mb-4">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-user me-2"></i>
                    Información del Contacto
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="contact-info-item" style="margin-bottom: 15px;">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Nombre</div>
                    <div style="color: var(--text-primary); font-weight: 600;">{{ $conversation->contact->name ?? 'María González' }}</div>
                </div>
                <div class="contact-info-item" style="margin-bottom: 15px;">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Teléfono</div>
                    <div style="color: var(--text-primary); font-weight: 600;">{{ $conversation->contact->phone ?? '+52 555 123 4567' }}</div>
                </div>
                <div class="contact-info-item" style="margin-bottom: 15px;">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Email</div>
                    <div style="color: var(--text-primary); font-weight: 600;">{{ $conversation->contact->email ?? 'maria.gonzalez@email.com' }}</div>
                </div>
                <div class="contact-info-item" style="margin-bottom: 15px;">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Primera Interacción</div>
                    <div style="color: var(--text-primary); font-weight: 600;">{{ $conversation->contact->created_at->format('d/m/Y H:i') ?? '15/01/2024 14:30' }}</div>
                </div>
                <div class="contact-info-item">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Total Conversaciones</div>
                    <div style="color: var(--text-primary); font-weight: 600;">{{ $conversation->contact->conversations->count() ?? 1 }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card-3d mb-4">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body-3d">
                <div class="d-grid gap-2">
                    <button class="btn-3d" onclick="changePriority()" style="padding: 10px;">
                        <i class="fas fa-flag me-2"></i>Cambiar Prioridad
                    </button>
                    <button class="btn-3d" style="background: linear-gradient(135deg, var(--info-neon) 0%, #0099cc 100%); padding: 10px;" onclick="addNote()">
                        <i class="fas fa-sticky-note me-2"></i>Agregar Nota
                    </button>
                    <button class="btn-3d" style="background: linear-gradient(135deg, var(--warning-neon) 0%, #cc8800 100%); padding: 10px;" onclick="escalateToSupervisor()">
                        <i class="fas fa-level-up-alt me-2"></i>Escalar a Supervisor
                    </button>
                    <button class="btn-3d" style="background: linear-gradient(135deg, var(--accent-neon) 0%, #8000ff 100%); padding: 10px;" onclick="sendTemplate()">
                        <i class="fas fa-file-alt me-2"></i>Enviar Plantilla
                    </button>
                </div>
            </div>
        </div>

        <!-- Historial de Conversaciones -->
        <div class="card-3d">
            <div class="card-header-3d">
                <h6 class="card-title-3d">
                    <i class="fas fa-history me-2"></i>
                    Historial del Contacto
                </h6>
            </div>
            <div class="card-body-3d">
                <ul class="list-unstyled">
                    @forelse($conversationHistory as $history)
                        <li style="margin-bottom: 15px;">
                            <a href="{{ route('admin.conversations.show', $history->id) }}" style="text-decoration: none; color: var(--text-secondary);">
                                <div style="font-size: 14px; color: var(--text-primary); margin-bottom: 4px;">
                                    <i class="fas fa-comment me-2"></i> Conversación #{{ $history->id }}
                                </div>
                                <div style="font-size: 12px;">
                                    <span class="me-3"><i class="fas fa-user-tie me-1"></i> {{ $history->assignedAgent->name ?? 'Sin asignar' }}</span>
                                    <span><i class="fas fa-clock me-1"></i> {{ $history->started_at->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>No hay conversaciones anteriores.</li>
                    @endforelse
                </ul>
            </div>
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
        padding: 12px;
        transition: all 0.3s ease;
    }

    .form-control-3d:focus {
        outline: none;
        border-color: var(--primary-neon);
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.3);
        background: rgba(255, 255, 255, 0.08);
    }

    .message-item {
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-item.incoming .message-bubble {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--glass-border);
    }

    .message-item.outgoing .message-bubble {
        background: linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%);
    }

    .message-item.system {
        opacity: 0.8;
    }

    #messagesContainer {
        scroll-behavior: smooth;
    }

    #messagesContainer::-webkit-scrollbar {
        width: 6px;
    }

    #messagesContainer::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    #messagesContainer::-webkit-scrollbar-thumb {
        background: var(--primary-neon);
        border-radius: 3px;
    }

    #messagesContainer::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-neon);
    }

    .typing-indicator {
        animation: pulse 1.5s infinite;
    }

    .contact-info-item {
        transition: all 0.3s ease;
        padding: 8px;
        border-radius: 6px;
    }

    .contact-info-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .history-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .history-item:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        transform: translateX(5px);
    }

    .quick-reply-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quick-reply-item:hover {
        background: rgba(37, 211, 102, 0.1);
        border-color: var(--primary-neon);
        transform: translateX(5px);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messagesContainer = document.getElementById('messagesContainer');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const conversationId = {{ $conversation->id }};
        const currentUserId = {{ Auth::id() }};
        const typingIndicator = document.getElementById('typingIndicator');
        let typingTimer;

        // Scroll to the bottom of the messages container
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        scrollToBottom();

        // Listen for form submission
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const message = messageInput.value;
            if (message.trim() === '') return;

            axios.post(`/admin/conversations/${conversationId}/message`, {
                message: message,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    messageInput.value = '';
                    appendMessage(response.data.data, true); // Append as my own message
                } else {
                    // Handle error, e.g., show a notification
                    console.error('Error sending message:', response.data.message);
                }
            })
            .catch(error => {
                console.error('An error occurred:', error);
            });
        });

        // Listen for typing
        messageInput.addEventListener('input', () => {
            // Clear previous timer
            clearTimeout(typingTimer);
            
            // Broadcast typing event
            Echo.private(`conversation.${conversationId}`).whisper('typing', {
                name: '{{ Auth::user()->name }}'
            });

            // Set a timer to broadcast 'stopped-typing'
            typingTimer = setTimeout(() => {
                Echo.private(`conversation.${conversationId}`).whisper('stopped-typing', {
                    name: '{{ Auth::user()->name }}'
                });
            }, 3000); // 3 seconds
        });


        // Listen for new messages
        Echo.private(`conversation.${conversationId}`)
            .listen('MessageSent', (e) => {
                appendMessage(e.message, e.message.sender_id === currentUserId);
                // Hide typing indicator when a message is received
                typingIndicator.style.display = 'none';
            })
            .listenForWhisper('typing', (e) => {
                const indicatorText = document.getElementById('typingIndicatorText');
                indicatorText.textContent = `${e.name} is typing...`;
                typingIndicator.style.display = 'flex';
                scrollToBottom();
            })
            .listenForWhisper('stopped-typing', (e) => {
                typingIndicator.style.display = 'none';
            });

        function appendMessage(message, isOwnMessage) {
            const messageHtml = createMessageHtml(message, isOwnMessage);
            messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        }

        function createMessageHtml(message, isOwnMessage) {
            const messageClass = isOwnMessage ? 'outgoing' : 'incoming';
            const senderName = isOwnMessage ? 'Tú' : (message.sender ? message.sender.name : 'Usuario');
            const avatarInitial = senderName.substring(0, 2).toUpperCase();
            
            const avatarHtml = `
                <div style="width: 32px; height: 32px; background: ${isOwnMessage ? 'var(--info-neon)' : 'var(--primary-neon)'}; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; ${isOwnMessage ? 'margin-left: 12px;' : 'margin-right: 12px;'}">
                    ${isOwnMessage ? `<span style="color: var(--dark-bg); font-size: 10px; font-weight: 600;">${avatarInitial}</span>` : `<i class="fas fa-user" style="color: var(--dark-bg); font-size: 14px;"></i>`}
                </div>`;

            const messageBubble = `
                <div style="flex: 1; max-width: 70%; ${isOwnMessage ? 'text-align: right;' : ''}">
                    <div style="background: ${isOwnMessage ? 'linear-gradient(135deg, var(--primary-neon) 0%, var(--secondary-neon) 100%)' : 'rgba(255, 255, 255, 0.1)'}; border-radius: 12px; padding: 12px 16px; display: inline-block; text-align: left; border: 1px solid var(--glass-border);">
                        <div style="color: ${isOwnMessage ? 'var(--dark-bg)' : 'var(--text-primary)'}; font-size: 14px; line-height: 1.4; font-weight: ${isOwnMessage ? '500' : 'normal'};">
                            ${message.content}
                        </div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 11px; margin-top: 4px; ${isOwnMessage ? 'margin-right: 16px;' : 'margin-left: 16px;'}">
                        ${!isOwnMessage ? `<span><i class="fas fa-user me-1"></i>${senderName}</span>` : ''}
                        <span class="ms-2"><i class="fas fa-clock me-1"></i> ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                </div>`;
            
            let messageContent;
            if (isOwnMessage) {
                messageContent = `<div style="display: flex; align-items: flex-start; justify-content: flex-end;">${messageBubble}${avatarHtml}</div>`;
            } else {
                messageContent = `<div style="display: flex; align-items: flex-start;">${avatarHtml}${messageBubble}</div>`;
            }

            return `
                <div class="message-item ${messageClass}" style="margin-bottom: 20px;">
                    ${messageContent}
                </div>`;
        }
    });
</script>
@endpush
