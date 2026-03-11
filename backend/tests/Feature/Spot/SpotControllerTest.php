<?php

declare(strict_types=1);

namespace Tests\Feature\Spot;

use App\Models\Photo;
use App\Models\Spot;
use App\Models\SpotMemo;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SpotControllerTest extends TestCase
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
    // GET /api/trips/{tripId}/spots
    // ========================================

    public function test_index_returns_spot_list(): void
    {
        Spot::factory()->create(['trip_id' => $this->trip->id, 'name' => 'スポットA', 'sort_order' => 1]);
        Spot::factory()->create(['trip_id' => $this->trip->id, 'name' => 'スポットB', 'sort_order' => 0]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/spots");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'address', 'category', 'sort_order'],
                ],
            ]);

        // sort_order 昇順で返るため、B が先
        $this->assertEquals('スポットB', $response->json('data.0.name'));
        $this->assertEquals('スポットA', $response->json('data.1.name'));
    }

    public function test_index_filters_by_category(): void
    {
        Spot::factory()->create(['trip_id' => $this->trip->id, 'name' => '観光スポット', 'category' => 'sightseeing']);
        Spot::factory()->create(['trip_id' => $this->trip->id, 'name' => 'レストラン', 'category' => 'food']);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/spots?category=food");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('レストラン', $response->json('data.0.name'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}/spots");

        $response->assertUnauthorized();
    }

    // ========================================
    // GET /api/trips/{tripId}/spots/{id}
    // ========================================

    public function test_show_returns_spot_with_memos_and_photos(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);
        SpotMemo::factory()->create(['spot_id' => $spot->id, 'user_id' => $this->user->id]);
        Photo::factory()->create(['trip_id' => $this->trip->id, 'spot_id' => $spot->id, 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/spots/{$spot->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'address', 'category',
                    'memos' => [['id', 'spot_id', 'user_id', 'body']],
                    'photos' => [['id', 'user_id', 'storage_path', 'original_filename']],
                ],
            ]);
    }

    public function test_show_returns_404_for_nonexistent_spot(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/spots/9999");

        $response->assertNotFound()
            ->assertJson(['message' => 'Not found.']);
    }

    // ========================================
    // POST /api/trips/{tripId}/spots/{id}/memos
    // ========================================

    public function test_store_memo_creates_memo_and_returns_201(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/spots/{$spot->id}/memos", [
                'body' => 'テストメモです',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'spot_id', 'user_id', 'body'],
            ])
            ->assertJson([
                'data' => [
                    'spot_id' => $spot->id,
                    'user_id' => $this->user->id,
                    'body' => 'テストメモです',
                ],
            ]);

        $this->assertDatabaseHas('spot_memos', [
            'spot_id' => $spot->id,
            'user_id' => $this->user->id,
            'body' => 'テストメモです',
        ]);
    }

    public function test_store_memo_returns_422_without_body(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/spots/{$spot->id}/memos", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    public function test_store_memo_returns_422_when_body_exceeds_max_length(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/spots/{$spot->id}/memos", [
                'body' => str_repeat('a', 1001),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    // ========================================
    // GET /api/trips/{tripId}/spots/{id}/photos
    // ========================================

    public function test_photos_returns_photos_for_spot(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);
        Photo::factory()->count(2)->create(['trip_id' => $this->trip->id, 'spot_id' => $spot->id, 'user_id' => $this->user->id]);
        Photo::factory()->create(['trip_id' => $this->trip->id, 'spot_id' => null, 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/spots/{$spot->id}/photos");

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
