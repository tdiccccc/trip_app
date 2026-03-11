<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\ItineraryItem;

interface ItineraryRepositoryInterface
{
    /**
     * @return ItineraryItem[]
     */
    public function findAll(int $tripId, ?string $date = null): array;

    public function findById(int $id): ?ItineraryItem;

    public function save(ItineraryItem $item): ItineraryItem;

    public function delete(int $id): void;

    /**
     * @param array<array{id: int, sort_order: int}> $items
     */
    public function updateSortOrders(array $items): void;
}
