<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Master;

use Packages\Domain\Enums\ExpenseCategory;

final class GetExpenseCategoriesUseCase
{
    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function execute(): array
    {
        return ExpenseCategory::toArray();
    }
}
