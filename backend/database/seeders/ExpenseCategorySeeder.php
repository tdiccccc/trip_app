<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * デフォルトの費用カテゴリを各旅行に作成する。
     *
     * マイグレーション時にも同一データが挿入されるが、
     * fresh + seed 実行時（マイグレーションで trips が空の場合）に備えてシーダーでも用意する。
     */
    public function run(): void
    {
        $now = Carbon::now();

        $defaultCategories = [
            ['key' => 'transport', 'name' => '交通費', 'sort_order' => 1],
            ['key' => 'food', 'name' => '食費', 'sort_order' => 2],
            ['key' => 'souvenir', 'name' => 'お土産', 'sort_order' => 3],
            ['key' => 'accommodation', 'name' => '宿泊費', 'sort_order' => 4],
            ['key' => 'other', 'name' => 'その他', 'sort_order' => 5],
        ];

        $tripIds = DB::table('trips')->pluck('id');

        foreach ($tripIds as $tripId) {
            foreach ($defaultCategories as $category) {
                DB::table('expense_categories')->insert([
                    'trip_id' => $tripId,
                    'name' => $category['name'],
                    'key' => $category['key'],
                    'color' => null,
                    'sort_order' => $category['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
