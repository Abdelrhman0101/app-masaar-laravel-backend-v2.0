<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OtpRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): ResponseAlias
    {
        $key = $this->resolveRequestSignature($request);
        
        // Allow 3 OTP requests per 15 minutes per IP/email combination
        $maxAttempts = 3;
        $decayMinutes = 15;
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            return response()->json([
                'status' => false,
                'message' => "تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة مرة أخرى بعد {$minutes} دقيقة.",
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
            'X-RateLimit-Reset' => RateLimiter::availableIn($key) + time(),
        ]);
        
        return $response;
    }
    
    /**
     * Resolve request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $email = $request->input('email', 'unknown');
        $ip = $request->ip();
        $route = $request->route()->getName() ?? $request->path();
        
        return sha1($route . '|' . $email . '|' . $ip);
    }
}