<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $expenses = [
            // Day 1: 2026-03-28
            [
                'user_id' => 1,
                'description' => '近鉄特急（名古屋→伊勢市）2名分',
                'amount' => 6000,
                'category' => 'transport',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => '外宮→内宮 バス代 2名分',
                'amount' => 860,
                'category' => 'transport',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'description' => '赤福餅（赤福本店）',
                'amount' => 300,
                'category' => 'food',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => '伊勢うどん（ふくすけ）2人分',
                'amount' => 1600,
                'category' => 'food',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'description' => '松阪牛串焼き 2本',
                'amount' => 1200,
                'category' => 'food',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => '内宮→二見浦 バス代 2名分',
                'amount' => 860,
                'category' => 'transport',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'description' => 'お守り（二見興玉神社）',
                'amount' => 1000,
                'category' => 'souvenir',
                'paid_at' => '2026-03-28',
                'is_shared' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => 'タクシー（二見浦→ホテル）',
                'amount' => 2500,
                'category' => 'transport',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => 'ホテル宿泊費（1泊2食付き）',
                'amount' => 30000,
                'category' => 'hotel',
                'paid_at' => '2026-03-28',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Day 2: 2026-03-29
            [
                'user_id' => 2,
                'description' => '伊勢志摩スカイライン通行料',
                'amount' => 1270,
                'category' => 'transport',
                'paid_at' => '2026-03-29',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'description' => 'VISON ランチ（海鮮丼 2人分）',
                'amount' => 4000,
                'category' => 'food',
                'paid_at' => '2026-03-29',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'description' => 'お土産（伊勢茶・干物セット）',
                'amount' => 3500,
                'category' => 'souvenir',
                'paid_at' => '2026-03-29',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'description' => '近鉄特急（伊勢市→名古屋）2名分',
                'amount' => 6000,
                'category' => 'transport',
                'paid_at' => '2026-03-29',
                'is_shared' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('expenses')->insert($expenses);
    }
}
