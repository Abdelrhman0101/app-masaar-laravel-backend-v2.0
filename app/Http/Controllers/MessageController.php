<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // جلب رسائل محادثة
    public function index($conversation_id)
    {
        $messages = Message::where('conversation_id', $conversation_id)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'messages' => $messages
        ]);
    }

    // إرسال رسالة جديدة
   public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $admin = auth()->user();
        $userId = $request->input('user_id');

        $conversation = Conversation::firstOrCreate(['user_id' => $userId]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'content' => $request->input('content'),
        ]);
        
        // --- [بث الحدث] ---
        // .toOthers() لكي لا يستقبل الأدمن الذي أرسل الرسالة نفس الحدث
        broadcast(new MessageSent($message->load('sender')))->toOthers();

        return response()->json([
            'status' => true,
            'message' => $message
        ], 201);
    }

    // حذف رسالة (Soft delete)
    public function destroy($id)
    {
        $user = auth()->user();
        $msg = Message::findOrFail($id);

        // فقط المرسل أو الأدمن يقدر يحذف
        if ($msg->sender_id != $user->id && $user->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مسموح'], 403);
        }

        $msg->update(['is_deleted' => true]);
        return response()->json(['status' => true, 'message' => 'تم حذف الرسالة']);
    }
}
