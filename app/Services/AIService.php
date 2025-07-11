<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $client;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('chatbot.ai.openai.api_key');
        $this->model = config('chatbot.ai.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Generar respuesta usando IA
     */
    public function generateResponse($message, $context = [])
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($context);
            
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => config('chatbot.ai.openai.max_tokens', 150),
                    'temperature' => config('chatbot.ai.openai.temperature', 0.7),
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['choices'][0]['message']['content'])) {
                return trim($result['choices'][0]['message']['content']);
            }

            return null;

        } catch (RequestException $e) {
            Log::error('OpenAI API error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            return null;
        }
    }

    /**
     * Analizar intención del mensaje
     */
    public function analyzeIntent($message)
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $prompt = "Analiza la siguiente consulta de un cliente y clasifica la intención en una de estas categorías: 
            - productos: preguntas sobre productos o servicios
            - soporte: problemas técnicos o solicitudes de ayuda
            - ventas: consultas sobre precios, cotizaciones o compras
            - informacion: solicitudes de información general
            - saludo: saludos o inicio de conversación
            - despedida: despedidas o fin de conversación
            - agente: solicitud para hablar con un humano
            - otro: cualquier otra cosa

            Responde solo con la categoría, sin explicaciones.

            Consulta: {$message}";

            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 10,
                    'temperature' => 0.1,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['choices'][0]['message']['content'])) {
                return strtolower(trim($result['choices'][0]['message']['content']));
            }

            return null;

        } catch (RequestException $e) {
            Log::error('Intent analysis error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            return null;
        }
    }

    /**
     * Extraer entidades del mensaje
     */
    public function extractEntities($message)
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $prompt = "Extrae las siguientes entidades del mensaje del cliente:
            - nombres de productos
            - números de teléfono
            - correos electrónicos
            - fechas
            - cantidades/números
            - ubicaciones

            Responde en formato JSON con las entidades encontradas.

            Mensaje: {$message}";

            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.1,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['choices'][0]['message']['content'])) {
                $content = trim($result['choices'][0]['message']['content']);
                return json_decode($content, true) ?? [];
            }

            return [];

        } catch (RequestException $e) {
            Log::error('Entity extraction error', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);

            return [];
        }
    }

    /**
     * Construir prompt del sistema
     */
    protected function buildSystemPrompt($context = [])
    {
        $basePrompt = "Eres un asistente virtual de atención al cliente para una empresa. 
        Responde siempre en español de manera amigable y profesional. 
        Mantén las respuestas concisas y útiles.
        Si no puedes ayudar con algo específico, ofrece transferir la conversación a un agente humano.";

        if (isset($context['company_info'])) {
            $basePrompt .= "\n\nInformación de la empresa: " . $context['company_info'];
        }

        if (isset($context['conversation_history'])) {
            $basePrompt .= "\n\nContexto de la conversación: " . $context['conversation_history'];
        }

        return $basePrompt;
    }

    /**
     * Verificar si la IA está disponible
     */
    public function isAvailable()
    {
        return !empty($this->apiKey) && config('chatbot.enable_ai', false);
    }
}
