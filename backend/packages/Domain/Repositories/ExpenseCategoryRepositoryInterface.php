<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\ExpenseCategory;

interface ExpenseCategoryRepositoryInterface
{
    /**
     * @return ExpenseCategory[]
     */
    public function findAll(int $tripId): array;

    public function findById(int $id): ?ExpenseCategory;

    public function findByKey(int $tripId, string $key): ?ExpenseCategory;

    public function save(ExpenseCategory $category): ExpenseCategory;

    public function delete(int $id): void;

    public function existsByKey(int $tripId, string $key): bool;

    public function isInUse(int $id): bool;
}
