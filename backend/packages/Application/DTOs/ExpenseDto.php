<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Expense;

final readonly class ExpenseDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public string $description,
        public int $amount,
        public string $category,
        public string $paidAt,
        public bool $isShared,
    ) {
    }

    public static function fromEntity(Expense $expense): self
    {
        return new self(
            id: $expense->id,
            tripId: $expense->tripId,
            userId: $expense->userId,
            description: $expense->description,
            amount: $expense->amount->amount,
            category: $expense->category->value,
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
            'category' => $this->category,
            'paid_at' => $this->paidAt,
            'is_shared' => $this->isShared,
        ];
    }
}
