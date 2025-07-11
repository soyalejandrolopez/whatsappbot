<?php

namespace App\Services;

use App\Models\WhatsappContact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatbotFlow;
use App\Models\ChatbotResponse;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected $whatsappService;
    protected $aiService;

    public function __construct(WhatsAppService $whatsappService, AIService $aiService)
    {
        $this->whatsappService = $whatsappService;
        $this->aiService = $aiService;
    }

    /**
     * Procesar mensaje entrante
     */
    public function processIncomingMessage($messageData)
    {
        try {
            // Obtener o crear contacto
            $contact = $this->getOrCreateContact($messageData['phone_number']);
            
            // Obtener o crear conversación activa
            $conversation = $this->getOrCreateActiveConversation($contact);
            
            // Guardar mensaje entrante
            $message = $this->saveIncomingMessage($conversation, $messageData);
            
            // Procesar respuesta del chatbot
            $this->processResponse($conversation, $message);
            
        } catch (\Exception $e) {
            Log::error('Error processing incoming message', [
                'error' => $e->getMessage(),
                'message_data' => $messageData
            ]);
        }
    }

    /**
     * Obtener o crear contacto
     */
    protected function getOrCreateContact($phoneNumber)
    {
        $contact = WhatsappContact::where('phone_number', $phoneNumber)->first();
        
        if (!$contact) {
            // Obtener información del perfil desde WhatsApp
            $profileData = $this->whatsappService->getContactProfile($phoneNumber);
            
            $contact = WhatsappContact::create([
                'phone_number' => $phoneNumber,
                'whatsapp_id' => $phoneNumber, // Usar el número como ID por defecto
                'name' => $profileData['name'] ?? null,
                'profile_name' => $profileData['name'] ?? null,
                'language' => 'es',
                'profile_data' => $profileData,
                'last_interaction_at' => now()
            ]);
        } else {
            $contact->updateLastInteraction();
        }
        
        return $contact;
    }

    /**
     * Obtener o crear conversación activa
     */
    protected function getOrCreateActiveConversation($contact)
    {
        $conversation = $contact->activeConversation;
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'contact_id' => $contact->id,
                'status' => 'active',
                'type' => 'chatbot',
                'language' => $contact->language,
                'started_at' => now()
            ]);
        }
        
        return $conversation;
    }

    /**
     * Guardar mensaje entrante
     */
    protected function saveIncomingMessage($conversation, $messageData)
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'whatsapp_message_id' => $messageData['whatsapp_message_id'],
            'direction' => 'inbound',
            'type' => $messageData['type'],
            'content' => is_array($messageData['content']) ? json_encode($messageData['content']) : $messageData['content'],
            'media_data' => $messageData['type'] !== 'text' ? $messageData['content'] : null,
            'whatsapp_timestamp' => date('Y-m-d H:i:s', $messageData['timestamp']),
            'metadata' => $messageData['metadata']
        ]);
        
        $conversation->incrementMessageCount();
        
        return $message;
    }

    /**
     * Procesar respuesta del chatbot
     */
    protected function processResponse($conversation, $message)
    {
        $content = $message->content;
        
        // Si la conversación está asignada a un humano, no procesar con bot
        if ($conversation->isAssigned() && $conversation->type === 'human') {
            return;
        }
        
        // Determinar el flujo apropiado
        $flow = $this->determineFlow($conversation, $content);
        
        if ($flow) {
            $this->executeFlow($conversation, $flow, $content);
        } else {
            // Intentar respuesta con IA si está disponible
            if ($this->aiService->isAvailable()) {
                $this->processWithAI($conversation, $content);
            } else {
                // Respuesta por defecto si no se encuentra flujo
                $this->sendDefaultResponse($conversation);
            }
        }
    }

    /**
     * Determinar el flujo apropiado
     */
    protected function determineFlow($conversation, $content)
    {
        // Si es una nueva conversación, usar flujo de bienvenida
        if ($conversation->message_count <= 1) {
            return ChatbotFlow::where('trigger_type', 'welcome')
                             ->where('is_active', true)
                             ->where('language', $conversation->language)
                             ->orderBy('priority', 'desc')
                             ->first();
        }
        
        // Buscar flujo por palabra clave
        $flows = ChatbotFlow::where('trigger_type', 'keyword')
                           ->where('is_active', true)
                           ->where('language', $conversation->language)
                           ->get();
        
        foreach ($flows as $flow) {
            $conditions = $flow->trigger_conditions;
            if (isset($conditions['keywords'])) {
                foreach ($conditions['keywords'] as $keyword) {
                    if (stripos($content, $keyword) !== false) {
                        return $flow;
                    }
                }
            }
        }
        
        // Buscar flujo por opción de menú
        if (is_numeric($content)) {
            return ChatbotFlow::where('trigger_type', 'menu_option')
                             ->where('is_active', true)
                             ->where('language', $conversation->language)
                             ->whereJsonContains('trigger_conditions->menu_option', $content)
                             ->first();
        }
        
        return null;
    }

    /**
     * Ejecutar flujo
     */
    protected function executeFlow($conversation, $flow, $userInput = null)
    {
        $steps = $flow->flow_steps;
        $context = $conversation->flow_context ?? [];
        
        // Determinar el paso actual
        $currentStep = $context['current_step'] ?? 1;
        
        // Buscar el paso en el flujo
        $step = collect($steps)->firstWhere('step', $currentStep);
        
        if (!$step) {
            $this->sendDefaultResponse($conversation);
            return;
        }
        
        switch ($step['type']) {
            case 'message':
                $this->sendFlowMessage($conversation, $step);
                break;
                
            case 'wait_input':
                // Ya estamos esperando input, procesar el siguiente paso
                $nextStep = $step['next_step'] ?? null;
                if ($nextStep) {
                    $context['current_step'] = $nextStep;
                    $conversation->updateFlowContext($context);
                    $this->executeFlow($conversation, $flow, $userInput);
                }
                break;
                
            case 'process_input':
                $this->processInputStep($conversation, $step, $userInput);
                break;
        }
        
        // Actualizar contexto del flujo
        $conversation->setCurrentFlow($flow->id);
        $flow->increment('usage_count');
        $flow->update(['last_used_at' => now()]);
    }

    /**
     * Enviar mensaje del flujo
     */
    protected function sendFlowMessage($conversation, $step)
    {
        $responseKey = $step['response_key'] ?? null;
        $content = $step['content'] ?? null;
        
        if ($responseKey) {
            $response = ChatbotResponse::where('key', $responseKey)
                                     ->where('language', $conversation->language)
                                     ->where('is_active', true)
                                     ->first();
            
            if ($response) {
                $this->sendResponse($conversation, $response);
            }
        } elseif ($content) {
            $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $content
            );
            
            $this->saveOutgoingMessage($conversation, $content, 'text', true);
        }
        
        // Avanzar al siguiente paso
        $nextStep = $step['next_step'] ?? null;
        if ($nextStep) {
            $context = $conversation->flow_context ?? [];
            $context['current_step'] = $nextStep;
            $conversation->updateFlowContext($context);
        }
    }

    /**
     * Procesar paso de entrada
     */
    protected function processInputStep($conversation, $step, $userInput)
    {
        $conditions = $step['conditions'] ?? [];
        $defaultNextStep = $step['default_next_step'] ?? null;
        
        foreach ($conditions as $condition) {
            if ($condition['input'] === $userInput) {
                if (isset($condition['response'])) {
                    $this->whatsappService->sendTextMessage(
                        $conversation->contact->phone_number,
                        $condition['response']
                    );
                    
                    $this->saveOutgoingMessage($conversation, $condition['response'], 'text', true);
                }
                
                if (isset($condition['next_step'])) {
                    $context = $conversation->flow_context ?? [];
                    $context['current_step'] = $condition['next_step'];
                    $conversation->updateFlowContext($context);
                }
                
                return;
            }
        }
        
        // Si no se encontró condición, usar respuesta por defecto
        if ($defaultNextStep === 'not_understood') {
            $this->sendNotUnderstoodResponse($conversation);
        } elseif ($defaultNextStep === 'main_menu') {
            $this->sendMainMenuResponse($conversation);
        }
    }

    /**
     * Enviar respuesta por defecto
     */
    protected function sendDefaultResponse($conversation)
    {
        $response = ChatbotResponse::where('key', 'not_understood')
                                 ->where('language', $conversation->language)
                                 ->where('is_active', true)
                                 ->first();
        
        if ($response) {
            $this->sendResponse($conversation, $response);
        }
    }

    /**
     * Enviar respuesta de no entendido
     */
    protected function sendNotUnderstoodResponse($conversation)
    {
        $this->sendDefaultResponse($conversation);
    }

    /**
     * Enviar menú principal
     */
    protected function sendMainMenuResponse($conversation)
    {
        $response = ChatbotResponse::where('key', 'main_menu')
                                 ->where('language', $conversation->language)
                                 ->where('is_active', true)
                                 ->first();
        
        if ($response) {
            $this->sendResponse($conversation, $response);
        }
    }

    /**
     * Enviar respuesta
     */
    protected function sendResponse($conversation, $response)
    {
        $messageData = $response->message_data;
        
        if ($messageData && isset($messageData['type']) && $messageData['type'] === 'interactive') {
            $this->whatsappService->sendInteractiveMessage(
                $conversation->contact->phone_number,
                $response->message_text,
                $messageData['buttons'] ?? []
            );
        } else {
            $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $response->message_text
            );
        }
        
        $this->saveOutgoingMessage($conversation, $response->message_text, 'text', true);
    }

    /**
     * Guardar mensaje saliente
     */
    protected function saveOutgoingMessage($conversation, $content, $type = 'text', $isAutomated = false)
    {
        Message::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => $type,
            'content' => $content,
            'is_automated' => $isAutomated,
            'status' => 'sent'
        ]);
        
        $conversation->incrementMessageCount();
    }

    /**
     * Procesar con IA
     */
    protected function processWithAI($conversation, $content)
    {
        // Analizar intención
        $intent = $this->aiService->analyzeIntent($content);

        // Extraer entidades
        $entities = $this->aiService->extractEntities($content);

        // Construir contexto para la IA
        $context = [
            'company_info' => 'Empresa de tecnología que ofrece software, consultoría y soporte técnico.',
            'conversation_history' => $this->getConversationHistory($conversation, 5)
        ];

        // Generar respuesta con IA
        $aiResponse = $this->aiService->generateResponse($content, $context);

        if ($aiResponse) {
            $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $aiResponse
            );

            $this->saveOutgoingMessage($conversation, $aiResponse, 'text', true);

            // Registrar métricas de IA
            ConversationAnalytic::recordMetric(
                $conversation->id,
                'ai_response',
                'intent',
                1,
                'count',
                ['intent' => $intent, 'entities' => $entities]
            );
        } else {
            $this->sendDefaultResponse($conversation);
        }
    }

    /**
     * Obtener historial de conversación
     */
    protected function getConversationHistory($conversation, $limit = 5)
    {
        $messages = $conversation->messages()
                                ->orderBy('created_at', 'desc')
                                ->limit($limit)
                                ->get()
                                ->reverse();

        $history = '';
        foreach ($messages as $message) {
            $sender = $message->isInbound() ? 'Cliente' : 'Bot';
            $history .= "{$sender}: {$message->content}\n";
        }

        return $history;
    }

    /**
     * Transferir a agente humano
     */
    public function transferToAgent($conversation, $reason = null)
    {
        // Buscar agente disponible
        $agent = User::where('role', 'agent')
                    ->where('is_active', true)
                    ->whereDoesntHave('assignedConversations', function($query) {
                        $query->where('status', 'active');
                    })
                    ->first();

        if ($agent) {
            $conversation->assignTo($agent);

            $transferMessage = "Te he conectado con {$agent->name}, uno de nuestros agentes. En un momento te atenderá.";

            $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $transferMessage
            );

            $this->saveOutgoingMessage($conversation, $transferMessage, 'text', true);

            // Notificar al agente (aquí podrías implementar notificaciones push, email, etc.)
            Log::info('Conversation transferred to agent', [
                'conversation_id' => $conversation->id,
                'agent_id' => $agent->id,
                'reason' => $reason
            ]);

            return true;
        } else {
            $noAgentMessage = "En este momento todos nuestros agentes están ocupados. Te contactaremos lo antes posible.";

            $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $noAgentMessage
            );

            $this->saveOutgoingMessage($conversation, $noAgentMessage, 'text', true);

            $conversation->update(['status' => 'waiting']);

            return false;
        }
    }

    /**
     * Actualizar estado del mensaje
     */
    public function updateMessageStatus($whatsappMessageId, $status, $timestamp)
    {
        $message = Message::where('whatsapp_message_id', $whatsappMessageId)->first();

        if ($message) {
            $message->update(['status' => $status]);
        }
    }
}
