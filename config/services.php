<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'web_api_enabled' => env('WHATSAPP_WEB_API_ENABLED', false),
        'web_api_url' => env('WHATSAPP_WEB_API_URL'),
        'web_session_id' => env('WHATSAPP_WEB_SESSION_ID'),
        'default_method' => env('WHATSAPP_DEFAULT_METHOD', 'direct_link'),
        'qr_code_enabled' => env('WHATSAPP_QR_CODE_ENABLED', true),
        'email_to_whatsapp_enabled' => env('WHATSAPP_EMAIL_ENABLED', true),
    ],

];
