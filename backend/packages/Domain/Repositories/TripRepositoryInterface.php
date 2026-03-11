<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\Trip;

interface TripRepositoryInterface
{
    /**
     * @return Trip[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Trip;

    /**
     * @return Trip[]
     */
    public function findByUserId(int $userId): array;

    public function save(Trip $trip): Trip;

    public function delete(int $id): void;
}
