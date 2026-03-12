<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\BoardPost;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ItineraryItem;
use App\Models\PackingItem;
use App\Models\Photo;
use App\Models\Reaction;
use App\Models\Spot;
use App\Models\Trip;
use App\Models\TripMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TripSummaryTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $member;

    private Trip $trip;

    /** @var array<string, ExpenseCategory> */
    private array $categories;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['name' => 'オーナー']);
        $this->member = User::factory()->create(['name' => 'メンバー']);

        $this->trip = Trip::factory()->create([
            'title' => '伊勢旅行',
            'start_date' => '2026-03-28',
            'end_date' => '2026-03-29',
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

        // デフォルトの費用カテゴリを作成
        $this->categories = [];
        $defaultCategories = [
            ['key' => 'transport', 'name' => '交通費', 'sort_order' => 1],
            ['key' => 'food', 'name' => '食費', 'sort_order' => 2],
            ['key' => 'souvenir', 'name' => 'お土産', 'sort_order' => 3],
            ['key' => 'accommodation', 'name' => '宿泊費', 'sort_order' => 4],
            ['key' => 'other', 'name' => 'その他', 'sort_order' => 5],
        ];
        foreach ($defaultCategories as $cat) {
            $this->categories[$cat['key']] = ExpenseCategory::factory()->create([
                'trip_id' => $this->trip->id,
                'name' => $cat['name'],
                'key' => $cat['key'],
                'sort_order' => $cat['sort_order'],
            ]);
        }
    }

    // ========================================
    // GET /api/trips/{tripId}/summary
    // ========================================

    public function test_summary_returns_correct_statistics(): void
    {
        // 写真 3枚
        Photo::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'taken_at' => '2026-03-28 11:20:00',
        ]);
        Photo::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'taken_at' => '2026-03-29 16:40:00',
        ]);
        Photo::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->member->id,
            'taken_at' => '2026-03-28 14:00:00',
        ]);

        // スポット 2件
        Spot::factory()->create(['trip_id' => $this->trip->id]);
        Spot::factory()->create(['trip_id' => $this->trip->id]);

        // 掲示板投稿 2件 + リアクション 3件
        $post1 = BoardPost::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
        ]);
        $post2 = BoardPost::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->member->id,
        ]);
        Reaction::factory()->create(['board_post_id' => $post1->id, 'user_id' => $this->member->id, 'emoji' => '👍']);
        Reaction::factory()->create(['board_post_id' => $post1->id, 'user_id' => $this->owner->id, 'emoji' => '❤️']);
        Reaction::factory()->create(['board_post_id' => $post2->id, 'user_id' => $this->owner->id, 'emoji' => '🎉']);

        // しおり 4件
        ItineraryItem::factory()->count(4)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
        ]);

        // 費用: transport 3000, food 1500
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'amount' => 3000,
            'expense_category_id' => $this->categories['transport']->id,
        ]);
        Expense::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->member->id,
            'amount' => 1500,
            'expense_category_id' => $this->categories['food']->id,
        ]);

        // パッキング 5件、うち3件チェック済み
        PackingItem::factory()->count(3)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'is_checked' => true,
        ]);
        PackingItem::factory()->count(2)->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
            'is_checked' => false,
        ]);

        $response = $this->actingAs($this->owner)
            ->getJson("/api/trips/{$this->trip->id}/summary");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'photo_count',
                    'spot_count',
                    'board_post_count',
                    'reaction_count',
                    'itinerary_item_count',
                    'total_expense',
                    'expense_per_person',
                    'expense_by_category',
                    'packing_total',
                    'packing_checked',
                    'first_photo_at',
                    'last_photo_at',
                    'trip_days',
                ],
            ]);

        $data = $response->json('data');

        $this->assertEquals(3, $data['photo_count']);
        $this->assertEquals(2, $data['spot_count']);
        $this->assertEquals(2, $data['board_post_count']);
        $this->assertEquals(3, $data['reaction_count']);
        $this->assertEquals(4, $data['itinerary_item_count']);
        $this->assertEquals(4500, $data['total_expense']);
        $this->assertEquals(2250, $data['expense_per_person']); // 4500 / 2 members
        $this->assertEquals(3000, $data['expense_by_category']['transport']);
        $this->assertEquals(1500, $data['expense_by_category']['food']);
        $this->assertEquals(0, $data['expense_by_category']['souvenir']);
        $this->assertEquals(0, $data['expense_by_category']['accommodation']);
        $this->assertEquals(0, $data['expense_by_category']['other']);
        $this->assertEquals(5, $data['packing_total']);
        $this->assertEquals(3, $data['packing_checked']);
        $this->assertEquals('2026-03-28 11:20:00', $data['first_photo_at']);
        $this->assertEquals('2026-03-29 16:40:00', $data['last_photo_at']);
        $this->assertEquals(2, $data['trip_days']); // 3/28 - 3/29 = 2 days
    }

    public function test_summary_returns_zeros_for_empty_trip(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson("/api/trips/{$this->trip->id}/summary");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'photo_count' => 0,
                    'spot_count' => 0,
                    'board_post_count' => 0,
                    'reaction_count' => 0,
                    'itinerary_item_count' => 0,
                    'total_expense' => 0,
                    'expense_per_person' => 0,
                    'expense_by_category' => [
                        'transport' => 0,
                        'food' => 0,
                        'souvenir' => 0,
                        'accommodation' => 0,
                        'other' => 0,
                    ],
                    'packing_total' => 0,
                    'packing_checked' => 0,
                    'first_photo_at' => null,
                    'last_photo_at' => null,
                    'trip_days' => 2,
                ],
            ]);
    }

    public function test_summary_returns_401_for_guest(): void
    {
        $response = $this->getJson("/api/trips/{$this->trip->id}/summary");

        $response->assertUnauthorized();
    }

    public function test_summary_returns_403_for_non_member(): void
    {
        $nonMember = User::factory()->create();

        $response = $this->actingAs($nonMember)
            ->getJson("/api/trips/{$this->trip->id}/summary");

        $response->assertForbidden();
    }

    public function test_summary_does_not_count_other_trip_data(): void
    {
        $otherTrip = Trip::factory()->create(['created_by' => $this->owner->id]);

        // 別の旅行にカテゴリを作成
        $otherCategory = ExpenseCategory::factory()->create([
            'trip_id' => $otherTrip->id,
            'name' => '交通費',
            'key' => 'transport',
            'sort_order' => 1,
        ]);

        // 別の旅行にデータを作成
        Photo::factory()->count(5)->create([
            'trip_id' => $otherTrip->id,
            'user_id' => $this->owner->id,
        ]);
        Spot::factory()->count(3)->create(['trip_id' => $otherTrip->id]);
        Expense::factory()->create([
            'trip_id' => $otherTrip->id,
            'user_id' => $this->owner->id,
            'amount' => 10000,
            'expense_category_id' => $otherCategory->id,
        ]);

        // 対象の旅行には写真1枚だけ
        Photo::factory()->create([
            'trip_id' => $this->trip->id,
            'user_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->getJson("/api/trips/{$this->trip->id}/summary");

        $response->assertOk();
        $data = $response->json('data');

        $this->assertEquals(1, $data['photo_count']);
        $this->assertEquals(0, $data['spot_count']);
        $this->assertEquals(0, $data['total_expense']);
    }
}
