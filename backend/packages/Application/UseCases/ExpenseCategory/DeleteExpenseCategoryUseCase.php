<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\ExpenseCategory;

use Packages\Domain\Exceptions\CategoryInUseException;
use Packages\Domain\Exceptions\DomainException;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

final class DeleteExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    public function execute(int $id): void
    {
        $existing = $this->expenseCategoryRepository->findById($id);

        if ($existing === null) {
            throw new DomainException('カテゴリが見つかりません。');
        }

        if ($this->expenseCategoryRepository->isInUse($id)) {
            throw new CategoryInUseException;
        }

        $this->expenseCategoryRepository->delete($id);
    }
}
