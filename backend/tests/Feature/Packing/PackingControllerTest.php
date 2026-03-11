<?php

declare(strict_types=1);

namespace Tests\Feature\Packing;

use App\Models\PackingItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PackingControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ========================================
    // GET /api/packing
    // ========================================

    public function test_index_returns_packing_list(): void
    {
        PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'name' => '歯ブラシ',
            'assignee' => 'self',
        ]);
        PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'カメラ',
            'assignee' => 'shared',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/packing');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'name', 'is_checked', 'assignee', 'category', 'sort_order'],
                ],
            ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_assignee(): void
    {
        PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'assignee' => 'self',
        ]);
        PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'assignee' => 'shared',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/packing?assignee=self');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson('/api/packing');

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/packing
    // ========================================

    public function test_store_creates_packing_item_and_returns_201(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/packing', [
                'name' => '日焼け止め',
                'assignee' => 'self',
                'category' => '衛生用品',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'name', 'is_checked', 'assignee', 'category', 'sort_order'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'name' => '日焼け止め',
                    'assignee' => 'self',
                    'is_checked' => false,
                ],
            ]);

        $this->assertDatabaseHas('packing_items', [
            'user_id' => $this->user->id,
            'name' => '日焼け止め',
        ]);
    }

    public function test_store_returns_422_without_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/packing', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    // ========================================
    // PATCH /api/packing/{id}
    // ========================================

    public function test_update_modifies_packing_item(): void
    {
        $item = PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'name' => '元の名前',
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/packing/{$item->id}", [
                'name' => '更新後の名前',
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => '更新後の名前',
                ],
            ]);
    }

    public function test_update_toggles_checked_status(): void
    {
        $item = PackingItem::factory()->create([
            'user_id' => $this->user->id,
            'is_checked' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/packing/{$item->id}", [
                'is_checked' => true,
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'is_checked' => true,
                ],
            ]);
    }

    public function test_update_returns_404_for_nonexistent_item(): void
    {
        $response = $this->actingAs($this->user)
            ->patchJson('/api/packing/9999', [
                'name' => '更新',
            ]);

        $response->assertNotFound()
            ->assertJson(['message' => 'Not found.']);
    }

    // ========================================
    // DELETE /api/packing/{id}
    // ========================================

    public function test_destroy_deletes_packing_item_and_returns_204(): void
    {
        $item = PackingItem::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/packing/{$item->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('packing_items', ['id' => $item->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_item(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/packing/9999');

        $response->assertNotFound();
    }
}
