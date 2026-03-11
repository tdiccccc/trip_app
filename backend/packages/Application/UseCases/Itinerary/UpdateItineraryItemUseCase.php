<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Itinerary;

use Packages\Application\DTOs\ItineraryItemDto;
use Packages\Domain\Entities\ItineraryItem;
use Packages\Domain\Enums\Transport;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;

final class UpdateItineraryItemUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(int $id, array $data): ?ItineraryItemDto
    {
        $existing = $this->itineraryRepository->findById($id);

        if ($existing === null) {
            return null;
        }

        $transport = $existing->transport;
        if (array_key_exists('transport', $data)) {
            $transport = $data['transport'] !== null ? Transport::from($data['transport']) : null;
        }

        $updated = new ItineraryItem(
            id: $existing->id,
            userId: $existing->userId,
            spotId: array_key_exists('spot_id', $data) ? $data['spot_id'] : $existing->spotId,
            title: $data['title'] ?? $existing->title,
            memo: array_key_exists('memo', $data) ? $data['memo'] : $existing->memo,
            date: $data['date'] ?? $existing->date,
            startTime: array_key_exists('start_time', $data) ? $data['start_time'] : $existing->startTime,
            endTime: array_key_exists('end_time', $data) ? $data['end_time'] : $existing->endTime,
            transport: $transport,
            sortOrder: $data['sort_order'] ?? $existing->sortOrder,
        );

        $saved = $this->itineraryRepository->save($updated);

        return ItineraryItemDto::fromEntity($saved);
    }
}
