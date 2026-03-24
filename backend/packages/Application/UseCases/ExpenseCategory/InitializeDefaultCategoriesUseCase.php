<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\ExpenseCategory;

use Packages\Domain\Entities\ExpenseCategory;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

final class InitializeDefaultCategoriesUseCase
{
    /** @var array<int, array{key: string, name: string, sort_order: int}> */
    private const array DEFAULT_CATEGORIES = [
        ['key' => 'transport', 'name' => '交通費', 'sort_order' => 1],
        ['key' => 'food', 'name' => '食費', 'sort_order' => 2],
        ['key' => 'souvenir', 'name' => 'お土産', 'sort_order' => 3],
        ['key' => 'accommodation', 'name' => '宿泊費', 'sort_order' => 4],
        ['key' => 'other', 'name' => 'その他', 'sort_order' => 5],
    ];

    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    public function execute(int $tripId): void
    {
        foreach (self::DEFAULT_CATEGORIES as $categoryData) {
            if ($this->expenseCategoryRepository->existsByKey($tripId, $categoryData['key'])) {
                continue;
            }

            $category = new ExpenseCategory(
                id: 0,
                tripId: $tripId,
                name: $categoryData['name'],
                key: $categoryData['key'],
                color: null,
                sortOrder: $categoryData['sort_order'],
            );

            $this->expenseCategoryRepository->save($category);
        }
    }
}
