<?php

declare(strict_types=1);

namespace Tests\Feature\Board;

use App\Models\BoardPost;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ========================================
    // GET /api/board
    // ========================================

    public function test_index_returns_board_posts_with_reactions(): void
    {
        $post = BoardPost::factory()->create([
            'user_id' => $this->user->id,
            'body' => 'テスト投稿',
        ]);

        Reaction::factory()->create([
            'board_post_id' => $post->id,
            'user_id' => $this->user->id,
            'emoji' => '👍',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/board');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'body', 'photo_id', 'is_best_shot', 'reactions'],
                ],
            ]);
        $this->assertCount(1, $response->json('data'));
        $this->assertCount(1, $response->json('data.0.reactions'));
    }

    public function test_index_returns_401_for_guest(): void
    {
        $response = $this->getJson('/api/board');

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/board
    // ========================================

    public function test_store_creates_board_post_and_returns_201(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/board', [
                'body' => '伊勢神宮に行きたい！',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'body', 'photo_id', 'is_best_shot'],
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'body' => '伊勢神宮に行きたい！',
                    'is_best_shot' => false,
                ],
            ]);

        $this->assertDatabaseHas('board_posts', [
            'user_id' => $this->user->id,
            'body' => '伊勢神宮に行きたい！',
        ]);
    }

    public function test_store_returns_422_without_body(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/board', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    // ========================================
    // POST /api/board/{id}/reactions
    // ========================================

    public function test_store_reaction_creates_reaction_and_returns_201(): void
    {
        $post = BoardPost::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/board/{$post->id}/reactions", [
                'emoji' => '❤️',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'board_post_id', 'user_id', 'emoji'],
            ])
            ->assertJson([
                'data' => [
                    'board_post_id' => $post->id,
                    'user_id' => $this->user->id,
                    'emoji' => '❤️',
                ],
            ]);
    }

    public function test_store_reaction_returns_422_for_duplicate(): void
    {
        $post = BoardPost::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Reaction::factory()->create([
            'board_post_id' => $post->id,
            'user_id' => $this->user->id,
            'emoji' => '👍',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/board/{$post->id}/reactions", [
                'emoji' => '👍',
            ]);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['emoji']]);
    }

    public function test_store_reaction_returns_404_for_nonexistent_post(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/board/9999/reactions', [
                'emoji' => '👍',
            ]);

        $response->assertNotFound()
            ->assertJson(['message' => 'Not found.']);
    }

    public function test_store_reaction_returns_401_for_guest(): void
    {
        $response = $this->postJson('/api/board/1/reactions', [
            'emoji' => '👍',
        ]);

        $response->assertUnauthorized();
    }
}
