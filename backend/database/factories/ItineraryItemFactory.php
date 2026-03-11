<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItineraryItem>
 */
final class ItineraryItemFactory extends Factory
{
    protected $model = ItineraryItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'spot_id' => null,
            'title' => $this->faker->sentence(3),
            'memo' => $this->faker->sentence(),
            'date' => '2026-04-01',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'transport' => null,
            'sort_order' => 0,
        ];
    }
}
