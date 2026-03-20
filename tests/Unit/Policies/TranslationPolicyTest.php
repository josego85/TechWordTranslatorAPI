<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\TranslationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationPolicyTest extends TestCase
{
    use RefreshDatabase;

    private TranslationPolicy $policy;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TranslationPolicy;
    }

    public function test_jwt_user_without_sanctum_token_can_write(): void
    {
        $user = User::factory()->create();
        // No Sanctum token set — simulates a JWT-authenticated user

        $this->assertTrue($this->policy->write($user));
    }

    public function test_sanctum_user_with_translations_write_ability_can_write(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('mcp-server', ['translations:write']);
        $user->withAccessToken($token->accessToken);

        $this->assertTrue($this->policy->write($user));
    }

    public function test_sanctum_user_without_translations_write_ability_cannot_write(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('mcp-server', ['words:write']);
        $user->withAccessToken($token->accessToken);

        $this->assertFalse($this->policy->write($user));
    }

    public function test_sanctum_user_with_no_abilities_cannot_write(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('mcp-server', []);
        $user->withAccessToken($token->accessToken);

        $this->assertFalse($this->policy->write($user));
    }
}
