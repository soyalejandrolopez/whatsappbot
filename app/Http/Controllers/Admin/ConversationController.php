<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\WhatsappContact;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConversationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of conversations with advanced filtering
     */
    public function index(Request $request)
    {
        $query = Conversation::with(['contact', 'assignedUser', 'messages' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filtros avanzados
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent_id')) {
            $query->where('assigned_user_id', $request->agent_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('contact', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $conversations = $query->paginate(20);

        // Estadísticas para el dashboard
        $stats = $this->getConversationStats();

        // Agentes disponibles para filtros
        $agents = User::whereIn('role', ['agent', 'admin'])
            ->orderBy('name')
            ->get();

        return view('admin.conversations', compact('conversations', 'stats', 'agents'));
    }

    /**
     * Show the form for creating a new conversation
     */
    public function create()
    {
        $contacts = WhatsappContact::where('is_blocked', false)
            ->orderBy('name')
            ->get();
            
        $agents = User::whereIn('role', ['agent', 'admin'])
            ->orderBy('name')
            ->get();

        return view('admin.conversations.create', compact('contacts', 'agents'));
    }

    /**
     * Store a newly created conversation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:whatsapp_contacts,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:chatbot,human,mixed',
            'language' => 'required|string|max:5',
            'notes' => 'nullable|string|max:1000',
            'initial_message' => 'nullable|string|max:4096'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Verificar si ya existe una conversación activa para este contacto
            $existingConversation = Conversation::where('contact_id', $request->contact_id)
                ->whereIn('status', ['active', 'pending', 'waiting'])
                ->first();

            if ($existingConversation) {
                return redirect()->back()
                    ->with('error', 'El contacto ya tiene una conversación activa.')
                    ->withInput();
            }

            // Crear la conversación
            $conversation = Conversation::create([
                'contact_id' => $request->contact_id,
                'assigned_user_id' => $request->assigned_user_id,
                'status' => $request->assigned_user_id ? 'active' : 'pending',
                'priority' => $request->priority,
                'type' => $request->type,
                'language' => $request->language,
                'notes' => $request->notes,
                'started_at' => now(),
                'assigned_at' => $request->assigned_user_id ? now() : null,
                'message_count' => 0,
                'usage_count' => 1
            ]);

            // Enviar mensaje inicial si se proporciona
            if ($request->filled('initial_message')) {
                $contact = WhatsappContact::find($request->contact_id);
                
                $result = $this->whatsappService->sendTextMessage(
                    $contact->phone_number,
                    $request->initial_message
                );

                if ($result['success']) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => Auth::id(),
                        'whatsapp_message_id' => $result['message_id'],
                        'direction' => 'outbound',
                        'type' => 'text',
                        'content' => $request->initial_message,
                        'status' => 'sent',
                        'whatsapp_timestamp' => now(),
                        'is_automated' => false
                    ]);

                    $conversation->increment('message_count');
                    $conversation->update(['last_message_at' => now()]);
                }
            }

            // Crear mensaje del sistema
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'content' => 'Conversación iniciada manualmente',
                'type' => 'system',
                'direction' => 'internal',
                'status' => 'sent',
                'whatsapp_timestamp' => now(),
                'is_automated' => true
            ]);

            DB::commit();

            return redirect()->route('admin.conversations.show', $conversation)
                ->with('success', 'Conversación creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating conversation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la conversación. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Display the specified conversation with full message history
     */
    public function show(Conversation $conversation)
    {
        $conversation->load([
            'contact',
            'assignedUser',
            'closedBy',
            'messages' => function($q) {
                $q->orderBy('created_at', 'asc');
            },
            'messages.sender'
        ]);

        // Marcar mensajes como leídos si el usuario tiene permisos
        if ($conversation->assigned_user_id === Auth::id() || Auth::user()->role === 'admin') {
            $conversation->messages()
                ->where('direction', 'inbound')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Obtener contactos relacionados (mismo número)
        $relatedContacts = WhatsappContact::where('phone_number', $conversation->contact->phone_number)
            ->where('id', '!=', $conversation->contact_id)
            ->get();

        // Historial de conversaciones del contacto
        $conversationHistory = Conversation::where('contact_id', $conversation->contact_id)
            ->where('id', '!=', $conversation->id)
            ->with('assignedUser')
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        // Respuestas rápidas disponibles
        $quickReplies = $this->getQuickReplies();

        // Agentes disponibles para transferir
        $availableAgents = User::whereIn('role', ['agent', 'admin'])
            ->where('id', '!=', $conversation->assigned_user_id)
            ->orderBy('name')
            ->get();

        // Estadísticas de la conversación
        $conversationStats = [
            'total_messages' => $conversation->messages->count(),
            'response_time' => $this->getConversationResponseTime($conversation),
            'duration' => $this->getConversationDuration($conversation),
            'agent_messages' => $conversation->messages->where('direction', 'outbound')->count(),
            'customer_messages' => $conversation->messages->where('direction', 'inbound')->count()
        ];

        return view('admin.conversations.show', compact(
            'conversation',
            'relatedContacts',
            'conversationHistory',
            'quickReplies',
            'availableAgents',
            'conversationStats'
        ));
    }

    /**
     * Show the form for editing the specified conversation.
     */
    public function edit(Conversation $conversation)
    {
        $agents = User::whereIn('role', ['agent', 'admin'])->orderBy('name')->get();
        $contacts = WhatsappContact::orderBy('name')->get();
        return view('admin.conversations.edit', compact('conversation', 'agents', 'contacts'));
    }

    /**
     * Update the specified conversation in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        $validator = Validator::make($request->all(), [
            'assigned_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,pending,closed,waiting,spam',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $oldAgentId = $conversation->assigned_user_id;
            $newAgentId = $request->input('assigned_user_id');

            $conversation->update($request->only(['status', 'priority', 'notes']));
            
            if ($oldAgentId != $newAgentId) {
                $conversation->assigned_user_id = $newAgentId;
                $conversation->assigned_at = now();
                
                $agentName = $newAgentId ? User::find($newAgentId)->name : 'unassigned';
                Message::create([
                    'conversation_id' => $conversation->id,
                    'type' => 'system',
                    'direction' => 'internal',
                    'content' => 'Conversation assigned to ' . $agentName . ' by ' . Auth::user()->name,
                    'is_automated' => true,
                    'sender_id' => Auth::id(),
                ]);
            }
            
            $conversation->save();

            return redirect()->route('admin.conversations.show', $conversation->id)
                             ->with('success', 'Conversation updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating conversation #{$conversation->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error updating the conversation.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        try {
            DB::beginTransaction();
            // Eliminar mensajes asociados
            $conversation->messages()->delete();
            // Eliminar la conversación
            $conversation->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Conversación eliminada exitosamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting conversation: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar la conversación.'], 500);
        }
    }

    /**
     * Assign conversation to an agent
     */
    public function assign(Request $request, Conversation $conversation)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $agent = User::findOrFail($request->agent_id);

        $conversation->update([
            'assigned_user_id' => $request->agent_id,
            'status' => 'active',
            'assigned_at' => now()
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'content' => "Conversation assigned to {$agent->name} by " . Auth::user()->name,
            'type' => 'system',
            'direction' => 'internal',
            'is_automated' => true,
            'sender_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Conversation successfully assigned to {$agent->name}",
            'agent' => ['id' => $agent->id, 'name' => $agent->name]
        ]);
    }

    /**
     * Send a new message in the conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:4096',
            'type' => 'sometimes|in:text,image,document,audio,video',
            'media_url' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $messageContent = $request->input('message');
            
            // Here you might integrate with WhatsAppService to send the message
            // $result = $this->whatsappService->sendTextMessage($conversation->contact->phone_number, $messageContent);
            // For now, we'll assume it's sent and create the message record

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'content' => $messageContent,
                'type' => $request->input('type', 'text'),
                'direction' => 'outbound',
                'status' => 'sent', // Assuming it's sent. Could be 'delivered', 'read' later.
                'is_automated' => false,
                'whatsapp_timestamp' => now()
            ]);

            $conversation->increment('message_count');
            $conversation->update(['last_message_at' => now()]);
            
            // Load sender relationship to be available in the broadcasted event
            $message->load('sender');

            // Broadcast the new message event
            broadcast(new MessageSent($message))->toOthers();

            return response()->json(['success' => true, 'message' => 'Message sent.', 'data' => $message]);

        } catch (\Exception $e) {
            Log::error('Error sending message in conversation #' . $conversation->id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error sending message.'], 500);
        }
    }

    /**
     * Close conversation
     */
    public function close(Conversation $conversation)
    {
        $conversation->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id()
        ]);

        // Crear mensaje del sistema
        Message::create([
            'conversation_id' => $conversation->id,
            'content' => 'Conversación cerrada por ' . Auth::user()->name,
            'type' => 'system',
            'direction' => 'internal',
            'sent_by' => Auth::id(),
            'sent_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversación cerrada exitosamente'
        ]);
    }

    /**
     * Reopen conversation
     */
    public function reopen(Conversation $conversation)
    {
        $conversation->update([
            'status' => 'active',
            'closed_at' => null,
            'closed_by' => null,
            'assigned_user_id' => Auth::id()
        ]);

        // Crear mensaje del sistema
        Message::create([
            'conversation_id' => $conversation->id,
            'content' => 'Conversación reabierta por ' . Auth::user()->name,
            'type' => 'system',
            'direction' => 'internal',
            'sent_by' => Auth::id(),
            'sent_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversación reabierta exitosamente'
        ]);
    }

    /**
     * Transfer conversation to another agent
     */
    public function transfer(Request $request, Conversation $conversation)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:users,id',
            'reason' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $newAgent = User::findOrFail($request->agent_id);
        $oldAgent = $conversation->assignedUser;

        $conversation->update([
            'assigned_user_id' => $request->agent_id,
            'assigned_at' => now()
        ]);

        // Crear mensaje del sistema
        $transferMessage = "Conversación transferida";
        if ($oldAgent) {
            $transferMessage .= " de {$oldAgent->name}";
        }
        $transferMessage .= " a {$newAgent->name}";

        if ($request->filled('reason')) {
            $transferMessage .= ". Motivo: {$request->reason}";
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'content' => $transferMessage,
            'type' => 'system',
            'direction' => 'internal',
            'sent_by' => Auth::id(),
            'sent_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Conversación transferida a {$newAgent->name} exitosamente",
            'agent' => $newAgent->only(['id', 'name', 'email'])
        ]);
    }

    /**
     * Get real-time conversation updates
     */
    public function getUpdates(Request $request)
    {
        $lastUpdate = $request->get('last_update', now()->subMinutes(5));

        $conversations = Conversation::with(['contact', 'assignedUser'])
            ->where('updated_at', '>', $lastUpdate)
            ->where(function($query) {
                if (Auth::user()->role !== 'admin') {
                    $query->where('assigned_user_id', Auth::id());
                }
            })
            ->get();

        $newMessages = Message::with(['conversation.contact', 'sender'])
            ->where('created_at', '>', $lastUpdate)
            ->whereHas('conversation', function($query) {
                if (Auth::user()->role !== 'admin') {
                    $query->where('assigned_user_id', Auth::id());
                }
            })
            ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
            'messages' => $newMessages,
            'timestamp' => now()
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Conversation $conversation)
    {
        $conversation->messages()
            ->where('direction', 'incoming')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Mensajes marcados como leídos'
        ]);
    }

    /**
     * Get conversation statistics
     */
    private function getConversationStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total' => Conversation::count(),
            'active' => Conversation::where('status', 'active')->count(),
            'pending' => Conversation::where('status', 'pending')->count(),
            'closed_today' => Conversation::whereDate('closed_at', $today)->count(),
            'new_today' => Conversation::whereDate('started_at', $today)->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'satisfaction_rate' => $this->getSatisfactionRate(),
            'by_status' => Conversation::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_priority' => Conversation::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'weekly_trend' => $this->getWeeklyTrend(),
            'top_agents' => $this->getTopAgents()
        ];
    }

    /**
     * Get average response time in minutes
     */
    private function getAverageResponseTime()
    {
        // Calculate average response time for messages sent in the last 7 days
        $avgTime = Message::where('direction', 'outgoing')
            ->whereNotNull('response_time_seconds')
            ->where('created_at', '>=', now()->subDays(7))
            ->avg('response_time_seconds');

        return $avgTime ? round($avgTime / 60, 2) : 0; // Return in minutes
    }

    /**
     * Get satisfaction rate percentage
     */
    private function getSatisfactionRate()
    {
        // Implementar lógica de cálculo de satisfacción
        // Por ahora retornamos un valor simulado
        return rand(85, 95); // porcentaje
    }

    /**
     * Get weekly conversation trend
     */
    private function getWeeklyTrend()
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'count' => Conversation::whereDate('started_at', $date)->count()
            ];
        }
        return $days;
    }

    /**
     * Get top performing agents
     */
    private function getTopAgents()
    {
        return User::select('users.*')
            ->selectRaw('COUNT(conversations.id) as conversation_count')
            ->selectRaw('AVG(CASE WHEN conversations.status = "closed" THEN 1 ELSE 0 END) * 100 as resolution_rate')
            ->leftJoin('conversations', 'users.id', '=', 'conversations.assigned_user_id')
            ->where('users.role', 'agent')
            ->orWhere('users.role', 'admin')
            ->groupBy('users.id')
            ->orderBy('conversation_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get quick replies for agents
     */
    private function getQuickReplies()
    {
        return [
            'Saludos' => [
                '¡Hola! ¿En qué puedo ayudarte hoy?',
                'Buenos días, gracias por contactarnos.',
            ],
            'Soporte' => [
                'Entiendo tu situación, permíteme ayudarte.',
                'Voy a revisar tu caso inmediatamente.',
            ],
            'Cierre' => [
                '¿Hay algo más en lo que pueda ayudarte?',
                'Gracias por contactarnos, ¡que tengas un excelente día!',
            ]
        ];
    }

    /**
     * Export conversations to a file (e.g., CSV)
     */
    public function export(Request $request)
    {
        $fileName = 'conversations_' . date('Y-m-d_H-i-s') . '.csv';

        // Reutilizar la lógica de filtrado de index()
        $query = Conversation::with(['contact', 'assignedUser']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('agent_id')) $query->where('assigned_user_id', $request->agent_id);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('date_from')) $query->whereDate('started_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('started_at', '<=', $request->date_to);

        $conversations = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Contacto', 'Teléfono', 'Agente Asignado', 'Estado', 'Prioridad', 'Fecha Inicio', 'Última Actualización', 'Mensajes', 'Notas'];

        $callback = function() use($conversations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($conversations as $convo) {
                fputcsv($file, [
                    $convo->id,
                    $convo->contact->name,
                    $convo->contact->phone_number,
                    $convo->assignedUser->name ?? 'N/A',
                    $convo->status,
                    $convo->priority,
                    $convo->started_at,
                    $convo->updated_at,
                    $convo->message_count,
                    $convo->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate average response time for a conversation
     */
    private function getConversationResponseTime(Conversation $conversation)
    {
        $avgTime = $conversation->messages()
            ->where('direction', 'outbound')
            ->whereNotNull('response_time_seconds')
            ->avg('response_time_seconds');

        return $avgTime ? round($avgTime / 60, 2) : null;
    }

    /**
     * Get conversation duration in minutes
     */
    private function getConversationDuration(Conversation $conversation)
    {
        return $conversation->closed_at
            ? $conversation->started_at->diffInMinutes($conversation->closed_at)
            : null;
    }

    /**
     * Perform bulk actions on conversations.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:assign,close,delete',
            'conversation_ids' => 'required|array',
            'conversation_ids.*' => 'exists:conversations,id',
            'agent_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:active,pending,closed,waiting,spam',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $action = $request->input('action');
        $conversationIds = $request->input('conversation_ids');
        $user = Auth::user();

        try {
            DB::beginTransaction();

            foreach ($conversationIds as $id) {
                $conversation = Conversation::find($id);
                if (!$conversation) continue;

                switch ($action) {
                    case 'assign':
                        if ($request->filled('agent_id')) {
                            $agent = User::find($request->input('agent_id'));
                            if ($agent) {
                                $conversation->update([
                                    'assigned_user_id' => $agent->id,
                                    'status' => 'active',
                                    'assigned_at' => now(),
                                ]);
                                Message::create([
                                    'conversation_id' => $conversation->id,
                                    'content' => "Conversación asignada a {$agent->name} por {$user->name}",
                                    'type' => 'system',
                                    'direction' => 'internal',
                                    'sender_id' => $user->id,
                                    'is_automated' => true
                                ]);
                            }
                        }
                        break;
                    case 'close':
                        $conversation->update([
                            'status' => 'closed',
                            'closed_at' => now(),
                        ]);
                        Message::create([
                            'conversation_id' => $conversation->id,
                            'content' => "Conversación cerrada por {$user->name}",
                            'type' => 'system',
                            'direction' => 'internal',
                            'sender_id' => $user->id,
                            'is_automated' => true
                        ]);
                        break;
                    case 'delete':
                        $conversation->messages()->delete();
                        $conversation->delete();
                        break;
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Acción masiva realizada exitosamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error performing bulk action on conversations: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al realizar la acción masiva.'], 500);
        }
    }
}
