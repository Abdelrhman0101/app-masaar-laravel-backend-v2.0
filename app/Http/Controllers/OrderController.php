<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Notifier;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * [خاص بالعميل] إنشاء طلب جديد بحالة "مقبول تلقائياً".
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'restaurant_id'      => 'required|exists:restaurant_details,id',
            'items'              => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'note'               => 'nullable|string|max:1000',
        ]);
        
        $user = auth()->user();
        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validatedData['items'] as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            $unitPrice = $menuItem->price;
            $total = $unitPrice * $item['quantity'];
            $subtotal += $total;
            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id, 'title' => $menuItem->title, 'quantity' => $item['quantity'],
                'unit_price' => $unitPrice, 'total_price' => $total, 'image' => $menuItem->image,
            ];
        }

        $deliveryFee = 0; $vat = 0; $total = $subtotal + $deliveryFee + $vat;
        $orderNumber = 'ORD-' . time() . rand(100, 999);
        
        $order = Order::create([
            'user_id' => $user->id,
            'restaurant_id' => $validatedData['restaurant_id'],
            'status' => 'accepted_by_admin', // الطلب مقبول تلقائياً
            'order_number' => $orderNumber,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'vat' => $vat,
            'total_price' => $total,
            'note' => $validatedData['note'] ?? null,
        ]);
        
        $order->items()->createMany($orderItemsData);

        // إرسال إشعارات الموافقة مباشرة للعميل والمطعم
        $this->sendOrderNotifications($order, 'accepted_by_admin');

        return response()->json([
            'status' => true,
            'message' => 'تم قبول طلبك بنجاح وجاري إرساله للمطعم.',
            'order' => $order->load('items')
        ], 201);
    }

    /**
     * [خاص بالمشرف] عرض كل الطلبات في النظام.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user:id,name', 'restaurant:id,restaurant_name', 'items']);
        if ($request->has('status')) { $query->where('status', $request->status); }
        return response()->json(['status' => true, 'orders' => $query->latest()->get()]);
    }

    /**
     * [عام] عرض تفاصيل طلب معين.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'restaurant', 'items.menuItem']);
        return response()->json(['status' => true, 'order' => $order]);
    }
    
    /**
     * [خاص بالمشرف] الموافقة على طلب (تبقى كوظيفة احتياطية).
     */
    public function approve(Order $order)
    {
        if ($order->status !== 'pending') { return response()->json(['status' => false, 'message' => 'يمكن فقط الموافقة على الطلبات التي في حالة الانتظار.'], 409); }
        $order->update(['status' => 'accepted_by_admin']);
        $this->sendOrderNotifications($order, 'accepted_by_admin');
        return response()->json(['status' => true, 'message' => 'تمت الموافقة على الطلب بنجاح.', 'order' => $order]);
    }

    /**
     * [خاص بالمشرف] رفض طلب مع ذكر السبب (تبقى كوظيفة احتياطية).
     */
    public function reject(Request $request, Order $order)
    {
        if ($order->status !== 'pending') { return response()->json(['status' => false, 'message' => 'يمكن فقط رفض الطلبات التي في حالة الانتظار.'], 409); }
        
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);
        $reason = $validated['reason'] ?? null;

        $order->update(['status' => 'rejected_by_admin', 'rejection_reason' => $reason]);
        $this->sendOrderNotifications($order, 'rejected_by_admin');
        return response()->json(['status' => true, 'message' => 'تم رفض الطلب.', 'order' => $order]);
    }

    // ===========================================
    //  !!     الدوال الخاصة بالمطعم فقط     !!
    // ===========================================
    
    public function restaurantOrders(Request $request)
    {
        $restaurantUser = Auth::user();
        if ($restaurantUser->user_type !== 'restaurant' || !$restaurantUser->restaurantDetail) {
            return response()->json(['status' => false, 'message' => 'هذا الحساب غير مصرح له بعرض الطلبات.'], 403);
        }
        $restaurantId = $restaurantUser->restaurantDetail->id;
        $query = Order::with(['user:id,name,phone', 'items'])->where('restaurant_id', $restaurantId);
        if ($request->has('status')) { $query->where('status', $request->status); }
        $orders = $query->latest()->get();
        return response()->json(['status' => true, 'orders' => $orders]);
    }

    public function process(Order $order)
    {
        $restaurantUser = Auth::user();
        if ($restaurantUser->user_type !== 'restaurant' || $restaurantUser->restaurantDetail?->id !== $order->restaurant_id) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك بتنفيذ هذا الإجراء.'], 403);
        }
        if ($order->status !== 'accepted_by_admin') {
            return response()->json(['status' => false, 'message' => 'يمكن فقط تنفيذ الطلبات التي تمت الموافقة عليها من الإدارة.'], 409);
        }
        $order->update(['status' => 'processing']);
        $this->sendOrderNotifications($order, 'processing');
        return response()->json(['status' => true, 'message' => 'تم بدء تنفيذ الطلب.', 'order' => $order]);
    }

    public function complete(Order $order)
    {
        $restaurantUser = Auth::user();
        if ($restaurantUser->user_type !== 'restaurant' || $restaurantUser->restaurantDetail?->id !== $order->restaurant_id) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك بتنفيذ هذا الإجراء.'], 403);
        }
        if ($order->status !== 'processing') {
            return response()->json(['status' => false, 'message' => 'يمكن فقط إنهاء الطلبات التي هي قيد التنفيذ.'], 409);
        }
        $order->update(['status' => 'completed']);
        $this->sendOrderNotifications($order, 'completed');
        return response()->json(['status' => true, 'message' => 'تم إنهاء الطلب بنجاح.', 'order' => $order]);
    }

    /**
     * دالة مركزية لإرسال إشعارات حالة الطلب.
     */
    private function sendOrderNotifications(Order $order, string $newStatus): void
    {
        $order->load(['user', 'restaurant.user']);
        $customer = $order->user;
        $restaurantOwner = $order->restaurant->user;

        try {
            switch ($newStatus) {
                case 'accepted_by_admin':
                    if ($customer) Notifier::send($customer, 'order_accepted', 'تم قبول طلبك!', 'تمت الموافقة على طلبك رقم ' . $order->order_number . ' وجاري إرساله للمطعم.');
                    if ($restaurantOwner) Notifier::send($restaurantOwner, 'new_order_for_restaurant', 'لديك طلب جديد!', 'يوجد طلب جديد برقم ' . $order->order_number . ' بانتظار التنفيذ.');
                    break;
                case 'rejected_by_admin':
                    $reason = $order->rejection_reason ? ' السبب: ' . $order->rejection_reason : '';
                    if ($customer) Notifier::send($customer, 'order_rejected', 'تم رفض طلبك', 'نأسف، لم نتمكن من قبول طلبك.' . $reason);
                    break;
                case 'processing':
                    if ($customer) Notifier::send($customer, 'order_processing', 'طلبك قيد التجهيز!', 'بدأ المطعم في تجهيز طلبك رقم ' . $order->order_number . '.');
                    break;
                case 'completed':
                    if ($customer) Notifier::send($customer, 'order_completed', 'طلبك جاهز!', 'أصبح طلبك رقم ' . $order->order_number . ' جاهزاً للاستلام.');
                    break;
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send notification for order #{$order->id} to status {$newStatus}: " . $e->getMessage());
        }
    }
}