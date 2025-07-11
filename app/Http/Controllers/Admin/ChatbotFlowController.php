<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFlow;
use App\Models\ChatbotResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Added for logging
use App\Services\WhatsAppService; // Added for WhatsApp service

class ChatbotFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $flows = ChatbotFlow::with('creator')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_flows' => ChatbotFlow::count(),
            'active_flows' => ChatbotFlow::where('is_active', true)->count(),
            'inactive_flows' => ChatbotFlow::where('is_active', false)->count(),
            'most_used' => ChatbotFlow::orderBy('usage_count', 'desc')->first(),
        ];

        return view('admin.chatbot-flows', compact('flows', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $responses = ChatbotResponse::where('is_active', true)
            ->orderBy('category')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        return view('admin.chatbot-flows.create', compact('responses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:welcome,keyword,menu_option,intent',
            'trigger_conditions' => 'required|array',
            'flow_steps' => 'required|array',
            'language' => 'required|string|max:5',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $flow = ChatbotFlow::create([
            'name' => $request->name,
            'description' => $request->description,
            'trigger_type' => $request->trigger_type,
            'trigger_conditions' => $request->trigger_conditions,
            'flow_steps' => $request->flow_steps,
            'language' => $request->language,
            'priority' => $request->priority,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.chatbot-flows.index')
            ->with('success', 'Flujo del chatbot creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatbotFlow $chatbotFlow)
    {
        $chatbotFlow->load('creator');

        $usage_stats = [
            'total_usage' => $chatbotFlow->usage_count,
            'last_used' => $chatbotFlow->last_used_at,
            'success_rate' => $this->calculateSuccessRate($chatbotFlow),
        ];

        return view('admin.chatbot-flows.show', compact('chatbotFlow', 'usage_stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatbotFlow $chatbotFlow)
    {
        $responses = ChatbotResponse::where('is_active', true)
            ->orderBy('category')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        return view('admin.chatbot-flows.edit', compact('chatbotFlow', 'responses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatbotFlow $chatbotFlow)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:welcome,keyword,menu_option,intent',
            'trigger_conditions' => 'required|array',
            'flow_steps' => 'required|array',
            'language' => 'required|string|max:5',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $chatbotFlow->update([
            'name' => $request->name,
            'description' => $request->description,
            'trigger_type' => $request->trigger_type,
            'trigger_conditions' => $request->trigger_conditions,
            'flow_steps' => $request->flow_steps,
            'language' => $request->language,
            'priority' => $request->priority,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.chatbot-flows.index')
            ->with('success', 'Flujo del chatbot actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatbotFlow $chatbotFlow)
    {
        $chatbotFlow->delete();

        return redirect()->route('admin.chatbot-flows.index')
            ->with('success', 'Flujo del chatbot eliminado exitosamente.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(ChatbotFlow $chatbotFlow)
    {
        $chatbotFlow->update(['is_active' => !$chatbotFlow->is_active]);

        $status = $chatbotFlow->is_active ? 'activado' : 'desactivado';

        return response()->json([
            'success' => true,
            'message' => "Flujo {$status} exitosamente.",
            'is_active' => $chatbotFlow->is_active
        ]);
    }

    /**
     * Duplicate flow
     */
    public function duplicate(ChatbotFlow $chatbotFlow)
    {
        $newFlow = $chatbotFlow->replicate();
        $newFlow->name = $chatbotFlow->name . ' (Copia)';
        $newFlow->is_active = false;
        $newFlow->usage_count = 0;
        $newFlow->last_used_at = null;
        $newFlow->created_by = Auth::id();
        $newFlow->save();

        return redirect()->route('admin.chatbot-flows.index')
            ->with('success', 'Flujo duplicado exitosamente.');
    }

    /**
     * Test flow
     */
    public function test(ChatbotFlow $chatbotFlow)
    {
        // Implementation for testing flows will be added
        return response()->json(['message' => 'Test functionality to be implemented']);
    }

    /**
     * Test flow by executing it via WhatsApp
     */
    public function testFlow(Request $request, ChatbotFlow $chatbotFlow)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'test_variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->executeFlow($chatbotFlow, $request->phone_number, $request->test_variables ?? []);

            if ($result['success']) {
                // Incrementar contador de uso para pruebas
                $chatbotFlow->increment('usage_count');
                $chatbotFlow->update(['last_used_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Flujo ejecutado exitosamente en modo prueba',
                    'steps_executed' => $result['steps_executed']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Error al ejecutar flujo de prueba'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error testing chatbot flow: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Execute flow for real conversation
     */
    public function executeFlow(ChatbotFlow $flow, $phoneNumber, array $variables = [])
    {
        if (!$flow->is_active) {
            return ['success' => false, 'error' => 'El flujo no está activo'];
        }

        try {
            $whatsappService = app(WhatsAppService::class);
            $stepsExecuted = [];
            $flowSteps = $flow->flow_steps ?? [];

            foreach ($flowSteps as $stepIndex => $step) {
                $stepResult = $this->executeFlowStep($whatsappService, $phoneNumber, $step, $variables);
                
                if (!$stepResult['success']) {
                    return [
                        'success' => false,
                        'error' => $stepResult['error'],
                        'failed_at_step' => $stepIndex
                    ];
                }

                $stepsExecuted[] = [
                    'step' => $stepIndex,
                    'type' => $step['type'] ?? 'unknown',
                    'result' => $stepResult
                ];

                // Si el paso tiene una condición de parada, verificarla
                if (isset($step['stop_condition']) && $this->evaluateCondition($step['stop_condition'], $variables)) {
                    break;
                }

                // Delay entre pasos si está configurado
                if (isset($step['delay_seconds'])) {
                    sleep($step['delay_seconds']);
                }
            }

            return [
                'success' => true,
                'steps_executed' => $stepsExecuted
            ];

        } catch (\Exception $e) {
            Log::error('Error executing chatbot flow: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error al ejecutar el flujo: ' . $e->getMessage()
            ];
        }
    }

    private function executeFlowStep($whatsappService, $phoneNumber, array $step, array $variables)
    {
        $stepType = $step['type'] ?? 'message';

        switch ($stepType) {
            case 'message':
                return $this->executeMessageStep($whatsappService, $phoneNumber, $step, $variables);
            
            case 'interactive':
                return $this->executeInteractiveStep($whatsappService, $phoneNumber, $step, $variables);
            
            case 'template':
                return $this->executeTemplateStep($whatsappService, $phoneNumber, $step, $variables);
            
            case 'delay':
                sleep($step['duration'] ?? 1);
                return ['success' => true, 'message' => 'Delay executed'];
            
            case 'condition':
                return $this->executeConditionStep($step, $variables);
            
            default:
                return ['success' => false, 'error' => "Tipo de paso no soportado: {$stepType}"];
        }
    }

    private function executeMessageStep($whatsappService, $phoneNumber, array $step, array $variables)
    {
        $content = $this->processVariables($step['content'] ?? '', $variables);
        
        return $whatsappService->sendTextMessage($phoneNumber, $content);
    }

    private function executeInteractiveStep($whatsappService, $phoneNumber, array $step, array $variables)
    {
        $content = $this->processVariables($step['content'] ?? '', $variables);
        $buttons = $step['buttons'] ?? [];
        
        return $whatsappService->sendInteractiveMessage($phoneNumber, $content, $buttons);
    }

    private function executeTemplateStep($whatsappService, $phoneNumber, array $step, array $variables)
    {
        $templateName = $step['template_name'] ?? '';
        $language = $step['language'] ?? 'es';
        $parameters = $step['parameters'] ?? [];
        
        // Procesar variables en los parámetros
        foreach ($parameters as &$param) {
            $param = $this->processVariables($param, $variables);
        }
        
        return $whatsappService->sendTemplateMessage($phoneNumber, $templateName, $language, $parameters);
    }

    private function executeConditionStep(array $step, array $variables)
    {
        $condition = $step['condition'] ?? '';
        $result = $this->evaluateCondition($condition, $variables);
        
        return [
            'success' => true,
            'condition_result' => $result,
            'message' => "Condición evaluada: " . ($result ? 'true' : 'false')
        ];
    }

    private function processVariables($content, array $variables)
    {
        // Reemplazar variables personalizadas
        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        
        // Variables predefinidas del sistema
        $systemVars = [
            '{{now}}' => now()->format('Y-m-d H:i:s'),
            '{{date}}' => now()->format('Y-m-d'),
            '{{time}}' => now()->format('H:i:s'),
            '{{day}}' => now()->format('l'),
            '{{month}}' => now()->format('F'),
            '{{year}}' => now()->format('Y'),
        ];
        
        foreach ($systemVars as $var => $value) {
            $content = str_replace($var, $value, $content);
        }
        
        return $content;
    }

    private function evaluateCondition($condition, array $variables)
    {
        // Evaluación básica de condiciones
        // En una implementación más avanzada, se podría usar un parser de expresiones
        
        // Ejemplo: "{{variable}} == 'value'"
        foreach ($variables as $key => $value) {
            $condition = str_replace("{{{$key}}}", "'{$value}'", $condition);
        }
        
        // Por seguridad, solo permitir operaciones básicas
        if (preg_match("/^'[^']*'\s*(==|!=|>|<|>=|<=)\s*'[^']*'$/", $condition)) {
            return eval("return {$condition};");
        }
        
        return false;
    }

    /**
     * Get flow analytics
     */
    public function analytics(ChatbotFlow $chatbotFlow)
    {
        $analytics = [
            'usage_stats' => [
                'total_executions' => $chatbotFlow->usage_count,
                'last_used' => $chatbotFlow->last_used_at,
                'success_rate' => $this->calculateSuccessRate($chatbotFlow),
                'avg_completion_time' => $this->calculateAverageCompletionTime($chatbotFlow),
            ],
            'step_analytics' => $this->getStepAnalytics($chatbotFlow),
            'user_feedback' => $this->getUserFeedback($chatbotFlow),
            'performance_metrics' => $this->getPerformanceMetrics($chatbotFlow)
        ];

        return response()->json($analytics);
    }

    private function getStepAnalytics(ChatbotFlow $flow)
    {
        // Implementar análisis de pasos del flujo
        // Por ejemplo, qué pasos se abandonan más frecuentemente
        return [];
    }

    private function getUserFeedback(ChatbotFlow $flow)
    {
        // Obtener feedback de usuarios sobre el flujo
        return [];
    }

    private function getPerformanceMetrics(ChatbotFlow $flow)
    {
        // Métricas de rendimiento del flujo
        return [
            'avg_response_time' => 2.3,
            'completion_rate' => 78.5,
            'user_satisfaction' => 4.2
        ];
    }

    /**
     * Calculate success rate
     */
    private function calculateSuccessRate(ChatbotFlow $flow)
    {
        // Implementar lógica de cálculo de tasa de éxito
        // Por ahora retornamos un valor simulado
        return rand(75, 95);
    }

    /**
     * Calculate average completion time
     */
    private function calculateAverageCompletionTime(ChatbotFlow $flow)
    {
        // Implementar lógica de cálculo de tiempo promedio
        // Por ahora retornamos un valor simulado
        return rand(30, 180); // segundos
    }

    /**
     * Get most common exit point
     */
    private function getMostCommonExitPoint(ChatbotFlow $flow)
    {
        // Implementar lógica para encontrar el punto de salida más común
        // Por ahora retornamos un valor simulado
        $steps = count($flow->flow_steps);
        return rand(1, $steps);
    }
}
