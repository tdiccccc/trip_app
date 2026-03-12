<?php

declare(strict_types=1);

namespace Tests\Feature\Master;

use App\Models\ExpenseCategory;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MasterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // GET /api/master/expense-categories?trip_id={tripId}
    // ========================================

    public function test_expense_categories_returns_all_categories(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['created_by' => $user->id]);

        // デフォルトカテゴリを作成
        ExpenseCategory::factory()->create([
            'trip_id' => $trip->id,
            'name' => '交通費',
            'key' => 'transport',
            'sort_order' => 1,
        ]);
        ExpenseCategory::factory()->create([
            'trip_id' => $trip->id,
            'name' => '食費',
            'key' => 'food',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->getJson("/api/master/expense-categories?trip_id={$trip->id}");

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'trip_id', 'name', 'key', 'color', 'sort_order'],
                ],
            ]);
    }

    public function test_expense_categories_returns_422_without_trip_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/master/expense-categories');

        $response->assertStatus(422);
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
