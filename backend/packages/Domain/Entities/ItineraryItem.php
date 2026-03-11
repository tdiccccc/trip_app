<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\Enums\Transport;

final class ItineraryItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly ?int $spotId,
        public readonly string $title,
        public readonly ?string $memo,
        public readonly string $date,
        public readonly ?string $startTime,
        public readonly ?string $endTime,
        public readonly ?Transport $transport,
        public readonly int $sortOrder,
    ) {
    }
}
