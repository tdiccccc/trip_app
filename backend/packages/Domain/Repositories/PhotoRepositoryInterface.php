<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\Photo;

interface PhotoRepositoryInterface
{
    /**
     * @return Photo[]
     */
    public function findAll(int $tripId, ?int $spotId = null, string $sort = 'taken_at', string $order = 'desc'): array;

    /**
     * @return Photo[]
     */
    public function findBySpotId(int $spotId): array;

    public function findById(int $id): ?Photo;

    public function save(Photo $photo): Photo;

    public function delete(int $id): void;
}
