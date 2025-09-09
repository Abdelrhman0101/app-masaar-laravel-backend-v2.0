<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\NewMessage;

class AdminChatController extends Controller
{
    /**
     * عرض كل المحادثات التي بدأها المستخدمون مع الأدمن الجاري
     * GET /api/admin/chats
     */
    public function index(Request $request)
    {
        // منع أي حساب غير الأدمن المحدد
        if (!$this->isAllowedAdmin($request)) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك برؤية محادثات الأدمن.'], 403);
        }
        $adminId = $request->user()->id;

        $q = Conversation::query()
            // لا توجد علاقة باسم "user" على Conversation، لذلك نحتاج لتحميل user1 و user2
            ->with(['user1:id,name,email,profile_image', 'user2:id,name,email,profile_image'])
            // تضمين كل المحادثات التي يكون فيها الأدمن طرفاً (سواء كان user1 أو user2)
            ->where(function ($qq) use ($adminId) {
                $qq->where('user1_id', $adminId)->orWhere('user2_id', $adminId);
            })
            ->orderByDesc('updated_at');

        // لا نقيد started_by هنا لنضمن ظهور كل المحادثات سواء بدأها المستخدم أو الأدمن

        $convs = $q->get();

        // الشكل الذي يتوقعه الفرونت: result.data.data
        $payload = $convs->map(function ($c) use ($adminId) {
            $last = $c->messages()->latest('created_at')->first();

            // تحديد الطرف الآخر (غير الأدمن) لإظهاره في القائمة
            $other = ($c->user1_id == $adminId) ? $c->user2 : $c->user1;

            // unread_count اختياري لو عندك read_at
            try {
                $unread = $c->messages()
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $adminId)
                    ->count();
            } catch (\Throwable $e) {
                $unread = 0;
            }

            return [
                'id'   => $c->id,
                'user' => $other ? [
                    'id'            => $other->id,
                    'name'          => $other->name,
                    'email'         => $other->email,
                    'profile_image' => $other->profile_image,
                ] : null,

                // نفس الاسم الذي يقرأه الفرونت
                'latest_message' => $last ? [
                    'content'    => $last->content,
                    'created_at' => $last->created_at,
                    'sender_id'  => $last->sender_id,
                ] : null,

                'unread_count' => $unread,
                'updated_at'   => $c->updated_at,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data'   => [
                'data' => $payload,  // الفرونت يقرأ result.data.data
            ],
        ]);
    }

    /**
     * جلب رسائل محادثة الأدمن مع مستخدم محدد (userId)
     * GET /api/admin/chats/{userId}
     */
    public function show(Request $request, int $userId)
    {
        // منع أي حساب غير الأدمن المحدد
        if (!$this->isAllowedAdmin($request)) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك برؤية محادثات الأدمن.'], 403);
        }
        $adminId = $request->user()->id;

        $conversation = Conversation::query()
            ->where(function ($q) use ($adminId, $userId) {
                $q->where('user1_id', $userId)->where('user2_id', $adminId);
            })
            ->orWhere(function ($q) use ($adminId, $userId) {
                $q->where('user1_id', $adminId)->where('user2_id', $userId);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'status' => true,
                'data'   => [
                    'conversation_id' => null,
                    'messages'        => [],
                ],
            ]);
        }

        // Mark all incoming (from user) unread messages as read now
        try {
            $conversation->messages()
                ->whereNull('read_at')
                ->where('sender_id', '!=', $adminId)
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {
            // ignore
        }

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get(['id','conversation_id','sender_id','content','created_at','read_at']);

        return response()->json([
            'status' => true,
            'data'   => [
                'conversation_id' => $conversation->id,
                'messages'        => $messages,
            ],
        ]);
    }

    /**
     * إرسال رسالة من الأدمن لمستخدم معين
     * POST /api/admin/chats
     * Body: { "user_id": <int>, "content": "<text>" }
     */
    public function store(Request $request)
    {
        // منع أي حساب غير الأدمن المحدد
        if (!$this->isAllowedAdmin($request)) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك بإرسال رسائل الأدمن.'], 403);
        }
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'content' => 'required|string',
        ]);

        $admin  = $request->user();
        $userId = (int) $request->user_id;

        // ابحث/أنشئ المحادثة
        $conversation = Conversation::query()
            ->where(function ($q) use ($admin, $userId) {
                $q->where('user1_id', $userId)->where('user2_id', $admin->id);
            })
            ->orWhere(function ($q) use ($admin, $userId) {
                $q->where('user1_id', $admin->id)->where('user2_id', $userId);
            })
            ->first();

        if (!$conversation) {
            $data = [
                'user1_id' => $userId,       // العميل user1
                'user2_id' => $admin->id,    // الأدمن user2
                'status'   => 'open',
            ];
            if (Schema::hasColumn('conversations', 'started_by')) {
                $data['started_by'] = 'admin';
            }
            $conversation = Conversation::create($data);
        }

        // أنشئ الرسالة
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $admin->id,
            'content'         => $request->content,
        ]);

        // تحديث زمن آخر نشاط
        $conversation->touch();

        // بثّ Realtime (لو الحدث موجود ومهيّأ)
        try {
            broadcast(new NewMessage($message))->toOthers();
        } catch (\Throwable $e) {
            // تجاهل لو البث غير مفعّل
        }

        // ===== إشعار Push + إشعار داخل التطبيق =====
        try {
            $recipient = User::find($userId);

            Log::info('ChatPush start', [
                'flow'          => 'admin->user',
                'recipient_id'  => $recipient?->id,
                'recipient_name'=> $recipient?->name,
            ]);

            if ($recipient) {
                // إشعار داخل التطبيق (يظهر في شاشة الإشعارات)
                $recipient->notifications()->create([
                    'type'    => 'chat_message',
                    'title'   => 'رسالة جديدة من ' . $admin->name,
                    'message' => Str::limit($request->content, 120),
                    'link'    => 'app://chat/' . $conversation->id,
                ]);

                // إرسال Push إن كان مفعّل
                $pushEnabled = $recipient->push_notifications_enabled ?? true;

                // هات التوكينات (بعلاقة أو مباشرة من الجدول)
                if (method_exists($recipient, 'deviceTokens')) {
                    $tokens = $recipient->deviceTokens()->where('is_enabled', 1)->pluck('token')->all();
                } else {
                    $tokens = DB::table('device_tokens')
                        ->where('user_id', $recipient->id)
                        ->where('is_enabled', 1)
                        ->pluck('token')
                        ->all();
                }

                Log::info('ChatPush tokens', [
                    'recipient_id' => $recipient->id,
                    'push_enabled' => $pushEnabled,
                    'tokens_count' => is_array($tokens) ? count($tokens) : 0,
                ]);

                if ($pushEnabled && !empty($tokens) && class_exists(\App\Services\FcmHttpV1Service::class)) {
                    $result = app(\App\Services\FcmHttpV1Service::class)->sendToTokens(
                        $tokens,
                        [
                            'title' => 'رسالة جديدة من ' . $admin->name,
                            'body'  => Str::limit($request->content, 120),
                        ],
                        [
                            'type'            => 'chat',
                            'conversation_id' => (string) $conversation->id,
                            'from_user_id'    => (string) $admin->id,
                            'from_user_name'  => (string) $admin->name,
                            'link'            => 'app://chat/' . $conversation->id,
                        ]
                    );

                    Log::info('ChatPush sent', [
                        'recipient_id' => $recipient->id,
                        'result_keys'  => array_keys($result ?? []),
                    ]);
                } else {
                    Log::info('ChatPush skipped', [
                        'recipient_id' => $recipient->id,
                        'reason' => !$pushEnabled ? 'push_disabled' : (empty($tokens) ? 'no_tokens' : 'no_service'),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Chat push (admin->user) failed: ' . $e->getMessage());
        }
        // ===========================================

        return response()->json([
            'status'  => true,
            'message' => $message,
        ], 201);
    }

    // التحقق من أن المستخدم هو الأدمن المصرّح به فقط
    private function isAllowedAdmin(Request $request): bool
    {
        $u = $request->user();
        if (!$u) return false;
        $email = strtolower((string) ($u->email ?? ''));
        return ($u->user_type === 'admin') && ($email === 'admin@msar.app');
    }
}
