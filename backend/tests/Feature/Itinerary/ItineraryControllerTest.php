<?php

declare(strict_types=1);

namespace Tests\Feature\Itinerary;

use App\Models\ItineraryItem;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ItineraryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ========================================
    // GET /api/itinerary
    // ========================================

    public function test_index_returns_itinerary_list(): void
    {
        ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'title' => '伊勢神宮参拝',
            'date' => '2026-04-01',
            'sort_order' => 0,
        ]);
        ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'おかげ横丁散策',
            'date' => '2026-04-01',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/itinerary');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'title', 'date', 'sort_order'],
                ],
            ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_date(): void
    {
        ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-04-01',
        ]);
        ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-04-02',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/itinerary?date=2026-04-01');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson('/api/itinerary');

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/itinerary
    // ========================================

    public function test_store_creates_itinerary_item_and_returns_201(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/itinerary', [
                'title' => '伊勢神宮参拝',
                'date' => '2026-04-01',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'transport' => 'train',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'title', 'date', 'start_time', 'end_time', 'transport'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'title' => '伊勢神宮参拝',
                    'date' => '2026-04-01',
                    'transport' => 'train',
                ],
            ]);

        $this->assertDatabaseHas('itinerary_items', [
            'user_id' => $this->user->id,
            'title' => '伊勢神宮参拝',
        ]);
    }

    public function test_store_with_spot_id(): void
    {
        $spot = Spot::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/itinerary', [
                'title' => 'スポット訪問',
                'date' => '2026-04-01',
                'spot_id' => $spot->id,
            ]);

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'spot_id' => $spot->id,
                ],
            ]);
    }

    public function test_store_returns_422_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/itinerary', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'date']);
    }

    public function test_store_returns_422_with_invalid_date_format(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/itinerary', [
                'title' => 'テスト',
                'date' => '2026/04/01',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['date']);
    }

    public function test_store_returns_422_with_invalid_transport(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/itinerary', [
                'title' => 'テスト',
                'date' => '2026-04-01',
                'transport' => 'bicycle',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['transport']);
    }

    // ========================================
    // PATCH /api/itinerary/{id}
    // ========================================

    public function test_update_modifies_itinerary_item(): void
    {
        $item = ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'title' => '元のタイトル',
            'date' => '2026-04-01',
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/itinerary/{$item->id}", [
                'title' => '更新後のタイトル',
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'title' => '更新後のタイトル',
                    'date' => '2026-04-01',
                ],
            ]);
    }

    public function test_update_returns_404_for_nonexistent_item(): void
    {
        $response = $this->actingAs($this->user)
            ->patchJson('/api/itinerary/9999', [
                'title' => '更新',
            ]);

        $response->assertNotFound()
            ->assertJson(['message' => 'Not found.']);
    }

    // ========================================
    // DELETE /api/itinerary/{id}
    // ========================================

    public function test_destroy_deletes_itinerary_item_and_returns_204(): void
    {
        $item = ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/itinerary/{$item->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('itinerary_items', ['id' => $item->id]);
    }

    // ========================================
    // PATCH /api/itinerary/reorder
    // ========================================

    public function test_reorder_updates_sort_orders(): void
    {
        $item1 = ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'sort_order' => 0,
            'date' => '2026-04-01',
        ]);
        $item2 = ItineraryItem::factory()->create([
            'user_id' => $this->user->id,
            'sort_order' => 1,
            'date' => '2026-04-01',
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson('/api/itinerary/reorder', [
                'items' => [
                    ['id' => $item1->id, 'sort_order' => 1],
                    ['id' => $item2->id, 'sort_order' => 0],
                ],
            ]);

        $response->assertOk()
            ->assertJson(['message' => 'Reordered successfully.']);

        $this->assertDatabaseHas('itinerary_items', ['id' => $item1->id, 'sort_order' => 1]);
        $this->assertDatabaseHas('itinerary_items', ['id' => $item2->id, 'sort_order' => 0]);
    }

    public function test_reorder_returns_422_with_empty_items(): void
    {
        $response = $this->actingAs($this->user)
            ->patchJson('/api/itinerary/reorder', [
                'items' => [],
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }

    public function test_reorder_returns_422_without_items(): void
    {
        $response = $this->actingAs($this->user)
            ->patchJson('/api/itinerary/reorder', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }
}
