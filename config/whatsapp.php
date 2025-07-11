<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con WhatsApp Business API
    |
    */

    'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v18.0'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'endpoint' => '/api/whatsapp/webhook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Types
    |--------------------------------------------------------------------------
    */

    'supported_message_types' => [
        'text',
        'image',
        'document',
        'audio',
        'video',
        'location',
        'contacts',
        'interactive',
        'template',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'messages_per_second' => 10,
        'messages_per_minute' => 600,
        'messages_per_hour' => 36000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    */

    'retry' => [
        'max_attempts' => env('CHATBOT_MAX_RETRIES', 3),
        'delay_seconds' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */

    'media' => [
        'max_file_size' => 16 * 1024 * 1024, // 16MB
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'audio' => ['mp3', 'wav', 'ogg', 'aac'],
            'video' => ['mp4', 'avi', 'mov'],
        ],
    ],
];
