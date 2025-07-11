<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatbot Language Lines
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se utilizan para los mensajes del chatbot
    | y las respuestas automáticas en español.
    |
    */

    // Mensajes de bienvenida
    'welcome' => [
        'greeting' => '¡Hola! 👋 Bienvenido a nuestro servicio de atención al cliente.',
        'introduction' => 'Soy tu asistente virtual y estoy aquí para ayudarte.',
        'menu_prompt' => '¿En qué puedo ayudarte hoy?',
        'options' => [
            'products' => '📦 Ver productos y servicios',
            'support' => '🔧 Soporte técnico',
            'sales' => '💰 Información de ventas',
            'hours' => '🕒 Horarios de atención',
            'agent' => '👤 Hablar con un agente'
        ]
    ],

    // Mensajes de productos
    'products' => [
        'title' => '📦 *Nuestros Productos y Servicios*',
        'categories' => [
            'software' => [
                'title' => 'Software',
                'crm' => [
                    'name' => 'CRM - Sistema de Gestión de Clientes',
                    'description' => '✅ Gestión completa de contactos\n✅ Seguimiento de ventas\n✅ Automatización de marketing\n✅ Reportes avanzados',
                    'price' => 'Desde $299/mes'
                ],
                'erp' => [
                    'name' => 'ERP - Planificación de Recursos',
                    'description' => '✅ Gestión financiera\n✅ Control de inventarios\n✅ Recursos humanos\n✅ Producción',
                    'price' => 'Desde $599/mes'
                ]
            ],
            'services' => [
                'title' => 'Servicios',
                'consulting' => [
                    'name' => 'Consultoría Especializada',
                    'description' => '✅ Análisis de procesos\n✅ Transformación digital\n✅ Optimización de sistemas\n✅ Capacitación',
                    'price' => '$150/hora'
                ],
                'development' => [
                    'name' => 'Desarrollo a Medida',
                    'description' => '✅ Aplicaciones web\n✅ Apps móviles\n✅ Integraciones\n✅ APIs',
                    'price' => 'Cotización personalizada'
                ]
            ]
        ],
        'next_steps' => '¿Qué te gustaría hacer ahora?',
        'actions' => [
            'demo' => '🎯 Solicitar demostración',
            'quote' => '💰 Pedir cotización',
            'agent' => '👤 Hablar con agente',
            'menu' => '🏠 Menú principal'
        ]
    ],

    // Mensajes de soporte
    'support' => [
        'title' => '🔧 *Soporte Técnico*',
        'greeting' => 'Estoy aquí para ayudarte con cualquier problema técnico.',
        'problem_types' => [
            'login' => '🔐 Problemas de acceso',
            'functionality' => '⚙️ Error en funcionalidad',
            'performance' => '🐌 Problemas de velocidad',
            'other' => '❓ Otro problema'
        ],
        'solutions' => [
            'login' => [
                'title' => '🔐 *Problemas de Acceso*',
                'steps' => "Para ayudarte mejor, por favor:\n\n1️⃣ Verifica tu usuario y contraseña\n2️⃣ Intenta restablecer tu contraseña\n3️⃣ Limpia la caché del navegador\n\n¿El problema persiste?"
            ],
            'functionality' => [
                'title' => '⚙️ *Error en Funcionalidad*',
                'questions' => "Para diagnosticar el problema:\n\n1️⃣ ¿En qué módulo ocurre?\n2️⃣ ¿Qué mensaje de error aparece?\n3️⃣ ¿Cuándo comenzó el problema?\n\nPor favor describe el problema detalladamente."
            ],
            'performance' => [
                'title' => '🐌 *Problemas de Velocidad*',
                'tips' => "Para mejorar el rendimiento:\n\n1️⃣ Cierra otras aplicaciones\n2️⃣ Verifica tu conexión a internet\n3️⃣ Actualiza tu navegador\n\n¿Necesitas más ayuda?"
            ]
        ],
        'resolution_check' => '¿Se resolvió tu problema?',
        'resolution_options' => [
            'yes' => '✅ Sí, está resuelto',
            'no' => '❌ No, necesito más ayuda',
            'partial' => '🔄 Parcialmente resuelto'
        ],
        'success' => '¡Excelente! 🎉 Me alegra haber podido ayudarte.',
        'transfer' => 'Te voy a conectar con un especialista técnico que podrá ayudarte mejor.'
    ],

    // Mensajes de ventas
    'sales' => [
        'demo_request' => [
            'title' => '🎯 *Solicitud de Demostración*',
            'info_needed' => "Para programar tu demo personalizada, necesito algunos datos:\n\n1️⃣ Nombre de tu empresa\n2️⃣ Tu nombre y cargo\n3️⃣ Número de empleados\n4️⃣ Fecha preferida para la demo",
            'agent_transfer' => 'Por favor proporciona esta información o te conectaré con un agente de ventas.'
        ],
        'quote_request' => [
            'title' => '💰 *Solicitud de Cotización*',
            'preparation' => "Para preparar una cotización personalizada, un agente de ventas se pondrá en contacto contigo.\n\nPor favor ten lista la siguiente información:",
            'info_list' => "• Productos de interés\n• Número de usuarios\n• Funcionalidades específicas\n• Presupuesto aproximado\n• Tiempo de implementación"
        ]
    ],

    // Información general
    'info' => [
        'business_hours' => [
            'title' => '🕒 *Nuestros horarios de atención son:*',
            'schedule' => "📅 Lunes a Viernes: 9:00 AM - 6:00 PM\n📅 Sábados: 9:00 AM - 2:00 PM\n📅 Domingos: Cerrado",
            'timezone' => '🌎 Zona horaria: México (GMT-6)'
        ],
        'contact' => [
            'phone' => '📞 Teléfono: +52 55 1234 5678',
            'email' => '📧 Email: contacto@empresa.com',
            'website' => '🌐 Sitio web: www.empresa.com'
        ]
    ],

    // Mensajes de transferencia
    'transfer' => [
        'to_agent' => 'Te estoy conectando con uno de nuestros agentes. Por favor espera un momento...',
        'agent_assigned' => 'Te he conectado con :agent_name, uno de nuestros agentes. En un momento te atenderá.',
        'no_agents' => 'En este momento todos nuestros agentes están ocupados. Te contactaremos lo antes posible.',
        'queue_position' => 'Estás en la posición :position de la cola de espera.'
    ],

    // Mensajes de error
    'errors' => [
        'not_understood' => 'Lo siento, no entendí tu mensaje. ¿Podrías reformularlo o elegir una de las opciones del menú?',
        'technical_error' => 'Disculpa, ha ocurrido un error técnico. Por favor, intenta nuevamente en unos momentos.',
        'timeout' => 'Tu sesión ha expirado por inactividad. Escribe cualquier mensaje para comenzar una nueva conversación.',
        'maintenance' => 'Estamos realizando mantenimiento en nuestro sistema. Por favor, intenta más tarde.',
        'invalid_option' => 'Opción no válida. Por favor selecciona una de las opciones disponibles.'
    ],

    // Mensajes de despedida
    'farewell' => [
        'thanks' => '¡Gracias por contactarnos! 😊',
        'future_help' => 'Si necesitas ayuda en el futuro, no dudes en escribirnos.',
        'goodbye' => '¡Que tengas un excelente día!',
        'satisfaction' => '¿Podrías calificar tu experiencia del 1 al 5? (1 = Muy malo, 5 = Excelente)'
    ],

    // Mensajes de satisfacción
    'satisfaction' => [
        'request' => 'Para mejorar nuestro servicio, ¿podrías calificar tu experiencia?',
        'scale' => 'Usa una escala del 1 al 5:\n1 = Muy malo\n2 = Malo\n3 = Regular\n4 = Bueno\n5 = Excelente',
        'thanks' => '¡Gracias por tu calificación! Tu opinión es muy importante para nosotros.',
        'comment_request' => '¿Te gustaría agregar algún comentario adicional?',
        'ratings' => [
            1 => 'Lamentamos que tu experiencia no haya sido satisfactoria. Un supervisor se pondrá en contacto contigo.',
            2 => 'Sentimos que no hayamos cumplido tus expectativas. Trabajaremos para mejorar.',
            3 => 'Gracias por tu feedback. Seguiremos trabajando para brindarte un mejor servicio.',
            4 => '¡Nos alegra saber que tuviste una buena experiencia!',
            5 => '¡Excelente! Nos encanta saber que estás completamente satisfecho.'
        ]
    ],

    // Comandos especiales
    'commands' => [
        'menu' => 'Menú principal',
        'help' => 'Ayuda',
        'agent' => 'Agente humano',
        'restart' => 'Reiniciar conversación',
        'end' => 'Terminar conversación'
    ],

    // Días de la semana
    'days' => [
        'monday' => 'Lunes',
        'tuesday' => 'Martes',
        'wednesday' => 'Miércoles',
        'thursday' => 'Jueves',
        'friday' => 'Viernes',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo'
    ],

    // Meses
    'months' => [
        'january' => 'Enero',
        'february' => 'Febrero',
        'march' => 'Marzo',
        'april' => 'Abril',
        'may' => 'Mayo',
        'june' => 'Junio',
        'july' => 'Julio',
        'august' => 'Agosto',
        'september' => 'Septiembre',
        'october' => 'Octubre',
        'november' => 'Noviembre',
        'december' => 'Diciembre'
    ]

];
