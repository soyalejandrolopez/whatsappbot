<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappContact;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ContactController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of contacts with advanced filtering
     */
    public function index(Request $request)
    {
        $query = WhatsappContact::with(['conversations' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blocked', false)->where('opt_in', true);
            } elseif ($request->status === 'inactive') {
                $query->where('opt_in', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $contacts = $query->paginate(20);

        // Estadísticas
        $stats = [
            'total_contacts' => WhatsappContact::count(),
            'active_contacts' => WhatsappContact::where('is_blocked', false)->where('opt_in', true)->count(),
            'blocked_contacts' => WhatsappContact::where('is_blocked', true)->count(),
            'inactive_contacts' => WhatsappContact::where('opt_in', false)->count(),
            'contacts_today' => WhatsappContact::whereDate('created_at', today())->count(),
            'contacts_this_month' => WhatsappContact::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.contacts', compact('contacts', 'stats'));
    }

    /**
     * Show the form for creating a new contact
     */
    public function create()
    {
        return view('admin.contacts.create');
    }

    /**
     * Store a newly created contact
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:whatsapp_contacts,phone_number',
            'notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
            'is_blocked' => 'boolean',
            'opt_in' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Crear el contacto
            $contact = WhatsappContact::create([
                'name' => $request->name,
                'phone_number' => $this->formatPhoneNumber($request->phone_number),
                'whatsapp_id' => $this->formatPhoneNumber($request->phone_number),
                'notes' => $request->notes,
                'tags' => $request->tags ?? [],
                'is_blocked' => $request->boolean('is_blocked', false),
                'opt_in' => $request->boolean('opt_in', true),
                'language' => 'es',
                'last_interaction_at' => now()
            ]);

            // Obtener información del perfil desde WhatsApp
            $this->syncContactProfile($contact);

            return redirect()->route('admin.contacts.index')
                ->with('success', 'Contacto creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el contacto. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Display the specified contact
     */
    public function show(WhatsappContact $contact)
    {
        $contact->load([
            'conversations.assignedAgent',
            'conversations' => function($q) {
                $q->orderBy('started_at', 'desc');
            }
        ]);

        // Estadísticas del contacto
        $contactStats = [
            'total_conversations' => $contact->conversations->count(),
            'active_conversations' => $contact->conversations->where('status', 'active')->count(),
            'total_messages' => Message::whereHas('conversation', function($q) use ($contact) {
                $q->where('contact_id', $contact->id);
            })->count(),
            'last_interaction' => $contact->conversations->first()?->last_message_at,
            'avg_response_time' => $this->getAverageResponseTime($contact),
            'satisfaction_rating' => $this->getAverageSatisfaction($contact)
        ];

        // Actividad reciente
        $recentActivity = $this->getRecentActivity($contact);

        return view('admin.contacts.show', compact('contact', 'contactStats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified contact
     */
    public function edit(WhatsappContact $contact)
    {
        return view('admin.contacts.edit', compact('contact'));
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, WhatsappContact $contact)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:whatsapp_contacts,phone_number,' . $contact->id,
            'notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
            'is_blocked' => 'boolean',
            'opt_in' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $contact->update([
                'name' => $request->name,
                'phone_number' => $this->formatPhoneNumber($request->phone_number),
                'notes' => $request->notes,
                'tags' => $request->tags ?? [],
                'is_blocked' => $request->boolean('is_blocked'),
                'opt_in' => $request->boolean('opt_in')
            ]);

            return redirect()->route('admin.contacts.index')
                ->with('success', 'Contacto actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el contacto. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Remove the specified contact
     */
    public function destroy(WhatsappContact $contact)
    {
        try {
            // Verificar si tiene conversaciones activas
            $activeConversations = $contact->conversations()
                ->whereIn('status', ['active', 'pending'])
                ->count();

            if ($activeConversations > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el contacto porque tiene conversaciones activas.');
            }

            $contact->delete();

            return redirect()->route('admin.contacts.index')
                ->with('success', 'Contacto eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el contacto. Inténtalo de nuevo.');
        }
    }

    /**
     * Send message to contact
     */
    public function sendMessage(Request $request, WhatsappContact $contact)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:4096',
            'type' => 'required|in:text,template,interactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->whatsappService->sendTextMessage(
                $contact->phone_number,
                $request->message
            );

            if ($result['success']) {
                // Crear o actualizar conversación
                $conversation = $this->getOrCreateConversation($contact);
                
                // Registrar el mensaje
                Message::create([
                    'conversation_id' => $conversation->id,
                    'whatsapp_message_id' => $result['message_id'],
                    'content' => $request->message,
                    'type' => 'text',
                    'direction' => 'outbound',
                    'status' => 'sent',
                    'sender_id' => auth()->id(),
                    'whatsapp_timestamp' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje enviado exitosamente',
                    'message_id' => $result['message_id']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Error al enviar mensaje'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error sending message to contact: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Sync contact profile from WhatsApp
     */
    public function syncProfile(WhatsappContact $contact)
    {
        try {
            $this->syncContactProfile($contact);
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil sincronizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing contact profile: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al sincronizar perfil'
            ], 500);
        }
    }

    /**
     * Block/Unblock contact
     */
    public function toggleBlock(WhatsappContact $contact)
    {
        try {
            $newBlockedStatus = !$contact->is_blocked;
            $contact->update(['is_blocked' => $newBlockedStatus]);

            $action = $newBlockedStatus ? 'bloqueado' : 'desbloqueado';

            return response()->json([
                'success' => true,
                'message' => "Contacto {$action} exitosamente",
                'is_blocked' => $newBlockedStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling contact block: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar estado del contacto'
            ], 500);
        }
    }

    // Métodos privados

    private function formatPhoneNumber($phoneNumber)
    {
        // Limpiar y formatear número de teléfono
        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Agregar código de país si no lo tiene
        if (!str_starts_with($clean, '52') && strlen($clean) === 10) {
            $clean = '52' . $clean;
        }
        
        return $clean;
    }

    private function syncContactProfile(WhatsappContact $contact)
    {
        $profile = $this->whatsappService->getContactProfile($contact->phone_number);
        
        if ($profile) {
            $profileData = $contact->profile_data ?? [];
            $profileData['whatsapp_profile'] = $profile;
            $profileData['last_sync'] = now()->toISOString();
            
            $contact->update([
                'profile_data' => $profileData,
                'profile_name' => $profile['name'] ?? $contact->profile_name
            ]);
        }
    }

    private function getOrCreateConversation(WhatsappContact $contact)
    {
        $conversation = Conversation::where('contact_id', $contact->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'contact_id' => $contact->id,
                'status' => 'active',
                'type' => 'chat',
                'started_at' => now(),
                'last_message_at' => now()
            ]);
        }

        return $conversation;
    }

    private function getAverageResponseTime(WhatsappContact $contact)
    {
        return Message::whereHas('conversation', function($q) use ($contact) {
            $q->where('contact_id', $contact->id);
        })
        ->whereNotNull('response_time_seconds')
        ->avg('response_time_seconds');
    }

    private function getAverageSatisfaction(WhatsappContact $contact)
    {
        return Conversation::where('contact_id', $contact->id)
            ->whereNotNull('satisfaction_rating')
            ->avg('satisfaction_rating');
    }

    private function getRecentActivity(WhatsappContact $contact)
    {
        return Message::whereHas('conversation', function($q) use ($contact) {
            $q->where('contact_id', $contact->id);
        })
        ->with(['conversation', 'sender'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    }
}
