<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flows = [
            [
                'name' => 'Flujo de Bienvenida',
                'description' => 'Flujo inicial que se ejecuta cuando un usuario inicia una conversación',
                'trigger_type' => 'welcome',
                'trigger_conditions' => json_encode([
                    'is_new_conversation' => true
                ]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'response_key' => 'welcome',
                        'next_step' => 2
                    ],
                    [
                        'step' => 2,
                        'type' => 'wait_input',
                        'timeout' => 300,
                        'next_step' => 3
                    ],
                    [
                        'step' => 3,
                        'type' => 'process_input',
                        'conditions' => [
                            ['input' => '1', 'next_step' => 'products_flow'],
                            ['input' => '2', 'next_step' => 'support_flow'],
                            ['input' => '3', 'next_step' => 'agent_transfer']
                        ],
                        'default_next_step' => 'not_understood'
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 100,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Información de Productos',
                'description' => 'Flujo para mostrar información sobre productos y servicios',
                'trigger_type' => 'menu_option',
                'trigger_conditions' => json_encode([
                    'menu_option' => '1',
                    'keywords' => ['productos', 'producto', 'catálogo', 'servicios']
                ]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'content' => '📦 *Nuestros productos principales:*\n\n1️⃣ Software de gestión\n2️⃣ Consultoría IT\n3️⃣ Soporte técnico\n4️⃣ Desarrollo web\n\n¿Sobre cuál te gustaría saber más?',
                        'next_step' => 2
                    ],
                    [
                        'step' => 2,
                        'type' => 'wait_input',
                        'timeout' => 300,
                        'next_step' => 3
                    ],
                    [
                        'step' => 3,
                        'type' => 'process_input',
                        'conditions' => [
                            ['input' => '1', 'response' => 'Nuestro software de gestión incluye CRM, ERP y herramientas de automatización.'],
                            ['input' => '2', 'response' => 'Ofrecemos consultoría especializada en transformación digital.'],
                            ['input' => '3', 'response' => 'Brindamos soporte técnico 24/7 para todos nuestros clientes.'],
                            ['input' => '4', 'response' => 'Desarrollamos sitios web y aplicaciones personalizadas.']
                        ],
                        'next_step' => 'main_menu'
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 50,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('chatbot_flows')->insert($flows);
    }
}
