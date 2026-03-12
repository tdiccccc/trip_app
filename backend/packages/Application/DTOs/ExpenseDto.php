<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Expense;
use Packages\Domain\Entities\ExpenseCategory;

final readonly class ExpenseDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public string $description,
        public int $amount,
        public int $categoryId,
        public string $categoryName,
        public string $categoryKey,
        public string $paidAt,
        public bool $isShared,
    ) {
    }

    public static function fromEntity(Expense $expense, ?ExpenseCategory $category = null): self
    {
        return new self(
            id: $expense->id,
            tripId: $expense->tripId,
            userId: $expense->userId,
            description: $expense->description,
            amount: $expense->amount->amount,
            categoryId: $expense->categoryId,
            categoryName: $category?->name ?? '',
            categoryKey: $category?->key ?? '',
            paidAt: $expense->paidAt,
            isShared: $expense->isShared,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'user_id' => $this->userId,
            'description' => $this->description,
            'amount' => $this->amount,
            'category_id' => $this->categoryId,
            'category_name' => $this->categoryName,
            'category_key' => $this->categoryKey,
            'paid_at' => $this->paidAt,
            'is_shared' => $this->isShared,
        ];
    }
}
