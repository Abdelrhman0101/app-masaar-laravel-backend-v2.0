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
 *  هذا هو الجزء الأهم لميزة المحادثات الخاصة بنا
 * ======================================================================
 * 
 * تعريف قناة البث الخاصة بكل محادثة.
 * اسم القناة سيكون ديناميكيًا، مثل 'chat.1' للمحادثة رقم 1، وهكذا.
 * {conversationId} هو متغير سيتم استبداله برقم المحادثة.
 */
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    
    // أولاً، نجد المحادثة المطلوبة من قاعدة البيانات باستخدام الـ ID.
    $conversation = Conversation::find($conversationId);

    // إذا لم تكن المحادثة موجودة لأي سبب، نرفض الوصول فورًا.
    if (! $conversation) {
        return false;
    }

    // الآن، نحدد منطق الصلاحية (Authorization Logic):
    // سنعيد 'true' (مما يعني "مسموح لك بالاستماع") فقط إذا تحقق أحد الشرطين:
    // الشرط الأول: هل المستخدم الذي يحاول الاستماع هو مشرف (admin)؟
    //      أو
    // الشرط الثاني: هل هو المستخدم العادي صاحب هذه المحادثة؟
    
    return $user->user_type === 'admin' || $user->id === $conversation->user_id;
});