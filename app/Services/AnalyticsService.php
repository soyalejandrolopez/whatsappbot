<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\ConversationAnalytic;
use App\Models\WhatsappContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Record response time metric
     */
    public function recordResponseTime($conversationId, $responseTimeSeconds)
    {
        ConversationAnalytic::recordMetric(
            $conversationId,
            'response_time',
            'agent_response_time',
            $responseTimeSeconds,
            'seconds'
        );
    }

    /**
     * Record satisfaction metric
     */
    public function recordSatisfaction($conversationId, $rating, $comment = null)
    {
        ConversationAnalytic::recordMetric(
            $conversationId,
            'satisfaction',
            'customer_satisfaction',
            $rating,
            'rating',
            ['comment' => $comment]
        );
    }

    /**
     * Record resolution metric
     */
    public function recordResolution($conversationId, $resolved, $resolutionTime = null)
    {
        ConversationAnalytic::recordMetric(
            $conversationId,
            'resolution',
            'conversation_resolved',
            $resolved ? 1 : 0,
            'boolean',
            ['resolution_time' => $resolutionTime]
        );
    }

    /**
     * Get conversation trends
     */
    public function getConversationTrends($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Conversation::selectRaw('
            DATE(started_at) as date,
            COUNT(*) as total_conversations,
            SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed_conversations,
            SUM(CASE WHEN type = "chatbot" THEN 1 ELSE 0 END) as bot_conversations,
            SUM(CASE WHEN type = "human" THEN 1 ELSE 0 END) as human_conversations,
            AVG(message_count) as avg_messages_per_conversation
        ')
        ->where('started_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    /**
     * Get peak hours analysis
     */
    public function getPeakHours($days = 7)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Message::selectRaw('
            HOUR(created_at) as hour,
            COUNT(*) as message_count,
            COUNT(DISTINCT conversation_id) as unique_conversations,
            AVG(CASE WHEN direction = "outbound" AND is_automated = 0 THEN 1 ELSE 0 END) as human_response_rate
        ')
        ->where('created_at', '>=', $startDate)
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();
    }

    /**
     * Get agent performance metrics
     */
    public function getAgentPerformance($agentId = null, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $query = DB::table('users')
            ->select([
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT conversations.id) as total_conversations'),
                DB::raw('COUNT(DISTINCT CASE WHEN conversations.status = "closed" THEN conversations.id END) as closed_conversations'),
                DB::raw('AVG(conversations.message_count) as avg_messages_per_conversation'),
                DB::raw('AVG(CASE WHEN conversations.satisfaction_rating IS NOT NULL THEN conversations.satisfaction_rating END) as avg_satisfaction'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, conversations.started_at, conversations.ended_at)) as avg_conversation_duration')
            ])
            ->leftJoin('conversations', 'users.id', '=', 'conversations.assigned_user_id')
            ->where('users.role', 'agent')
            ->where('conversations.started_at', '>=', $startDate)
            ->groupBy('users.id', 'users.name');
            
        if ($agentId) {
            $query->where('users.id', $agentId);
        }
        
        return $query->get();
    }

    /**
     * Get chatbot effectiveness metrics
     */
    public function getChatbotEffectiveness($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $totalConversations = Conversation::where('started_at', '>=', $startDate)->count();
        $botOnlyConversations = Conversation::where('started_at', '>=', $startDate)
            ->where('type', 'chatbot')
            ->where('status', 'closed')
            ->count();
        
        $transferredConversations = Conversation::where('started_at', '>=', $startDate)
            ->whereNotNull('assigned_user_id')
            ->count();
        
        $avgMessagesBeforeTransfer = Conversation::where('started_at', '>=', $startDate)
            ->whereNotNull('assigned_user_id')
            ->avg('message_count');
        
        return [
            'total_conversations' => $totalConversations,
            'bot_resolved_conversations' => $botOnlyConversations,
            'bot_resolution_rate' => $totalConversations > 0 ? ($botOnlyConversations / $totalConversations) * 100 : 0,
            'transferred_conversations' => $transferredConversations,
            'transfer_rate' => $totalConversations > 0 ? ($transferredConversations / $totalConversations) * 100 : 0,
            'avg_messages_before_transfer' => round($avgMessagesBeforeTransfer ?? 0, 2)
        ];
    }

    /**
     * Get customer satisfaction trends
     */
    public function getSatisfactionTrends($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Conversation::selectRaw('
            DATE(ended_at) as date,
            AVG(satisfaction_rating) as avg_satisfaction,
            COUNT(satisfaction_rating) as total_ratings,
            SUM(CASE WHEN satisfaction_rating >= 4 THEN 1 ELSE 0 END) as positive_ratings,
            SUM(CASE WHEN satisfaction_rating <= 2 THEN 1 ELSE 0 END) as negative_ratings
        ')
        ->whereNotNull('satisfaction_rating')
        ->where('ended_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    /**
     * Get response time analysis
     */
    public function getResponseTimeAnalysis($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return ConversationAnalytic::selectRaw('
            DATE(date) as date,
            AVG(metric_value) as avg_response_time,
            MIN(metric_value) as min_response_time,
            MAX(metric_value) as max_response_time,
            PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY metric_value) as median_response_time,
            PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY metric_value) as p95_response_time
        ')
        ->where('metric_type', 'response_time')
        ->where('date', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    /**
     * Get contact engagement metrics
     */
    public function getContactEngagement($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return WhatsappContact::selectRaw('
            COUNT(*) as total_contacts,
            COUNT(CASE WHEN last_interaction_at >= ? THEN 1 END) as active_contacts,
            AVG(DATEDIFF(NOW(), last_interaction_at)) as avg_days_since_last_interaction
        ', [$startDate])
        ->first();
    }

    /**
     * Get conversation flow analysis
     */
    public function getFlowAnalysis($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('chatbot_flows')
            ->select([
                'chatbot_flows.name',
                'chatbot_flows.trigger_type',
                'chatbot_flows.usage_count',
                'chatbot_flows.last_used_at',
                DB::raw('COUNT(conversations.id) as conversations_using_flow'),
                DB::raw('AVG(conversations.message_count) as avg_messages_in_flow')
            ])
            ->leftJoin('conversations', 'chatbot_flows.id', '=', 'conversations.current_flow_id')
            ->where('chatbot_flows.is_active', true)
            ->where('conversations.started_at', '>=', $startDate)
            ->groupBy('chatbot_flows.id', 'chatbot_flows.name', 'chatbot_flows.trigger_type', 'chatbot_flows.usage_count', 'chatbot_flows.last_used_at')
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    /**
     * Generate daily report
     */
    public function generateDailyReport($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::yesterday();
        
        $conversations = Conversation::whereDate('started_at', $date)->count();
        $messages = Message::whereDate('created_at', $date)->count();
        $newContacts = WhatsappContact::whereDate('created_at', $date)->count();
        $closedConversations = Conversation::whereDate('ended_at', $date)->count();
        
        $avgSatisfaction = Conversation::whereDate('ended_at', $date)
            ->whereNotNull('satisfaction_rating')
            ->avg('satisfaction_rating');
        
        $avgResponseTime = ConversationAnalytic::where('metric_type', 'response_time')
            ->whereDate('date', $date)
            ->avg('metric_value');
        
        return [
            'date' => $date->toDateString(),
            'conversations_started' => $conversations,
            'conversations_closed' => $closedConversations,
            'total_messages' => $messages,
            'new_contacts' => $newContacts,
            'avg_satisfaction' => round($avgSatisfaction ?? 0, 2),
            'avg_response_time' => round($avgResponseTime ?? 0, 2),
            'resolution_rate' => $conversations > 0 ? ($closedConversations / $conversations) * 100 : 0
        ];
    }

    /**
     * Export analytics data
     */
    public function exportData($type, $startDate, $endDate, $format = 'json')
    {
        $data = match($type) {
            'conversations' => $this->getConversationTrends(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))),
            'agents' => $this->getAgentPerformance(null, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))),
            'satisfaction' => $this->getSatisfactionTrends(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))),
            'response_time' => $this->getResponseTimeAnalysis(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))),
            default => []
        };
        
        return match($format) {
            'csv' => $this->convertToCSV($data),
            'excel' => $this->convertToExcel($data),
            default => $data
        };
    }

    /**
     * Convert data to CSV format
     */
    protected function convertToCSV($data)
    {
        if (empty($data)) {
            return '';
        }
        
        $csv = '';
        $headers = array_keys((array) $data[0]);
        $csv .= implode(',', $headers) . "\n";
        
        foreach ($data as $row) {
            $csv .= implode(',', array_values((array) $row)) . "\n";
        }
        
        return $csv;
    }

    /**
     * Convert data to Excel format (placeholder)
     */
    protected function convertToExcel($data)
    {
        // Aquí se implementaría la conversión a Excel usando una librería como PhpSpreadsheet
        return $data;
    }
}
