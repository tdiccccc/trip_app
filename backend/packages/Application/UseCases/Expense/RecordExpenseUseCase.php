<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Expense;

use Packages\Application\DTOs\ExpenseDto;
use Packages\Domain\Entities\Expense;
use Packages\Domain\Enums\ExpenseCategory;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;
use Packages\Domain\ValueObjects\Money;

final class RecordExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
    ) {
    }

    public function execute(
        int $userId,
        string $description,
        int $amount,
        string $category,
        string $paidAt,
        bool $isShared,
    ): ExpenseDto {
        $expense = new Expense(
            id: 0,
            userId: $userId,
            description: $description,
            amount: new Money($amount),
            category: ExpenseCategory::from($category),
            paidAt: $paidAt,
            isShared: $isShared,
        );

        $saved = $this->expenseRepository->save($expense);

        return ExpenseDto::fromEntity($saved);
    }
}
