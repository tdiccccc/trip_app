<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BoardPost;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reaction>
 */
final class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_post_id' => BoardPost::factory(),
            'user_id' => User::factory(),
            'emoji' => $this->faker->randomElement(['👍', '❤️', '😂', '🎉', '😍']),
        ];
    }
}
