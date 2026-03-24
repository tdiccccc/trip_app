<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Itinerary;

use Packages\Application\DTOs\ItineraryItemDto;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;

final class GetItineraryUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
    ) {}

    /**
     * @return ItineraryItemDto[]
     */
    public function execute(int $tripId, ?string $date = null): array
    {
        $items = $this->itineraryRepository->findAll($tripId, $date);

        return array_map(
            fn ($item) => ItineraryItemDto::fromEntity($item),
            $items,
        );
    }
}
