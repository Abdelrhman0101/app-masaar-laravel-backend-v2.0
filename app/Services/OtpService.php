<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OtpService
{
    // OTP configuration constants
    const OTP_LENGTH = 6;
    const OTP_EXPIRY_MINUTES = 10;
    const MAX_ATTEMPTS = 3;
    const RATE_LIMIT_MINUTES = 5; // Minimum time between OTP requests
    
    /**
     * Generate a secure OTP code
     *
     * @param int $length
     * @return string
     */
    public function generateOtp(int $length = self::OTP_LENGTH): string
    {
        $characters = '0123456789';
        $otp = '';
        
        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $otp;
    }
    
    /**
     * Generate and store email verification OTP
     *
     * @param User $user
     * @return array
     */
    public function generateEmailVerificationOtp(User $user): array
    {
        // Check rate limiting
        if ($this->isRateLimited($user, 'email_verification')) {
            return [
                'success' => false,
                'message' => 'Please wait before requesting another verification code.',
                'retry_after' => $this->getRateLimitRetryAfter($user, 'email_verification')
            ];
        }
        
        $otp = $this->generateOtp();
        $hashedOtp = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES);
        
        $user->update([
            'email_verification_code' => $hashedOtp,
            'email_verification_expires_at' => $expiresAt,
            'email_verification_sent_at' => Carbon::now(),
            'email_verification_attempts' => 0, // Reset attempts on new OTP
        ]);
        
        Log::info('Email verification OTP generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => $expiresAt
        ]);
        
        return [
            'success' => true,
            'otp' => $otp, // Return plain OTP for sending via email
            'expires_at' => $expiresAt,
            'message' => 'Verification code sent successfully.'
        ];
    }
    
    /**
     * Generate and store password reset OTP
     *
     * @param User $user
     * @return array
     */
    public function generatePasswordResetOtp(User $user): array
    {
        // Check rate limiting
        if ($this->isRateLimited($user, 'password_reset')) {
            return [
                'success' => false,
                'message' => 'Please wait before requesting another reset code.',
                'retry_after' => $this->getRateLimitRetryAfter($user, 'password_reset')
            ];
        }
        
        $otp = $this->generateOtp();
        $hashedOtp = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES);
        
        $user->update([
            'password_reset_code' => $hashedOtp,
            'password_reset_expires_at' => $expiresAt,
            'password_reset_sent_at' => Carbon::now(),
            'password_reset_attempts' => 0, // Reset attempts on new OTP
        ]);
        
        Log::info('Password reset OTP generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => $expiresAt
        ]);
        
        return [
            'success' => true,
            'otp' => $otp, // Return plain OTP for sending via email
            'expires_at' => $expiresAt,
            'message' => 'Reset code sent successfully.'
        ];
    }
    
    /**
     * Verify email verification OTP
     *
     * @param User $user
     * @param string $otp
     * @return array
     */
    public function verifyEmailVerificationOtp(User $user, string $otp): array
    {
        Log::info('DEBUG OtpService: verifyEmailVerificationOtp called', [
            'user_id' => $user->id,
            'email' => $user->email,
            'input_otp' => $otp,
            'stored_otp_exists' => !empty($user->email_verification_code),
            'expires_at' => $user->email_verification_expires_at,
            'attempts' => $user->email_verification_attempts
        ]);
        
        // Check if OTP exists
        if (!$user->email_verification_code) {
            Log::warning('DEBUG OtpService: No verification code found for user', ['user_id' => $user->id]);
            return [
                'success' => false,
                'message' => 'No verification code found. Please request a new one.'
            ];
        }
        
        // Check if OTP has expired
        if (Carbon::now()->isAfter($user->email_verification_expires_at)) {
            Log::warning('DEBUG OtpService: OTP expired', [
                'user_id' => $user->id,
                'expires_at' => $user->email_verification_expires_at,
                'current_time' => Carbon::now()
            ]);
            $this->clearEmailVerificationOtp($user);
            return [
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.'
            ];
        }
        
        // Check attempts limit
        if ($user->email_verification_attempts >= self::MAX_ATTEMPTS) {
            $this->clearEmailVerificationOtp($user);
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please request a new verification code.'
            ];
        }
        
        // Verify OTP
        $hashCheck = Hash::check($otp, $user->email_verification_code);
        Log::info('DEBUG OtpService: Hash check result', [
            'user_id' => $user->id,
            'input_otp' => $otp,
            'hash_check_result' => $hashCheck,
            'stored_hash' => $user->email_verification_code
        ]);
        
        if (!$hashCheck) {
            $user->increment('email_verification_attempts');
            
            Log::warning('Invalid email verification OTP attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'input_otp' => $otp,
                'attempts' => $user->email_verification_attempts + 1
            ]);
            
            return [
                'success' => false,
                'message' => 'Invalid verification code.',
                'attempts_remaining' => self::MAX_ATTEMPTS - ($user->email_verification_attempts + 1)
            ];
        }
        
        // OTP is valid - activate account and clear OTP
        $user->update([
            'email_verified_at' => Carbon::now(),
            'is_email_verified' => true,
            'account_active' => true,
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'email_verification_sent_at' => null,
            'email_verification_attempts' => 0,
        ]);
        
        Log::info('Email verification successful', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return [
            'success' => true,
            'message' => 'Email verified successfully. Your account is now active.'
        ];
    }
    
    /**
     * Verify password reset OTP
     *
     * @param User $user
     * @param string $otp
     * @return array
     */
    public function verifyPasswordResetOtp(User $user, string $otp): array
    {
        // Check if OTP exists
        if (!$user->password_reset_code) {
            return [
                'success' => false,
                'message' => 'No reset code found. Please request a new one.'
            ];
        }
        
        // Check if OTP has expired
        if (Carbon::now()->isAfter($user->password_reset_expires_at)) {
            $this->clearPasswordResetOtp($user);
            return [
                'success' => false,
                'message' => 'Reset code has expired. Please request a new one.'
            ];
        }
        
        // Check attempts limit
        if ($user->password_reset_attempts >= self::MAX_ATTEMPTS) {
            $this->clearPasswordResetOtp($user);
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please request a new reset code.'
            ];
        }
        
        // Verify OTP
        if (!Hash::check($otp, $user->password_reset_code)) {
            $user->increment('password_reset_attempts');
            
            Log::warning('Invalid password reset OTP attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'attempts' => $user->password_reset_attempts + 1
            ]);
            
            return [
                'success' => false,
                'message' => 'Invalid reset code.',
                'attempts_remaining' => self::MAX_ATTEMPTS - ($user->password_reset_attempts + 1)
            ];
        }
        
        Log::info('Password reset OTP verified', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return [
            'success' => true,
            'message' => 'Reset code verified successfully.'
        ];
    }
    
    /**
     * Clear email verification OTP data
     *
     * @param User $user
     * @return void
     */
    public function clearEmailVerificationOtp(User $user): void
    {
        $user->update([
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'email_verification_sent_at' => null,
            'email_verification_attempts' => 0,
        ]);
    }
    
    /**
     * Clear password reset OTP data
     *
     * @param User $user
     * @return void
     */
    public function clearPasswordResetOtp(User $user): void
    {
        $user->update([
            'password_reset_code' => null,
            'password_reset_expires_at' => null,
            'password_reset_sent_at' => null,
            'password_reset_attempts' => 0,
        ]);
    }
    
    /**
     * Check if user is rate limited for OTP requests
     *
     * @param User $user
     * @param string $type
     * @return bool
     */
    private function isRateLimited(User $user, string $type): bool
    {
        $sentAtField = $type === 'email_verification' ? 'email_verification_sent_at' : 'password_reset_sent_at';
        
        if (!$user->$sentAtField) {
            return false;
        }
        
        $lastSentAt = Carbon::parse($user->$sentAtField);
        $rateLimitUntil = $lastSentAt->addMinutes(self::RATE_LIMIT_MINUTES);
        
        return Carbon::now()->isBefore($rateLimitUntil);
    }
    
    /**
     * Get rate limit retry after time in seconds
     *
     * @param User $user
     * @param string $type
     * @return int
     */
    private function getRateLimitRetryAfter(User $user, string $type): int
    {
        $sentAtField = $type === 'email_verification' ? 'email_verification_sent_at' : 'password_reset_sent_at';
        
        if (!$user->$sentAtField) {
            return 0;
        }
        
        $lastSentAt = Carbon::parse($user->$sentAtField);
        $rateLimitUntil = $lastSentAt->addMinutes(self::RATE_LIMIT_MINUTES);
        
        return Carbon::now()->diffInSeconds($rateLimitUntil, false);
    }
}