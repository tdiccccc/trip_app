<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Expense;

use Packages\Domain\Repositories\ExpenseRepositoryInterface;

final class DeleteExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
    ) {
    }

    public function execute(int $tripId, int $id): void
    {
        $this->expenseRepository->delete($id);
    }
}
