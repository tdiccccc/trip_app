<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SpotMemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $memos = [
            // 伊勢神宮 内宮 (spot_id: 1)
            [
                'spot_id' => 1,
                'user_id' => 1,
                'body' => '早朝参拝がおすすめ！人が少なくて神聖な雰囲気を味わえる。五十鈴川の手水舎で手を清めるのを忘れずに。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'spot_id' => 1,
                'user_id' => 2,
                'body' => '御朱印帳を忘れないこと！神楽殿でいただけます。参拝後はおかげ横丁でゆっくりしたい。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 伊勢神宮 外宮 (spot_id: 2)
            [
                'spot_id' => 2,
                'user_id' => 2,
                'body' => '外宮から内宮の順で参拝するのが正式な順序。所要時間は約1時間みておくと安心。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // おかげ横丁 (spot_id: 3)
            [
                'spot_id' => 3,
                'user_id' => 1,
                'body' => '赤福本店の赤福餅は絶対食べたい！できたてが最高。伊勢うどんも名物なので要チェック。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'spot_id' => 3,
                'user_id' => 2,
                'body' => '食べ歩きが楽しい！松阪牛の串焼き、てこね寿司、伊勢海老コロッケなど目移りしちゃう。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // VISON (spot_id: 4)
            [
                'spot_id' => 4,
                'user_id' => 1,
                'body' => '広いので全部回ろうとすると半日かかる。マルシェヴィソンでお土産を見るのが楽しそう。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 夫婦岩 (spot_id: 5)
            [
                'spot_id' => 5,
                'user_id' => 2,
                'body' => '二人で行くのにぴったりのスポット！縁結びのご利益があるらしい。写真映えもする。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'spot_id' => 5,
                'user_id' => 1,
                'body' => '日の出の時間帯が最高のシャッターチャンスらしいけど、朝早すぎるかも...',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 天の岩戸 (spot_id: 6)
            [
                'spot_id' => 6,
                'user_id' => 1,
                'body' => '名水百選の湧き水があるらしい。自然豊かでパワースポット感がすごいとの口コミ。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 金剛證寺 (spot_id: 7)
            [
                'spot_id' => 7,
                'user_id' => 2,
                'body' => '伊勢志摩スカイラインの途中にあるお寺。朝熊山展望台からの眺めも楽しみ！',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // ホテル (spot_id: 8)
            [
                'spot_id' => 8,
                'user_id' => 1,
                'body' => 'チェックイン15時なので、荷物だけ先に預けて観光に出かけるのがいいかも。',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('spot_memos')->insert($memos);
    }
}
