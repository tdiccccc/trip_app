<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\ExpenseCategory;

use Packages\Application\DTOs\ExpenseCategoryDto;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

final class GetExpenseCategoriesUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {
    }

    /**
     * @return ExpenseCategoryDto[]
     */
    public function execute(int $tripId): array
    {
        $categories = $this->expenseCategoryRepository->findAll($tripId);

        return array_map(
            fn ($category) => ExpenseCategoryDto::fromEntity($category),
            $categories,
        );
    }
}
