<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OtpService;
use App\Notifications\EmailVerificationOtp;
use App\Notifications\PasswordResetOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->otpService = new OtpService();
        Notification::fake();
    }

    /** @test */
    public function user_registration_creates_inactive_account()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
            'governorate' => 'Test Governorate',
            'user_type' => 'normal_user'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => true,
                    'message' => 'تم إنشاء الحساب بنجاح. يرجى تأكيد بريدك الإلكتروني لتفعيل الحساب.'
                ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertFalse($user->is_email_verified);
        $this->assertFalse($user->account_active);
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotNull($user->email_verification_expires_at);

        Notification::assertSentTo($user, EmailVerificationOtp::class);
    }

    /** @test */
    public function inactive_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_email_verified' => false,
            'account_active' => false
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'status' => false,
                    'message' => 'الحساب غير مفعل. يرجى تأكيد بريدك الإلكتروني أولاً.',
                    'email_verification_required' => true
                ]);
    }

    /** @test */
    public function can_send_email_verification_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_email_verified' => false,
            'account_active' => false
        ]);

        $response = $this->postJson('/api/otp/send-email-verification', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'تم إرسال رمز التأكيد إلى بريدك الإلكتروني.'
                ]);

        $user->refresh();
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotNull($user->email_verification_expires_at);
        $this->assertNotNull($user->email_verification_sent_at);

        Notification::assertSentTo($user, EmailVerificationOtp::class);
    }

    /** @test */
    public function can_verify_email_with_correct_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_email_verified' => false,
            'account_active' => false
        ]);

        // Generate OTP
        $this->otpService->generateEmailVerificationOtp($user);
        $user->refresh();
        
        // Get the plain OTP (in real scenario, user gets this via email)
        $plainOtp = $user->email_verification_code; // This is hashed in DB
        
        // We need to generate a test OTP for verification
        $testOtp = '123456';
        $user->update([
            'email_verification_code' => Hash::make($testOtp),
            'email_verification_expires_at' => now()->addMinutes(15)
        ]);

        $response = $this->postJson('/api/otp/verify-email', [
            'email' => 'test@example.com',
            'verification_code' => $testOtp
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'تم تأكيد البريد الإلكتروني بنجاح. يمكنك الآن تسجيل الدخول.'
                ]);

        $user->refresh();
        $this->assertTrue($user->is_email_verified);
        $this->assertTrue($user->account_active);
        $this->assertNull($user->email_verification_code);
    }

    /** @test */
    public function cannot_verify_email_with_incorrect_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_email_verified' => false,
            'account_active' => false,
            'email_verification_code' => Hash::make('123456'),
            'email_verification_expires_at' => now()->addMinutes(15)
        ]);

        $response = $this->postJson('/api/otp/verify-email', [
            'email' => 'test@example.com',
            'verification_code' => '654321' // Wrong OTP
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'رمز التأكيد غير صحيح.'
                ]);

        $user->refresh();
        $this->assertFalse($user->is_email_verified);
        $this->assertFalse($user->account_active);
    }

    /** @test */
    public function cannot_verify_email_with_expired_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_email_verified' => false,
            'account_active' => false,
            'email_verification_code' => Hash::make('123456'),
            'email_verification_expires_at' => now()->subMinutes(1) // Expired
        ]);

        $response = $this->postJson('/api/otp/verify-email', [
            'email' => 'test@example.com',
            'verification_code' => '123456'
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'انتهت صلاحية رمز التأكيد. يرجى طلب رمز جديد.'
                ]);
    }

    /** @test */
    public function can_send_password_reset_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $response = $this->postJson('/api/otp/send-password-reset', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.'
                ]);

        $user->refresh();
        $this->assertNotNull($user->password_reset_code);
        $this->assertNotNull($user->password_reset_expires_at);

        Notification::assertSentTo($user, PasswordResetOtp::class);
    }

    /** @test */
    public function can_reset_password_with_valid_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword')
        ]);

        // Set up password reset OTP
        $testOtp = '123456';
        $user->update([
            'password_reset_code' => Hash::make($testOtp),
            'password_reset_expires_at' => now()->addMinutes(15)
        ]);

        // Verify OTP first
        $verifyResponse = $this->postJson('/api/otp/verify-password-reset', [
            'email' => 'test@example.com',
            'verification_code' => $testOtp
        ]);

        $verifyResponse->assertStatus(200);

        // Reset password
        $resetResponse = $this->postJson('/api/otp/reset-password', [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $resetResponse->assertStatus(200)
                     ->assertJson([
                         'status' => true,
                         'message' => 'تم تغيير كلمة المرور بنجاح.'
                     ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertNull($user->password_reset_code);
    }

    /** @test */
    public function rate_limiting_prevents_otp_spam()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        // Send 3 OTP requests (should be allowed)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/otp/send-email-verification', [
                'email' => 'test@example.com'
            ]);
            $response->assertStatus(200);
        }

        // 4th request should be rate limited
        $response = $this->postJson('/api/otp/send-email-verification', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(429)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'retry_after'
                ]);
    }

    /** @test */
    public function otp_service_generates_valid_codes()
    {
        $user = User::factory()->create();
        
        $result = $this->otpService->generateEmailVerificationOtp($user);
        
        $this->assertTrue($result);
        $user->refresh();
        
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotNull($user->email_verification_expires_at);
        $this->assertTrue($user->email_verification_expires_at->isFuture());
    }

    /** @test */
    public function otp_service_validates_codes_correctly()
    {
        $user = User::factory()->create();
        $testOtp = '123456';
        
        $user->update([
            'email_verification_code' => Hash::make($testOtp),
            'email_verification_expires_at' => now()->addMinutes(15),
            'email_verification_attempts' => 0
        ]);

        // Valid OTP should return true
        $result = $this->otpService->verifyEmailOtp($user, $testOtp);
        $this->assertTrue($result);

        // Invalid OTP should return false
        $user->update([
            'email_verification_code' => Hash::make($testOtp),
            'email_verification_expires_at' => now()->addMinutes(15),
            'email_verification_attempts' => 0
        ]);
        
        $result = $this->otpService->verifyEmailOtp($user, '654321');
        $this->assertFalse($result);
    }
}