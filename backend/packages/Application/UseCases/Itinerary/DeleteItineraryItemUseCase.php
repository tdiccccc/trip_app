<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Itinerary;

use Packages\Domain\Repositories\ItineraryRepositoryInterface;

final class DeleteItineraryItemUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
    ) {
    }

    public function execute(int $tripId, int $id): void
    {
        $this->itineraryRepository->delete($id);
    }
}
