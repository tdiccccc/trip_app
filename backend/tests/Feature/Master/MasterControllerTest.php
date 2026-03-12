<?php

declare(strict_types=1);

namespace Tests\Feature\Master;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MasterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // GET /api/master/expense-categories
    // ========================================

    public function test_expense_categories_returns_all_categories_with_labels(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/master/expense-categories');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    ['key' => 'transport', 'label' => '交通'],
                    ['key' => 'food', 'label' => '食事'],
                    ['key' => 'souvenir', 'label' => 'お土産'],
                    ['key' => 'accommodation', 'label' => '宿泊'],
                    ['key' => 'other', 'label' => 'その他'],
                ],
            ]);
    }

    public function test_expense_categories_as_guest_returns_401(): void
    {
        $response = $this->getJson('/api/master/expense-categories');

        $response->assertUnauthorized();
    }

    // ========================================
    // GET /api/master/assignees
    // ========================================

    public function test_assignees_returns_all_assignees_with_labels(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/master/assignees');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    ['key' => 'self', 'label' => '自分'],
                    ['key' => 'partner', 'label' => 'パートナー'],
                    ['key' => 'shared', 'label' => '共有'],
                ],
            ]);
    }

    public function test_assignees_as_guest_returns_401(): void
    {
        $response = $this->getJson('/api/master/assignees');

        $response->assertUnauthorized();
    }
}
