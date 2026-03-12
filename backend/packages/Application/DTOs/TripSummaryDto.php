<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

final readonly class TripSummaryDto
{
    /**
     * @param array<string, int> $expenseByCategory
     */
    public function __construct(
        public int $photoCount,
        public int $spotCount,
        public int $boardPostCount,
        public int $reactionCount,
        public int $itineraryItemCount,
        public int $totalExpense,
        public int $expensePerPerson,
        public array $expenseByCategory,
        public int $packingTotal,
        public int $packingChecked,
        public ?string $firstPhotoAt,
        public ?string $lastPhotoAt,
        public int $tripDays,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'photo_count' => $this->photoCount,
            'spot_count' => $this->spotCount,
            'board_post_count' => $this->boardPostCount,
            'reaction_count' => $this->reactionCount,
            'itinerary_item_count' => $this->itineraryItemCount,
            'total_expense' => $this->totalExpense,
            'expense_per_person' => $this->expensePerPerson,
            'expense_by_category' => $this->expenseByCategory,
            'packing_total' => $this->packingTotal,
            'packing_checked' => $this->packingChecked,
            'first_photo_at' => $this->firstPhotoAt,
            'last_photo_at' => $this->lastPhotoAt,
            'trip_days' => $this->tripDays,
        ];
    }
}
