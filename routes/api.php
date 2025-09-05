<?php

use App\Http\Controllers\AdminServiceRequestController;
use App\Http\Controllers\CarRentalOfficesDetailController;
use App\Http\Controllers\ProviderServiceRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SecurityPermitController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuSectionController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarServiceOrderController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\Api\UserChatController;
use App\Http\Controllers\Api\AdminChatController;
use App\Http\Controllers\Api\RestaurantProfileController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Api\RestaurantBannerController;
use App\Http\Controllers\Api\PublicRestaurantController;
use App\Http\Controllers\Api\PublicPropertyController;
use App\Http\Controllers\Api\MyOrdersController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OtpAuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/settings', [AppSettingController::class, 'index']);

// Google OAuth Routes
Route::post('/auth/google/mobile', [\App\Http\Controllers\Auth\SocialLoginController::class, 'handleGoogleMobileLogin']);

// OTP Routes
Route::prefix('otp')->middleware('otp.rate.limit')->group(function () {
    // Email Verification OTP
    Route::post('/send-email-verification', [OtpAuthController::class, 'sendEmailVerificationOtp']);
    Route::post('/verify-email', [OtpAuthController::class, 'verifyEmailVerificationOtp']);
    Route::post('/resend-email-verification', [OtpAuthController::class, 'resendEmailVerificationOtp']);
    
    // Password Reset OTP
    Route::post('/send-password-reset', [OtpAuthController::class, 'sendPasswordResetOtp']);
    Route::post('/verify-password-reset', [OtpAuthController::class, 'verifyPasswordResetOtp']);
    Route::post('/reset-password', [OtpAuthController::class, 'resetPassword']);
});

// ======= Authenticated Routes =======
Route::middleware('auth:sanctum')->group(function () {

    // User Profile
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->loadMissing([
            'normalUser',
            'realEstate.officeDetail',
            'realEstate.individualDetail',
            'restaurantDetail',
            'carRental.officeDetail',
            'carRental.driverDetail',
        ]);
        return response()->json($user);
    });

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Properties
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::get('/all-properties', [PropertyController::class, 'allProperties']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);
    Route::get('/provider-appointments', [AppointmentController::class, 'providerAppointments']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    // Security Permits
    Route::post('/security-permits', [SecurityPermitController::class, 'store']);
    Route::get('/my-security-permits', [SecurityPermitController::class, 'myPermits']);
    Route::get('/all-security-permits', [SecurityPermitController::class, 'allPermits']);

    // Restaurant Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/restaurant/orders', [OrderController::class, 'restaurantOrders']);
    Route::post('/restaurant/orders/{order}/process', [OrderController::class, 'process']);
    Route::post('/restaurant/orders/{order}/complete', [OrderController::class, 'complete']);

    // Restaurant Profile
    Route::prefix('restaurant')->group(function () {
        Route::get('/details', [RestaurantProfileController::class, 'show']);
        Route::post('/details/update', [RestaurantProfileController::class, 'update']);
    });

    // Cars
    Route::get('/car-rentals/{carRentalId}/cars', [CarController::class, 'index']);
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{id}', [CarController::class, 'update']);
    Route::delete('/cars/{id}', [CarController::class, 'destroy']);
    Route::get('/cars/{id}', [CarController::class, 'show']);

    // Car Service Orders
    Route::post('/car-orders', [CarServiceOrderController::class, 'store']);
    Route::get('/car-orders', [CarServiceOrderController::class, 'index']);
    Route::get('/car-orders/{id}', [CarServiceOrderController::class, 'show']);
    Route::post('/car-orders/{id}/offer', [CarServiceOrderController::class, 'offer']);
    Route::post('/car-orders/{order_id}/offer/{offer_id}/accept', [CarServiceOrderController::class, 'acceptOffer']);
    Route::patch('/car-rental-office-detail/{id}/availability', [CarRentalOfficesDetailController::class, 'updateAvailability']);

    // Service Requests
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    Route::get('/service-requests', [ServiceRequestController::class, 'index']);
    Route::post('/offers', [OfferController::class, 'store']);

    // Provider Service Requests
    Route::get('/provider/service-requests', [ProviderServiceRequestController::class, 'index']);
    Route::post('/provider/service-requests/{id}/accept', [ProviderServiceRequestController::class, 'accept']);
    Route::get('/provider/service-requests/accept', [ProviderServiceRequestController::class, 'acceptedRequests']);
    Route::post('/provider/service-requests/{id}/complete', [ProviderServiceRequestController::class, 'complete']);
    Route::get('/provider/service-requests/complete', [ProviderServiceRequestController::class, 'completedRequests']);

    // ======= Conversations & Messages System =======
    Route::prefix('conversations')->group(function () {
        // Conversation Management
        Route::get('/', [ConversationController::class, 'index']); // Get user's conversations
        Route::post('/', [ConversationController::class, 'store']); // Create new conversation
        Route::get('/{conversation}', [ConversationController::class, 'show']); // Get specific conversation
        Route::patch('/{conversation}/status', [ConversationController::class, 'updateStatus']); // Update conversation status
        Route::delete('/{conversation}', [ConversationController::class, 'destroy']); // Archive conversation
        Route::post('/{conversation}/mark-all-read', [ConversationController::class, 'markAllAsRead']); // Mark all messages as read
        
        // Messages within conversations
        Route::get('/{conversation}/messages', [MessageController::class, 'index']); // Get conversation messages
        Route::post('/{conversation}/messages', [MessageController::class, 'store']); // Send message to conversation
    });
    
    // Message Management
    Route::prefix('messages')->group(function () {
        Route::patch('/{message}/read', [MessageController::class, 'markAsRead']); // Mark message as read
        Route::patch('/{message}', [MessageController::class, 'update']); // Edit message
        Route::delete('/{message}', [MessageController::class, 'destroy']); // Delete message
    });
    
    // Advanced conversation features
    Route::middleware(['conversation.participant'])->group(function () {
        Route::post('/conversations/{conversation}/typing', [ConversationFeatureController::class, 'typing']);
        Route::get('/conversations/{conversation}/typing-users', [ConversationFeatureController::class, 'getTypingUsers']);
        Route::get('/conversations/{conversation}/participants-status', [ConversationFeatureController::class, 'getConversationParticipantsStatus']);
    });
    
    // User status features
    Route::post('/user/status', [ConversationFeatureController::class, 'updateStatus']);
    Route::get('/user/{user}/status', [ConversationFeatureController::class, 'getUserStatus']);
    Route::post('/user/heartbeat', [ConversationFeatureController::class, 'heartbeat']);
    
    // Admin Conversation Features
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::get('/conversations/statistics', [ConversationController::class, 'statistics']); // Admin statistics
        Route::post('/messages/system', [MessageController::class, 'sendSystemMessage']); // Send system messages
    });}]}
    
    // Legacy Chat Routes (for backward compatibility)
    Route::get('/chat', [UserChatController::class, 'show']);
    Route::post('/chat', [UserChatController::class, 'store']);
    Route::post('/chat/{conversation}/read', [UserChatController::class, 'markAsRead']);
    
    Route::get('/admin/chats/{userId}', [AdminChatController::class, 'show']);
    Route::get('/admin/chats', [AdminChatController::class, 'index'])->name('api.admin.chats.index');
    Route::post('/admin/chats', [AdminChatController::class, 'store']);

    // Settings
    Route::put('/settings/{key}', [AppSettingController::class, 'update']);

    // ===== Device Tokens & Push Prefs (Protected) =====
    Route::post('/device-tokens', function (Request $r) {
        $user = $r->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $r->validate([
            'token'    => 'required|string',
            'platform' => 'nullable|string',
        ]);

        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $user->id, 'token' => $r->input('token')],
            [
                'platform'   => $r->input('platform', 'android'),
                'is_enabled' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    });

    Route::delete('/device-tokens', function (Request $r) {
        $user = $r->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $q = DB::table('device_tokens')->where('user_id', $user->id);
        if ($token = $r->input('token')) {
            $q->where('token', $token);
        }
        $deleted = $q->delete();

        return response()->json(['ok' => true, 'deleted' => $deleted]);
    });

    Route::patch('/settings/push', function (Request $r) {
        $user = $r->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $r->validate(['enabled' => 'required|boolean']);
        $user->push_notifications_enabled = $r->boolean('enabled');
        $user->save();

        return response()->json(['status' => true, 'enabled' => $user->push_notifications_enabled]);
    });
    // ===== End Device Tokens & Push Prefs =====
});

// ======= Admin Routes (extra) =======
// ملاحظة: تم الإبقاء على باقي مسارات الأدمن، مع إزالة تكرار admin/chats المعرّف أعلاه.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/security-permits', [SecurityPermitController::class, 'index']);
    Route::put('/security-permits/{id}/status', [SecurityPermitController::class, 'updateStatus']);
    Route::put('/restaurants/{id}/the-best', [RestaurantController::class, 'updateTheBest']);
    Route::put('/properties/{id}/the-best', [PropertyController::class, 'updateTheBest']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('/orders/{order}/reject', [OrderController::class, 'reject']);
    Route::post('/car-orders/{id}/admin-approve', [CarServiceOrderController::class, 'approveByAdmin']);
    Route::post('/car-orders/{id}/admin-reject', [CarServiceOrderController::class, 'rejectByAdmin']);
    Route::get('/admin/service-requests/all', [AdminServiceRequestController::class, 'archive']);
});

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/service-requests', [\App\Http\Controllers\Admin\ServiceRequestAdminController::class, 'index']);
    Route::post('/service-requests/{id}/approve', [\App\Http\Controllers\Admin\ServiceRequestAdminController::class, 'approve']);
    Route::post('/service-requests/{id}/reject', [\App\Http\Controllers\Admin\ServiceRequestAdminController::class, 'reject']);
});

// ======= Menu Sections and Items (Public) =======
Route::post('/menu-sections', [MenuSectionController::class, 'store']);
Route::put('/menu-sections/{id}', [MenuSectionController::class, 'update']);
Route::delete('/menu-sections/{id}', [MenuSectionController::class, 'destroy']);
Route::get('/restaurants/{restaurantId}/menu-sections', [MenuSectionController::class, 'index']);

Route::post('/menu-items', [MenuItemController::class, 'store']);
Route::put('/menu-items/{id}', [MenuItemController::class, 'update']);
Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy']);
Route::get('/menu-sections/{sectionId}/items', [MenuItemController::class, 'index']);

// ======= Restaurant Banners (Public) =======
Route::get('/restaurant-banners', [RestaurantBannerController::class, 'index']);
Route::post('/restaurant-banners', [RestaurantBannerController::class, 'store']);
Route::delete('/restaurant-banners/{banner}', [RestaurantBannerController::class, 'destroy']);

// ======= Public Restaurants (Public) =======
Route::get('/public-restaurants', [PublicRestaurantController::class, 'index']);
Route::get('/public-restaurants/{user}', [PublicRestaurantController::class, 'show']);

// !! المسار الجديد الخاص بالعقارات !!
Route::get('/public-properties', [PublicPropertyController::class, 'index']);

// My Orders (Authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-orders/all', [MyOrdersController::class, 'getAllMyOrders']);
});

// Legacy conversation read route (for backward compatibility)
Route::post('/{id}/read', [ConversationController::class, 'markAsRead']);

// Password reset (Public)
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
