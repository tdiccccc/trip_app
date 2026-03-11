<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BoardPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            // 旅行前のやりとり
            [
                'trip_id' => 1,
                'user_id' => 1,
                'body' => '伊勢旅行の計画立てよう！3/28-29 の1泊2日でどうかな？',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-01 20:00:00'),
                'updated_at' => Carbon::parse('2026-03-01 20:00:00'),
            ],
            [
                'trip_id' => 1,
                'user_id' => 2,
                'body' => 'いいね！楽しみ！内宮と外宮は絶対行きたい。おかげ横丁で食べ歩きもしたいな。',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-01 20:15:00'),
                'updated_at' => Carbon::parse('2026-03-01 20:15:00'),
            ],
            [
                'trip_id' => 1,
                'user_id' => 1,
                'body' => '近鉄特急の切符予約した！名古屋8:00発だから朝早いけど頑張ろう。',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-10 21:30:00'),
                'updated_at' => Carbon::parse('2026-03-10 21:30:00'),
            ],
            [
                'trip_id' => 1,
                'user_id' => 2,
                'body' => 'ありがとう！御朱印帳を新しく買ったよ。伊勢神宮でデビューさせる！',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-15 19:00:00'),
                'updated_at' => Carbon::parse('2026-03-15 19:00:00'),
            ],
            [
                'trip_id' => 1,
                'user_id' => 1,
                'body' => '天気予報見たら3/28は晴れっぽい！最高の参拝日和になりそう。',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-25 12:00:00'),
                'updated_at' => Carbon::parse('2026-03-25 12:00:00'),
            ],
            // 旅行当日
            [
                'trip_id' => 1,
                'user_id' => 2,
                'body' => '外宮参拝完了！空気が澄んでいて気持ちよかった。次は内宮へ！',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-28 10:35:00'),
                'updated_at' => Carbon::parse('2026-03-28 10:35:00'),
            ],
            [
                'trip_id' => 1,
                'user_id' => 1,
                'body' => '赤福餅おいしすぎた...！おかわりしたい。この写真ベストショットにする！',
                'photo_id' => 5,
                'is_best_shot' => true,
                'created_at' => Carbon::parse('2026-03-28 12:50:00'),
                'updated_at' => Carbon::parse('2026-03-28 12:50:00'),
            ],
            // 旅行後
            [
                'trip_id' => 1,
                'user_id' => 2,
                'body' => '2日間ありがとう！すごく楽しかった。また伊勢に行きたいね。朝熊山からの景色が一番印象に残ってる。',
                'photo_id' => null,
                'is_best_shot' => false,
                'created_at' => Carbon::parse('2026-03-29 20:00:00'),
                'updated_at' => Carbon::parse('2026-03-29 20:00:00'),
            ],
        ];

        DB::table('board_posts')->insert($posts);
    }
}
