<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Notifications\EmailVerificationOtp;
use App\Notifications\PasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class OtpAuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send email verification OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmailVerificationOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rate limiting
        $key = 'email-verification-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة مرة أخرى بعد ' . ceil($seconds / 60) . ' دقائق.',
                'retry_after' => $seconds
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        // Check if user is already verified
        if ($user->is_email_verified) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني مؤكد بالفعل.'
            ], 400);
        }

        $result = $this->otpService->generateEmailVerificationOtp($user);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Send OTP via email
        try {
            $user->notify(new EmailVerificationOtp(
                $result['otp'],
                $result['expires_at'],
                $user->name
            ));

            RateLimiter::hit($key, 300); // 5 minutes

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
                'expires_at' => $result['expires_at']->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email verification OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال رمز التحقق. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * Verify email verification OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmailVerificationOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rate limiting for verification attempts
        $key = 'verify-email-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة مرة أخرى بعد ' . ceil($seconds / 60) . ' دقائق.',
                'retry_after' => $seconds
            ], 429);
        }

        $user = User::where('email', $request->email)->first();
        $result = $this->otpService->verifyEmailVerificationOtp($user, $request->otp);

        if (!$result['success']) {
            RateLimiter::hit($key, 300); // 5 minutes
            return response()->json($result, 400);
        }

        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_email_verified' => $user->is_email_verified,
                'account_active' => $user->account_active
            ]
        ]);
    }

    /**
     * Send password reset OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPasswordResetOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rate limiting
        $key = 'password-reset-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة مرة أخرى بعد ' . ceil($seconds / 60) . ' دقائق.',
                'retry_after' => $seconds
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        // Check if user account is active
        if (!$user->account_active) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب غير مفعل. يرجى تأكيد بريدك الإلكتروني أولاً.'
            ], 400);
        }

        $result = $this->otpService->generatePasswordResetOtp($user);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Send OTP via email
        try {
            $user->notify(new PasswordResetOtp(
                $result['otp'],
                $result['expires_at'],
                $user->name
            ));

            RateLimiter::hit($key, 300); // 5 minutes

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.',
                'expires_at' => $result['expires_at']->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال رمز إعادة التعيين. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * Verify password reset OTP and reset password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rate limiting for reset attempts
        $key = 'reset-password-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة مرة أخرى بعد ' . ceil($seconds / 60) . ' دقائق.',
                'retry_after' => $seconds
            ], 429);
        }

        $user = User::where('email', $request->email)->first();
        $result = $this->otpService->verifyPasswordResetOtp($user, $request->otp);

        if (!$result['success']) {
            RateLimiter::hit($key, 300); // 5 minutes
            return response()->json($result, 400);
        }

        // Reset password
        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Clear password reset OTP
            $this->otpService->clearPasswordResetOtp($user);

            RateLimiter::clear($key);

            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين كلمة المرور بنجاح.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset password', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إعادة تعيين كلمة المرور. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * Reset password with OTP (alias for resetPasswordWithOtp)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->resetPasswordWithOtp($request);
    }

    /**
     * Verify password reset OTP (alias for resetPasswordWithOtp)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPasswordResetOtp(Request $request): JsonResponse
    {
        return $this->resetPasswordWithOtp($request);
    }

    /**
     * Resend OTP (for both email verification and password reset)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'type' => 'required|string|in:email_verification,password_reset',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->type === 'email_verification') {
            return $this->sendEmailVerificationOtp($request);
        } else {
            return $this->sendPasswordResetOtp($request);
        }
    }
}