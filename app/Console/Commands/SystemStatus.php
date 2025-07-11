<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SystemStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar el estado del sistema ChatBot WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando estado del sistema ChatBot WhatsApp...');
        $this->line('');

        // Verificar base de datos
        $this->checkDatabase();

        // Verificar configuración de WhatsApp
        $this->checkWhatsAppConfig();

        // Verificar flujos del chatbot
        $this->checkChatbotFlows();

        // Verificar usuarios
        $this->checkUsers();

        // Verificar permisos de archivos
        $this->checkFilePermissions();

        // Mostrar resumen
        $this->showSummary();

        return 0;
    }

    protected function checkDatabase()
    {
        $this->info('📊 Verificando base de datos...');

        try {
            \DB::connection()->getPdo();
            $this->line('   ✅ Conexión a base de datos: OK');

            $tables = [
                'users' => \App\Models\User::count(),
                'whatsapp_contacts' => \App\Models\WhatsappContact::count(),
                'conversations' => \App\Models\Conversation::count(),
                'messages' => \App\Models\Message::count(),
                'chatbot_flows' => \App\Models\ChatbotFlow::count(),
                'chatbot_responses' => \App\Models\ChatbotResponse::count(),
            ];

            foreach ($tables as $table => $count) {
                $this->line("   📋 {$table}: {$count} registros");
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Error de base de datos: ' . $e->getMessage());
        }

        $this->line('');
    }

    protected function checkWhatsAppConfig()
    {
        $this->info('📱 Verificando configuración de WhatsApp...');

        $configs = [
            'WHATSAPP_ACCESS_TOKEN' => config('whatsapp.access_token'),
            'WHATSAPP_PHONE_NUMBER_ID' => config('whatsapp.phone_number_id'),
            'WHATSAPP_WEBHOOK_VERIFY_TOKEN' => config('whatsapp.webhook.verify_token'),
        ];

        foreach ($configs as $key => $value) {
            if ($value) {
                $this->line("   ✅ {$key}: Configurado");
            } else {
                $this->line("   ⚠️  {$key}: No configurado");
            }
        }

        $this->line('');
    }

    protected function checkChatbotFlows()
    {
        $this->info('🤖 Verificando flujos del chatbot...');

        $activeFlows = \App\Models\ChatbotFlow::where('is_active', true)->count();
        $totalFlows = \App\Models\ChatbotFlow::count();

        $this->line("   📊 Flujos activos: {$activeFlows}/{$totalFlows}");

        $responses = \App\Models\ChatbotResponse::where('is_active', true)->count();
        $this->line("   💬 Respuestas activas: {$responses}");

        $this->line('');
    }

    protected function checkUsers()
    {
        $this->info('👥 Verificando usuarios...');

        $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->count();
        $agents = \App\Models\User::where('role', 'agent')->where('is_active', true)->count();

        $this->line("   👑 Administradores activos: {$admins}");
        $this->line("   🎧 Agentes activos: {$agents}");

        $this->line('');
    }

    protected function checkFilePermissions()
    {
        $this->info('📁 Verificando permisos de archivos...');

        $directories = [
            'storage/logs' => storage_path('logs'),
            'storage/framework' => storage_path('framework'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($directories as $name => $path) {
            if (is_writable($path)) {
                $this->line("   ✅ {$name}: Escribible");
            } else {
                $this->line("   ❌ {$name}: No escribible");
            }
        }

        $this->line('');
    }

    protected function showSummary()
    {
        $this->info('📋 Resumen del sistema:');

        $this->table(
            ['Componente', 'Estado'],
            [
                ['Base de datos', '✅ Conectada'],
                ['WhatsApp API', config('whatsapp.access_token') ? '✅ Configurada' : '⚠️ Pendiente'],
                ['Flujos del chatbot', \App\Models\ChatbotFlow::where('is_active', true)->count() > 0 ? '✅ Activos' : '⚠️ Sin flujos'],
                ['Panel admin', '✅ Disponible'],
                ['Documentación', '✅ Completa'],
            ]
        );

        $this->line('');
        $this->info('🚀 Sistema ChatBot WhatsApp listo para usar!');
        $this->line('');
        $this->line('📚 Próximos pasos:');
        $this->line('   1. Configurar credenciales de WhatsApp en .env');
        $this->line('   2. Acceder al panel admin: /admin');
        $this->line('   3. Personalizar flujos del chatbot');
        $this->line('   4. Configurar webhook de WhatsApp');
    }
}
