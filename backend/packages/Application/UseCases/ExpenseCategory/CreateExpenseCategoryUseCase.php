<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\ExpenseCategory;

use Packages\Application\DTOs\ExpenseCategoryDto;
use Packages\Domain\Entities\ExpenseCategory;
use Packages\Domain\Exceptions\DomainException;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

final class CreateExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {
    }

    public function execute(
        int $tripId,
        string $name,
        string $key,
        ?string $color = null,
        int $sortOrder = 0,
    ): ExpenseCategoryDto {
        if ($this->expenseCategoryRepository->existsByKey($tripId, $key)) {
            throw new DomainException("キー '{$key}' は既にこの旅行で使用されています。");
        }

        $category = new ExpenseCategory(
            id: 0,
            tripId: $tripId,
            name: $name,
            key: $key,
            color: $color,
            sortOrder: $sortOrder,
        );

        $saved = $this->expenseCategoryRepository->save($category);

        return ExpenseCategoryDto::fromEntity($saved);
    }
}
