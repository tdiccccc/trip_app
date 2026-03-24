<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
final class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'spot_id' => null,
            'storage_path' => 'photos/1/'.$this->faker->uuid().'.jpg',
            'thumbnail_path' => null,
            'original_filename' => $this->faker->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(100000, 5000000),
            'caption' => $this->faker->sentence(),
            'taken_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];
    }
}
