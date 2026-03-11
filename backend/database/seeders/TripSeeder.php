<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('trips')->insert([
            'title' => '伊勢旅行 2026',
            'description' => '2026年春の伊勢・志摩旅行。内宮・外宮参拝、おかげ横丁、夫婦岩、朝熊山など盛りだくさんの1泊2日。',
            'destination' => '伊勢・志摩',
            'start_date' => '2026-03-28',
            'end_date' => '2026-03-29',
            'cover_image_url' => null,
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
