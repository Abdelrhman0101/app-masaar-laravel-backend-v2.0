<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use Symfony\Component\HttpFoundation\Response;

class ConversationParticipant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بالوصول'
            ], 401);
        }

        // Get conversation from route parameter
        $conversation = $request->route('conversation');
        
        if (!$conversation instanceof Conversation) {
            // Try to get conversation by ID
            $conversationId = $request->route('conversation') ?? $request->input('conversation_id');
            
            if (!$conversationId) {
                return response()->json([
                    'status' => false,
                    'message' => 'معرف المحادثة مطلوب'
                ], 400);
            }
            
            $conversation = Conversation::find($conversationId);
        }
        
        if (!$conversation) {
            return response()->json([
                'status' => false,
                'message' => 'المحادثة غير موجودة'
            ], 404);
        }

        // Check if user is a participant in this conversation
        if (!$conversation->hasParticipant($user)) {
            // Allow admin users to access all conversations
            if ($user->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مسموح لك بالوصول إلى هذه المحادثة'
                ], 403);
            }
        }

        // Add conversation to request for easy access in controller
        $request->merge(['conversation_instance' => $conversation]);
        
        return $next($request);
    }
}