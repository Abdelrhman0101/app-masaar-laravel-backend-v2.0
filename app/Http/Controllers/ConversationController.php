<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConversationController extends Controller
{
    /**
     * عرض جميع محادثات المستخدم
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $conversations = Conversation::forUser($user->id)
                ->with([
                    'user1:id,name,user_type',
                    'user2:id,name,user_type',
                    'lastMessage:id,conversation_id,sender_id,content,created_at',
                    'lastMessage.sender:id,name,user_type'
                ])
                ->when($request->has('type'), function ($query) use ($request) {
                    return $query->ofType($request->type);
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->withLastMessage()
                ->orderBy('last_message_at', 'desc')
                ->paginate($request->per_page ?? 20);

            // إضافة معلومات إضافية لكل محادثة
            $conversations->getCollection()->transform(function ($conversation) use ($user) {
                $otherParticipant = $conversation->getOtherParticipant($user->id);
                $unreadCount = $conversation->unreadMessagesCount($user->id);
                
                return [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'title' => $conversation->getTitleForUser($user->id),
                    'other_participant' => $otherParticipant ? [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'user_type' => $otherParticipant->user_type
                    ] : null,
                    'last_message' => $conversation->lastMessage ? [
                        'id' => $conversation->lastMessage->id,
                        'content' => $conversation->lastMessage->formatted_content,
                        'sender' => $conversation->lastMessage->sender,
                        'created_at' => $conversation->lastMessage->created_at
                    ] : null,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at
                ];
            });

            return response()->json([
                'status' => true,
                'conversations' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في جلب المحادثات',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * عرض محادثة محددة
     */
    public function show($conversationId)
    {
        try {
            $user = Auth::user();
            
            $conversation = Conversation::with([
                'user1:id,name,user_type',
                'user2:id,name,user_type'
            ])->findOrFail($conversationId);

            // التحقق من الصلاحية
            if (!$conversation->hasParticipant($user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بالوصول لهذه المحادثة'
                ], 403);
            }

            $otherParticipant = $conversation->getOtherParticipant($user->id);
            $unreadCount = $conversation->unreadMessagesCount($user->id);

            return response()->json([
                'status' => true,
                'conversation' => [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'title' => $conversation->getTitleForUser($user->id),
                    'other_participant' => $otherParticipant ? [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'user_type' => $otherParticipant->user_type
                    ] : null,
                    'participants' => [
                        $conversation->user1,
                        $conversation->user2
                    ],
                    'unread_count' => $unreadCount,
                    'metadata' => $conversation->metadata,
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في جلب المحادثة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * إنشاء محادثة جديدة
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'participant_id' => ['required_without:participant_email', 'nullable', 'exists:users,id', 'different:' . Auth::id()],
                'participant_email' => ['required_without:participant_id', 'nullable', 'email', 'exists:users,email'],
                'type' => ['sometimes', Rule::in(['admin_user', 'user_service_provider', 'support'])],
                'title' => 'sometimes|string|max:255',
                'metadata' => 'sometimes|array'
            ]);

            $user = Auth::user();
            
            // حدد المشارك إما عبر البريد الإلكتروني أو المعرف
            $participant = null;
            $participantId = null;
            
            if ($request->filled('participant_email')) {
              $participant = User::where('email', $request->participant_email)->firstOrFail();
              if ($participant->id === $user->id) {
                return response()->json([
                  'status' => false,
                  'message' => 'لا يمكنك بدء محادثة مع نفسك'
                ], 422);
              }
              $participantId = $participant->id;
            } else {
              $participantId = $request->participant_id;
              $participant = User::findOrFail($participantId);
            }

            // التحقق من وجود محادثة مسبقة
            $sortedUsers = collect([$user->id, $participantId])->sort()->values();
            $existingConversation = Conversation::where('user1_id', $sortedUsers[0])
                ->where('user2_id', $sortedUsers[1])
                ->first();
            
            if ($existingConversation) {
                return response()->json([
                    'status' => true,
                    'message' => 'المحادثة موجودة مسبقاً',
                    'conversation' => [
                        'id' => $existingConversation->id,
                        'type' => $existingConversation->type,
                        'status' => $existingConversation->status,
                        'title' => $existingConversation->getTitleForUser($user->id)
                    ]
                ]);
            }

            // تحديد نوع المحادثة تلقائياً
            $supportAdminEmail = env('SUPPORT_ADMIN_EMAIL', 'admin@msar.app');
            if ($participant->user_type === 'admin' || strcasecmp($participant->email, $supportAdminEmail) === 0) {
                $conversationType = 'admin_user';
            } else {
                $conversationType = $this->determineConversationType($user, $participant, $request->type);
            }

            DB::beginTransaction();

            // إنشاء المحادثة
            $conversation = Conversation::createBetweenUsers(
                $user->id,
                $participantId,
                $conversationType,
                $request->title
            );

            if ($request->filled('metadata')) {
                $conversation->metadata = $request->metadata;
                $conversation->save();
            }

            // إرسال رسالة ترحيب تلقائية
            if ($conversationType === 'admin_user') {
                Message::createSystemMessage(
                    $conversation->id,
                    'مرحباً! كيف يمكننا مساعدتك اليوم؟',
                    ['auto_generated' => true]
                );
            }

            DB::commit();

            $conversation->load(['user1:id,name,user_type', 'user2:id,name,user_type']);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء المحادثة بنجاح',
                'conversation' => [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'title' => $conversation->getTitleForUser($user->id),
                    'other_participant' => $conversation->getOtherParticipant($user->id),
                    'created_at' => $conversation->created_at
                ]
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
                'message' => 'حدث خطأ في إنشاء المحادثة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تحديث حالة المحادثة
     */
    public function updateStatus(Request $request, $conversationId)
    {
        try {
            $request->validate([
                'status' => ['required', Rule::in(['open', 'closed', 'archived'])]
            ]);

            $user = Auth::user();
            $conversation = Conversation::findOrFail($conversationId);

            // فقط الأدمن أو المشاركين يمكنهم تحديث الحالة
            if (!$conversation->hasParticipant($user->id) && $user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            $oldStatus = $conversation->status;
            $conversation->update(['status' => $request->status]);

            // إرسال رسالة نظام عند تغيير الحالة
            if ($oldStatus !== $request->status) {
                $statusMessages = [
                    'open' => 'تم فتح المحادثة',
                    'closed' => 'تم إغلاق المحادثة',
                    'archived' => 'تم أرشفة المحادثة'
                ];

                Message::createSystemMessage(
                    $conversation->id,
                    $statusMessages[$request->status],
                    ['status_change' => true, 'old_status' => $oldStatus, 'new_status' => $request->status]
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث حالة المحادثة بنجاح',
                'conversation_status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في تحديث حالة المحادثة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * حذف محادثة (أرشفة)
     */
    public function destroy($conversationId)
    {
        try {
            $user = Auth::user();
            $conversation = Conversation::findOrFail($conversationId);

            // فقط الأدمن أو المشاركين يمكنهم حذف المحادثة
            if (!$conversation->hasParticipant($user->id) && $user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            // أرشفة بدلاً من الحذف الفعلي
            $conversation->update(['status' => 'archived']);

            return response()->json([
                'status' => true,
                'message' => 'تم أرشفة المحادثة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في حذف المحادثة',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تحديد جميع رسائل المحادثة كمقروءة
     */
    public function markAllAsRead($conversationId)
    {
        try {
            $user = Auth::user();
            $conversation = Conversation::findOrFail($conversationId);

            if (!$conversation->hasParticipant($user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            $conversation->markAllAsRead($user->id);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديد جميع الرسائل كمقروءة'
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
     * إحصائيات المحادثات للأدمن
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            
            if ($user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح'
                ], 403);
            }

            $stats = [
                'total_conversations' => Conversation::count(),
                'open_conversations' => Conversation::open()->count(),
                'closed_conversations' => Conversation::closed()->count(),
                'archived_conversations' => Conversation::where('status', 'archived')->count(),
                'conversations_by_type' => [
                    'admin_user' => Conversation::ofType('admin_user')->count(),
                    'user_service_provider' => Conversation::ofType('user_service_provider')->count(),
                    'support' => Conversation::ofType('support')->count()
                ],
                'total_messages' => Message::count(),
                'unread_messages' => Message::unread()->count(),
                'recent_conversations' => Conversation::with(['user1:id,name', 'user2:id,name'])
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($conv) {
                        return [
                            'id' => $conv->id,
                            'type' => $conv->type,
                            'participants' => [$conv->user1->name, $conv->user2->name],
                            'created_at' => $conv->created_at
                        ];
                    })
            ];

            return response()->json([
                'status' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تحديد نوع المحادثة تلقائياً
     */
    private function determineConversationType(User $user1, User $user2, $requestedType = null)
    {
        if ($requestedType) {
            return $requestedType;
        }

        // إذا كان أحد المشاركين أدمن
        if ($user1->user_type === 'admin' || $user2->user_type === 'admin') {
            return 'admin_user';
        }

        // إذا كان أحد المشاركين مقدم خدمة
        if ($user1->user_type === 'service_provider' || $user2->user_type === 'service_provider') {
            return 'user_service_provider';
        }

        // افتراضي
        return 'support';
    }
}