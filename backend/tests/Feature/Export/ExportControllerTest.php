<?php

declare(strict_types=1);

namespace Tests\Feature\Export;

use App\Models\ItineraryItem;
use App\Models\Photo;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ExportControllerTest extends TestCase
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
    // POST /api/trips/{tripId}/export/itinerary-pdf
    // ========================================

    public function test_itinerary_pdf_returns_pdf_binary(): void
    {
        ItineraryItem::factory()->count(3)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/itinerary-pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="itinerary.pdf"');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_itinerary_pdf_returns_pdf_even_when_empty(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/itinerary-pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_itinerary_pdf_returns_401_for_guest(): void
    {
        $response = $this->postJson("/api/trips/{$this->trip->id}/export/itinerary-pdf");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/export/photobook-pdf
    // ========================================

    public function test_photobook_pdf_returns_pdf_binary(): void
    {
        Photo::factory()->count(2)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/photobook-pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="photobook.pdf"');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_photobook_pdf_returns_pdf_even_when_empty(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/photobook-pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_photobook_pdf_returns_401_for_guest(): void
    {
        $response = $this->postJson("/api/trips/{$this->trip->id}/export/photobook-pdf");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/export/slideshow-video
    // ========================================

    public function test_slideshow_video_returns_501(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/slideshow-video");

        $response->assertStatus(501);
        $response->assertJson([
            'message' => 'This feature is currently under development.',
        ]);
    }

    public function test_slideshow_video_returns_401_for_guest(): void
    {
        $response = $this->postJson("/api/trips/{$this->trip->id}/export/slideshow-video");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/export/zip
    // ========================================

    public function test_zip_returns_zip_binary(): void
    {
        Storage::fake('s3');

        ItineraryItem::factory()->count(2)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
        ]);

        $storagePath = 'photos/1/test-photo.jpg';
        Storage::disk('s3')->put($storagePath, 'fake-image-content');

        Photo::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'storage_path' => $storagePath,
            'original_filename' => 'test-photo.jpg',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/zip");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/zip');
        $response->assertHeader('Content-Disposition', 'attachment; filename="ise-trip-export.zip"');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_zip_returns_zip_even_when_empty(): void
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/export/zip");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/zip');
    }

    public function test_zip_returns_401_for_guest(): void
    {
        $response = $this->postJson("/api/trips/{$this->trip->id}/export/zip");

        $response->assertUnauthorized();
    }
}
