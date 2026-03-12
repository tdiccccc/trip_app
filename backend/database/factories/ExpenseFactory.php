<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
final class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->numberBetween(100, 10000),
            'expense_category_id' => ExpenseCategory::factory(),
            'paid_at' => $this->faker->date('Y-m-d'),
            'is_shared' => true,
        ];
    }
}
