<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Expense;

use Packages\Application\DTOs\ExpenseDto;
use Packages\Domain\Entities\Expense;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;
use Packages\Domain\ValueObjects\Money;

final class RecordExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    public function execute(
        int $tripId,
        int $userId,
        string $description,
        int $amount,
        int $categoryId,
        string $paidAt,
        bool $isShared,
    ): ExpenseDto {
        $expense = new Expense(
            id: 0,
            tripId: $tripId,
            userId: $userId,
            description: $description,
            amount: new Money($amount),
            categoryId: $categoryId,
            paidAt: $paidAt,
            isShared: $isShared,
        );

        $saved = $this->expenseRepository->save($expense);

        $category = $this->expenseCategoryRepository->findById($saved->categoryId);

        return ExpenseDto::fromEntity($saved, $category);
    }
}
