<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $reactions = [
            // Post 1: 「伊勢旅行の計画立てよう！」
            [
                'board_post_id' => 1,
                'user_id' => 2,
                'emoji' => '🎉',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 2: 「いいね！楽しみ！」
            [
                'board_post_id' => 2,
                'user_id' => 1,
                'emoji' => '👍',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 2,
                'user_id' => 1,
                'emoji' => '😊',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 3: 「近鉄特急の切符予約した！」
            [
                'board_post_id' => 3,
                'user_id' => 2,
                'emoji' => '✨',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 3,
                'user_id' => 2,
                'emoji' => '👍',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 4: 「御朱印帳を新しく買ったよ」
            [
                'board_post_id' => 4,
                'user_id' => 1,
                'emoji' => '❤️',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 5: 「天気予報見たら晴れ」
            [
                'board_post_id' => 5,
                'user_id' => 2,
                'emoji' => '🎉',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 5,
                'user_id' => 2,
                'emoji' => '😊',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 6: 「外宮参拝完了！」
            [
                'board_post_id' => 6,
                'user_id' => 1,
                'emoji' => '✨',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 7: 「赤福餅おいしすぎた」
            [
                'board_post_id' => 7,
                'user_id' => 2,
                'emoji' => '😂',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 7,
                'user_id' => 2,
                'emoji' => '❤️',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 7,
                'user_id' => 1,
                'emoji' => '😊',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Post 8: 「2日間ありがとう！」
            [
                'board_post_id' => 8,
                'user_id' => 1,
                'emoji' => '❤️',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'board_post_id' => 8,
                'user_id' => 1,
                'emoji' => '🎉',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('reactions')->insert($reactions);
    }
}
