<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Expense as ExpenseModel;
use Packages\Domain\Entities\Expense;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;
use Packages\Domain\ValueObjects\Money;

class EloquentExpenseRepository implements ExpenseRepositoryInterface
{
    /**
     * @return Expense[]
     */
    public function findAll(int $tripId, ?int $categoryId = null): array
    {
        $query = ExpenseModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('paid_at', 'desc');

        if ($categoryId !== null) {
            $query->where('expense_category_id', $categoryId);
        }

        return $query->get()
            ->map(fn (ExpenseModel $model): Expense => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?Expense
    {
        $model = ExpenseModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(Expense $expense): Expense
    {
        $model = $expense->id === 0
            ? new ExpenseModel
            : ExpenseModel::findOrFail($expense->id);

        $model->fill([
            'trip_id' => $expense->tripId,
            'user_id' => $expense->userId,
            'description' => $expense->description,
            'amount' => $expense->amount->amount,
            'expense_category_id' => $expense->categoryId,
            'paid_at' => $expense->paidAt,
            'is_shared' => $expense->isShared,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        ExpenseModel::findOrFail($id)->delete();
    }

    private function toEntity(ExpenseModel $model): Expense
    {
        return new Expense(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            description: $model->description,
            amount: new Money($model->amount),
            categoryId: $model->expense_category_id,
            paidAt: $model->paid_at,
            isShared: $model->is_shared,
        );
    }
}
