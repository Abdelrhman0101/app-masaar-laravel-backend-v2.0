<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores credentials for third party services like Mailgun,
    | Postmark, AWS, Slack, FCM...etc.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (HTTP v1)
    |--------------------------------------------------------------------------
    | استخدم هذه الإعدادات مع خدمة الإرسال FcmHttpV1Service.
    | القيم تأتي من:
    |  - FCM_PROJECT_ID
    |  - FCM_V1_CREDENTIALS (مسار ملف JSON الخاص بالـ Service Account)
    */
    'fcm_v1' => [
        'project_id'  => env('FCM_PROJECT_ID'),          // مثال: msar-90137
        'credentials' => env('FCM_V1_CREDENTIALS'),      // مثال: /home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json
    ],

    /*
    |--------------------------------------------------------------------------
    | (اختياري) FCM Legacy HTTP
    |--------------------------------------------------------------------------
    | لو محتاج تبقي إعداد الـ Legacy لأي سبب، سيبه هنا.
    | لو مش هتستخدمه سيبه فاضي بدون SERVER KEY.
    */
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    |--------------------------------------------------------------------------
    | إعدادات تسجيل الدخول عبر Google
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

];

