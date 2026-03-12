<?php

declare(strict_types=1);

namespace Tests\Feature\Expense;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Trip $trip;

    private ExpenseCategory $transportCategory;

    private ExpenseCategory $foodCategory;

    private ExpenseCategory $souvenirCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->trip = Trip::factory()->create(['created_by' => $this->user->id]);
        TripMember::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);

        // テスト用カテゴリを作成
        $this->transportCategory = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
            'sort_order' => 1,
        ]);
        $this->foodCategory = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '食費',
            'key' => 'food',
            'sort_order' => 2,
        ]);
        $this->souvenirCategory = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => 'お土産',
            'key' => 'souvenir',
            'sort_order' => 3,
        ]);
    }

    // ========================================
    // GET /api/trips/{tripId}/expenses
    // ========================================

    public function test_index_returns_expense_list(): void
    {
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'description' => '電車代',
            'amount' => 1500,
            'expense_category_id' => $this->transportCategory->id,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'description' => '赤福',
            'amount' => 800,
            'expense_category_id' => $this->foodCategory->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'description', 'amount', 'category_id', 'category_name', 'category_key', 'paid_at', 'is_shared'],
                ],
            ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_category(): void
    {
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'expense_category_id' => $this->transportCategory->id,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'expense_category_id' => $this->foodCategory->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses?category_id={$this->transportCategory->id}");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}/expenses");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/expenses
    // ========================================

    public function test_store_creates_expense_and_returns_201(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expenses", [
                'description' => '近鉄特急券',
                'amount' => 3000,
                'expense_category_id' => $this->transportCategory->id,
                'paid_at' => '2026-04-01',
                'is_shared' => true,
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'description', 'amount', 'category_id', 'category_name', 'category_key', 'paid_at', 'is_shared'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'description' => '近鉄特急券',
                    'amount' => 3000,
                    'category_id' => $this->transportCategory->id,
                    'category_key' => 'transport',
                    'category_name' => '交通費',
                    'paid_at' => '2026-04-01',
                    'is_shared' => true,
                ],
            ]);

        $this->assertDatabaseHas('expenses', [
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'description' => '近鉄特急券',
            'amount' => 3000,
        ]);
    }

    public function test_store_returns_422_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expenses", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['description', 'amount', 'paid_at', 'expense_category_id']);
    }

    public function test_store_returns_422_with_nonexistent_expense_category_id(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expenses", [
                'description' => 'テスト費用',
                'amount' => 1000,
                'expense_category_id' => 99999,
                'paid_at' => '2026-04-01',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['expense_category_id']);
    }

    // ========================================
    // DELETE /api/trips/{tripId}/expenses/{id}
    // ========================================

    public function test_destroy_deletes_expense_and_returns_204(): void
    {
        $expense = Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'expense_category_id' => $this->transportCategory->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/trips/{$this->trip->id}/expenses/{$expense->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    // ========================================
    // GET /api/trips/{tripId}/expenses/summary
    // ========================================

    public function test_summary_returns_expense_summary(): void
    {
        $user2 = User::factory()->create();
        TripMember::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $user2->id,
            'role' => 'member',
        ]);

        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'amount' => 3000,
            'expense_category_id' => $this->transportCategory->id,
            'is_shared' => true,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $user2->id,
            'amount' => 1000,
            'expense_category_id' => $this->foodCategory->id,
            'is_shared' => true,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'amount' => 500,
            'expense_category_id' => $this->souvenirCategory->id,
            'is_shared' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses/summary");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'total_amount',
                    'shared_total',
                    'per_person',
                    'by_category',
                    'by_user',
                    'settlement',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(4500, $data['total_amount']);
        $this->assertEquals(4000, $data['shared_total']);
        $this->assertEquals(2000, $data['per_person']);
    }

    public function test_summary_returns_zeros_for_empty_data(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses/summary");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'total_amount' => 0,
                    'shared_total' => 0,
                    'per_person' => 0,
                    'by_category' => [],
                    'by_user' => [],
                    'settlement' => [],
                ],
            ]);
    }
}
