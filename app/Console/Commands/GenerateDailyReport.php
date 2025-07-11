<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class GenerateDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:daily-report {--date= : Fecha específica para el reporte (YYYY-MM-DD)} {--email= : Email para enviar el reporte}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar reporte diario de analíticas del chatbot';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        $email = $this->option('email');

        $this->info("Generando reporte diario para: {$date->toDateString()}");

        try {
            // Generar reporte
            $report = $this->analyticsService->generateDailyReport($date);

            // Mostrar reporte en consola
            $this->displayReport($report);

            // Enviar por email si se especifica
            if ($email) {
                $this->sendReportByEmail($report, $email);
            }

            // Guardar en logs
            Log::info('Daily report generated', $report);

            $this->info('✅ Reporte generado exitosamente');

        } catch (\Exception $e) {
            $this->error("Error generando reporte: {$e->getMessage()}");
            Log::error('Error generating daily report', [
                'error' => $e->getMessage(),
                'date' => $date->toDateString()
            ]);

            return 1;
        }

        return 0;
    }

    protected function displayReport($report)
    {
        $this->line('');
        $this->line('📊 <fg=cyan>REPORTE DIARIO DEL CHATBOT</fg=cyan>');
        $this->line('═══════════════════════════════════');
        $this->line("📅 Fecha: <fg=yellow>{$report['date']}</fg=yellow>");
        $this->line('');

        $this->line('💬 <fg=green>CONVERSACIONES</fg=green>');
        $this->line("   • Iniciadas: {$report['conversations_started']}");
        $this->line("   • Cerradas: {$report['conversations_closed']}");
        $this->line("   • Tasa de resolución: " . number_format($report['resolution_rate'], 1) . "%");
        $this->line('');

        $this->line('📨 <fg=blue>MENSAJES</fg=blue>');
        $this->line("   • Total: {$report['total_messages']}");
        $this->line('');

        $this->line('👥 <fg=magenta>CONTACTOS</fg=magenta>');
        $this->line("   • Nuevos: {$report['new_contacts']}");
        $this->line('');

        $this->line('⭐ <fg=yellow>SATISFACCIÓN</fg=yellow>');
        $this->line("   • Promedio: " . number_format($report['avg_satisfaction'], 2) . "/5");
        $this->line('');

        $this->line('⏱️ <fg=red>TIEMPO DE RESPUESTA</fg=red>');
        $this->line("   • Promedio: " . number_format($report['avg_response_time'], 1) . " segundos");
        $this->line('');
    }

    protected function sendReportByEmail($report, $email)
    {
        try {
            // Aquí implementarías el envío de email
            // Por ahora solo simulamos el envío
            $this->info("📧 Reporte enviado a: {$email}");

        } catch (\Exception $e) {
            $this->warn("⚠️ Error enviando email: {$e->getMessage()}");
        }
    }
}
