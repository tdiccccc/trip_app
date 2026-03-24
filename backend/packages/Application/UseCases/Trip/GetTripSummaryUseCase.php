<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Application\DTOs\TripSummaryDto;
use Packages\Domain\Repositories\TripRepositoryInterface;
use Packages\Domain\Repositories\TripSummaryRepositoryInterface;

final class GetTripSummaryUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripSummaryRepositoryInterface $tripSummaryRepository,
    ) {}

    public function execute(int $tripId): ?TripSummaryDto
    {
        $trip = $this->tripRepository->findById($tripId);

        if ($trip === null) {
            return null;
        }

        $totalExpense = $this->tripSummaryRepository->sumExpenses($tripId);
        $memberCount = $this->tripSummaryRepository->countTripMembers($tripId);
        $expensePerPerson = $memberCount > 0
            ? (int) floor($totalExpense / $memberCount)
            : 0;

        // カテゴリ別費用（リポジトリが全カテゴリを含めて返す）
        $expenseByCategory = $this->tripSummaryRepository->sumExpensesByCategory($tripId);

        // trip_days: start_date と end_date の日数差 + 1
        $startDate = new \DateTimeImmutable($trip->startDate);
        $endDate = new \DateTimeImmutable($trip->endDate);
        $tripDays = (int) $endDate->diff($startDate)->days + 1;

        return new TripSummaryDto(
            photoCount: $this->tripSummaryRepository->countPhotos($tripId),
            spotCount: $this->tripSummaryRepository->countSpots($tripId),
            boardPostCount: $this->tripSummaryRepository->countBoardPosts($tripId),
            reactionCount: $this->tripSummaryRepository->countReactions($tripId),
            itineraryItemCount: $this->tripSummaryRepository->countItineraryItems($tripId),
            totalExpense: $totalExpense,
            expensePerPerson: $expensePerPerson,
            expenseByCategory: $expenseByCategory,
            packingTotal: $this->tripSummaryRepository->countPackingItems($tripId),
            packingChecked: $this->tripSummaryRepository->countCheckedPackingItems($tripId),
            firstPhotoAt: $this->tripSummaryRepository->firstPhotoAt($tripId),
            lastPhotoAt: $this->tripSummaryRepository->lastPhotoAt($tripId),
            tripDays: $tripDays,
        );
    }
}
