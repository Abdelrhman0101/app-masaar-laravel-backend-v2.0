<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ConversationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Create sample users if they don't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@msar.app'],
            [
                'name' => 'مدير النظام',
                'user_type' => 'admin',
                'password' => bcrypt('password'),
                'is_approved' => true,
                'account_active' => true,
                'is_email_verified' => true,
            ]
        );
        
        $normalUser = User::firstOrCreate(
            ['email' => 'user@msar.app'],
            [
                'name' => 'مستخدم عادي',
                'user_type' => 'normal',
                'password' => bcrypt('password'),
                'is_approved' => true,
                'account_active' => true,
                'is_email_verified' => true,
            ]
        );
        
        $providerUser = User::firstOrCreate(
            ['email' => 'provider@msar.app'],
            [
                'name' => 'مقدم خدمة',
                'user_type' => 'real_estate_office',
                'password' => bcrypt('password'),
                'is_approved' => true,
                'account_active' => true,
                'is_email_verified' => true,
            ]
        );
        
        // Clear existing conversations and messages to avoid duplicates
        \App\Models\Message::truncate();
        \App\Models\Conversation::truncate();
        
        // Create sample conversations
        
        // 1. Admin-User conversation
        $adminUserConversation = Conversation::create([
            'user1_id' => $adminUser->id,
            'user2_id' => $normalUser->id,
            'type' => 'admin_user',
            'title' => 'دعم فني - استفسار عام',
            'status' => 'open',
            'last_message_at' => now(),
            'metadata' => json_encode([
                'priority' => 'medium',
                'category' => 'support',
                'tags' => ['استفسار', 'دعم فني']
            ])
        ]);
        
        // 2. User-Provider conversation
        $userProviderConversation = Conversation::create([
            'user1_id' => $normalUser->id,
            'user2_id' => $providerUser->id,
            'type' => 'provider_user',
            'title' => 'استفسار عن عقار',
            'status' => 'open',
            'last_message_at' => now(),
            'metadata' => json_encode([
                'property_id' => 1,
                'service_type' => 'real_estate',
                'tags' => ['عقار', 'استفسار']
            ])
        ]);
        
        // 3. Admin-Provider conversation (different users to avoid duplicate)
        $adminProviderConversation = Conversation::create([
            'user1_id' => $adminUser->id,
            'user2_id' => $providerUser->id,
            'type' => 'admin_user',
            'title' => 'محادثة إدارية مع مقدم الخدمة',
            'status' => 'open',
            'last_message_at' => now(),
            'metadata' => json_encode([
                'tags' => ['إداري', 'مقدم خدمة']
            ])
        ]);
        
        // Create sample messages
        
        // Messages for admin-user conversation
        Message::create([
            'conversation_id' => $adminUserConversation->id,
            'sender_id' => $normalUser->id,
            'content' => 'مرحباً، أحتاج مساعدة في استخدام التطبيق',
            'type' => 'text',
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        Message::create([
            'conversation_id' => $adminUserConversation->id,
            'sender_id' => $adminUser->id,
            'content' => 'أهلاً وسهلاً، كيف يمكنني مساعدتك؟',
            'type' => 'text',
            'is_read' => false,
        ]);
        
        // System message
        Message::create([
            'conversation_id' => $adminUserConversation->id,
            'sender_id' => $adminUser->id,
            'content' => 'تم إنشاء المحادثة بنجاح',
            'type' => 'system',
            'is_read' => true,
            'read_at' => now(),
            'metadata' => json_encode([
                'system_action' => 'conversation_created',
                'timestamp' => now()->toISOString()
            ])
        ]);
        
        // Messages for user-provider conversation
        Message::create([
            'conversation_id' => $userProviderConversation->id,
            'sender_id' => $normalUser->id,
            'content' => 'مرحباً، أريد الاستفسار عن العقار المعروض',
            'type' => 'text',
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        Message::create([
            'conversation_id' => $userProviderConversation->id,
            'sender_id' => $providerUser->id,
            'content' => 'أهلاً بك، العقار متاح للمعاينة. متى يناسبك الوقت؟',
            'type' => 'text',
            'is_read' => false,
        ]);
        
        // Messages for admin-provider conversation
        Message::create([
            'conversation_id' => $adminProviderConversation->id,
            'sender_id' => $adminUser->id,
            'content' => 'مرحباً، نحتاج لمراجعة بعض الإجراءات معك',
            'type' => 'text',
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        Message::create([
            'conversation_id' => $adminProviderConversation->id,
            'sender_id' => $providerUser->id,
            'content' => 'أهلاً وسهلاً، أنا في الخدمة',
            'type' => 'text',
            'is_read' => false,
        ]);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('تم إنشاء بيانات تجريبية لنظام المحادثات بنجاح!');
    }
}