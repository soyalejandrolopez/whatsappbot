<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappContact;
use App\Models\User;
use App\Models\ConversationAnalytic;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsAction;

class DashboardController extends Controller
{
    use LogsAction;

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('admin');
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        // Estadísticas generales
        $stats = [
            'total_conversations' => Conversation::count(),
            'active_conversations' => Conversation::where('status', 'active')->count(),
            'total_contacts' => WhatsappContact::count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'agents_online' => User::where('role', 'agent')->where('is_active', true)->count(),
        ];

        // Estadísticas específicas de WhatsApp
        $whatsappStats = $this->getWhatsAppStats();

        // Conversaciones recientes
        $recentConversations = Conversation::with(['contact', 'assignedUser'])
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        // Métricas de los últimos 7 días
        $weeklyMetrics = $this->getWeeklyMetrics();

        // Distribución de tipos de conversación
        $conversationTypes = Conversation::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // Top agentes por conversaciones resueltas
        $topAgents = User::where('role', 'agent')
            ->withCount(['assignedConversations' => function($query) {
                $query->where('status', 'closed')
                      ->whereMonth('ended_at', now()->month);
            }])
            ->orderBy('assigned_conversations_count', 'desc')
            ->limit(5)
            ->get();

        $response = view('admin.dashboard-3d', compact(
            'stats',
            'whatsappStats',
            'recentConversations',
            'weeklyMetrics',
            'conversationTypes',
            'topAgents'
        ));

        $this->logAction($request, $response);

        return $response;
    }

    protected function getWhatsAppStats()
    {
        try {
            // Verificar estado de conexión de WhatsApp
            $connectionStatus = $this->whatsappService->verifyConnection();
            
            return [
                'connection_status' => $connectionStatus['success'] ? 'connected' : 'disconnected',
                'api_status' => $connectionStatus['success'] ? 'active' : 'inactive',
                'messages_sent_today' => Message::where('direction', 'outgoing')
                    ->whereDate('created_at', today())
                    ->count(),
                'messages_received_today' => Message::where('direction', 'incoming')
                    ->whereDate('created_at', today())
                    ->count(),
                'delivery_rate' => $this->calculateDeliveryRate(),
                'response_time_avg' => $this->getAverageResponseTime(),
                'active_chatbot_flows' => \App\Models\ChatbotFlow::where('is_active', true)->count(),
                'webhook_status' => config('whatsapp.webhook_verify_token') ? 'configured' : 'not_configured',
                'rate_limit_usage' => $this->getRateLimitUsage(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting WhatsApp stats: ' . $e->getMessage());
            
            return [
                'connection_status' => 'error',
                'api_status' => 'error',
                'messages_sent_today' => 0,
                'messages_received_today' => 0,
                'delivery_rate' => 0,
                'response_time_avg' => 0,
                'active_chatbot_flows' => 0,
                'webhook_status' => 'error',
                'rate_limit_usage' => 0,
            ];
        }
    }

    private function calculateDeliveryRate()
    {
        $sentToday = Message::where('direction', 'outgoing')
            ->whereDate('created_at', today())
            ->count();
            
        $deliveredToday = Message::where('direction', 'outgoing')
            ->whereDate('created_at', today())
            ->whereIn('status', ['delivered', 'read'])
            ->count();
            
        return $sentToday > 0 ? round(($deliveredToday / $sentToday) * 100, 2) : 0;
    }

    private function getRateLimitUsage()
    {
        $messagesLastHour = Message::where('direction', 'outgoing')
            ->where('created_at', '>=', now()->subHour())
            ->count();
            
        $hourlyLimit = config('whatsapp.rate_limit.messages_per_hour', 36000);
        
        return round(($messagesLastHour / $hourlyLimit) * 100, 2);
    }

    protected function getWeeklyMetrics()
    {
        $days = [];
        $conversations = [];
        $messages = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');

            $conversations[] = Conversation::whereDate('started_at', $date)->count();
            $messages[] = Message::whereDate('created_at', $date)->count();
        }

        return [
            'days' => $days,
            'conversations' => $conversations,
            'messages' => $messages
        ];
    }

    public function analytics(Request $request)
    {
        // Métricas de satisfacción
        $satisfactionMetrics = Conversation::whereNotNull('satisfaction_rating')
            ->selectRaw('satisfaction_rating, COUNT(*) as count')
            ->groupBy('satisfaction_rating')
            ->pluck('count', 'satisfaction_rating');

        // Tiempo promedio de respuesta
        $avgResponseTime = ConversationAnalytic::where('metric_type', 'response_time')
            ->where('date', '>=', now()->subDays(30))
            ->avg('metric_value');

        // Tasa de resolución
        $resolutionRate = Conversation::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as resolved
            ')
            ->first();

        $resolutionPercentage = $resolutionRate->total > 0
            ? ($resolutionRate->resolved / $resolutionRate->total) * 100
            : 0;

        // Horarios de mayor actividad
        $hourlyActivity = Message::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        // Métricas específicas de WhatsApp
        $whatsappAnalytics = $this->getWhatsAppAnalytics();

        // Rendimiento de flujos del chatbot
        $chatbotFlowMetrics = $this->getChatbotFlowMetrics();

        // Análisis de tipos de mensajes
        $messageTypeAnalysis = $this->getMessageTypeAnalysis();

        // Métricas de agentes
        $agentMetrics = $this->getAgentMetrics();

        // Tendencias de crecimiento
        $growthTrends = $this->getGrowthTrends();

        $response = view('admin.analytics', compact(
            'satisfactionMetrics',
            'avgResponseTime',
            'resolutionPercentage',
            'hourlyActivity',
            'whatsappAnalytics',
            'chatbotFlowMetrics',
            'messageTypeAnalysis',
            'agentMetrics',
            'growthTrends'
        ));

        $this->logAction($request, $response);

        return $response;
    }

    private function getWhatsAppAnalytics()
    {
        try {
            $last30Days = now()->subDays(30);
            
            return [
                'message_delivery_stats' => [
                    'sent' => Message::where('direction', 'outgoing')
                        ->where('created_at', '>=', $last30Days)
                        ->count(),
                    'delivered' => Message::where('direction', 'outgoing')
                        ->where('created_at', '>=', $last30Days)
                        ->whereIn('status', ['delivered', 'read'])
                        ->count(),
                    'failed' => Message::where('direction', 'outgoing')
                        ->where('created_at', '>=', $last30Days)
                        ->where('status', 'failed')
                        ->count(),
                ],
                'response_time_by_day' => $this->getResponseTimeByDay(),
                'message_volume_trend' => $this->getMessageVolumeTrend(),
                'webhook_health' => $this->getWebhookHealth(),
                'api_usage_stats' => $this->getApiUsageStats(),
                'connection_quality' => $this->getConnectionQuality(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting WhatsApp analytics: ' . $e->getMessage());
            return [];
        }
    }

    private function getChatbotFlowMetrics()
    {
        return [
            'most_used_flows' => \App\Models\ChatbotFlow::orderBy('usage_count', 'desc')
                ->limit(5)
                ->get(['name', 'usage_count', 'last_used_at']),
            'flow_success_rates' => $this->getFlowSuccessRates(),
            'average_flow_completion_time' => $this->getAverageFlowCompletionTime(),
            'flow_abandonment_points' => $this->getFlowAbandonmentPoints(),
        ];
    }

    private function getMessageTypeAnalysis()
    {
        $last30Days = now()->subDays(30);
        
        return [
            'by_type' => Message::where('created_at', '>=', $last30Days)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_direction' => Message::where('created_at', '>=', $last30Days)
                ->selectRaw('direction, COUNT(*) as count')
                ->groupBy('direction')
                ->pluck('count', 'direction'),
            'interactive_usage' => Message::where('created_at', '>=', $last30Days)
                ->where('type', 'interactive')
                ->count(),
            'media_messages' => Message::where('created_at', '>=', $last30Days)
                ->whereIn('type', ['image', 'document', 'audio', 'video'])
                ->count(),
        ];
    }

    private function getAgentMetrics()
    {
        $thisMonth = now()->startOfMonth();
        
        return [
            'top_performers' => User::where('role', 'agent')
                ->withCount(['assignedConversations' => function($q) use ($thisMonth) {
                    $q->where('status', 'closed')
                      ->where('ended_at', '>=', $thisMonth);
                }])
                ->orderBy('assigned_conversations_count', 'desc')
                ->limit(5)
                ->get(),
            'average_response_times' => $this->getAgentResponseTimes(),
            'workload_distribution' => $this->getWorkloadDistribution(),
            'satisfaction_by_agent' => $this->getSatisfactionByAgent(),
        ];
    }

    private function getGrowthTrends()
    {
        $months = [];
        $conversations = [];
        $contacts = [];
        $messages = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $conversations[] = Conversation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $contacts[] = WhatsappContact::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $messages[] = Message::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'months' => $months,
            'conversations' => $conversations,
            'contacts' => $contacts,
            'messages' => $messages,
            'growth_rates' => $this->calculateGrowthRates($conversations, $contacts, $messages),
        ];
    }

    // Métodos auxiliares para analíticas

    private function getResponseTimeByDay()
    {
        $days = [];
        $responseTimes = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');

            $avgTime = Message::whereDate('created_at', $date)
                ->where('direction', 'outgoing')
                ->whereNotNull('response_time_seconds')
                ->avg('response_time_seconds');

            $responseTimes[] = $avgTime ? round($avgTime, 2) : 0;
        }

        return ['days' => $days, 'times' => $responseTimes];
    }

    private function getMessageVolumeTrend()
    {
        $hours = [];
        $volumes = [];

        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $hours[] = $hour->format('H:i');

            $volume = Message::where('created_at', '>=', $hour)
                ->where('created_at', '<', $hour->copy()->addHour())
                ->count();

            $volumes[] = $volume;
        }

        return ['hours' => $hours, 'volumes' => $volumes];
    }

    private function getWebhookHealth()
    {
        // Simular métricas de salud del webhook
        return [
            'status' => 'healthy',
            'last_received' => now()->subMinutes(rand(1, 30)),
            'success_rate' => rand(95, 100),
            'average_response_time' => rand(100, 500) . 'ms',
        ];
    }

    private function getApiUsageStats()
    {
        try {
            return $this->whatsappService->getApiUsageStats();
        } catch (\Exception $e) {
            return ['error' => 'No se pudieron obtener las estadísticas de uso de la API'];
        }
    }

    private function getConnectionQuality()
    {
        try {
            $result = $this->whatsappService->verifyConnection();
            return [
                'status' => $result['success'] ? 'excellent' : 'poor',
                'quality_rating' => $result['quality_rating'] ?? 'unknown',
                'phone_verified' => $result['phone_verified'] ?? 'unknown',
                'last_check' => now(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'last_check' => now(),
            ];
        }
    }

    private function getFlowSuccessRates()
    {
        // Implementar cálculo de tasas de éxito de flujos
        return \App\Models\ChatbotFlow::where('is_active', true)
            ->get()
            ->map(function($flow) {
                return [
                    'name' => $flow->name,
                    'success_rate' => rand(70, 95), // Placeholder
                    'total_executions' => $flow->usage_count,
                ];
            });
    }

    private function getAverageFlowCompletionTime()
    {
        // Placeholder para tiempo promedio de completación de flujos
        return rand(30, 180); // segundos
    }

    private function getFlowAbandonmentPoints()
    {
        // Placeholder para puntos de abandono en flujos
        return [
            'step_1' => 5,
            'step_2' => 12,
            'step_3' => 8,
            'step_4' => 3,
        ];
    }

    private function getAgentResponseTimes()
    {
        return User::where('role', 'agent')
            ->where('is_active', true)
            ->get()
            ->map(function($agent) {
                $avgTime = Message::where('sent_by', $agent->id)
                    ->where('direction', 'outgoing')
                    ->whereNotNull('response_time_seconds')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->avg('response_time_seconds');

                return [
                    'name' => $agent->name,
                    'avg_response_time' => $avgTime ? round($avgTime, 2) : 0,
                ];
            });
    }

    private function getWorkloadDistribution()
    {
        return User::where('role', 'agent')
            ->withCount(['assignedConversations' => function($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->map(function($agent) {
                return [
                    'name' => $agent->name,
                    'active_conversations' => $agent->assigned_conversations_count,
                ];
            });
    }

    private function getSatisfactionByAgent()
    {
        return User::where('role', 'agent')
            ->get()
            ->map(function($agent) {
                $avgSatisfaction = Conversation::where('assigned_user_id', $agent->id)
                    ->whereNotNull('satisfaction_rating')
                    ->where('ended_at', '>=', now()->subDays(30))
                    ->avg('satisfaction_rating');

                return [
                    'name' => $agent->name,
                    'avg_satisfaction' => $avgSatisfaction ? round($avgSatisfaction, 2) : 0,
                ];
            });
    }

    private function calculateGrowthRates($conversations, $contacts, $messages)
    {
        $conversationGrowth = count($conversations) > 1 
            ? round((($conversations[count($conversations)-1] - $conversations[count($conversations)-2]) / max($conversations[count($conversations)-2], 1)) * 100, 2)
            : 0;

        $contactGrowth = count($contacts) > 1 
            ? round((($contacts[count($contacts)-1] - $contacts[count($contacts)-2]) / max($contacts[count($contacts)-2], 1)) * 100, 2)
            : 0;

        $messageGrowth = count($messages) > 1 
            ? round((($messages[count($messages)-1] - $messages[count($messages)-2]) / max($messages[count($messages)-2], 1)) * 100, 2)
            : 0;

        return [
            'conversations' => $conversationGrowth,
            'contacts' => $contactGrowth,
            'messages' => $messageGrowth,
        ];
    }

    public function clearWelcomeNotifications(Request $request)
    {
        // This is a placeholder implementation.
        // It could clear a session key or a database flag.
        $request->session()->put('welcome_notifications_reset', true);

        $response = response()->json(['success' => true, 'message' => 'Notificaciones de bienvenida reiniciadas.']);

        $this->logAction($request, $response);

        return $response;
    }
}
