<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $responses = [
            [
                'key' => 'welcome',
                'category' => 'greeting',
                'message_text' => '¡Hola! 👋 Bienvenido a nuestro servicio de atención al cliente. ¿En qué puedo ayudarte hoy?',
                'message_data' => json_encode([
                    'type' => 'interactive',
                    'buttons' => [
                        ['id' => '1', 'title' => 'Información de productos'],
                        ['id' => '2', 'title' => 'Soporte técnico'],
                        ['id' => '3', 'title' => 'Hablar con agente']
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'main_menu',
                'category' => 'menu',
                'message_text' => 'Selecciona una opción del menú principal:',
                'message_data' => json_encode([
                    'type' => 'interactive',
                    'buttons' => [
                        ['id' => '1', 'title' => '📦 Productos'],
                        ['id' => '2', 'title' => '🔧 Soporte'],
                        ['id' => '3', 'title' => '💰 Ventas'],
                        ['id' => '4', 'title' => '🕒 Horarios'],
                        ['id' => '5', 'title' => '👤 Agente']
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'goodbye',
                'category' => 'farewell',
                'message_text' => '¡Gracias por contactarnos! Que tengas un excelente día. 😊',
                'message_data' => null,
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'not_understood',
                'category' => 'error',
                'message_text' => 'Lo siento, no entendí tu mensaje. ¿Podrías reformularlo o elegir una de las opciones del menú?',
                'message_data' => null,
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'business_hours',
                'category' => 'information',
                'message_text' => '🕒 *Nuestros horarios de atención son:*\n\n📅 Lunes a Viernes: 9:00 AM - 6:00 PM\n📅 Sábados: 9:00 AM - 2:00 PM\n📅 Domingos: Cerrado\n\n🌎 Zona horaria: México (GMT-6)',
                'message_data' => null,
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'transfer_to_agent',
                'category' => 'transfer',
                'message_text' => 'Te estoy conectando con uno de nuestros agentes. Por favor espera un momento...',
                'message_data' => null,
                'language' => 'es',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('chatbot_responses')->insert($responses);
    }
}
