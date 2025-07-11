<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateWhatsAppWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo validar para requests POST (webhooks)
        if ($request->isMethod('POST')) {
            if (!$this->validateWebhookSignature($request)) {
                Log::warning('Invalid WhatsApp webhook signature', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'headers' => $request->headers->all()
                ]);

                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!$this->validateWebhookStructure($request)) {
                Log::warning('Invalid WhatsApp webhook structure', [
                    'ip' => $request->ip(),
                    'data' => $request->all()
                ]);

                return response()->json(['error' => 'Invalid webhook structure'], 400);
            }
        }

        return $next($request);
    }

    /**
     * Validate webhook signature (if configured)
     */
    protected function validateWebhookSignature(Request $request): bool
    {
        $appSecret = config('whatsapp.app_secret');

        // Si no hay app secret configurado, omitir validación de firma
        if (!$appSecret) {
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Validate webhook structure
     */
    protected function validateWebhookStructure(Request $request): bool
    {
        $data = $request->all();

        // Validar estructura básica del webhook
        if (!isset($data['object'])) {
            return false;
        }

        // Debe ser un webhook de WhatsApp Business
        if ($data['object'] !== 'whatsapp_business_account') {
            return false;
        }

        // Debe tener entradas
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return false;
        }

        // Validar cada entrada
        foreach ($data['entry'] as $entry) {
            if (!isset($entry['id']) || !isset($entry['changes'])) {
                return false;
            }

            // Validar cambios
            foreach ($entry['changes'] as $change) {
                if (!isset($change['field']) || !isset($change['value'])) {
                    return false;
                }

                // Validar campos específicos según el tipo
                if ($change['field'] === 'messages') {
                    if (!$this->validateMessageChange($change['value'])) {
                        return false;
                    }
                } elseif ($change['field'] === 'message_status') {
                    if (!$this->validateStatusChange($change['value'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Validate message change structure
     */
    protected function validateMessageChange(array $value): bool
    {
        // Debe tener metadata
        if (!isset($value['metadata'])) {
            return false;
        }

        // Si hay mensajes, validar estructura
        if (isset($value['messages'])) {
            foreach ($value['messages'] as $message) {
                if (!isset($message['id']) || !isset($message['from']) || !isset($message['timestamp'])) {
                    return false;
                }

                // Validar tipo de mensaje
                if (!isset($message['type']) || !in_array($message['type'], [
                    'text', 'image', 'document', 'audio', 'video', 'location', 'contacts', 'interactive'
                ])) {
                    return false;
                }
            }
        }

        // Si hay contactos, validar estructura
        if (isset($value['contacts'])) {
            foreach ($value['contacts'] as $contact) {
                if (!isset($contact['wa_id'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate status change structure
     */
    protected function validateStatusChange(array $value): bool
    {
        // Debe tener metadata
        if (!isset($value['metadata'])) {
            return false;
        }

        // Si hay estados, validar estructura
        if (isset($value['statuses'])) {
            foreach ($value['statuses'] as $status) {
                if (!isset($status['id']) || !isset($status['status']) || !isset($status['timestamp'])) {
                    return false;
                }

                // Validar estado válido
                if (!in_array($status['status'], ['sent', 'delivered', 'read', 'failed'])) {
                    return false;
                }
            }
        }

        return true;
    }
}
