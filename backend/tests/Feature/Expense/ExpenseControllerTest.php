<?php

declare(strict_types=1);

namespace Tests\Feature\Expense;

use App\Models\Expense;
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
            'category' => 'transport',
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'description' => '赤福',
            'amount' => 800,
            'category' => 'food',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'description', 'amount', 'category', 'paid_at', 'is_shared'],
                ],
            ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_category(): void
    {
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'category' => 'transport',
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'category' => 'food',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expenses?category=transport");

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
                'category' => 'transport',
                'paid_at' => '2026-04-01',
                'is_shared' => true,
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'description', 'amount', 'category', 'paid_at', 'is_shared'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'description' => '近鉄特急券',
                    'amount' => 3000,
                    'category' => 'transport',
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
            ->assertJsonValidationErrors(['description', 'amount', 'paid_at']);
    }

    // ========================================
    // DELETE /api/trips/{tripId}/expenses/{id}
    // ========================================

    public function test_destroy_deletes_expense_and_returns_204(): void
    {
        $expense = Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
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
            'category' => 'transport',
            'is_shared' => true,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $user2->id,
            'amount' => 1000,
            'category' => 'food',
            'is_shared' => true,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'amount' => 500,
            'category' => 'souvenir',
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
