<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Master;

use Packages\Application\DTOs\ExpenseCategoryDto;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

/**
 * @deprecated 旅行スコープの ExpenseCategory/GetExpenseCategoriesUseCase を使用してください。
 *             マスターAPIの後方互換性のために残しています。
 */
final class GetExpenseCategoriesUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(int $tripId): array
    {
        $categories = $this->expenseCategoryRepository->findAll($tripId);

        return array_map(
            fn ($category) => ExpenseCategoryDto::fromEntity($category)->toArray(),
            $categories,
        );
    }
}
