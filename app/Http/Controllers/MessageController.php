<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    /**
     * عرض رسائل محادثة معينة
     */
    public function index(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            
            // التحقق من وجود المحادثة والصلاحية
            $conversation = Conversation::findOrFail($conversationId);
            
            if (!$conversation->hasParticipant($user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بالوصول لهذه المحادثة'
                ], 403);
            }

            // جلب الرسائل مع المرسل
            $messages = $conversation->messages()
                ->with(['sender:id,name,user_type'])
                ->when($request->has('limit'), function ($query) use ($request) {
                    return $query->limit($request->limit);
                })
                ->when($request->has('offset'), function ($query) use ($request) {
                    return $query->offset($request->offset);
                })
                ->oldest()
                ->get();

            // تحديد الرسائل كمقروءة
            $conversation->markAllAsRead($user->id);

            return response()->json([
                'status' => true,
                'messages' => $messages,
                'conversation' => [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'title' => $conversation->getTitleForUser($user->id),
                    'unread_count' => 0 // تم تحديدها كمقروءة
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في جلب الرسائل',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * إرسال رسالة جديدة
     */
    public function store(Request $request, $conversationId)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:5000',
                'message_type' => ['sometimes', Rule::in(array_keys(Message::MESSAGE_TYPES))],
                'metadata' => 'sometimes|array'
            ]);

            $user = Auth::user();
            $conversation = Conversation::findOrFail($conversationId);

            // التحقق من الصلاحية
            if (!$conversation->hasParticipant($user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بإرسال رسائل في هذه المحادثة'
                ], 403);
            }

            // التحقق من حالة المحادثة
            if ($conversation->status === 'closed' && $user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'هذه المحادثة مغلقة'
                ], 400);
            }

            DB::beginTransaction();

            // إنشاء الرسالة
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'content' => $request->content,
                'message_type' => $request->message_type ?? 'text',
                'metadata' => $request->metadata ?? []
            ]);

            // تحديث وقت آخر رسالة في المحادثة
            $conversation->updateLastMessageTime();

            // تحميل البيانات المطلوبة
            $message->load(['sender:id,name,user_type', 'conversation']);

            DB::commit();

            // بث الحدث
            broadcast(new NewMessage($message))->toOthers();

            return response()->json([
                'status' => true,
                'message' => 'تم إرسال الرسالة بنجاح',
                'data' => $message
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في إرسال الرسالة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تحديد رسالة كمقروءة
     */
    public function markAsRead(Request $request, $messageId)
    {
        try {
            $user = Auth::user();
            $message = Message::findOrFail($messageId);
            
            // التحقق من الصلاحية
            if (!$message->conversation->hasParticipant($user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            // لا يمكن للمرسل تحديد رسالته كمقروءة
            if ($message->sender_id === $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكنك تحديد رسالتك كمقروءة'
                ], 400);
            }

            $message->markAsRead();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديد الرسالة كمقروءة'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * حذف رسالة
     */
    public function destroy($messageId)
    {
        try {
            $user = Auth::user();
            $message = Message::findOrFail($messageId);

            // التحقق من الصلاحية
            if (!$message->canBeDeletedBy($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بحذف هذه الرسالة'
                ], 403);
            }

            $message->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الرسالة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في حذف الرسالة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تعديل رسالة
     */
    public function update(Request $request, $messageId)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:5000'
            ]);

            $user = Auth::user();
            $message = Message::findOrFail($messageId);

            // التحقق من الصلاحية
            if (!$message->canBeEditedBy($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بتعديل هذه الرسالة'
                ], 403);
            }

            $message->update([
                'content' => $request->content,
                'updated_at' => now()
            ]);

            $message->load(['sender:id,name,user_type', 'conversation']);

            return response()->json([
                'status' => true,
                'message' => 'تم تعديل الرسالة بنجاح',
                'data' => $message
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في تعديل الرسالة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * إرسال رسالة نظام
     */
    public function sendSystemMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'content' => 'required|string|max:1000',
                'metadata' => 'sometimes|array'
            ]);

            $user = Auth::user();
            
            // فقط الأدمن يمكنه إرسال رسائل النظام
            if ($user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            $conversation = Conversation::findOrFail($request->conversation_id);
            
            $message = Message::createSystemMessage(
                $conversation->id,
                $request->content,
                $request->metadata ?? []
            );

            $conversation->updateLastMessageTime();
            $message->load(['conversation']);

            // بث الحدث إذا كان مطلوباً
            if ($request->metadata['broadcast'] ?? true) {
                broadcast(new NewMessage($message))->toOthers();
            }

            return response()->json([
                'status' => true,
                'message' => 'تم إرسال رسالة النظام بنجاح',
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في إرسال رسالة النظام',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
