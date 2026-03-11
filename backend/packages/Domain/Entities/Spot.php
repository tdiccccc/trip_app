<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\Enums\SpotCategory;
use Packages\Domain\ValueObjects\SpotLocation;

final class Spot
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $address,
        public readonly ?SpotLocation $location,
        public readonly ?string $businessHours,
        public readonly ?string $priceInfo,
        public readonly ?string $googleMapsUrl,
        public readonly ?string $imageUrl,
        public readonly SpotCategory $category,
        public readonly int $sortOrder,
    ) {
    }
}
