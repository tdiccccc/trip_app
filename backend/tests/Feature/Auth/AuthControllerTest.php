<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    // ========================================
    // POST /api/login
    // ========================================

    public function test_login_with_valid_credentials_returns_user_data(): void
    {
        $response = $this->withHeaders([
                'Origin' => 'http://localhost',
            ])
            ->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'テストユーザー',
                    'email' => 'test@example.com',
                ],
            ]);
    }

    public function test_login_with_wrong_password_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_nonexistent_email_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_without_email_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_without_password_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_with_invalid_email_format_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    // ========================================
    // POST /api/logout
    // ========================================

    public function test_logout_as_authenticated_user_returns_204(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeaders([
                'Origin' => 'http://localhost',
            ])
            ->postJson('/api/logout');

        $response->assertNoContent();
    }

    public function test_logout_as_guest_returns_401(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }

    // ========================================
    // GET /api/user
    // ========================================

    public function test_me_as_authenticated_user_returns_user_data(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
            ])
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => 'テストユーザー',
                    'email' => 'test@example.com',
                ],
            ]);
    }

    public function test_me_as_guest_returns_401(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }
}
