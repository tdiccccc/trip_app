<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Expense as ExpenseModel;
use App\Models\ExpenseCategory as ExpenseCategoryModel;
use Packages\Domain\Entities\ExpenseCategory;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;

class EloquentExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    /**
     * @return ExpenseCategory[]
     */
    public function findAll(int $tripId): array
    {
        return ExpenseCategoryModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ExpenseCategoryModel $model): ExpenseCategory => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?ExpenseCategory
    {
        $model = ExpenseCategoryModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByKey(int $tripId, string $key): ?ExpenseCategory
    {
        $model = ExpenseCategoryModel::query()
            ->where('trip_id', $tripId)
            ->where('key', $key)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(ExpenseCategory $category): ExpenseCategory
    {
        $model = $category->id === 0
            ? new ExpenseCategoryModel
            : ExpenseCategoryModel::findOrFail($category->id);

        $model->fill([
            'trip_id' => $category->tripId,
            'name' => $category->name,
            'key' => $category->key,
            'color' => $category->color,
            'sort_order' => $category->sortOrder,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        ExpenseCategoryModel::findOrFail($id)->delete();
    }

    public function existsByKey(int $tripId, string $key): bool
    {
        return ExpenseCategoryModel::query()
            ->where('trip_id', $tripId)
            ->where('key', $key)
            ->exists();
    }

    public function isInUse(int $id): bool
    {
        return ExpenseModel::query()
            ->where('expense_category_id', $id)
            ->exists();
    }

    private function toEntity(ExpenseCategoryModel $model): ExpenseCategory
    {
        return new ExpenseCategory(
            id: $model->id,
            tripId: $model->trip_id,
            name: $model->name,
            key: $model->key,
            color: $model->color,
            sortOrder: $model->sort_order,
        );
    }
}
