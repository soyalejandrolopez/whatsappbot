<?php

namespace Tests\Feature;

use App\Models\WhatsappContact;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar variables de entorno para testing
        config(['whatsapp.webhook.verify_token' => 'test_verify_token']);
    }

    /**
     * Test webhook verification
     */
    public function test_webhook_verification_success(): void
    {
        $response = $this->get('/api/whatsapp/webhook', [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'test_verify_token',
            'hub_challenge' => 'test_challenge'
        ]);

        $response->assertStatus(200);
        $response->assertSeeText('test_challenge');
    }

    /**
     * Test webhook verification failure
     */
    public function test_webhook_verification_failure(): void
    {
        $response = $this->get('/api/whatsapp/webhook', [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'wrong_token',
            'hub_challenge' => 'test_challenge'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test incoming message webhook
     */
    public function test_incoming_message_webhook(): void
    {
        $webhookData = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'entry_id',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '1234567890',
                                    'phone_number_id' => 'phone_id'
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Test User'],
                                        'wa_id' => '521234567890'
                                    ]
                                ],
                                'messages' => [
                                    [
                                        'from' => '521234567890',
                                        'id' => 'message_id_123',
                                        'timestamp' => '1234567890',
                                        'text' => ['body' => 'Hola'],
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/whatsapp/webhook', $webhookData);

        $response->assertStatus(200);
        $response->assertSeeText('OK');

        // Verificar que se creó el contacto
        $this->assertDatabaseHas('whatsapp_contacts', [
            'phone_number' => '521234567890'
        ]);

        // Verificar que se creó la conversación
        $this->assertDatabaseHas('conversations', [
            'status' => 'active'
        ]);

        // Verificar que se guardó el mensaje
        $this->assertDatabaseHas('messages', [
            'whatsapp_message_id' => 'message_id_123',
            'content' => 'Hola',
            'direction' => 'inbound'
        ]);
    }

    /**
     * Test message status webhook
     */
    public function test_message_status_webhook(): void
    {
        // Crear mensaje existente
        $contact = WhatsappContact::factory()->create();
        $conversation = Conversation::factory()->create(['contact_id' => $contact->id]);
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'whatsapp_message_id' => 'test_message_id',
            'status' => 'sent'
        ]);

        $webhookData = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'entry_id',
                    'changes' => [
                        [
                            'field' => 'message_status',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '1234567890',
                                    'phone_number_id' => 'phone_id'
                                ],
                                'statuses' => [
                                    [
                                        'id' => 'test_message_id',
                                        'status' => 'delivered',
                                        'timestamp' => '1234567890',
                                        'recipient_id' => '521234567890'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/whatsapp/webhook', $webhookData);

        $response->assertStatus(200);

        // Verificar que se actualizó el estado del mensaje
        $message->refresh();
        $this->assertEquals('delivered', $message->status);
    }

    /**
     * Test invalid webhook structure
     */
    public function test_invalid_webhook_structure(): void
    {
        $invalidData = [
            'invalid' => 'structure'
        ];

        $response = $this->postJson('/api/whatsapp/webhook', $invalidData);

        $response->assertStatus(400);
    }

    /**
     * Test rate limiting
     */
    public function test_rate_limiting(): void
    {
        $webhookData = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'entry_id',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '1234567890',
                                    'phone_number_id' => 'phone_id'
                                ],
                                'messages' => [
                                    [
                                        'from' => '521234567890',
                                        'id' => 'message_id_' . time(),
                                        'timestamp' => '1234567890',
                                        'text' => ['body' => 'Test message'],
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Enviar múltiples requests rápidamente
        for ($i = 0; $i < 15; $i++) {
            $webhookData['entry'][0]['changes'][0]['value']['messages'][0]['id'] = 'message_id_' . $i;
            $response = $this->postJson('/api/whatsapp/webhook', $webhookData);

            if ($i < 10) {
                $response->assertStatus(200);
            } else {
                // Después de 10 requests, debería activarse el rate limiting
                $response->assertStatus(429);
                break;
            }
        }
    }
}
