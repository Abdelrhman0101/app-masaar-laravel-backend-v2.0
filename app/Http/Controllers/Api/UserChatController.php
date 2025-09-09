<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Support\Notifier;

class UserChatController extends Controller
{
    /**
     * إرسال رسالة من المستخدم الحالي إلى الإدارة.
     */
    public function store(Request $request)
    {
        $request->validate(['content' => 'required|string|max:5000']);
        
        $user = $request->user();
        
        // إيجاد أو إنشاء محادثة المستخدم الوحيدة
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'open']
        );

        // إنشاء الرسالة
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content'   => $request->input('content'),
        ]);

        $conversation->touch();

        // بث الرسالة للمشرفين
        broadcast(new NewMessage($message->load('sender')))->toOthers();

        // إرسال إشعار للمشرفين
        $this->notifyAdmins($user, $conversation, $message);

        return response()->json([
            'status'  => true,
            'message' => 'تم إرسال الرسالة',
            'data'    => $message
        ], 201);
    }

    /**
     * دالة مساعدة لإرسال الإشعارات لجميع المشرفين.
     */
    private function notifyAdmins(User $fromUser, Conversation $conversation, $message)
    {
        $admins = User::where('user_type', 'admin')->get();
        if ($admins->isEmpty()) return;

        $notificationTitle = 'رسالة جديدة من ' . $fromUser->name;
        $notificationBody  = Str::limit($message->content, 120);

        foreach ($admins as $admin) {
            try {
                Notifier::send(
                    $admin,
                    'chat_message',
                    $notificationTitle,
                    $notificationBody,
                    ['conversation_id' => (string)$conversation->id, 'sender_id' => (string)$fromUser->id],
                    // استخدم رابط الويب لفتح محادثة المستخدم على صفحة الشات
                    '/chat?user_id=' . $conversation->user_id
                );
            } catch (\Throwable $e) {
                Log::error('Chat Notification Failed for admin ' . $admin->id . ': ' . $e->getMessage());
            }
        }
    }
    
    public function show(Request $request)
    {
        $user = $request->user();
        
        // استخدام نفس المنطق لإيجاد المحادثة
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'open']
        );

        $messages = $conversation->messages()
            ->with('sender:id,name') // جلب بيانات المرسل لتحسين الأداء
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            // تغيير هنا: نرسل كائن واحد اسمه "data" كما يتوقع Flutter
            'data'   => [
                'conversation_id' => $conversation->id,
                'messages'        => $messages,
            ],
        ]);
    }
  
  public function markAsRead(Conversation $conversation, Request $request)
{
    // استخدام الـ Policy للحماية
    $this->authorize('view', $conversation);

    $conversation->messages()
        ->where('sender_id', '!=', $request->user()->id)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['status' => true, 'message' => 'Messages marked as read.']);
}
}