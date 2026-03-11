<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ItineraryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            // === Day 1: 2026-03-28 (土) ===
            [
                'user_id' => 1,
                'spot_id' => null,
                'title' => '近鉄特急で伊勢市駅へ',
                'memo' => '名古屋駅 8:00発 → 伊勢市駅 9:20着。近鉄特急の指定席を事前予約済み。',
                'date' => '2026-03-28',
                'start_time' => '08:00',
                'end_time' => '09:20',
                'transport' => null,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'spot_id' => 2,
                'title' => '伊勢神宮 外宮 参拝',
                'memo' => '伊勢市駅から徒歩5分。正宮→別宮の順で参拝。御朱印もいただく。',
                'date' => '2026-03-28',
                'start_time' => '09:30',
                'end_time' => '10:30',
                'transport' => 'walk',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'spot_id' => 1,
                'title' => '伊勢神宮 内宮 参拝',
                'memo' => '外宮からバスで移動（約15分）。宇治橋を渡って五十鈴川で手を清めてから正宮へ。',
                'date' => '2026-03-28',
                'start_time' => '11:00',
                'end_time' => '12:30',
                'transport' => 'bus',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'spot_id' => 3,
                'title' => 'おかげ横丁でランチ＆散策',
                'memo' => '伊勢うどんと赤福餅は必須！松阪牛の串焼きも食べたい。お土産もここで。',
                'date' => '2026-03-28',
                'start_time' => '12:30',
                'end_time' => '14:30',
                'transport' => 'walk',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'spot_id' => 5,
                'title' => '夫婦岩・二見興玉神社',
                'memo' => 'おかげ横丁からバスで二見浦へ。夫婦岩で写真を撮って、神社で縁結びのお参り。',
                'date' => '2026-03-28',
                'start_time' => '15:00',
                'end_time' => '16:00',
                'transport' => 'bus',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'spot_id' => 8,
                'title' => 'ホテルにチェックイン・夕食',
                'memo' => 'ホテルで温泉に入ってゆっくり。夕食はホテルの和食レストランを予約済み。',
                'date' => '2026-03-28',
                'start_time' => '17:00',
                'end_time' => '21:00',
                'transport' => 'taxi',
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // === Day 2: 2026-03-29 (日) ===
            [
                'user_id' => 2,
                'spot_id' => 7,
                'title' => '金剛證寺・朝熊山展望台',
                'memo' => '伊勢志摩スカイラインを通って朝熊山へ。展望台からの眺望を楽しんでからお寺を参拝。',
                'date' => '2026-03-29',
                'start_time' => '09:00',
                'end_time' => '10:30',
                'transport' => 'car',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'spot_id' => 6,
                'title' => '天の岩戸',
                'memo' => '名水百選の湧き水を見に行く。自然の中を散策してリフレッシュ。',
                'date' => '2026-03-29',
                'start_time' => '11:00',
                'end_time' => '12:00',
                'transport' => 'car',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'spot_id' => 4,
                'title' => 'VISONでランチ＆ショッピング',
                'memo' => 'マルシェヴィソンでお土産探し。ランチは地元食材を使ったレストランで。',
                'date' => '2026-03-29',
                'start_time' => '12:30',
                'end_time' => '15:00',
                'transport' => 'car',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 1,
                'spot_id' => null,
                'title' => '帰路：伊勢市駅から近鉄特急',
                'memo' => '伊勢市駅 16:30発 → 名古屋駅 17:50着。お疲れさまでした！',
                'date' => '2026-03-29',
                'start_time' => '16:00',
                'end_time' => '17:50',
                'transport' => 'train',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('itinerary_items')->insert($items);
    }
}
