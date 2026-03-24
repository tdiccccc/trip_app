<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $spots = [
            [
                'trip_id' => 1,
                'name' => '伊勢神宮 内宮',
                'description' => '正式名称は皇大神宮。天照大御神をお祀りする、日本の神社の最高峰。',
                'address' => '三重県伊勢市宇治館町1',
                'latitude' => 34.4554,
                'longitude' => 136.7254,
                'business_hours' => '1月〜4月・9月 5:00〜18:00、5月〜8月 5:00〜19:00、10月〜12月 5:00〜17:00',
                'price_info' => '参拝無料',
                'google_maps_url' => 'https://maps.google.com/?q=伊勢神宮内宮&ll=34.4554,136.7254',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => '伊勢神宮 外宮',
                'description' => '正式名称は豊受大神宮。衣食住の守り神である豊受大御神をお祀りする。参拝は外宮から内宮の順が古くからのならわし。',
                'address' => '三重県伊勢市豊川町279',
                'latitude' => 34.4873,
                'longitude' => 136.7036,
                'business_hours' => '1月〜4月・9月 5:00〜18:00、5月〜8月 5:00〜19:00、10月〜12月 5:00〜17:00',
                'price_info' => '参拝無料',
                'google_maps_url' => 'https://maps.google.com/?q=伊勢神宮外宮&ll=34.4873,136.7036',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => 'おかげ横丁',
                'description' => '内宮門前町の中ほどにある、江戸から明治期の伊勢路の建築物を再現した商業施設。伊勢名物の赤福や伊勢うどんなどが楽しめる。',
                'address' => '三重県伊勢市宇治中之切町52',
                'latitude' => 34.4562,
                'longitude' => 136.7262,
                'business_hours' => '9:30〜17:00（季節により変動あり）',
                'price_info' => '入場無料（各店舗により異なる）',
                'google_maps_url' => 'https://maps.google.com/?q=おかげ横丁&ll=34.4562,136.7262',
                'image_url' => null,
                'category' => 'food',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => 'VISON',
                'description' => '日本最大級の商業リゾート施設。地元食材を活かしたグルメ、温浴施設、宿泊施設などが揃う複合型リゾート。',
                'address' => '三重県多気郡多気町ヴィソン672-1',
                'latitude' => 34.4358,
                'longitude' => 136.5524,
                'business_hours' => '店舗により異なる（概ね 9:00〜21:00）',
                'price_info' => '入場無料（各施設により異なる）',
                'google_maps_url' => 'https://maps.google.com/?q=VISON多気&ll=34.4358,136.5524',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => '夫婦岩',
                'description' => '二見興玉神社の境内にある、大小2つの岩が注連縄で結ばれた名勝。日の出の名所としても有名。',
                'address' => '三重県伊勢市二見町江575',
                'latitude' => 34.5094,
                'longitude' => 136.7913,
                'business_hours' => '参拝自由（二見興玉神社 授与所 7:00〜17:00）',
                'price_info' => '参拝無料',
                'google_maps_url' => 'https://maps.google.com/?q=夫婦岩&ll=34.5094,136.7913',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => '天の岩戸',
                'description' => '天照大御神が隠れたと伝わる伝説の地。恵利原の水穴から湧き出す清水は名水百選にも選ばれている。',
                'address' => '三重県志摩市磯部町恵利原',
                'latitude' => 34.4267,
                'longitude' => 136.8069,
                'business_hours' => '散策自由',
                'price_info' => '無料',
                'google_maps_url' => 'https://maps.google.com/?q=天の岩戸志摩&ll=34.4267,136.8069',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => '金剛證寺',
                'description' => '朝熊山の山頂にある臨済宗の寺院。「お伊勢参らば朝熊をかけよ、朝熊かけねば片参り」と言われる伊勢参拝ゆかりの寺。',
                'address' => '三重県伊勢市朝熊町548',
                'latitude' => 34.4604,
                'longitude' => 136.7850,
                'business_hours' => '9:00〜16:00',
                'price_info' => '境内無料',
                'google_maps_url' => 'https://maps.google.com/?q=金剛證寺&ll=34.4604,136.7850',
                'image_url' => null,
                'category' => 'sightseeing',
                'sort_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'trip_id' => 1,
                'name' => 'ホテル（宿泊先）',
                'description' => '宿泊先ホテル。詳細は後日確定。',
                'address' => '未定',
                'latitude' => null,
                'longitude' => null,
                'business_hours' => 'チェックイン 15:00 / チェックアウト 10:00',
                'price_info' => null,
                'google_maps_url' => null,
                'image_url' => null,
                'category' => 'hotel',
                'sort_order' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('spots')->insert($spots);
    }
}
