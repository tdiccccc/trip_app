<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BoardPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardPost>
 */
final class BoardPostFactory extends Factory
{
    protected $model = BoardPost::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => $this->faker->sentence(),
            'photo_id' => null,
            'is_best_shot' => false,
        ];
    }
}
