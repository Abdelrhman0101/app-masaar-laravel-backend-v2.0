<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardChatController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للمحادثات مع قائمة المحادثات.
     * هذه هي الدالة التي تعرض ملف Blade الرئيسي.
     */
    public function index()
    {
        // جلب كل المحادثات مع بيانات المستخدم صاحبها وآخر رسالة
        // لجعلها تظهر في القائمة اليسرى عند تحميل الصفحة لأول مرة.
        $conversations = Conversation::with('user:id,name,profile_image')
            ->latest('updated_at') // الأحدث تظهر أولاً
            ->get();

        return view('admin.chat.index', compact('conversations'));
    }

    /**
     * إرسال رسالة جديدة من المشرف.
     * سيتم استدعاء هذه الدالة عبر AJAX من نموذج الإرسال.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate(['content' => 'required|string|max:5000']);

        $admin = Auth::user();

        $message = $conversation->messages()->create([
            'sender_id' => $admin->id,
            'content' => $request->content,
        ]);
        
        // تحديث updated_at للمحادثة لجعلها تظهر في الأعلى
        $conversation->touch();

        // إطلاق الحدث لإعلام المستخدم في تطبيق Flutter وأي مشرف آخر فاتح الصفحة
        // broadcast(new \App\Events\MessageSent($message))->toOthers(); // سنضبط هذا لاحقًا

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully.',
            'data' => $message->load('sender:id,name'),
        ]);
    }
}