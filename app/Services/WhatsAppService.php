<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $apiUrl;
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('whatsapp.api_url');
        $this->accessToken = config('whatsapp.access_token');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
    }

    /**
     * Enviar mensaje de texto
     */
    public function sendTextMessage($to, $message)
    {
        return $this->sendMessageBase($to, [
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ]);
    }

    /**
     * Enviar mensaje interactivo con botones
     */
    public function sendInteractiveMessage($to, $text, $buttons)
    {
        $interactive = [
            'type' => 'button',
            'body' => [
                'text' => $text
            ],
            'action' => [
                'buttons' => []
            ]
        ];

        foreach ($buttons as $button) {
            $interactive['action']['buttons'][] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $button['id'],
                    'title' => $button['title']
                ]
            ];
        }

        return $this->sendMessageBase($to, [
            'type' => 'interactive',
            'interactive' => $interactive
        ]);
    }

    /**
     * Enviar mensaje con lista
     */
    public function sendListMessage($to, $text, $buttonText, $sections)
    {
        $interactive = [
            'type' => 'list',
            'body' => [
                'text' => $text
            ],
            'action' => [
                'button' => $buttonText,
                'sections' => $sections
            ]
        ];

        return $this->sendMessageBase($to, [
            'type' => 'interactive',
            'interactive' => $interactive
        ]);
    }

    /**
     * Enviar mensaje con template
     */
    public function sendTemplateMessage($to, $templateName, $language = 'es', $parameters = [])
    {
        $template = [
            'name' => $templateName,
            'language' => [
                'code' => $language
            ]
        ];

        if (!empty($parameters)) {
            $template['components'] = [
                [
                    'type' => 'body',
                    'parameters' => $parameters
                ]
            ];
        }

        return $this->sendMessageBase($to, [
            'type' => 'template',
            'template' => $template
        ]);
    }

    /**
     * Enviar mensaje flexible (método mejorado para el controlador)
     */
    public function sendMessage($to, $content, $type = 'text', $mediaUrl = null)
    {
        switch ($type) {
            case 'text':
                return $this->sendTextMessage($to, $content);
            
            case 'image':
                return $this->sendMediaMessage($to, $mediaUrl, 'image', $content);
            
            case 'document':
                return $this->sendMediaMessage($to, $mediaUrl, 'document', $content);
            
            case 'audio':
                return $this->sendMediaMessage($to, $mediaUrl, 'audio');
            
            case 'video':
                return $this->sendMediaMessage($to, $mediaUrl, 'video', $content);
            
            default:
                return $this->sendTextMessage($to, $content);
        }
    }

    /**
     * Enviar mensaje con media
     */
    public function sendMediaMessage($to, $mediaUrl, $mediaType, $caption = null)
    {
        $messageData = [
            'type' => $mediaType,
            $mediaType => [
                'link' => $mediaUrl
            ]
        ];

        if ($caption && in_array($mediaType, ['image', 'video', 'document'])) {
            $messageData[$mediaType]['caption'] = $caption;
        }

        return $this->sendMessageBase($to, $messageData);
    }

    /**
     * Enviar mensaje base (método original renombrado)
     */
    protected function sendMessageBase($to, $messageData)
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'recipient_type' => 'individual'
            ];

            $payload = array_merge($payload, $messageData);

            $response = $this->client->post(
                "{$this->apiUrl}/{$this->phoneNumberId}/messages",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload
                ]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            
            Log::info('WhatsApp message sent successfully', [
                'to' => $to,
                'message_id' => $result['messages'][0]['id'] ?? null,
                'payload' => $payload
            ]);

            return [
                'success' => true,
                'message_id' => $result['messages'][0]['id'] ?? null,
                'data' => $result
            ];

        } catch (RequestException $e) {
            $error = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            
            Log::error('WhatsApp API error', [
                'to' => $to,
                'error' => $error,
                'payload' => $payload ?? null
            ]);

            return [
                'success' => false,
                'error' => $error
            ];
        }
    }

    /**
     * Marcar mensaje como leído
     */
    public function markAsRead($messageId)
    {
        try {
            $response = $this->client->post(
                "{$this->apiUrl}/{$this->phoneNumberId}/messages",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'messaging_product' => 'whatsapp',
                        'status' => 'read',
                        'message_id' => $messageId
                    ]
                ]
            );

            return ['success' => true];

        } catch (RequestException $e) {
            Log::error('Error marking message as read', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener información del perfil del contacto
     */
    public function getContactProfile($phoneNumber)
    {
        try {
            $response = $this->client->get(
                "{$this->apiUrl}/{$phoneNumber}",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                    ],
                    'query' => [
                        'fields' => 'name,profile_pic'
                    ]
                ]
            );

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            Log::error('Error getting contact profile', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Verificar webhook
     */
    public function verifyWebhook($mode, $token, $challenge)
    {
        $verifyToken = config('whatsapp.webhook.verify_token');
        
        if ($mode === 'subscribe' && $token === $verifyToken) {
            return $challenge;
        }
        
        return false;
    }

    /**
     * Verificar conexión con la API de WhatsApp
     */
    public function verifyConnection()
    {
        try {
            // Intentar obtener información del número de teléfono
            $response = $this->client->get(
                "{$this->apiUrl}/{$this->phoneNumberId}",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                    ],
                    'query' => [
                        'fields' => 'verified_name,code_verification_status,quality_rating'
                    ]
                ]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            
            Log::info('WhatsApp connection verified', ['phone_data' => $result]);

            return [
                'success' => true,
                'data' => $result,
                'phone_verified' => $result['code_verification_status'] ?? 'unknown',
                'quality_rating' => $result['quality_rating'] ?? 'unknown'
            ];

        } catch (RequestException $e) {
            $error = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            
            Log::error('WhatsApp connection verification failed', [
                'error' => $error,
                'phone_number_id' => $this->phoneNumberId
            ]);

            return [
                'success' => false,
                'error' => $error
            ];
        }
    }

    /**
     * Obtener estadísticas de uso de la API
     */
    public function getApiUsageStats()
    {
        try {
            $response = $this->client->get(
                "{$this->apiUrl}/{config('whatsapp.business_account_id')}/phone_numbers",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                    ]
                ]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            
            return [
                'success' => true,
                'data' => $result
            ];

        } catch (RequestException $e) {
            Log::error('Error getting API usage stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
