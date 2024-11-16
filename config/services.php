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
    's3' => [
    'bucket' => env('AWS_BUCKET'),
    ],

    'razor' => [
        'key' => env('RAZOR_KEY'),
    ],
    'whatsapp' => [
    'api_base_url' => env('WHATSAPP_API_BASE_URL'),
    'api_token' => env('WHATSAPP_API_TOKEN'),
    'api_version' => env('WHATSAPP_API_VERSION'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
],

];