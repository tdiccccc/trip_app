<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\ItineraryItem;

final readonly class ItineraryItemDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public ?int $spotId,
        public string $title,
        public ?string $memo,
        public string $date,
        public ?string $startTime,
        public ?string $endTime,
        public ?string $transport,
        public int $sortOrder,
    ) {}

    public static function fromEntity(ItineraryItem $item): self
    {
        return new self(
            id: $item->id,
            tripId: $item->tripId,
            userId: $item->userId,
            spotId: $item->spotId,
            title: $item->title,
            memo: $item->memo,
            date: $item->date,
            startTime: $item->startTime,
            endTime: $item->endTime,
            transport: $item->transport?->value,
            sortOrder: $item->sortOrder,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'user_id' => $this->userId,
            'spot_id' => $this->spotId,
            'title' => $this->title,
            'memo' => $this->memo,
            'date' => $this->date,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'transport' => $this->transport,
            'sort_order' => $this->sortOrder,
        ];
    }
}
