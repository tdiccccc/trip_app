<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Spot;

final readonly class SpotDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $address,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $businessHours,
        public ?string $priceInfo,
        public ?string $googleMapsUrl,
        public ?string $imageUrl,
        public string $category,
        public int $sortOrder,
    ) {
    }

    public static function fromEntity(Spot $spot): self
    {
        return new self(
            id: $spot->id,
            name: $spot->name,
            description: $spot->description,
            address: $spot->address,
            latitude: $spot->location?->latitude,
            longitude: $spot->location?->longitude,
            businessHours: $spot->businessHours,
            priceInfo: $spot->priceInfo,
            googleMapsUrl: $spot->googleMapsUrl,
            imageUrl: $spot->imageUrl,
            category: $spot->category->value,
            sortOrder: $spot->sortOrder,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'business_hours' => $this->businessHours,
            'price_info' => $this->priceInfo,
            'google_maps_url' => $this->googleMapsUrl,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'sort_order' => $this->sortOrder,
        ];
    }
}
