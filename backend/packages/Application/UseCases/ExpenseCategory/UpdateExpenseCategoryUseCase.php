<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\ExpenseCategory;

use Packages\Application\DTOs\ExpenseCategoryDto;
use Packages\Domain\Entities\ExpenseCategory;
use Packages\Domain\Exceptions\DomainException;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

final class UpdateExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(int $id, array $data): ExpenseCategoryDto
    {
        $existing = $this->expenseCategoryRepository->findById($id);

        if ($existing === null) {
            throw new DomainException('カテゴリが見つかりません。');
        }

        $updated = new ExpenseCategory(
            id: $existing->id,
            tripId: $existing->tripId,
            name: $data['name'] ?? $existing->name,
            key: $existing->key,
            color: array_key_exists('color', $data) ? $data['color'] : $existing->color,
            sortOrder: $data['sort_order'] ?? $existing->sortOrder,
        );

        $saved = $this->expenseCategoryRepository->save($updated);

        return ExpenseCategoryDto::fromEntity($saved);
    }
}
