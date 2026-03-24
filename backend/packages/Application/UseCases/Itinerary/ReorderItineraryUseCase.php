<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Itinerary;

use Packages\Domain\Repositories\ItineraryRepositoryInterface;

final class ReorderItineraryUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
    ) {}

    /**
     * @param  array<array{id: int, sort_order: int}>  $items
     */
    public function execute(int $tripId, array $items): void
    {
        $this->itineraryRepository->updateSortOrders($items);
    }
}
