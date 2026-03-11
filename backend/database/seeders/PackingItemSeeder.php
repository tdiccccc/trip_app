<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PackingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            // 共有アイテム
            [
                'user_id' => 1,
                'name' => 'カメラ',
                'is_checked' => true,
                'assignee' => 'shared',
                'category' => 'electronics',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => 'モバイルバッテリー',
                'is_checked' => true,
                'assignee' => 'shared',
                'category' => 'electronics',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => 'お菓子・飲み物（車内用）',
                'is_checked' => false,
                'assignee' => 'shared',
                'category' => 'other',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => 'ガイドブック（伊勢志摩）',
                'is_checked' => true,
                'assignee' => 'shared',
                'category' => 'other',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => '折りたたみ傘',
                'is_checked' => false,
                'assignee' => 'shared',
                'category' => 'other',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // たろうのアイテム
            [
                'user_id' => 1,
                'name' => '着替え（1泊分）',
                'is_checked' => true,
                'assignee' => 'taro',
                'category' => 'clothing',
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => 'スマホ充電器',
                'is_checked' => true,
                'assignee' => 'taro',
                'category' => 'electronics',
                'sort_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => '財布（現金多めに）',
                'is_checked' => true,
                'assignee' => 'taro',
                'category' => 'valuables',
                'sort_order' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => '歩きやすいスニーカー',
                'is_checked' => true,
                'assignee' => 'taro',
                'category' => 'clothing',
                'sort_order' => 9,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => '近鉄特急の予約確認書',
                'is_checked' => false,
                'assignee' => 'taro',
                'category' => 'documents',
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // はなこのアイテム
            [
                'user_id' => 2,
                'name' => '着替え（1泊分）',
                'is_checked' => true,
                'assignee' => 'hanako',
                'category' => 'clothing',
                'sort_order' => 11,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => 'スマホ充電器',
                'is_checked' => true,
                'assignee' => 'hanako',
                'category' => 'electronics',
                'sort_order' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => '御朱印帳',
                'is_checked' => true,
                'assignee' => 'hanako',
                'category' => 'other',
                'sort_order' => 13,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => '化粧ポーチ',
                'is_checked' => true,
                'assignee' => 'hanako',
                'category' => 'toiletries',
                'sort_order' => 14,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => '日焼け止め',
                'is_checked' => false,
                'assignee' => 'hanako',
                'category' => 'toiletries',
                'sort_order' => 15,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => 'エコバッグ（お土産用）',
                'is_checked' => false,
                'assignee' => 'hanako',
                'category' => 'other',
                'sort_order' => 16,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'name' => '常備薬',
                'is_checked' => false,
                'assignee' => 'taro',
                'category' => 'toiletries',
                'sort_order' => 17,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'name' => 'ハンカチ・ティッシュ',
                'is_checked' => true,
                'assignee' => 'hanako',
                'category' => 'other',
                'sort_order' => 18,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('packing_items')->insert($items);
    }
}
