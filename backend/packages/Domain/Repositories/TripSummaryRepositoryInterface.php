<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

interface TripSummaryRepositoryInterface
{
    public function countPhotos(int $tripId): int;

    public function countSpots(int $tripId): int;

    public function countBoardPosts(int $tripId): int;

    public function countReactions(int $tripId): int;

    public function countItineraryItems(int $tripId): int;

    public function sumExpenses(int $tripId): int;

    /**
     * @return array<string, int>
     */
    public function sumExpensesByCategory(int $tripId): array;

    public function countPackingItems(int $tripId): int;

    public function countCheckedPackingItems(int $tripId): int;

    public function firstPhotoAt(int $tripId): ?string;

    public function lastPhotoAt(int $tripId): ?string;

    public function countTripMembers(int $tripId): int;
}
