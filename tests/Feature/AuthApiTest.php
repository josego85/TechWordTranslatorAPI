<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \App\Http\Middleware\JWTMiddleware::class,
        ]);
        Log::spy();
    }

    public function test_register_creates_user_successfully(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!@#Test',
            'password_confirmation' => 'Password123!@#Test',
        ];

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['user'],
                'message',
            ])
            ->assertJson([
                'message' => 'User registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('User registered successfully', \Mockery::on(fn ($context) => $context['email'] === 'test@example.com' && isset($context['ip'])));
    }

    public function test_register_fails_with_invalid_password(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ];

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!@#Test',
            'password_confirmation' => 'Password123!@#Test',
        ];

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_register_fails_without_password_confirmation(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!@#Test',
        ];

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_login_successful_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token'],
                'message',
            ])
            ->assertJson([
                'message' => 'Successful login',
            ]);

        $this->assertNotEmpty($response->json('data.token'));

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Successful login', \Mockery::on(fn ($context) => $context['email'] === 'test@example.com' && isset($context['ip'])));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Failed login attempt', \Mockery::on(fn ($context) => $context['email'] === 'test@example.com' && isset($context['ip'])));
    }

    public function test_login_fails_with_nonexistent_user(): void
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_login_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/user/login', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_logout_successfully(): void
    {
        $user  = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/user/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('User logged out', \Mockery::on(fn ($context) => isset($context['ip'])));
    }

    public function test_logout_fails_without_token(): void
    {
        $response = $this->postJson('/api/v1/user/logout');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Logout failed',
            ]);

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Logout failed', \Mockery::on(fn ($context) => isset($context['error']) && isset($context['ip'])));
    }

    public function test_send_response_helper_formats_correctly(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
            ]);
    }

    public function test_send_error_helper_formats_correctly(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
