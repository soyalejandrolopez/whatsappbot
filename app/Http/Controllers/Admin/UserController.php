<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of users with advanced filtering
     */
    public function index(Request $request)
    {
        $query = User::with(['assignedConversations' => function($q) {
            $q->where('status', 'active');
        }]);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);

        // Estadísticas
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'agents_count' => User::where('role', 'agent')->count(),
            'admins_count' => User::where('role', 'admin')->count(),
            'online_users' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['admin', 'agent', 'supervisor'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,agent,supervisor',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', true),
                'whatsapp_notifications' => $request->boolean('whatsapp_notifications', false),
                'email_notifications' => $request->boolean('email_notifications', true),
                'created_by' => Auth::id(),
            ]);

            // Enviar notificación de bienvenida por WhatsApp si está habilitada
            if ($user->whatsapp_notifications && $user->phone) {
                $this->sendWelcomeNotification($user);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el usuario. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load([
            'assignedConversations.contact',
            'sentMessages' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        // Estadísticas del usuario
        $userStats = [
            'total_conversations' => $user->assignedConversations->count(),
            'active_conversations' => $user->assignedConversations->where('status', 'active')->count(),
            'closed_conversations' => $user->assignedConversations->where('status', 'closed')->count(),
            'total_messages_sent' => $user->sentMessages->count(),
            'avg_response_time' => $this->getAverageResponseTime($user),
            'satisfaction_rating' => $this->getAverageSatisfaction($user),
            'last_activity' => $user->last_activity,
        ];

        // Actividad reciente
        $recentActivity = $this->getRecentActivity($user);

        // Métricas de rendimiento
        $performanceMetrics = $this->getPerformanceMetrics($user);

        return view('admin.users.show', compact('user', 'userStats', 'recentActivity', 'performanceMetrics'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = ['admin', 'agent', 'supervisor'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,agent,supervisor',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'is_active' => $request->boolean('is_active'),
                'whatsapp_notifications' => $request->boolean('whatsapp_notifications'),
                'email_notifications' => $request->boolean('email_notifications'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el usuario. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Verificar si tiene conversaciones activas
            $activeConversations = $user->assignedConversations()
                ->whereIn('status', ['active', 'pending'])
                ->count();

            if ($activeConversations > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el usuario porque tiene conversaciones activas asignadas.');
            }

            // No eliminar al usuario actual
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'No puedes eliminar tu propia cuenta.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el usuario. Inténtalo de nuevo.');
        }
    }

    /**
     * Send WhatsApp notification to user
     */
    public function sendNotification(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:4096',
            'type' => 'required|in:info,warning,urgent'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if (!$user->whatsapp_notifications || !$user->phone) {
                return response()->json([
                    'success' => false,
                    'error' => 'El usuario no tiene habilitadas las notificaciones de WhatsApp o no tiene teléfono configurado'
                ], 400);
            }

            $message = "🔔 *Notificación del Sistema*\n\n" . $request->message;
            
            if ($request->type === 'urgent') {
                $message = "🚨 *URGENTE* 🚨\n\n" . $request->message;
            } elseif ($request->type === 'warning') {
                $message = "⚠️ *Advertencia*\n\n" . $request->message;
            }

            $result = $this->whatsappService->sendTextMessage($user->phone, $message);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notificación enviada exitosamente',
                    'message_id' => $result['message_id']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Error al enviar notificación'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error sending notification to user: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        try {
            // No desactivar al usuario actual
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No puedes desactivar tu propia cuenta'
                ], 400);
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'message' => "Usuario {$status} exitosamente",
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar estado del usuario'
            ], 500);
        }
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,send_notification',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'message' => 'required_if:action,send_notification|string|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $users = User::whereIn('id', $request->user_ids)
                ->where('id', '!=', Auth::id()); // Excluir usuario actual
            $count = 0;

            switch ($request->action) {
                case 'activate':
                    $count = $users->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $count = $users->update(['is_active' => false]);
                    break;
                case 'delete':
                    $count = $users->delete();
                    break;
                case 'send_notification':
                    $count = $this->sendBulkNotifications($users->get(), $request->message);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "Acción aplicada a {$count} usuarios exitosamente.",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al ejecutar la acción masiva.'
            ], 500);
        }
    }

    // Métodos privados

    private function sendWelcomeNotification(User $user)
    {
        try {
            $message = "¡Bienvenido/a {$user->name}! 👋\n\n";
            $message .= "Tu cuenta ha sido creada exitosamente en el sistema de chatbot.\n";
            $message .= "Rol: " . ucfirst($user->role) . "\n";
            $message .= "Email: {$user->email}\n\n";
            $message .= "¡Ya puedes comenzar a usar el sistema!";

            $this->whatsappService->sendTextMessage($user->phone, $message);
        } catch (\Exception $e) {
            Log::error('Error sending welcome notification: ' . $e->getMessage());
        }
    }

    private function sendBulkNotifications($users, $message)
    {
        $count = 0;
        $notificationMessage = "🔔 *Notificación del Sistema*\n\n" . $message;

        foreach ($users as $user) {
            if ($user->whatsapp_notifications && $user->phone) {
                try {
                    $result = $this->whatsappService->sendTextMessage($user->phone, $notificationMessage);
                    if ($result['success']) {
                        $count++;
                    }
                } catch (\Exception $e) {
                    Log::error("Error sending notification to user {$user->id}: " . $e->getMessage());
                }
            }
        }

        return $count;
    }

    private function getAverageResponseTime(User $user)
    {
        return Message::where('sent_by', $user->id)
            ->where('direction', 'outgoing')
            ->whereNotNull('response_time_seconds')
            ->avg('response_time_seconds');
    }

    private function getAverageSatisfaction(User $user)
    {
        return Conversation::where('assigned_user_id', $user->id)
            ->whereNotNull('satisfaction_rating')
            ->avg('satisfaction_rating');
    }

    private function getRecentActivity(User $user)
    {
        return Message::where('sent_by', $user->id)
            ->with(['conversation.contact'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getPerformanceMetrics(User $user)
    {
        $thisMonth = now()->startOfMonth();
        
        return [
            'conversations_handled_this_month' => Conversation::where('assigned_user_id', $user->id)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'conversations_closed_this_month' => Conversation::where('assigned_user_id', $user->id)
                ->where('status', 'closed')
                ->where('ended_at', '>=', $thisMonth)
                ->count(),
            'messages_sent_this_month' => Message::where('sent_by', $user->id)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'avg_satisfaction_this_month' => Conversation::where('assigned_user_id', $user->id)
                ->where('ended_at', '>=', $thisMonth)
                ->whereNotNull('satisfaction_rating')
                ->avg('satisfaction_rating'),
        ];
    }
}
