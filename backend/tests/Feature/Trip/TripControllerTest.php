<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $member;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['name' => 'オーナー']);
        $this->member = User::factory()->create(['name' => 'メンバー']);

        $this->trip = Trip::factory()->create([
            'title' => '伊勢旅行',
            'description' => '伊勢神宮を参拝',
            'destination' => '伊勢',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-03',
            'created_by' => $this->owner->id,
        ]);

        TripMember::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
        ]);
        TripMember::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);
    }

    // ========================================
    // GET /api/trips
    // ========================================

    public function test_index_returns_trip_list_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson('/api/trips');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'destination', 'start_date', 'end_date', 'created_by', 'members'],
                ],
            ]);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('伊勢旅行', $response->json('data.0.title'));
    }

    public function test_index_returns_trips_where_user_is_member(): void
    {
        $response = $this->actingAs($this->member)
            ->getJson('/api/trips');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson('/api/trips');

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips
    // ========================================

    public function test_store_creates_trip_and_returns_201(): void
    {
        $response = $this->actingAs($this->owner)
            ->postJson('/api/trips', [
                'title' => '新しい旅行',
                'description' => '京都旅行の計画',
                'destination' => '京都',
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-03',
                'member_ids' => [$this->member->id],
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'title', 'description', 'destination', 'start_date', 'end_date', 'created_by', 'members'],
            ])
            ->assertJson([
                'data' => [
                    'title' => '新しい旅行',
                    'created_by' => $this->owner->id,
                ],
            ]);

        $this->assertDatabaseHas('trips', [
            'title' => '新しい旅行',
            'created_by' => $this->owner->id,
        ]);

        // owner は自動的に trip_members に追加される
        $tripId = $response->json('data.id');
        $this->assertDatabaseHas('trip_members', [
            'trip_id' => $tripId,
            'user_id' => $this->owner->id,
            'role' => 'owner',
        ]);
    }

    public function test_store_creates_default_expense_categories(): void
    {
        $response = $this->actingAs($this->owner)
            ->postJson('/api/trips', [
                'title' => 'カテゴリ確認旅行',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-03',
            ]);

        $response->assertCreated();
        $tripId = $response->json('data.id');

        // デフォルト5カテゴリが生成されることを確認
        $this->assertDatabaseCount('expense_categories', 5);
        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $tripId,
            'key' => 'transport',
            'name' => '交通費',
        ]);
        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $tripId,
            'key' => 'food',
            'name' => '食費',
        ]);
        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $tripId,
            'key' => 'souvenir',
            'name' => 'お土産',
        ]);
        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $tripId,
            'key' => 'accommodation',
            'name' => '宿泊費',
        ]);
        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $tripId,
            'key' => 'other',
            'name' => 'その他',
        ]);
    }

    public function test_store_returns_422_without_required_fields(): void
    {
        $response = $this->actingAs($this->owner)
            ->postJson('/api/trips', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'start_date', 'end_date']);
    }

    public function test_store_returns_422_when_end_date_before_start_date(): void
    {
        $response = $this->actingAs($this->owner)
            ->postJson('/api/trips', [
                'title' => 'テスト',
                'start_date' => '2026-05-03',
                'end_date' => '2026-05-01',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_store_returns_401_for_guest(): void
    {
        $response = $this->postJson('/api/trips', [
            'title' => '旅行',
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-03',
        ]);

        $response->assertUnauthorized();
    }

    // ========================================
    // GET /api/trips/{tripId}
    // ========================================

    public function test_show_returns_trip_detail_with_current_user_role(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson("/api/trips/{$this->trip->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'destination',
                    'start_date', 'end_date', 'created_by',
                    'members', 'current_user_role',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $this->trip->id,
                    'title' => '伊勢旅行',
                    'current_user_role' => 'owner',
                ],
            ]);
    }

    public function test_show_returns_member_role_for_member(): void
    {
        $response = $this->actingAs($this->member)
            ->getJson("/api/trips/{$this->trip->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'current_user_role' => 'member',
                ],
            ]);
    }

    public function test_show_returns_403_for_non_member(): void
    {
        $nonMember = User::factory()->create();

        $response = $this->actingAs($nonMember)
            ->getJson("/api/trips/{$this->trip->id}");

        $response->assertForbidden();
    }

    public function test_show_returns_401_for_guest(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}");

        $response->assertUnauthorized();
    }

    // ========================================
    // PATCH /api/trips/{tripId}
    // ========================================

    public function test_update_modifies_trip_as_owner(): void
    {
        $response = $this->actingAs($this->owner)
            ->patchJson("/api/trips/{$this->trip->id}", [
                'title' => '更新した伊勢旅行',
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'title' => '更新した伊勢旅行',
                ],
            ]);
    }

    public function test_update_returns_403_for_member(): void
    {
        $response = $this->actingAs($this->member)
            ->patchJson("/api/trips/{$this->trip->id}", [
                'title' => '勝手に更新',
            ]);

        $response->assertForbidden();
    }

    public function test_update_returns_401_for_guest(): void
    {
        $response = $this->patchJson("/api/trips/{$this->trip->id}", [
            'title' => '更新',
        ]);

        $response->assertUnauthorized();
    }

    // ========================================
    // DELETE /api/trips/{tripId}
    // ========================================

    public function test_destroy_deletes_trip_as_owner(): void
    {
        $response = $this->actingAs($this->owner)
            ->deleteJson("/api/trips/{$this->trip->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('trips', ['id' => $this->trip->id]);
    }

    public function test_destroy_returns_403_for_member(): void
    {
        $response = $this->actingAs($this->member)
            ->deleteJson("/api/trips/{$this->trip->id}");

        $response->assertForbidden();
    }

    public function test_destroy_returns_401_for_guest(): void
    {
        $response = $this->deleteJson("/api/trips/{$this->trip->id}");

        $response->assertUnauthorized();
    }
}
