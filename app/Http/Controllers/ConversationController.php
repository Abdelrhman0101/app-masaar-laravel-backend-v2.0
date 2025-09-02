<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // <--- [الحل هنا] تأكد من وجود هذا السطر

class ConversationController extends Controller
{
    /**
     * For admin to list all conversations, sorted by the latest message.
     */
    public function index()
    {
        $conversations = Conversation::with(['user:id,name', 'latestMessage'])
            ->withCount('messages')
            ->get()
            // [تحسين] تم تعديل الترتيب ليكون أكثر أمانًا ويتجنب الأخطاء
            // إذا كانت هناك محادثة بدون رسائل
            ->sortByDesc(function ($convo) {
                return $convo->latestMessage->created_at ?? $convo->created_at;
            })
            ->values(); // Reset array keys

        return response()->json([
            'status' => true,
            'data' => [
                'data' => $conversations,
            ],
        ]);
    }

    /**
     * For admin to get messages of a specific user AND mark them as read.
     */
    public function getMessagesForAdmin($userId)
    {
        $conversation = Conversation::where('user_id', $userId)->first();

        if (!$conversation) {
            return response()->json([
                'status' => true,
                'data' => ['messages' => []]
            ]);
        }

        // Mark messages from the user as read
        $conversation->messages()
            ->where('sender_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        // Fetch all messages for the conversation
        $messages = $conversation->messages()->get();

        return response()->json([
            'status' => true,
            'data' => [
                'messages' => $messages
            ]
        ]);
    }
}