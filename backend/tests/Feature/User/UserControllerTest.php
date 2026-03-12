<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // GET /api/users
    // ========================================

    public function test_index_returns_all_users(): void
    {
        $user1 = User::factory()->create(['name' => 'たろう', 'email' => 'taro@example.com']);
        $user2 = User::factory()->create(['name' => 'はなこ', 'email' => 'hanako@example.com']);

        $response = $this->actingAs($user1)->getJson('/api/users');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email'],
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    ['id' => $user1->id, 'name' => 'たろう', 'email' => 'taro@example.com'],
                    ['id' => $user2->id, 'name' => 'はなこ', 'email' => 'hanako@example.com'],
                ],
            ]);
    }

    public function test_index_as_guest_returns_401(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertUnauthorized();
    }
}
