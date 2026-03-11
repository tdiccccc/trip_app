<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    /**
     * @return Expense[]
     */
    public function findAll(int $tripId, ?string $category = null): array;

    public function findById(int $id): ?Expense;

    public function save(Expense $expense): Expense;

    public function delete(int $id): void;
}
