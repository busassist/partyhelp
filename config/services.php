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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY', env('MAIL_PASSWORD')),
        'templates' => [
            'venue_introduction' => env('SENDGRID_TEMPLATE_VENUE_INTRODUCTION'),
            'form_confirmation' => env('SENDGRID_TEMPLATE_FORM_CONFIRMATION'),
            'invoice_receipt' => env('SENDGRID_TEMPLATE_INVOICE_RECEIPT'),
        ],
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => (function () {
        $mode = env('STRIPE_MODE', 'sandbox');
        $isLive = $mode === 'live';

        return [
            'mode' => $mode,
            'key' => $isLive ? env('STRIPE_KEY_LIVE') : env('STRIPE_KEY_SANDBOX'),
            'secret' => $isLive ? env('STRIPE_SECRET_LIVE') : env('STRIPE_SECRET_SANDBOX'),
            'webhook_secret' => $isLive ? env('STRIPE_WEBHOOK_SECRET_LIVE') : env('STRIPE_WEBHOOK_SECRET_SANDBOX'),
            'currency' => env('STRIPE_CURRENCY', 'aud'),
        ];
    })(),

];
