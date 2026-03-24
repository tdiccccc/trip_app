<?php

declare(strict_types=1);

namespace Tests\Feature\ExpenseCategory;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ExpenseCategoryControllerTest extends TestCase
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
    // GET /api/trips/{tripId}/expense-categories
    // ========================================

    public function test_index_カテゴリ一覧をsort_order順で取得できる(): void
    {
        ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => 'お土産',
            'key' => 'souvenir',
            'sort_order' => 3,
        ]);
        ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
            'sort_order' => 1,
        ]);
        ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '食費',
            'key' => 'food',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expense-categories");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'trip_id', 'name', 'key', 'color', 'sort_order'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('交通費', $data[0]['name']);
        $this->assertEquals('食費', $data[1]['name']);
        $this->assertEquals('お土産', $data[2]['name']);
    }

    public function test_index_他の旅行のカテゴリは含まれない(): void
    {
        $otherTrip = Trip::factory()->create(['created_by' => $this->user->id]);

        ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
        ]);
        ExpenseCategory::factory()->create([
            'trip_id' => $otherTrip->id,
            'name' => '食費',
            'key' => 'food',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/trips/{$this->trip->id}/expense-categories");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_未認証ユーザーは401を返す(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}/expense-categories");

        $response->assertUnauthorized();
    }

    // ========================================
    // POST /api/trips/{tripId}/expense-categories
    // ========================================

    public function test_store_カテゴリを正常に作成できる(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expense-categories", [
                'name' => '入場料',
                'key' => 'entrance-fee',
                'color' => '#FF5733',
                'sort_order' => 6,
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'trip_id', 'name', 'key', 'color', 'sort_order'],
            ])
            ->assertJson([
                'data' => [
                    'trip_id' => $this->trip->id,
                    'name' => '入場料',
                    'key' => 'entrance-fee',
                    'color' => '#FF5733',
                    'sort_order' => 6,
                ],
            ]);

        $this->assertDatabaseHas('expense_categories', [
            'trip_id' => $this->trip->id,
            'name' => '入場料',
            'key' => 'entrance-fee',
        ]);
    }

    public function test_store_keyを省略すると自動生成される(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expense-categories", [
                'name' => 'custom category',
            ]);

        $response->assertCreated();
        $this->assertNotEmpty($response->json('data.key'));
    }

    public function test_store_name未指定で422を返す(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expense-categories", [
                'key' => 'test-key',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_key不正形式で422を返す(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expense-categories", [
                'name' => 'テスト',
                'key' => 'INVALID KEY!',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['key']);
    }

    public function test_store_key重複で422を返す(): void
    {
        ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'key' => 'transport',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/trips/{$this->trip->id}/expense-categories", [
                'name' => '交通費2',
                'key' => 'transport',
            ]);

        $response->assertUnprocessable();
    }

    public function test_store_未認証ユーザーは401を返す(): void
    {
        $response = $this->postJson("/api/trips/{$this->trip->id}/expense-categories", [
            'name' => 'テスト',
            'key' => 'test',
        ]);

        $response->assertUnauthorized();
    }

    // ========================================
    // PUT /api/trips/{tripId}/expense-categories/{id}
    // ========================================

    public function test_update_カテゴリを正常に更新できる(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}", [
                'name' => '移動費',
                'color' => '#00FF00',
                'sort_order' => 10,
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => '移動費',
                    'key' => 'transport',
                    'color' => '#00FF00',
                    'sort_order' => 10,
                ],
            ]);

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => '移動費',
        ]);
    }

    public function test_update_nameだけの部分更新ができる(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
            'color' => '#FF0000',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}", [
                'name' => '移動費',
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => '移動費',
                    'key' => 'transport',
                    'color' => '#FF0000',
                    'sort_order' => 1,
                ],
            ]);
    }

    public function test_update_存在しない_i_dで404を返す(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/trips/{$this->trip->id}/expense-categories/99999", [
                'name' => '存在しない',
            ]);

        $response->assertNotFound();
    }

    public function test_update_未認証ユーザーは401を返す(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
        ]);

        $response = $this->putJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}", [
            'name' => '更新',
        ]);

        $response->assertUnauthorized();
    }

    // ========================================
    // DELETE /api/trips/{tripId}/expense-categories/{id}
    // ========================================

    public function test_destroy_未使用カテゴリを正常に削除できる(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '不要カテゴリ',
            'key' => 'unused',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('expense_categories', ['id' => $category->id]);
    }

    public function test_destroy_使用中カテゴリは409を返す(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
            'name' => '交通費',
            'key' => 'transport',
        ]);

        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'expense_category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}");

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'このカテゴリは使用中のため削除できません。',
            ]);

        // カテゴリが残っていることを確認
        $this->assertDatabaseHas('expense_categories', ['id' => $category->id]);
    }

    public function test_destroy_存在しない_i_dで404を返す(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/trips/{$this->trip->id}/expense-categories/99999");

        $response->assertNotFound();
    }

    public function test_destroy_未認証ユーザーは401を返す(): void
    {
        $category = ExpenseCategory::factory()->create([
            'trip_id' => $this->trip->id,
        ]);

        $response = $this->deleteJson("/api/trips/{$this->trip->id}/expense-categories/{$category->id}");

        $response->assertUnauthorized();
    }
}
