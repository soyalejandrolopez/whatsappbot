<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $whatsappService;
    protected $chatbotService;

    public function __construct(WhatsAppService $whatsappService, ChatbotService $chatbotService)
    {
        $this->whatsappService = $whatsappService;
        $this->chatbotService = $chatbotService;
    }

    /**
     * Verificar webhook (GET)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $result = $this->whatsappService->verifyWebhook($mode, $token, $challenge);

        if ($result) {
            return response($result, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Recibir webhooks (POST)
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('WhatsApp webhook received', $data);

            // Verificar que es un mensaje de WhatsApp
            if (!isset($data['object']) || $data['object'] !== 'whatsapp_business_account') {
                return response('OK', 200);
            }

            // Procesar cada entrada
            foreach ($data['entry'] as $entry) {
                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'messages') {
                            $this->processMessage($change['value']);
                        } elseif ($change['field'] === 'message_status') {
                            $this->processMessageStatus($change['value']);
                        }
                    }
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Procesar mensaje recibido
     */
    protected function processMessage($data)
    {
        if (!isset($data['messages'])) {
            return;
        }

        foreach ($data['messages'] as $message) {
            // Ignorar mensajes enviados por nosotros
            if (isset($message['from']) && $message['from'] === config('whatsapp.phone_number_id')) {
                continue;
            }

            $phoneNumber = $message['from'];
            $messageId = $message['id'];
            $timestamp = $message['timestamp'];

            // Marcar mensaje como leído
            $this->whatsappService->markAsRead($messageId);

            // Procesar el mensaje con el chatbot
            $this->chatbotService->processIncomingMessage([
                'phone_number' => $phoneNumber,
                'whatsapp_message_id' => $messageId,
                'timestamp' => $timestamp,
                'type' => $message['type'],
                'content' => $this->extractMessageContent($message),
                'metadata' => $data['metadata'] ?? []
            ]);
        }
    }

    /**
     * Procesar estado de mensaje
     */
    protected function processMessageStatus($data)
    {
        if (!isset($data['statuses'])) {
            return;
        }

        foreach ($data['statuses'] as $status) {
            $messageId = $status['id'];
            $statusValue = $status['status'];
            $timestamp = $status['timestamp'];

            // Actualizar estado del mensaje en la base de datos
            $this->chatbotService->updateMessageStatus($messageId, $statusValue, $timestamp);
        }
    }

    /**
     * Extraer contenido del mensaje según su tipo
     */
    protected function extractMessageContent($message)
    {
        switch ($message['type']) {
            case 'text':
                return $message['text']['body'];

            case 'image':
                return [
                    'media_id' => $message['image']['id'],
                    'mime_type' => $message['image']['mime_type'],
                    'caption' => $message['image']['caption'] ?? null
                ];

            case 'document':
                return [
                    'media_id' => $message['document']['id'],
                    'mime_type' => $message['document']['mime_type'],
                    'filename' => $message['document']['filename'] ?? null,
                    'caption' => $message['document']['caption'] ?? null
                ];

            case 'audio':
                return [
                    'media_id' => $message['audio']['id'],
                    'mime_type' => $message['audio']['mime_type']
                ];

            case 'video':
                return [
                    'media_id' => $message['video']['id'],
                    'mime_type' => $message['video']['mime_type'],
                    'caption' => $message['video']['caption'] ?? null
                ];

            case 'location':
                return [
                    'latitude' => $message['location']['latitude'],
                    'longitude' => $message['location']['longitude'],
                    'name' => $message['location']['name'] ?? null,
                    'address' => $message['location']['address'] ?? null
                ];

            case 'interactive':
                if (isset($message['interactive']['button_reply'])) {
                    return $message['interactive']['button_reply']['id'];
                } elseif (isset($message['interactive']['list_reply'])) {
                    return $message['interactive']['list_reply']['id'];
                }
                return null;

            default:
                return null;
        }
    }
}
