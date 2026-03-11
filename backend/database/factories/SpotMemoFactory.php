<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Spot;
use App\Models\SpotMemo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpotMemo>
 */
final class SpotMemoFactory extends Factory
{
    protected $model = SpotMemo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spot_id' => Spot::factory(),
            'user_id' => User::factory(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
