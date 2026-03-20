<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ServiceTokenApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
        Log::spy();
    }

    public function test_create_service_token_returns_201(): void
    {
        $response = $this->postJson('/api/v1/service-tokens');

        $response->assertStatus(201)
            ->assertJsonStructure(['token']);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_create_service_token_stores_token_in_database(): void
    {
        $this->postJson('/api/v1/service-tokens')->assertStatus(201);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'tokenable_type' => User::class,
            'name' => 'mcp-server',
        ]);
    }

    public function test_create_service_token_without_auth_returns_401(): void
    {
        $this->withMiddleware();

        $response = $this->postJson('/api/v1/service-tokens');

        $response->assertStatus(401);
    }

    public function test_revoke_service_token_returns_204(): void
    {
        $tokenResult = $this->user->createToken('mcp-server', ['words:write', 'translations:write']);

        $response = $this->deleteJson("/api/v1/service-tokens/{$tokenResult->accessToken->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenResult->accessToken->id,
        ]);
    }

    public function test_revoke_service_token_without_auth_returns_401(): void
    {
        $this->withMiddleware();

        $tokenResult = $this->user->createToken('mcp-server', ['words:write', 'translations:write']);

        $response = $this->deleteJson("/api/v1/service-tokens/{$tokenResult->accessToken->id}");

        $response->assertStatus(401);
    }

    public function test_revoke_nonexistent_token_returns_204(): void
    {
        // Idempotent DELETE — deleting a non-existent token is not an error
        $response = $this->deleteJson('/api/v1/service-tokens/99999');

        $response->assertStatus(204);
    }

    public function test_user_cannot_revoke_another_users_token(): void
    {
        $owner       = User::factory()->create();
        $tokenResult = $owner->createToken('mcp-server', ['words:write', 'translations:write']);

        // Attacker (current actingAs user) tries to delete the owner's token
        $response = $this->deleteJson("/api/v1/service-tokens/{$tokenResult->accessToken->id}");

        $response->assertStatus(204);

        // Token must still exist — tokens() scope is scoped to the attacker's user
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenResult->accessToken->id,
        ]);
    }

    public function test_create_service_token_logs_info(): void
    {
        $this->postJson('/api/v1/service-tokens')->assertStatus(201);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Service token created', \Mockery::on(
                fn ($ctx) => $ctx['user_id'] === $this->user->id && isset($ctx['ip'])
            ));
    }

    public function test_revoke_service_token_logs_warning(): void
    {
        $tokenResult = $this->user->createToken('mcp-server', ['words:write', 'translations:write']);

        $this->deleteJson("/api/v1/service-tokens/{$tokenResult->accessToken->id}")
            ->assertStatus(204);

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Service token revoked', \Mockery::on(
                fn ($ctx) => $ctx['token_id'] === $tokenResult->accessToken->id
                    && $ctx['user_id'] === $this->user->id
                    && isset($ctx['ip'])
            ));
    }
}
