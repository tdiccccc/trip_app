<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Expense;

use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;

final class GetExpenseSummaryUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
        private readonly ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(int $tripId): array
    {
        $expenses = $this->expenseRepository->findAll($tripId);

        // カテゴリマップを構築（id -> key）
        $categories = $this->expenseCategoryRepository->findAll($tripId);
        $categoryKeyMap = [];
        foreach ($categories as $category) {
            $categoryKeyMap[$category->id] = $category->key;
        }

        $totalAmount = 0;
        $sharedTotal = 0;
        /** @var array<string, int> $byCategory */
        $byCategory = [];
        /** @var array<int, int> $byUser */
        $byUser = [];
        /** @var array<int, int> $sharedPaidByUser */
        $sharedPaidByUser = [];

        foreach ($expenses as $expense) {
            $amount = $expense->amount->amount;
            $totalAmount += $amount;

            // カテゴリ別集計
            $categoryKey = $categoryKeyMap[$expense->categoryId] ?? 'unknown';
            $byCategory[$categoryKey] = ($byCategory[$categoryKey] ?? 0) + $amount;

            // ユーザー別集計
            $byUser[$expense->userId] = ($byUser[$expense->userId] ?? 0) + $amount;

            // 共有費用の集計
            if ($expense->isShared) {
                $sharedTotal += $amount;
                $sharedPaidByUser[$expense->userId] = ($sharedPaidByUser[$expense->userId] ?? 0) + $amount;
            }
        }

        $perPerson = (int) floor($sharedTotal / 2);

        // 精算計算: 各ユーザーの共有費用支払額と折半額の差分
        $settlement = [];
        foreach ($sharedPaidByUser as $userId => $paid) {
            $diff = $paid - $perPerson;
            $settlement[$userId] = $diff;
        }

        return [
            'total_amount' => $totalAmount,
            'shared_total' => $sharedTotal,
            'per_person' => $perPerson,
            'by_category' => $byCategory,
            'by_user' => $byUser,
            'settlement' => $settlement,
        ];
    }
}
