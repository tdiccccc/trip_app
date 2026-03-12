<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BoardPost;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ItineraryItem;
use App\Models\PackingItem;
use App\Models\Photo;
use App\Models\Reaction;
use App\Models\Spot;
use App\Models\TripMember;
use Packages\Domain\Repositories\TripSummaryRepositoryInterface;

class EloquentTripSummaryRepository implements TripSummaryRepositoryInterface
{
    public function countPhotos(int $tripId): int
    {
        return Photo::where('trip_id', $tripId)->count();
    }

    public function countSpots(int $tripId): int
    {
        return Spot::where('trip_id', $tripId)->count();
    }

    public function countBoardPosts(int $tripId): int
    {
        return BoardPost::where('trip_id', $tripId)->count();
    }

    public function countReactions(int $tripId): int
    {
        return Reaction::whereHas('boardPost', function ($query) use ($tripId): void {
            $query->where('trip_id', $tripId);
        })->count();
    }

    public function countItineraryItems(int $tripId): int
    {
        return ItineraryItem::where('trip_id', $tripId)->count();
    }

    public function sumExpenses(int $tripId): int
    {
        return (int) Expense::where('trip_id', $tripId)->sum('amount');
    }

    /**
     * @return array<string, int>
     */
    public function sumExpensesByCategory(int $tripId): array
    {
        $results = Expense::where('expenses.trip_id', $tripId)
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.key as category_key, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.key')
            ->pluck('total', 'category_key')
            ->toArray();

        // 全カテゴリを含める（0の場合も）
        $categories = ExpenseCategory::where('trip_id', $tripId)
            ->orderBy('sort_order')
            ->pluck('key')
            ->toArray();

        $typed = [];
        foreach ($categories as $key) {
            $typed[$key] = (int) ($results[$key] ?? 0);
        }

        // DB に登録済みだが categories テーブルにない費用のフォールバック
        foreach ($results as $key => $total) {
            if (! isset($typed[$key])) {
                $typed[(string) $key] = (int) $total;
            }
        }

        return $typed;
    }

    public function countPackingItems(int $tripId): int
    {
        return PackingItem::where('trip_id', $tripId)->count();
    }

    public function countCheckedPackingItems(int $tripId): int
    {
        return PackingItem::where('trip_id', $tripId)
            ->where('is_checked', true)
            ->count();
    }

    public function firstPhotoAt(int $tripId): ?string
    {
        $photo = Photo::where('trip_id', $tripId)
            ->whereNotNull('taken_at')
            ->orderBy('taken_at', 'asc')
            ->first();

        return $photo?->taken_at;
    }

    public function lastPhotoAt(int $tripId): ?string
    {
        $photo = Photo::where('trip_id', $tripId)
            ->whereNotNull('taken_at')
            ->orderBy('taken_at', 'desc')
            ->first();

        return $photo?->taken_at;
    }

    public function countTripMembers(int $tripId): int
    {
        return TripMember::where('trip_id', $tripId)->count();
    }
}
