<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->user_type === 'admin') {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'غير مسموح لك بالوصول إلى هذا المسار.'
        ], 403);
    }
}