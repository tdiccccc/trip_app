<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TripSeeder::class,
            TripMemberSeeder::class,
            SpotSeeder::class,
            SpotMemoSeeder::class,
            ItineraryItemSeeder::class,
            PhotoSeeder::class,
            BoardPostSeeder::class,
            ReactionSeeder::class,
            PackingItemSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
