<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeChatbotFlows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:init-flows {--force : Sobrescribir flujos existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar flujos predeterminados del chatbot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Inicializando flujos del chatbot...');

        $force = $this->option('force');

        // Verificar si ya existen flujos
        if (!$force && \App\Models\ChatbotFlow::count() > 0) {
            if (!$this->confirm('Ya existen flujos configurados. ¿Deseas continuar?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        try {
            $this->createWelcomeFlow();
            $this->createProductsFlow();
            $this->createSupportFlow();
            $this->createSalesFlow();

            $this->info('✅ Flujos inicializados correctamente');
            $this->table(
                ['Flujo', 'Estado', 'Prioridad'],
                [
                    ['Bienvenida', 'Activo', '100'],
                    ['Productos', 'Activo', '70'],
                    ['Soporte', 'Activo', '80'],
                    ['Ventas', 'Activo', '70']
                ]
            );

        } catch (\Exception $e) {
            $this->error("Error inicializando flujos: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    protected function createWelcomeFlow()
    {
        \App\Models\ChatbotFlow::updateOrCreate(
            ['name' => 'Flujo de Bienvenida'],
            [
                'description' => 'Flujo principal de bienvenida para nuevos usuarios',
                'trigger_type' => 'welcome',
                'trigger_conditions' => json_encode(['is_new_conversation' => true]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'response_key' => 'welcome',
                        'next_step' => 2
                    ],
                    [
                        'step' => 2,
                        'type' => 'interactive_menu',
                        'content' => '¿En qué puedo ayudarte hoy?',
                        'options' => [
                            ['id' => 'productos', 'title' => '📦 Ver productos'],
                            ['id' => 'soporte', 'title' => '🔧 Soporte técnico'],
                            ['id' => 'ventas', 'title' => '💰 Información de ventas'],
                            ['id' => 'agente', 'title' => '👤 Hablar con agente']
                        ],
                        'next_step' => 3
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 100,
                'created_by' => 1
            ]
        );
    }

    protected function createProductsFlow()
    {
        \App\Models\ChatbotFlow::updateOrCreate(
            ['name' => 'Información de Productos'],
            [
                'description' => 'Flujo para mostrar información de productos y servicios',
                'trigger_type' => 'menu_option',
                'trigger_conditions' => json_encode([
                    'menu_option' => 'productos',
                    'keywords' => ['productos', 'producto', 'catálogo', 'servicios']
                ]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'content' => '📦 *Nuestros Productos y Servicios*\n\nTenemos soluciones completas para empresas de todos los tamaños.',
                        'next_step' => 2
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 70,
                'created_by' => 1
            ]
        );
    }

    protected function createSupportFlow()
    {
        \App\Models\ChatbotFlow::updateOrCreate(
            ['name' => 'Soporte Técnico'],
            [
                'description' => 'Flujo para atención de problemas técnicos',
                'trigger_type' => 'menu_option',
                'trigger_conditions' => json_encode([
                    'menu_option' => 'soporte',
                    'keywords' => ['problema', 'error', 'ayuda', 'soporte', 'técnico']
                ]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'content' => '🔧 *Soporte Técnico*\n\nEstoy aquí para ayudarte con cualquier problema técnico.',
                        'next_step' => 2
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 80,
                'created_by' => 1
            ]
        );
    }

    protected function createSalesFlow()
    {
        \App\Models\ChatbotFlow::updateOrCreate(
            ['name' => 'Información de Ventas'],
            [
                'description' => 'Flujo para consultas de ventas y cotizaciones',
                'trigger_type' => 'menu_option',
                'trigger_conditions' => json_encode([
                    'menu_option' => 'ventas',
                    'keywords' => ['precio', 'costo', 'comprar', 'cotización', 'venta']
                ]),
                'flow_steps' => json_encode([
                    [
                        'step' => 1,
                        'type' => 'message',
                        'content' => '💰 *Información de Ventas*\n\n¡Perfecto! Estás interesado en nuestros productos.',
                        'next_step' => 2
                    ]
                ]),
                'language' => 'es',
                'is_active' => true,
                'priority' => 70,
                'created_by' => 1
            ]
        );
    }
}
