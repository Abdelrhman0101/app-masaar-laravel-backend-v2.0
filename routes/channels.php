<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


/**
 * ======================================================================
 *  قنوات البث للمحادثات والميزات المتقدمة
 * ======================================================================
 */

// قناة البث الخاصة بكل محادثة للرسائل والأحداث
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // التحقق من أن المستخدم مشارك في المحادثة أو مدير
    return $user->user_type === 'admin' || $conversation->hasParticipant($user);
});

// قناة البث الشخصية لكل مستخدم (حالة الاتصال، الإشعارات)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// قناة البث للمدراء (إحصائيات، تنبيهات إدارية)
Broadcast::channel('admin.notifications', function ($user) {
    return $user->user_type === 'admin';
});

// قناة البث العامة للإشعارات المهمة
Broadcast::channel('system.announcements', function ($user) {
    // جميع المستخدمين المسجلين يمكنهم الاستماع للإعلانات العامة
    return $user !== null;
});