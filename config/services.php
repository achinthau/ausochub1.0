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
        'mode' => env('WHATSAPP_MODE', 'api'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'token' => env('WHATSAPP_TOKEN'),
        'webjs_url' => env('WHATSAPP_WEBJS_URL', 'http://localhost:3001'),
    ],

    'mobile' => [
        'token' => env('MOBILE_TEXTIT_BIT_TOKEN'),
        'url' => env('MOBILE_TEXTIT_BIT_URL'),
    ],

    'email' => [
        'address' => env('MAIL_FROM_ADDRESS'),
        'subject' => env('MAIL_MESSAGE_SUBJECT'),
        'name' => env('MAIL_MESSAGE_NAME'),
    ]

];
