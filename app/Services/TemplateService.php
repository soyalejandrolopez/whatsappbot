<?php

namespace App\Services;

use Illuminate\Support\Facades\Lang;

class TemplateService
{
    /**
     * Get welcome message template
     */
    public function getWelcomeTemplate($contactName = null)
    {
        $greeting = Lang::get('chatbot.welcome.greeting');
        $introduction = Lang::get('chatbot.welcome.introduction');
        $menuPrompt = Lang::get('chatbot.welcome.menu_prompt');
        
        $message = $greeting;
        if ($contactName) {
            $message = str_replace('¡Hola!', "¡Hola {$contactName}!", $message);
        }
        
        $message .= "\n\n" . $introduction;
        $message .= "\n\n" . $menuPrompt;
        
        return [
            'type' => 'interactive',
            'text' => $message,
            'buttons' => [
                ['id' => 'products', 'title' => Lang::get('chatbot.welcome.options.products')],
                ['id' => 'support', 'title' => Lang::get('chatbot.welcome.options.support')],
                ['id' => 'sales', 'title' => Lang::get('chatbot.welcome.options.sales')],
                ['id' => 'agent', 'title' => Lang::get('chatbot.welcome.options.agent')]
            ]
        ];
    }

    /**
     * Get main menu template
     */
    public function getMainMenuTemplate()
    {
        return [
            'type' => 'interactive',
            'text' => 'Selecciona una opción:',
            'buttons' => [
                ['id' => 'products', 'title' => '📦 Productos'],
                ['id' => 'support', 'title' => '🔧 Soporte'],
                ['id' => 'sales', 'title' => '💰 Ventas'],
                ['id' => 'hours', 'title' => '🕒 Horarios'],
                ['id' => 'agent', 'title' => '👤 Agente']
            ]
        ];
    }

    /**
     * Get products template
     */
    public function getProductsTemplate()
    {
        $title = Lang::get('chatbot.products.title');
        
        return [
            'type' => 'list',
            'text' => $title,
            'button_text' => 'Ver opciones',
            'sections' => [
                [
                    'title' => Lang::get('chatbot.products.categories.software.title'),
                    'rows' => [
                        [
                            'id' => 'crm',
                            'title' => 'CRM',
                            'description' => 'Sistema de gestión de clientes'
                        ],
                        [
                            'id' => 'erp',
                            'title' => 'ERP',
                            'description' => 'Planificación de recursos empresariales'
                        ]
                    ]
                ],
                [
                    'title' => Lang::get('chatbot.products.categories.services.title'),
                    'rows' => [
                        [
                            'id' => 'consulting',
                            'title' => 'Consultoría',
                            'description' => 'Asesoría especializada'
                        ],
                        [
                            'id' => 'development',
                            'title' => 'Desarrollo',
                            'description' => 'Desarrollo a medida'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get product details template
     */
    public function getProductDetailsTemplate($productId)
    {
        $products = [
            'crm' => [
                'name' => Lang::get('chatbot.products.categories.software.crm.name'),
                'description' => Lang::get('chatbot.products.categories.software.crm.description'),
                'price' => Lang::get('chatbot.products.categories.software.crm.price')
            ],
            'erp' => [
                'name' => Lang::get('chatbot.products.categories.software.erp.name'),
                'description' => Lang::get('chatbot.products.categories.software.erp.description'),
                'price' => Lang::get('chatbot.products.categories.software.erp.price')
            ],
            'consulting' => [
                'name' => Lang::get('chatbot.products.categories.services.consulting.name'),
                'description' => Lang::get('chatbot.products.categories.services.consulting.description'),
                'price' => Lang::get('chatbot.products.categories.services.consulting.price')
            ],
            'development' => [
                'name' => Lang::get('chatbot.products.categories.services.development.name'),
                'description' => Lang::get('chatbot.products.categories.services.development.description'),
                'price' => Lang::get('chatbot.products.categories.services.development.price')
            ]
        ];

        if (!isset($products[$productId])) {
            return null;
        }

        $product = $products[$productId];
        $message = "🔧 *{$product['name']}*\n\n{$product['description']}\n\n💰 Precio: {$product['price']}\n\n¿Te gustaría más información?";

        return [
            'type' => 'interactive',
            'text' => $message,
            'buttons' => [
                ['id' => 'demo', 'title' => Lang::get('chatbot.products.actions.demo')],
                ['id' => 'quote', 'title' => Lang::get('chatbot.products.actions.quote')],
                ['id' => 'agent', 'title' => Lang::get('chatbot.products.actions.agent')],
                ['id' => 'menu', 'title' => Lang::get('chatbot.products.actions.menu')]
            ]
        ];
    }

    /**
     * Get support template
     */
    public function getSupportTemplate()
    {
        $title = Lang::get('chatbot.support.title');
        $greeting = Lang::get('chatbot.support.greeting');

        return [
            'type' => 'interactive',
            'text' => "{$title}\n\n{$greeting}\n\n¿Qué tipo de problema tienes?",
            'buttons' => [
                ['id' => 'login', 'title' => Lang::get('chatbot.support.problem_types.login')],
                ['id' => 'functionality', 'title' => Lang::get('chatbot.support.problem_types.functionality')],
                ['id' => 'performance', 'title' => Lang::get('chatbot.support.problem_types.performance')],
                ['id' => 'other', 'title' => Lang::get('chatbot.support.problem_types.other')]
            ]
        ];
    }

    /**
     * Get support solution template
     */
    public function getSupportSolutionTemplate($problemType)
    {
        $solutions = [
            'login' => [
                'title' => Lang::get('chatbot.support.solutions.login.title'),
                'content' => Lang::get('chatbot.support.solutions.login.steps')
            ],
            'functionality' => [
                'title' => Lang::get('chatbot.support.solutions.functionality.title'),
                'content' => Lang::get('chatbot.support.solutions.functionality.questions')
            ],
            'performance' => [
                'title' => Lang::get('chatbot.support.solutions.performance.title'),
                'content' => Lang::get('chatbot.support.solutions.performance.tips')
            ]
        ];

        if (!isset($solutions[$problemType])) {
            return null;
        }

        $solution = $solutions[$problemType];

        return [
            'type' => 'interactive',
            'text' => $solution['content'],
            'buttons' => [
                ['id' => 'resolved_yes', 'title' => Lang::get('chatbot.support.resolution_options.yes')],
                ['id' => 'resolved_no', 'title' => Lang::get('chatbot.support.resolution_options.no')],
                ['id' => 'resolved_partial', 'title' => Lang::get('chatbot.support.resolution_options.partial')]
            ]
        ];
    }

    /**
     * Get business hours template
     */
    public function getBusinessHoursTemplate()
    {
        $title = Lang::get('chatbot.info.business_hours.title');
        $schedule = Lang::get('chatbot.info.business_hours.schedule');
        $timezone = Lang::get('chatbot.info.business_hours.timezone');

        return [
            'type' => 'text',
            'text' => "{$title}\n\n{$schedule}\n\n{$timezone}"
        ];
    }

    /**
     * Get satisfaction survey template
     */
    public function getSatisfactionSurveyTemplate()
    {
        $request = Lang::get('chatbot.satisfaction.request');
        $scale = Lang::get('chatbot.satisfaction.scale');

        return [
            'type' => 'interactive',
            'text' => "{$request}\n\n{$scale}",
            'buttons' => [
                ['id' => 'rating_5', 'title' => '⭐⭐⭐⭐⭐ (5)'],
                ['id' => 'rating_4', 'title' => '⭐⭐⭐⭐ (4)'],
                ['id' => 'rating_3', 'title' => '⭐⭐⭐ (3)'],
                ['id' => 'rating_2', 'title' => '⭐⭐ (2)'],
                ['id' => 'rating_1', 'title' => '⭐ (1)']
            ]
        ];
    }

    /**
     * Get satisfaction response template
     */
    public function getSatisfactionResponseTemplate($rating)
    {
        $thanks = Lang::get('chatbot.satisfaction.thanks');
        $ratingResponse = Lang::get("chatbot.satisfaction.ratings.{$rating}");

        return [
            'type' => 'text',
            'text' => "{$thanks}\n\n{$ratingResponse}"
        ];
    }

    /**
     * Get farewell template
     */
    public function getFarewellTemplate()
    {
        $thanks = Lang::get('chatbot.farewell.thanks');
        $futureHelp = Lang::get('chatbot.farewell.future_help');
        $goodbye = Lang::get('chatbot.farewell.goodbye');

        return [
            'type' => 'text',
            'text' => "{$thanks}\n\n{$futureHelp}\n\n{$goodbye}"
        ];
    }

    /**
     * Get error template
     */
    public function getErrorTemplate($errorType = 'not_understood')
    {
        $message = Lang::get("chatbot.errors.{$errorType}");

        return [
            'type' => 'text',
            'text' => $message
        ];
    }

    /**
     * Get transfer template
     */
    public function getTransferTemplate($agentName = null)
    {
        if ($agentName) {
            $message = Lang::get('chatbot.transfer.agent_assigned', ['agent_name' => $agentName]);
        } else {
            $message = Lang::get('chatbot.transfer.to_agent');
        }

        return [
            'type' => 'text',
            'text' => $message
        ];
    }

    /**
     * Get demo request template
     */
    public function getDemoRequestTemplate()
    {
        $title = Lang::get('chatbot.sales.demo_request.title');
        $infoNeeded = Lang::get('chatbot.sales.demo_request.info_needed');
        $agentTransfer = Lang::get('chatbot.sales.demo_request.agent_transfer');

        return [
            'type' => 'text',
            'text' => "{$title}\n\n{$infoNeeded}\n\n{$agentTransfer}"
        ];
    }

    /**
     * Get quote request template
     */
    public function getQuoteRequestTemplate()
    {
        $title = Lang::get('chatbot.sales.quote_request.title');
        $preparation = Lang::get('chatbot.sales.quote_request.preparation');
        $infoList = Lang::get('chatbot.sales.quote_request.info_list');

        return [
            'type' => 'text',
            'text' => "{$title}\n\n{$preparation}\n\n{$infoList}"
        ];
    }

    /**
     * Process template variables
     */
    public function processVariables($template, $variables = [])
    {
        if (isset($template['text'])) {
            foreach ($variables as $key => $value) {
                $template['text'] = str_replace("{{$key}}", $value, $template['text']);
            }
        }

        return $template;
    }

    /**
     * Get template by key
     */
    public function getTemplate($key, $params = [])
    {
        return match($key) {
            'welcome' => $this->getWelcomeTemplate($params['name'] ?? null),
            'main_menu' => $this->getMainMenuTemplate(),
            'products' => $this->getProductsTemplate(),
            'product_details' => $this->getProductDetailsTemplate($params['product_id'] ?? null),
            'support' => $this->getSupportTemplate(),
            'support_solution' => $this->getSupportSolutionTemplate($params['problem_type'] ?? null),
            'business_hours' => $this->getBusinessHoursTemplate(),
            'satisfaction_survey' => $this->getSatisfactionSurveyTemplate(),
            'satisfaction_response' => $this->getSatisfactionResponseTemplate($params['rating'] ?? 3),
            'farewell' => $this->getFarewellTemplate(),
            'error' => $this->getErrorTemplate($params['error_type'] ?? 'not_understood'),
            'transfer' => $this->getTransferTemplate($params['agent_name'] ?? null),
            'demo_request' => $this->getDemoRequestTemplate(),
            'quote_request' => $this->getQuoteRequestTemplate(),
            default => $this->getErrorTemplate()
        };
    }
}
