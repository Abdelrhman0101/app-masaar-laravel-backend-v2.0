<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment variables
        config([
            'services.google.client_id' => 'test_client_id',
            'services.google.client_secret' => 'test_client_secret',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);
    }

    /** @test */
    public function it_can_redirect_to_google_oauth()
    {
        $response = $this->get(route('google.redirect'));
        
        // Should redirect to Google OAuth
        $this->assertTrue($response->isRedirection());
    }

    /** @test */
    public function it_can_handle_google_callback_for_existing_user()
    {
        // Create existing user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'user_type' => 'normal',
            'google_id' => null,
        ]);

        // Mock Socialite
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Test User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get(route('google.callback'));

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');
        
        // User should be authenticated
        $this->assertTrue(Auth::check());
        
        // User should have Google ID updated
        $user->refresh();
        $this->assertEquals('google_123', $user->google_id);
        $this->assertEquals('google', $user->login_type);
    }

    /** @test */
    public function it_can_create_new_user_from_google_oauth()
    {
        // Mock Socialite
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_456');
        $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('New User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar2.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get(route('google.callback'));

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');
        
        // User should be authenticated
        $this->assertTrue(Auth::check());
        
        // New user should be created
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google_456', $user->google_id);
        $this->assertEquals('normal', $user->user_type);
        $this->assertEquals('google', $user->login_type);
        $this->assertTrue($user->is_email_verified);
        $this->assertTrue($user->account_active);
        $this->assertTrue($user->is_approved);
    }

    /** @test */
    public function it_handles_google_oauth_errors_gracefully()
    {
        // Mock Socialite to throw exception
        Socialite::shouldReceive('driver->user')->andThrow(new \Exception('OAuth Error'));

        $response = $this->get(route('google.callback'));

        // Should redirect to login with error
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'حدث خطأ أثناء تسجيل الدخول بـ Google');
        
        // User should not be authenticated
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function it_ensures_normal_user_type_restriction()
    {
        // Mock Socialite
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_789');
        $socialiteUser->shouldReceive('getEmail')->andReturn('normaluser@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Normal User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar3.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get(route('google.callback'));

        // New user should be created with normal type
        $user = User::where('email', 'normaluser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('normal', $user->user_type);
        
        // User should not have admin privileges
        $this->assertNotEquals('admin', $user->user_type);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}