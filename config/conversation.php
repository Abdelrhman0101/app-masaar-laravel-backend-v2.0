<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Conversation System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the flexible conversation
    | system that supports communication between admins, users, and service providers.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Conversation Types
    |--------------------------------------------------------------------------
    |
    | Define the available conversation types and their configurations.
    |
    */
    'types' => [
        'user_user' => [
            'name' => 'محادثة بين المستخدمين',
            'description' => 'محادثة عادية بين مستخدمين',
            'auto_approve' => true,
            'max_participants' => 2,
            'features' => [
                'file_upload' => true,
                'image_upload' => true,
                'message_editing' => true,
                'message_deletion' => true,
                'read_receipts' => true,
                'typing_indicators' => true,
            ]
        ],
        'admin_user' => [
            'name' => 'دعم فني',
            'description' => 'محادثة بين الإدارة والمستخدم',
            'auto_approve' => true,
            'max_participants' => 2,
            'features' => [
                'file_upload' => true,
                'image_upload' => true,
                'message_editing' => true,
                'message_deletion' => true,
                'read_receipts' => true,
                'typing_indicators' => true,
                'system_messages' => true,
                'priority_levels' => true,
            ]
        ],
        'provider_user' => [
            'name' => 'استفسار عن خدمة',
            'description' => 'محادثة بين مقدم الخدمة والمستخدم',
            'auto_approve' => true,
            'max_participants' => 2,
            'features' => [
                'file_upload' => true,
                'image_upload' => true,
                'message_editing' => true,
                'message_deletion' => true,
                'read_receipts' => true,
                'typing_indicators' => true,
                'service_context' => true,
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Types
    |--------------------------------------------------------------------------
    |
    | Define the available message types and their configurations.
    |
    */
    'message_types' => [
        'text' => [
            'name' => 'رسالة نصية',
            'max_length' => 5000,
            'supports_formatting' => true,
        ],
        'image' => [
            'name' => 'صورة',
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'auto_thumbnail' => true,
        ],
        'file' => [
            'name' => 'ملف',
            'max_size' => 10 * 1024 * 1024, // 10MB
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'],
            'virus_scan' => true,
        ],
        'system' => [
            'name' => 'رسالة النظام',
            'auto_generated' => true,
            'admin_only' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversation Status
    |--------------------------------------------------------------------------
    |
    | Define the available conversation statuses.
    |
    */
    'statuses' => [
        'open' => [
            'name' => 'مفتوحة',
            'color' => '#28a745',
            'allows_messages' => true,
        ],
        'closed' => [
            'name' => 'مغلقة',
            'color' => '#dc3545',
            'allows_messages' => false,
        ],
        'archived' => [
            'name' => 'مؤرشفة',
            'color' => '#6c757d',
            'allows_messages' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time Features
    |--------------------------------------------------------------------------
    |
    | Configuration for real-time features like broadcasting and notifications.
    |
    */
    'realtime' => [
        'enabled' => true,
        'broadcast_driver' => env('BROADCAST_DRIVER', 'pusher'),
        'typing_timeout' => 3000, // milliseconds
        'online_timeout' => 300, // seconds
        'notification_sound' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for conversations and messages.
    |
    */
    'pagination' => [
        'conversations_per_page' => 20,
        'messages_per_page' => 50,
        'load_more_count' => 25,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-cleanup
    |--------------------------------------------------------------------------
    |
    | Automatic cleanup settings for old conversations and messages.
    |
    */
    'cleanup' => [
        'enabled' => env('CONVERSATION_CLEANUP_ENABLED', true),
        'schedule' => 'daily', // daily, weekly, monthly
        'soft_deleted_messages_days' => env('CLEANUP_SOFT_DELETED_MESSAGES_DAYS', 30),
        'archived_conversations_days' => env('CLEANUP_ARCHIVED_CONVERSATIONS_DAYS', 90),
        'typing_indicators_minutes' => env('CLEANUP_TYPING_INDICATORS_MINUTES', 10),
        'user_status_cache_hours' => env('CLEANUP_USER_STATUS_CACHE_HOURS', 24),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security settings for the conversation system.
    |
    */
    'security' => [
        'message_encryption' => false,
        'content_filtering' => true,
        'spam_detection' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_messages_per_minute' => 10,
            'max_conversations_per_hour' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Notification settings for new messages and conversation updates.
    |
    */
    'notifications' => [
        'enabled' => env('CONVERSATION_NOTIFICATIONS_ENABLED', true),
        'channels' => ['database', 'fcm', 'mail'], // database, mail, fcm
        'queue' => env('CONVERSATION_NOTIFICATIONS_QUEUE', 'default'),
        'email_for_admin_conversations' => env('EMAIL_NOTIFICATIONS_FOR_ADMIN_CONVERSATIONS', true),
        'push_sound' => env('PUSH_NOTIFICATION_SOUND', 'default'),
        'email_template' => 'emails.new-message', // Custom email template
        'admin_notifications' => [
            'new_conversation' => true,
            'urgent_messages' => true,
        ],
        'user_notifications' => [
            'new_message' => true,
            'conversation_status_change' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage
    |--------------------------------------------------------------------------
    |
    | Configuration for file and image storage in conversations.
    |
    */
    'storage' => [
        'disk' => env('CONVERSATION_STORAGE_DISK', 'public'),
        'path' => 'conversations',
        'url_expiry' => 3600, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | User Type Mappings
    |--------------------------------------------------------------------------
    |
    | Map user types to their display names and permissions.
    |
    */
    'user_types' => [
        'admin' => [
            'name' => 'مدير النظام',
            'can_create_system_messages' => true,
            'can_access_all_conversations' => true,
            'can_moderate' => true,
        ],
        'normal' => [
            'name' => 'مستخدم عادي',
            'can_create_system_messages' => false,
            'can_access_all_conversations' => false,
            'can_moderate' => false,
        ],
        'real_estate' => [
            'name' => 'وسيط عقاري',
            'can_create_system_messages' => false,
            'can_access_all_conversations' => false,
            'can_moderate' => false,
        ],
        'restaurant' => [
            'name' => 'مطعم',
            'can_create_system_messages' => false,
            'can_access_all_conversations' => false,
            'can_moderate' => false,
        ],
        'car_rental' => [
            'name' => 'تأجير سيارات',
            'can_create_system_messages' => false,
            'can_access_all_conversations' => false,
            'can_moderate' => false,
        ],
    ],

    // Advanced features
    'features' => [
        'typing_indicators' => env('CONVERSATION_TYPING_INDICATORS', true),
        'user_status' => env('CONVERSATION_USER_STATUS', true),
        'message_reactions' => env('CONVERSATION_MESSAGE_REACTIONS', false),
        'message_forwarding' => env('CONVERSATION_MESSAGE_FORWARDING', false),
        'conversation_search' => env('CONVERSATION_SEARCH', true),
        'auto_translation' => env('CONVERSATION_AUTO_TRANSLATION', false),
    ],

    // Rate limiting
    'rate_limits' => [
        'messages_per_minute' => env('CONVERSATION_MESSAGES_PER_MINUTE', 30),
        'typing_events_per_minute' => env('CONVERSATION_TYPING_EVENTS_PER_MINUTE', 60),
        'status_updates_per_minute' => env('CONVERSATION_STATUS_UPDATES_PER_MINUTE', 10),
    ],
];