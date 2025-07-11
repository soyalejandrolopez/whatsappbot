<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatbot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración general del chatbot
    |
    */

    'default_language' => env('CHATBOT_DEFAULT_LANGUAGE', 'es'),
    'session_timeout' => env('CHATBOT_SESSION_TIMEOUT', 1800), // 30 minutos
    'enable_ai' => env('CHATBOT_ENABLE_AI', true),

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    */

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai'),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
            'max_tokens' => 150,
            'temperature' => 0.7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Responses
    |--------------------------------------------------------------------------
    */

    'default_responses' => [
        'welcome' => '¡Hola! 👋 Bienvenido a nuestro servicio de atención al cliente. ¿En qué puedo ayudarte hoy?',
        'goodbye' => '¡Gracias por contactarnos! Que tengas un excelente día. 😊',
        'not_understood' => 'Lo siento, no entendí tu mensaje. ¿Podrías reformularlo o elegir una de las opciones del menú?',
        'error' => 'Disculpa, ha ocurrido un error técnico. Por favor, intenta nuevamente en unos momentos.',
        'timeout' => 'Tu sesión ha expirado por inactividad. Escribe cualquier mensaje para comenzar una nueva conversación.',
        'maintenance' => 'Estamos realizando mantenimiento en nuestro sistema. Por favor, intenta más tarde.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Options
    |--------------------------------------------------------------------------
    */

    'main_menu' => [
        '1' => 'Información de productos',
        '2' => 'Soporte técnico',
        '3' => 'Ventas y cotizaciones',
        '4' => 'Horarios de atención',
        '5' => 'Hablar con un agente',
        '0' => 'Menú principal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours
    |--------------------------------------------------------------------------
    */

    'business_hours' => [
        'timezone' => 'America/Mexico_City',
        'schedule' => [
            'monday' => ['09:00', '18:00'],
            'tuesday' => ['09:00', '18:00'],
            'wednesday' => ['09:00', '18:00'],
            'thursday' => ['09:00', '18:00'],
            'friday' => ['09:00', '18:00'],
            'saturday' => ['09:00', '14:00'],
            'sunday' => null, // Cerrado
        ],
        'holidays' => [
            // Fechas en formato Y-m-d
            '2024-01-01', // Año Nuevo
            '2024-12-25', // Navidad
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversation Flow
    |--------------------------------------------------------------------------
    */

    'flows' => [
        'max_depth' => 10,
        'auto_reset_after_minutes' => 60,
        'save_incomplete_flows' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */

    'analytics' => [
        'track_user_interactions' => true,
        'track_response_times' => true,
        'track_satisfaction' => true,
        'retention_days' => 365,
    ],
];
