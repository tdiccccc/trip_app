<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PackingItem;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackingItem>
 */
final class PackingItemFactory extends Factory
{
    protected $model = PackingItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'is_checked' => false,
            'assignee' => 'shared',
            'category' => null,
            'sort_order' => 0,
        ];
    }
}
