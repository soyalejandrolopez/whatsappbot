<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappContact;
use App\Models\ConversationAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_conversations' => Conversation::count(),
            'active_conversations' => Conversation::where('status', 'active')->count(),
            'total_contacts' => WhatsappContact::count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'satisfaction_rate' => $this->getSatisfactionRate(),
            'resolution_rate' => $this->getResolutionRate()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get conversation metrics by date range
     */
    public function conversations(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'in:day,week,month'
        ]);

        $groupBy = $request->get('group_by', 'day');
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $conversations = Conversation::selectRaw("
            DATE_FORMAT(started_at, '{$dateFormat}') as period,
            COUNT(*) as total,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
            SUM(CASE WHEN type = 'chatbot' THEN 1 ELSE 0 END) as bot_handled,
            SUM(CASE WHEN type = 'human' THEN 1 ELSE 0 END) as human_handled
        ")
        ->whereBetween('started_at', [$startDate, $endDate])
        ->groupBy('period')
        ->orderBy('period')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Get message metrics
     */
    public function messages(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $messages = Message::selectRaw("
            DATE(created_at) as date,
            COUNT(*) as total,
            SUM(CASE WHEN direction = 'inbound' THEN 1 ELSE 0 END) as inbound,
            SUM(CASE WHEN direction = 'outbound' THEN 1 ELSE 0 END) as outbound,
            SUM(CASE WHEN is_automated = 1 THEN 1 ELSE 0 END) as automated
        ")
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get hourly activity
     */
    public function hourlyActivity(): JsonResponse
    {
        $activity = Message::selectRaw('
            HOUR(created_at) as hour,
            COUNT(*) as message_count,
            COUNT(DISTINCT conversation_id) as conversation_count
        ')
        ->whereDate('created_at', '>=', now()->subDays(7))
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    /**
     * Get agent performance
     */
    public function agentPerformance(): JsonResponse
    {
        $agents = \App\Models\User::where('role', 'agent')
            ->withCount([
                'assignedConversations as total_conversations',
                'assignedConversations as closed_conversations' => function($query) {
                    $query->where('status', 'closed');
                },
                'sentMessages as total_messages' => function($query) {
                    $query->where('is_automated', false);
                }
            ])
            ->with(['assignedConversations' => function($query) {
                $query->whereNotNull('satisfaction_rating')
                      ->selectRaw('assigned_user_id, AVG(satisfaction_rating) as avg_satisfaction')
                      ->groupBy('assigned_user_id');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }

    /**
     * Get satisfaction metrics
     */
    public function satisfaction(): JsonResponse
    {
        $satisfaction = Conversation::whereNotNull('satisfaction_rating')
            ->selectRaw('
                satisfaction_rating,
                COUNT(*) as count,
                (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM conversations WHERE satisfaction_rating IS NOT NULL)) as percentage
            ')
            ->groupBy('satisfaction_rating')
            ->orderBy('satisfaction_rating')
            ->get();

        $avgSatisfaction = Conversation::whereNotNull('satisfaction_rating')
            ->avg('satisfaction_rating');

        return response()->json([
            'success' => true,
            'data' => [
                'distribution' => $satisfaction,
                'average' => round($avgSatisfaction, 2)
            ]
        ]);
    }

    /**
     * Get response time metrics
     */
    public function responseTime(): JsonResponse
    {
        $responseTime = ConversationAnalytic::where('metric_type', 'response_time')
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('
                DATE(date) as date,
                AVG(metric_value) as avg_response_time,
                MIN(metric_value) as min_response_time,
                MAX(metric_value) as max_response_time
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $responseTime
        ]);
    }

    /**
     * Calculate average response time
     */
    protected function getAverageResponseTime()
    {
        return ConversationAnalytic::where('metric_type', 'response_time')
            ->where('date', '>=', now()->subDays(7))
            ->avg('metric_value') ?? 0;
    }

    /**
     * Calculate satisfaction rate
     */
    protected function getSatisfactionRate()
    {
        $total = Conversation::whereNotNull('satisfaction_rating')->count();
        $satisfied = Conversation::where('satisfaction_rating', '>=', 4)->count();

        return $total > 0 ? ($satisfied / $total) * 100 : 0;
    }

    /**
     * Calculate resolution rate
     */
    protected function getResolutionRate()
    {
        $total = Conversation::where('created_at', '>=', now()->subDays(30))->count();
        $resolved = Conversation::where('status', 'closed')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return $total > 0 ? ($resolved / $total) * 100 : 0;
    }
}
