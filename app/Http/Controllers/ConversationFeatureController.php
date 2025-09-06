<?php

namespace App\Http\Controllers;

use App\Events\UserTyping;
use App\Events\UserStatusChanged;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ConversationFeatureController extends Controller
{
    /**
     * Handle typing indicator events
     */
    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user is participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مسموح لك بالوصول إلى هذه المحادثة'
            ], 403);
        }

        // Check if conversation is open
        if ($conversation->status !== 'open') {
            return response()->json([
                'status' => false,
                'message' => 'المحادثة مغلقة'
            ], 400);
        }

        $isTyping = $request->boolean('is_typing');
        
        // Store typing status in cache with expiry
        $cacheKey = "typing.{$conversation->id}.{$user->id}";
        
        if ($isTyping) {
            Cache::put($cacheKey, true, now()->addSeconds(10));
        } else {
            Cache::forget($cacheKey);
        }

        // Broadcast typing event
        broadcast(new UserTyping($user, $conversation, $isTyping));

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة الكتابة'
        ]);
    }

    /**
     * Get typing users for a conversation
     */
    public function getTypingUsers(Conversation $conversation): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user is participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مسموح لك بالوصول إلى هذه المحادثة'
            ], 403);
        }

        $typingUsers = [];
        $participants = $conversation->participants();
        
        foreach ($participants as $participant) {
            if ($participant->id === $user->id) {
                continue; // Skip current user
            }
            
            $cacheKey = "typing.{$conversation->id}.{$participant->id}";
            if (Cache::has($cacheKey)) {
                $typingUsers[] = [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'user_type' => $participant->user_type,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'typing_users' => $typingUsers
        ]);
    }

    /**
     * Update user online status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:online,offline,away',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $status = $request->input('status');
        $lastSeen = now();
        
        // Store user status in cache
        $cacheKey = "user_status.{$user->id}";
        Cache::put($cacheKey, [
            'status' => $status,
            'last_seen' => $lastSeen,
        ], now()->addHours(24));

        // Update user's last_seen in database if going offline
        if ($status === 'offline') {
            $user->update(['last_seen_at' => $lastSeen]);
        }

        // Broadcast status change
        broadcast(new UserStatusChanged($user, $status, $lastSeen));

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة المستخدم',
            'user_status' => $status,
            'last_seen' => $lastSeen->toISOString()
        ]);
    }

    /**
     * Get user online status
     */
    public function getUserStatus(User $targetUser): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if users have any common conversations (without relying on a non-existent relation)
        $hasCommonConversation = Conversation::query()
            ->where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
            })
            ->where(function ($q) use ($targetUser) {
                $q->where('user1_id', $targetUser->id)
                  ->orWhere('user2_id', $targetUser->id);
            })
            ->exists();
            
        if (!$hasCommonConversation && $user->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'غير مسموح لك بمشاهدة حالة هذا المستخدم'
            ], 403);
        }

        $cacheKey = "user_status.{$targetUser->id}";
        $cachedStatus = Cache::get($cacheKey);
        
        if ($cachedStatus) {
            $status = $cachedStatus['status'];
            $lastSeen = $cachedStatus['last_seen'];
        } else {
            $status = 'offline';
            $lastSeen = $targetUser->last_seen_at ?? $targetUser->updated_at;
        }

        return response()->json([
            'status' => true,
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'user_type' => $targetUser->user_type,
            ],
            'online_status' => $status,
            'last_seen' => $lastSeen,
            'is_online' => $status === 'online'
        ]);
    }

    /**
     * Get conversation participants status
     */
    public function getConversationParticipantsStatus(Conversation $conversation): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user is participant in this conversation
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مسموح لك بالوصول إلى هذه المحادثة'
            ], 403);
        }

        $participants = $conversation->participants();
        $participantsStatus = [];
        
        foreach ($participants as $participant) {
            if ($participant->id === $user->id) {
                continue; // Skip current user
            }
            
            $cacheKey = "user_status.{$participant->id}";
            $cachedStatus = Cache::get($cacheKey);
            
            if ($cachedStatus) {
                $status = $cachedStatus['status'];
                $lastSeen = $cachedStatus['last_seen'];
            } else {
                $status = 'offline';
                $lastSeen = $participant->last_seen_at ?? $participant->updated_at;
            }
            
            $participantsStatus[] = [
                'id' => $participant->id,
                'name' => $participant->name,
                'user_type' => $participant->user_type,
                'online_status' => $status,
                'last_seen' => $lastSeen,
                'is_online' => $status === 'online'
            ];
        }

        return response()->json([
            'status' => true,
            'participants' => $participantsStatus
        ]);
    }

    /**
     * Mark user as active (heartbeat)
     */
    public function heartbeat(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update user status to online
        $cacheKey = "user_status.{$user->id}";
        Cache::put($cacheKey, [
            'status' => 'online',
            'last_seen' => now(),
        ], now()->addMinutes(5));

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة النشاط'
        ]);
    }
}