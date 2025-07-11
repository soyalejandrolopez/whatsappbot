<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConversationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of conversations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Conversation::with(['contact', 'assignedUser'])
            ->orderBy('last_message_at', 'desc');

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('assigned_user_id')) {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }

        $conversations = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Display the specified conversation
     */
    public function show(Conversation $conversation): JsonResponse
    {
        $conversation->load(['contact', 'assignedUser', 'messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'in:text,image,document'
        ]);

        try {
            // Enviar mensaje por WhatsApp
            $result = $this->whatsappService->sendTextMessage(
                $conversation->contact->phone_number,
                $request->message
            );

            if ($result['success']) {
                // Guardar mensaje en la base de datos
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => auth()->id(),
                    'whatsapp_message_id' => $result['message_id'],
                    'direction' => 'outbound',
                    'type' => $request->get('type', 'text'),
                    'content' => $request->message,
                    'is_automated' => false,
                    'status' => 'sent'
                ]);

                $conversation->incrementMessageCount();

                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje enviado correctamente',
                    'data' => $message
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar mensaje',
                    'error' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign conversation to user
     */
    public function assign(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = \App\Models\User::find($request->user_id);
        $conversation->assignTo($user);

        return response()->json([
            'success' => true,
            'message' => 'Conversación asignada correctamente'
        ]);
    }

    /**
     * Close conversation
     */
    public function close(Conversation $conversation): JsonResponse
    {
        $conversation->close();

        return response()->json([
            'success' => true,
            'message' => 'Conversación cerrada correctamente'
        ]);
    }

    /**
     * Get conversation messages
     */
    public function messages(Conversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }
}
