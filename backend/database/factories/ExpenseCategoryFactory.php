<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseCategory>
 */
final class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'name' => $this->faker->word(),
            'key' => $this->faker->unique()->slug(1),
            'color' => $this->faker->optional()->hexColor(),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
