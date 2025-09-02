<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // جلب إشعارات المستخدم الحالي
    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->notifications()->latest()->get();

        return response()->json([
            'status' => true,
            'notifications' => $notifications
        ]);
    }

    // تعيين إشعار كمقروء
    public function markAsRead($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين الإشعار كمقروء',
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الإشعار بنجاح',
        ]);
    }
}
