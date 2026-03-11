<?php

declare(strict_types=1);

namespace Tests\Feature\Album;

use App\Models\Photo;
use App\Models\Spot;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AlbumControllerTest extends TestCase
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
    // GET /api/trips/{tripId}/photos
    // ========================================

    public function test_index_returns_photo_list(): void
    {
        Photo::factory()->count(3)->create(['trip_id' => $this->trip->id, 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/photos");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'storage_path', 'original_filename', 'mime_type', 'file_size'],
                ],
            ]);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_index_filters_by_spot_id(): void
    {
        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);
        Photo::factory()->count(2)->create(['trip_id' => $this->trip->id, 'user_id' => $this->user->id, 'spot_id' => $spot->id]);
        Photo::factory()->create(['trip_id' => $this->trip->id, 'user_id' => $this->user->id, 'spot_id' => null]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/photos?spot_id={$spot->id}");

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}/photos");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/photos
    // ========================================

    public function test_store_uploads_photo_and_returns_201(): void
    {
        Storage::fake('s3');

        $file = UploadedFile::fake()->create('test-photo.jpg', 1024, 'image/jpeg');

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/photos", [
                'photo' => $file,
                'caption' => 'テスト写真',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'storage_path', 'original_filename', 'mime_type', 'file_size', 'caption'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'original_filename' => 'test-photo.jpg',
                    'caption' => 'テスト写真',
                ],
            ]);

        // ストレージにファイルが保存されたことを確認
        $storagePath = $response->json('data.storage_path');
        Storage::disk('s3')->assertExists($storagePath);
    }

    public function test_store_with_spot_id(): void
    {
        Storage::fake('s3');

        $spot = Spot::factory()->create(['trip_id' => $this->trip->id]);
        $file = UploadedFile::fake()->create('spot-photo.png', 512, 'image/png');

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/photos", [
                'photo' => $file,
                'spot_id' => $spot->id,
            ]);

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'spot_id' => $spot->id,
                ],
            ]);
    }

    public function test_store_returns_422_without_photo(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/photos", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_store_returns_422_with_non_image_file(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/photos", [
                'photo' => $file,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_store_returns_422_when_file_exceeds_max_size(): void
    {
        $file = UploadedFile::fake()->create('large.jpg', 10241, 'image/jpeg');

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/photos", [
                'photo' => $file,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['photo']);
    }

    // ========================================
    // DELETE /api/trips/{tripId}/photos/{id}
    // ========================================

    public function test_destroy_deletes_photo_and_returns_204(): void
    {
        $photo = Photo::factory()->create(['trip_id' => $this->trip->id, 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/trips/{$this->trip->id}/photos/{$photo->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
    }
}
