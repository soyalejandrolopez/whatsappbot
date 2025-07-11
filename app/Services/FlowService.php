<?php

namespace App\Services;

use App\Models\ChatbotFlow;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class FlowService
{
    protected $whatsappService;
    protected $chatbotService;

    public function __construct(WhatsAppService $whatsappService, ChatbotService $chatbotService)
    {
        $this->whatsappService = $whatsappService;
        $this->chatbotService = $chatbotService;
    }

    /**
     * Crear flujo de bienvenida
     */
    public function createWelcomeFlow($userId)
    {
        return ChatbotFlow::create([
            'name' => 'Flujo de Bienvenida Avanzado',
            'description' => 'Flujo de bienvenida con opciones interactivas',
            'trigger_type' => 'welcome',
            'trigger_conditions' => [
                'is_new_conversation' => true
            ],
            'flow_steps' => [
                [
                    'step' => 1,
                    'type' => 'message',
                    'response_key' => 'welcome',
                    'next_step' => 2
                ],
                [
                    'step' => 2,
                    'type' => 'interactive_menu',
                    'content' => '¿En qué puedo ayudarte hoy?',
                    'options' => [
                        ['id' => 'productos', 'title' => '📦 Ver productos'],
                        ['id' => 'soporte', 'title' => '🔧 Soporte técnico'],
                        ['id' => 'ventas', 'title' => '💰 Información de ventas'],
                        ['id' => 'agente', 'title' => '👤 Hablar con agente']
                    ],
                    'next_step' => 3
                ],
                [
                    'step' => 3,
                    'type' => 'process_selection',
                    'conditions' => [
                        ['input' => 'productos', 'next_flow' => 'products_flow'],
                        ['input' => 'soporte', 'next_flow' => 'support_flow'],
                        ['input' => 'ventas', 'next_flow' => 'sales_flow'],
                        ['input' => 'agente', 'action' => 'transfer_to_agent']
                    ],
                    'default_action' => 'not_understood'
                ]
            ],
            'language' => 'es',
            'is_active' => true,
            'priority' => 100,
            'created_by' => $userId
        ]);
    }

    /**
     * Crear flujo de productos
     */
    public function createProductsFlow($userId)
    {
        return ChatbotFlow::create([
            'name' => 'Información de Productos',
            'description' => 'Flujo para mostrar información detallada de productos',
            'trigger_type' => 'menu_option',
            'trigger_conditions' => [
                'menu_option' => 'productos',
                'keywords' => ['productos', 'producto', 'catálogo', 'servicios']
            ],
            'flow_steps' => [
                [
                    'step' => 1,
                    'type' => 'message',
                    'content' => '📦 *Nuestros productos y servicios:*',
                    'next_step' => 2
                ],
                [
                    'step' => 2,
                    'type' => 'interactive_list',
                    'content' => 'Selecciona una categoría para más información:',
                    'button_text' => 'Ver opciones',
                    'sections' => [
                        [
                            'title' => 'Software',
                            'rows' => [
                                ['id' => 'crm', 'title' => 'CRM', 'description' => 'Sistema de gestión de clientes'],
                                ['id' => 'erp', 'title' => 'ERP', 'description' => 'Planificación de recursos empresariales']
                            ]
                        ],
                        [
                            'title' => 'Servicios',
                            'rows' => [
                                ['id' => 'consultoria', 'title' => 'Consultoría', 'description' => 'Asesoría especializada'],
                                ['id' => 'desarrollo', 'title' => 'Desarrollo', 'description' => 'Desarrollo a medida']
                            ]
                        ]
                    ],
                    'next_step' => 3
                ],
                [
                    'step' => 3,
                    'type' => 'process_selection',
                    'conditions' => [
                        [
                            'input' => 'crm',
                            'response' => '🔧 *CRM - Sistema de Gestión de Clientes*\n\n✅ Gestión completa de contactos\n✅ Seguimiento de ventas\n✅ Automatización de marketing\n✅ Reportes avanzados\n\n💰 Precio: Desde $299/mes\n\n¿Te gustaría una demostración?',
                            'next_step' => 4
                        ],
                        [
                            'input' => 'erp',
                            'response' => '📊 *ERP - Planificación de Recursos*\n\n✅ Gestión financiera\n✅ Control de inventarios\n✅ Recursos humanos\n✅ Producción\n\n💰 Precio: Desde $599/mes\n\n¿Te gustaría más información?',
                            'next_step' => 4
                        ],
                        [
                            'input' => 'consultoria',
                            'response' => '👨‍💼 *Consultoría Especializada*\n\n✅ Análisis de procesos\n✅ Transformación digital\n✅ Optimización de sistemas\n✅ Capacitación\n\n💰 Precio: $150/hora\n\n¿Necesitas una consulta?',
                            'next_step' => 4
                        ],
                        [
                            'input' => 'desarrollo',
                            'response' => '💻 *Desarrollo a Medida*\n\n✅ Aplicaciones web\n✅ Apps móviles\n✅ Integraciones\n✅ APIs\n\n💰 Precio: Cotización personalizada\n\n¿Tienes un proyecto en mente?',
                            'next_step' => 4
                        ]
                    ],
                    'default_action' => 'not_understood'
                ],
                [
                    'step' => 4,
                    'type' => 'interactive_menu',
                    'content' => '¿Qué te gustaría hacer ahora?',
                    'options' => [
                        ['id' => 'demo', 'title' => '🎯 Solicitar demo'],
                        ['id' => 'cotizacion', 'title' => '💰 Pedir cotización'],
                        ['id' => 'agente', 'title' => '👤 Hablar con agente'],
                        ['id' => 'menu', 'title' => '🏠 Menú principal']
                    ],
                    'next_step' => 5
                ],
                [
                    'step' => 5,
                    'type' => 'process_selection',
                    'conditions' => [
                        ['input' => 'demo', 'action' => 'collect_demo_info'],
                        ['input' => 'cotizacion', 'action' => 'collect_quote_info'],
                        ['input' => 'agente', 'action' => 'transfer_to_agent'],
                        ['input' => 'menu', 'next_flow' => 'welcome_flow']
                    ],
                    'default_action' => 'not_understood'
                ]
            ],
            'language' => 'es',
            'is_active' => true,
            'priority' => 50,
            'created_by' => $userId
        ]);
    }

    /**
     * Crear flujo de soporte
     */
    public function createSupportFlow($userId)
    {
        return ChatbotFlow::create([
            'name' => 'Soporte Técnico',
            'description' => 'Flujo para atención de problemas técnicos',
            'trigger_type' => 'menu_option',
            'trigger_conditions' => [
                'menu_option' => 'soporte',
                'keywords' => ['problema', 'error', 'ayuda', 'soporte', 'técnico']
            ],
            'flow_steps' => [
                [
                    'step' => 1,
                    'type' => 'message',
                    'content' => '🔧 *Soporte Técnico*\n\nEstoy aquí para ayudarte con cualquier problema técnico.',
                    'next_step' => 2
                ],
                [
                    'step' => 2,
                    'type' => 'interactive_menu',
                    'content' => '¿Qué tipo de problema tienes?',
                    'options' => [
                        ['id' => 'login', 'title' => '🔐 Problemas de acceso'],
                        ['id' => 'funcionalidad', 'title' => '⚙️ Error en funcionalidad'],
                        ['id' => 'rendimiento', 'title' => '🐌 Problemas de velocidad'],
                        ['id' => 'otro', 'title' => '❓ Otro problema']
                    ],
                    'next_step' => 3
                ],
                [
                    'step' => 3,
                    'type' => 'process_selection',
                    'conditions' => [
                        [
                            'input' => 'login',
                            'response' => '🔐 *Problemas de Acceso*\n\nPara ayudarte mejor, por favor:\n\n1️⃣ Verifica tu usuario y contraseña\n2️⃣ Intenta restablecer tu contraseña\n3️⃣ Limpia la caché del navegador\n\n¿El problema persiste?',
                            'next_step' => 4
                        ],
                        [
                            'input' => 'funcionalidad',
                            'response' => '⚙️ *Error en Funcionalidad*\n\nPara diagnosticar el problema:\n\n1️⃣ ¿En qué módulo ocurre?\n2️⃣ ¿Qué mensaje de error aparece?\n3️⃣ ¿Cuándo comenzó el problema?\n\nPor favor describe el problema detalladamente.',
                            'next_step' => 'collect_error_details'
                        ],
                        [
                            'input' => 'rendimiento',
                            'response' => '🐌 *Problemas de Velocidad*\n\nPara mejorar el rendimiento:\n\n1️⃣ Cierra otras aplicaciones\n2️⃣ Verifica tu conexión a internet\n3️⃣ Actualiza tu navegador\n\n¿Necesitas más ayuda?',
                            'next_step' => 4
                        ],
                        [
                            'input' => 'otro',
                            'response' => '❓ *Otro Problema*\n\nPor favor describe tu problema con el mayor detalle posible. Nuestro equipo técnico te ayudará.',
                            'action' => 'transfer_to_agent'
                        ]
                    ],
                    'default_action' => 'not_understood'
                ],
                [
                    'step' => 4,
                    'type' => 'interactive_menu',
                    'content' => '¿Se resolvió tu problema?',
                    'options' => [
                        ['id' => 'si', 'title' => '✅ Sí, está resuelto'],
                        ['id' => 'no', 'title' => '❌ No, necesito más ayuda'],
                        ['id' => 'parcial', 'title' => '🔄 Parcialmente resuelto']
                    ],
                    'next_step' => 5
                ],
                [
                    'step' => 5,
                    'type' => 'process_selection',
                    'conditions' => [
                        [
                            'input' => 'si',
                            'response' => '¡Excelente! 🎉 Me alegra haber podido ayudarte. ¿Hay algo más en lo que pueda asistirte?',
                            'action' => 'end_conversation'
                        ],
                        [
                            'input' => 'no',
                            'response' => 'Entiendo. Te voy a conectar con un especialista técnico que podrá ayudarte mejor.',
                            'action' => 'transfer_to_agent'
                        ],
                        [
                            'input' => 'parcial',
                            'response' => 'Te conectaré con un técnico para completar la solución de tu problema.',
                            'action' => 'transfer_to_agent'
                        ]
                    ],
                    'default_action' => 'transfer_to_agent'
                ]
            ],
            'language' => 'es',
            'is_active' => true,
            'priority' => 50,
            'created_by' => $userId
        ]);
    }

    /**
     * Ejecutar acción especial del flujo
     */
    public function executeFlowAction($conversation, $action, $data = null)
    {
        switch ($action) {
            case 'transfer_to_agent':
                return $this->chatbotService->transferToAgent($conversation, 'Solicitud del usuario');
                
            case 'collect_demo_info':
                return $this->collectDemoInformation($conversation);
                
            case 'collect_quote_info':
                return $this->collectQuoteInformation($conversation);
                
            case 'collect_error_details':
                return $this->collectErrorDetails($conversation);
                
            case 'end_conversation':
                return $this->endConversation($conversation);
                
            default:
                Log::warning('Unknown flow action', ['action' => $action]);
                return false;
        }
    }

    /**
     * Recopilar información para demo
     */
    protected function collectDemoInformation($conversation)
    {
        $message = "🎯 *Solicitud de Demostración*\n\nPara programar tu demo personalizada, necesito algunos datos:\n\n1️⃣ Nombre de tu empresa\n2️⃣ Tu nombre y cargo\n3️⃣ Número de empleados\n4️⃣ Fecha preferida para la demo\n\nPor favor proporciona esta información o te conectaré con un agente de ventas.";
        
        $this->whatsappService->sendTextMessage(
            $conversation->contact->phone_number,
            $message
        );
        
        // Transferir a agente de ventas después de mostrar el mensaje
        return $this->chatbotService->transferToAgent($conversation, 'Solicitud de demo');
    }

    /**
     * Recopilar información para cotización
     */
    protected function collectQuoteInformation($conversation)
    {
        $message = "💰 *Solicitud de Cotización*\n\nPara preparar una cotización personalizada, un agente de ventas se pondrá en contacto contigo.\n\nPor favor ten lista la siguiente información:\n\n• Productos de interés\n• Número de usuarios\n• Funcionalidades específicas\n• Presupuesto aproximado\n• Tiempo de implementación";
        
        $this->whatsappService->sendTextMessage(
            $conversation->contact->phone_number,
            $message
        );
        
        return $this->chatbotService->transferToAgent($conversation, 'Solicitud de cotización');
    }

    /**
     * Recopilar detalles del error
     */
    protected function collectErrorDetails($conversation)
    {
        $message = "🔍 *Recopilación de Detalles*\n\nPara ayudarte mejor, por favor describe:\n\n1️⃣ ¿Qué estabas haciendo cuando ocurrió?\n2️⃣ ¿Qué mensaje de error aparece exactamente?\n3️⃣ ¿Puedes reproducir el error?\n4️⃣ ¿Desde cuándo ocurre?\n\nUn técnico especializado revisará tu caso.";
        
        $this->whatsappService->sendTextMessage(
            $conversation->contact->phone_number,
            $message
        );
        
        return $this->chatbotService->transferToAgent($conversation, 'Problema técnico detallado');
    }

    /**
     * Finalizar conversación
     */
    protected function endConversation($conversation)
    {
        $message = "¡Gracias por contactarnos! 😊\n\nSi necesitas ayuda en el futuro, no dudes en escribirnos.\n\n¡Que tengas un excelente día!";
        
        $this->whatsappService->sendTextMessage(
            $conversation->contact->phone_number,
            $message
        );
        
        $conversation->close();
        
        return true;
    }
}
