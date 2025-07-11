<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotResponse;
use App\Models\ChatbotFlow;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotResponseController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of chatbot responses
     */
    public function index(Request $request)
    {
        $query = ChatbotResponse::with('creator');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $responses = $query->paginate(20);

        // Estadísticas
        $stats = [
            'total_responses' => ChatbotResponse::count(),
            'active_responses' => ChatbotResponse::where('is_active', true)->count(),
            'inactive_responses' => ChatbotResponse::where('is_active', false)->count(),
            'categories_count' => ChatbotResponse::distinct('category')->count(),
            'most_used' => ChatbotResponse::orderBy('usage_count', 'desc')->first(),
        ];

        // Categorías disponibles
        $categories = ChatbotResponse::distinct('category')
            ->pluck('category')
            ->filter()
            ->sort();

        return view('admin.chatbot-responses', compact('responses', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new response
     */
    public function create()
    {
        $categories = ChatbotResponse::distinct('category')
            ->pluck('category')
            ->filter()
            ->sort();

        $flows = ChatbotFlow::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.chatbot-responses.create', compact('categories', 'flows'));
    }

    /**
     * Store a newly created response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:chatbot_responses,key',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'type' => 'required|in:text,template,interactive,media',
            'language' => 'required|string|max:5',
            'triggers' => 'nullable|array',
            'variables' => 'nullable|array',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'required|integer|min:1|max:10',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $response = ChatbotResponse::create([
                'key' => $request->key,
                'content' => $request->content,
                'category' => $request->category,
                'type' => $request->type,
                'language' => $request->language,
                'triggers' => $request->triggers ?? [],
                'variables' => $request->variables ?? [],
                'conditions' => $request->conditions ?? [],
                'is_active' => $request->boolean('is_active', true),
                'priority' => $request->priority,
                'metadata' => $request->metadata ?? [],
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.chatbot-responses.index')
                ->with('success', 'Respuesta del chatbot creada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error creating chatbot response: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la respuesta. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Display the specified response
     */
    public function show(ChatbotResponse $chatbotResponse)
    {
        $chatbotResponse->load('creator');

        // Estadísticas de uso
        $usageStats = [
            'total_usage' => $chatbotResponse->usage_count,
            'last_used' => $chatbotResponse->last_used_at,
            'effectiveness_rate' => $this->calculateEffectivenessRate($chatbotResponse),
            'avg_response_time' => $this->getAverageResponseTime($chatbotResponse),
        ];

        // Flujos que usan esta respuesta
        $relatedFlows = ChatbotFlow::whereJsonContains('flow_steps', ['response_key' => $chatbotResponse->key])
            ->get();

        return view('admin.chatbot-responses.show', compact('chatbotResponse', 'usageStats', 'relatedFlows'));
    }

    /**
     * Show the form for editing the specified response
     */
    public function edit(ChatbotResponse $chatbotResponse)
    {
        $categories = ChatbotResponse::distinct('category')
            ->pluck('category')
            ->filter()
            ->sort();

        $flows = ChatbotFlow::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.chatbot-responses.edit', compact('chatbotResponse', 'categories', 'flows'));
    }

    /**
     * Update the specified response
     */
    public function update(Request $request, ChatbotResponse $chatbotResponse)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:chatbot_responses,key,' . $chatbotResponse->id,
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'type' => 'required|in:text,template,interactive,media',
            'language' => 'required|string|max:5',
            'triggers' => 'nullable|array',
            'variables' => 'nullable|array',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'required|integer|min:1|max:10',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $chatbotResponse->update([
                'key' => $request->key,
                'content' => $request->content,
                'category' => $request->category,
                'type' => $request->type,
                'language' => $request->language,
                'triggers' => $request->triggers ?? [],
                'variables' => $request->variables ?? [],
                'conditions' => $request->conditions ?? [],
                'is_active' => $request->boolean('is_active'),
                'priority' => $request->priority,
                'metadata' => $request->metadata ?? [],
            ]);

            return redirect()->route('admin.chatbot-responses.index')
                ->with('success', 'Respuesta del chatbot actualizada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error updating chatbot response: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la respuesta. Inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Remove the specified response
     */
    public function destroy(ChatbotResponse $chatbotResponse)
    {
        try {
            // Verificar si está siendo usada en algún flujo
            $usedInFlows = ChatbotFlow::whereJsonContains('flow_steps', ['response_key' => $chatbotResponse->key])
                ->count();

            if ($usedInFlows > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la respuesta porque está siendo usada en flujos activos.');
            }

            $chatbotResponse->delete();

            return redirect()->route('admin.chatbot-responses.index')
                ->with('success', 'Respuesta del chatbot eliminada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error deleting chatbot response: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la respuesta. Inténtalo de nuevo.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggle(ChatbotResponse $chatbotResponse)
    {
        try {
            $chatbotResponse->update(['is_active' => !$chatbotResponse->is_active]);

            $status = $chatbotResponse->is_active ? 'activada' : 'desactivada';

            return response()->json([
                'success' => true,
                'message' => "Respuesta {$status} exitosamente.",
                'is_active' => $chatbotResponse->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling chatbot response: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar el estado de la respuesta.'
            ], 500);
        }
    }

    /**
     * Test response by sending it via WhatsApp
     */
    public function test(Request $request, ChatbotResponse $chatbotResponse)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $content = $this->processResponseContent($chatbotResponse, $request->variables ?? []);
            
            $result = $this->sendResponseViaWhatsApp(
                $request->phone_number,
                $chatbotResponse,
                $content
            );

            if ($result['success']) {
                // Incrementar contador de uso para pruebas
                $chatbotResponse->increment('usage_count');
                $chatbotResponse->update(['last_used_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Respuesta enviada exitosamente para prueba',
                    'message_id' => $result['message_id']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Error al enviar respuesta de prueba'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error testing chatbot response: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Duplicate response
     */
    public function duplicate(ChatbotResponse $chatbotResponse)
    {
        try {
            $newResponse = $chatbotResponse->replicate();
            $newResponse->key = $chatbotResponse->key . '_copy_' . time();
            $newResponse->is_active = false;
            $newResponse->usage_count = 0;
            $newResponse->last_used_at = null;
            $newResponse->created_by = Auth::id();
            $newResponse->save();

            return redirect()->route('admin.chatbot-responses.edit', $newResponse)
                ->with('success', 'Respuesta duplicada exitosamente. Edita los detalles necesarios.');

        } catch (\Exception $e) {
            Log::error('Error duplicating chatbot response: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al duplicar la respuesta. Inténtalo de nuevo.');
        }
    }

    /**
     * Get responses by category (AJAX)
     */
    public function getByCategory(Request $request)
    {
        $category = $request->get('category');
        
        $responses = ChatbotResponse::where('category', $category)
            ->where('is_active', true)
            ->orderBy('key')
            ->get(['id', 'key', 'content', 'type']);

        return response()->json($responses);
    }

    /**
     * Bulk action for responses
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,change_category',
            'response_ids' => 'required|array',
            'response_ids.*' => 'exists:chatbot_responses,id',
            'new_category' => 'required_if:action,change_category|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $responses = ChatbotResponse::whereIn('id', $request->response_ids);
            $count = 0;

            switch ($request->action) {
                case 'activate':
                    $count = $responses->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $count = $responses->update(['is_active' => false]);
                    break;
                case 'delete':
                    $count = $responses->delete();
                    break;
                case 'change_category':
                    $count = $responses->update(['category' => $request->new_category]);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "Acción aplicada a {$count} respuestas exitosamente.",
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

    private function processResponseContent(ChatbotResponse $response, array $variables = [])
    {
        $content = $response->content;
        
        // Reemplazar variables en el contenido
        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        
        // Reemplazar variables predefinidas
        $predefinedVars = [
            '{{now}}' => now()->format('Y-m-d H:i:s'),
            '{{date}}' => now()->format('Y-m-d'),
            '{{time}}' => now()->format('H:i:s'),
        ];
        
        foreach ($predefinedVars as $var => $value) {
            $content = str_replace($var, $value, $content);
        }
        
        return $content;
    }

    private function sendResponseViaWhatsApp($phoneNumber, ChatbotResponse $response, $content)
    {
        switch ($response->type) {
            case 'text':
                return $this->whatsappService->sendTextMessage($phoneNumber, $content);
                
            case 'interactive':
                $metadata = $response->metadata ?? [];
                $buttons = $metadata['buttons'] ?? [];
                return $this->whatsappService->sendInteractiveMessage($phoneNumber, $content, $buttons);
                
            case 'template':
                $metadata = $response->metadata ?? [];
                $templateName = $metadata['template_name'] ?? null;
                $language = $response->language ?? 'es';
                
                if ($templateName) {
                    return $this->whatsappService->sendTemplateMessage($phoneNumber, $templateName, $language);
                }
                break;
                
            default:
                return $this->whatsappService->sendTextMessage($phoneNumber, $content);
        }
        
        return ['success' => false, 'error' => 'Tipo de respuesta no soportado'];
    }

    private function calculateEffectivenessRate(ChatbotResponse $response)
    {
        // Lógica para calcular la efectividad de la respuesta
        // Por ejemplo, basado en satisfacción del usuario o conversiones
        return 85; // Placeholder
    }

    private function getAverageResponseTime(ChatbotResponse $response)
    {
        // Lógica para calcular el tiempo promedio de respuesta
        return 1.5; // Placeholder en segundos
    }
}
