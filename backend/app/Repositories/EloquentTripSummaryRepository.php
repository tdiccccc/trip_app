<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BoardPost;
use App\Models\Expense;
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
        $results = Expense::where('trip_id', $tripId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $typed = [];
        foreach ($results as $category => $total) {
            $typed[(string) $category] = (int) $total;
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
