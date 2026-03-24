<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Itinerary;

use Packages\Application\DTOs\ItineraryItemDto;
use Packages\Domain\Entities\ItineraryItem;
use Packages\Domain\Enums\Transport;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;

final class CreateItineraryItemUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
    ) {}

    public function execute(
        int $tripId,
        int $userId,
        ?int $spotId,
        string $title,
        ?string $memo,
        string $date,
        ?string $startTime,
        ?string $endTime,
        ?string $transport,
        ?int $sortOrder,
    ): ItineraryItemDto {
        $item = new ItineraryItem(
            id: 0,
            tripId: $tripId,
            userId: $userId,
            spotId: $spotId,
            title: $title,
            memo: $memo,
            date: $date,
            startTime: $startTime,
            endTime: $endTime,
            transport: $transport !== null ? Transport::from($transport) : null,
            sortOrder: $sortOrder ?? 0,
        );

        $saved = $this->itineraryRepository->save($item);

        return ItineraryItemDto::fromEntity($saved);
    }
}
