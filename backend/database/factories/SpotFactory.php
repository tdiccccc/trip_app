<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Spot;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Spot>
 */
final class SpotFactory extends Factory
{
    protected $model = Spot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'name' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(34.4, 34.6),
            'longitude' => $this->faker->longitude(136.6, 136.8),
            'business_hours' => '09:00-17:00',
            'price_info' => '無料',
            'google_maps_url' => $this->faker->url(),
            'image_url' => $this->faker->imageUrl(),
            'category' => 'sightseeing',
            'sort_order' => 0,
        ];
    }
}
