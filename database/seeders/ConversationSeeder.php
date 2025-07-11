<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\WhatsappContact;
use App\Models\User;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos contactos de prueba si no existen
        $contacts = [];

        if (WhatsappContact::count() == 0) {
            $contacts[] = WhatsappContact::create([
                'name' => 'María González',
                'phone_number' => '+5255512345567',
                'whatsapp_id' => 'wa_maria_gonzalez_001',
                'profile_name' => 'María González',
                'language' => 'es',
                'opt_in' => true,
                'last_interaction_at' => Carbon::now()->subMinutes(2),
                'notes' => 'Cliente frecuente',
            ]);

            $contacts[] = WhatsappContact::create([
                'name' => 'Carlos Rodríguez',
                'phone_number' => '+5255598765543',
                'whatsapp_id' => 'wa_carlos_rodriguez_002',
                'profile_name' => 'Carlos Rodríguez',
                'language' => 'es',
                'opt_in' => true,
                'last_interaction_at' => Carbon::now()->subMinutes(15),
                'notes' => 'Nuevo cliente',
            ]);

            $contacts[] = WhatsappContact::create([
                'name' => 'Ana López',
                'phone_number' => '+5255545678990',
                'whatsapp_id' => 'wa_ana_lopez_003',
                'profile_name' => 'Ana López',
                'language' => 'es',
                'opt_in' => true,
                'last_interaction_at' => Carbon::now()->subHours(1),
                'notes' => 'Cliente satisfecho',
            ]);

            $contacts[] = WhatsappContact::create([
                'name' => 'Luis Martínez',
                'phone_number' => '+5255532109987',
                'whatsapp_id' => 'wa_luis_martinez_004',
                'profile_name' => 'Luis Martínez',
                'language' => 'es',
                'opt_in' => true,
                'last_interaction_at' => Carbon::now()->subMinutes(30),
                'notes' => 'Requiere seguimiento',
            ]);
        } else {
            $contacts = WhatsappContact::limit(4)->get()->toArray();
        }

        // Obtener usuarios para asignar como agentes
        $users = User::all();
        $agent = $users->first();

        // Crear conversaciones de prueba
        $conversations = [
            [
                'contact_id' => $contacts[0]['id'] ?? 1,
                'assigned_user_id' => $agent?->id,
                'status' => 'active',
                'priority' => 'high',
                'type' => 'human',
                'language' => 'es',
                'message_count' => 5,
                'usage_count' => 1,
                'last_message_at' => Carbon::now()->subMinutes(2),
                'started_at' => Carbon::now()->subHours(2),
                'assigned_at' => Carbon::now()->subHours(1),
                'satisfaction_rating' => null,
                'notes' => 'Cliente con pedido urgente',
            ],
            [
                'contact_id' => $contacts[1]['id'] ?? 2,
                'assigned_user_id' => null,
                'status' => 'pending',
                'priority' => 'medium',
                'type' => 'chatbot',
                'language' => 'es',
                'message_count' => 2,
                'usage_count' => 1,
                'last_message_at' => Carbon::now()->subMinutes(15),
                'started_at' => Carbon::now()->subMinutes(20),
                'satisfaction_rating' => null,
                'notes' => null,
            ],
            [
                'contact_id' => $contacts[2]['id'] ?? 3,
                'assigned_user_id' => $agent?->id,
                'status' => 'closed',
                'priority' => 'low',
                'type' => 'mixed',
                'language' => 'es',
                'message_count' => 8,
                'usage_count' => 1,
                'last_message_at' => Carbon::now()->subHours(1),
                'started_at' => Carbon::now()->subHours(3),
                'closed_at' => Carbon::now()->subHours(1),
                'closed_by' => $agent?->id,
                'assigned_at' => Carbon::now()->subHours(2),
                'satisfaction_rating' => 5,
                'satisfaction_comment' => 'Excelente servicio',
                'notes' => 'Consulta resuelta satisfactoriamente',
            ],
            [
                'contact_id' => $contacts[3]['id'] ?? 4,
                'assigned_user_id' => $agent?->id,
                'status' => 'waiting',
                'priority' => 'urgent',
                'type' => 'human',
                'language' => 'es',
                'message_count' => 3,
                'usage_count' => 1,
                'last_message_at' => Carbon::now()->subMinutes(30),
                'started_at' => Carbon::now()->subHours(1),
                'assigned_at' => Carbon::now()->subMinutes(45),
                'satisfaction_rating' => null,
                'notes' => 'Esperando respuesta del cliente',
            ],
        ];

        foreach ($conversations as $conversationData) {
            $conversation = Conversation::create($conversationData);

            // Crear algunos mensajes de ejemplo para cada conversación
            $this->createSampleMessages($conversation);
        }
    }

    private function createSampleMessages(Conversation $conversation)
    {
        $messages = [];

        switch ($conversation->status) {
            case 'active':
                $messages = [
                    [
                        'content' => 'Hola, necesito ayuda urgente con mi pedido.',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->started_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Hola! Claro, te ayudo inmediatamente. ¿Cuál es tu número de pedido?',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'sender_id' => $conversation->assigned_user_id,
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(2),
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'El número es #12345',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(5),
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Perfecto, estoy revisando tu pedido ahora mismo.',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'sender_id' => $conversation->assigned_user_id,
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(7),
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Gracias, estaré esperando.',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->last_message_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                ];
                break;

            case 'pending':
                $messages = [
                    [
                        'content' => 'Hola, tengo una consulta sobre sus servicios.',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->started_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Hola! Gracias por contactarnos. Un agente te atenderá en breve.',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(1),
                        'status' => 'delivered',
                        'is_automated' => true,
                    ],
                ];
                break;

            case 'closed':
                $messages = [
                    [
                        'content' => '¿Podrían ayudarme con información sobre sus productos?',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->started_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Por supuesto! Te envío nuestro catálogo completo.',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'sender_id' => $conversation->assigned_user_id,
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(5),
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Perfecto, muchas gracias por la información.',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->last_message_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                ];
                break;

            case 'waiting':
                $messages = [
                    [
                        'content' => 'Necesito soporte técnico urgente.',
                        'type' => 'text',
                        'direction' => 'inbound',
                        'whatsapp_timestamp' => $conversation->started_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Entiendo la urgencia. ¿Podrías describir el problema?',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'sender_id' => $conversation->assigned_user_id,
                        'whatsapp_timestamp' => $conversation->started_at->addMinutes(10),
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                    [
                        'content' => 'Déjame revisar y te respondo en 5 minutos.',
                        'type' => 'text',
                        'direction' => 'outbound',
                        'sender_id' => $conversation->assigned_user_id,
                        'whatsapp_timestamp' => $conversation->last_message_at,
                        'status' => 'delivered',
                        'is_automated' => false,
                    ],
                ];
                break;
        }

        foreach ($messages as $messageData) {
            $messageData['conversation_id'] = $conversation->id;
            Message::create($messageData);
        }
    }
}
