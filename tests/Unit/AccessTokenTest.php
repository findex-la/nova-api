<?php

namespace Opscale\NovaAPI\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Opscale\NovaAPI\Tests\Fixtures\FixtureLoader;
use Opscale\NovaAPI\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Models\User;

#[CoversClass(User::class)]
final class AccessTokenTest extends TestCase
{
    use DatabaseTransactions;
    use FixtureLoader;

    #[Test]
    final public function can_create_access_token_without_expiration(): void
    {
        $testUser = $this->createTestUser();
        $abilities = ['products:read', 'products:create'];

        $newAccessToken = $testUser->createToken('Test Token', $abilities);

        /** @var \Laravel\Sanctum\PersonalAccessToken $accessToken */
        $accessToken = $newAccessToken->accessToken;
        $this->assertEquals('Test Token', $accessToken->name);
        $this->assertEquals($abilities, $accessToken->abilities);
        $this->assertNull($accessToken->expires_at);
    }

    #[Test]
    final public function can_create_access_token_with_expiration(): void
    {
        $testUser = $this->createTestUser();
        $abilities = ['users:read'];
        $expiresAt = Carbon::now()->addHour();

        $newAccessToken = $testUser->createToken('Expiring Token', $abilities, $expiresAt);

        /** @var \Laravel\Sanctum\PersonalAccessToken $accessToken */
        $accessToken = $newAccessToken->accessToken;
        $this->assertEquals('Expiring Token', $accessToken->name);
        $this->assertEquals($abilities, $accessToken->abilities);
        $this->assertNotNull($accessToken->expires_at);
        $this->assertEquals($expiresAt->format('Y-m-d H:i:s'), $accessToken->expires_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    final public function token_can_check_abilities(): void
    {
        $testUser = $this->createTestUser();
        $abilities = ['products:read', 'products:create'];

        $newAccessToken = $testUser->createToken('Test Token', $abilities);
        $testUser->withAccessToken($newAccessToken->accessToken);

        $this->assertTrue($testUser->tokenCan('products:read'));
        $this->assertTrue($testUser->tokenCan('products:create'));
        $this->assertFalse($testUser->tokenCan('products:delete'));
        $this->assertFalse($testUser->tokenCan('users:read'));
    }

    #[Test]
    final public function expired_token_is_identified(): void
    {
        $testUser = $this->createTestUser();
        $expiresAt = Carbon::now()->subHour(); // Expired 1 hour ago

        $newAccessToken = $testUser->createToken('Expired Token', ['products:read'], $expiresAt);

        /** @var \Laravel\Sanctum\PersonalAccessToken $accessToken */
        $accessToken = $newAccessToken->accessToken;
        $this->assertNotNull($accessToken->expires_at);
        $this->assertTrue($accessToken->expires_at->isPast());
    }

    #[Test]
    final public function token_without_abilities_can_do_nothing(): void
    {
        $testUser = $this->createTestUser();
        $newAccessToken = $testUser->createToken('No Abilities Token', []);
        $testUser->withAccessToken($newAccessToken->accessToken);

        $this->assertFalse($testUser->tokenCan('products:read'));
        $this->assertFalse($testUser->tokenCan('users:read'));
        $this->assertFalse($testUser->tokenCan('any:ability'));
    }

    #[Test]
    final public function token_can_be_revoked(): void
    {
        $testUser = $this->createTestUser();
        $newAccessToken = $testUser->createToken('Test Token', ['products:read']);

        /** @var \Laravel\Sanctum\PersonalAccessToken $accessToken */
        $accessToken = $newAccessToken->accessToken;
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $accessToken->id,
        ]);

        $accessToken->delete();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->id,
        ]);
    }

    private function createTestUser(): User
    {
        $userData = $this->getUserFixture('test_user');

        return User::query()->create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
        ]);
    }
}
