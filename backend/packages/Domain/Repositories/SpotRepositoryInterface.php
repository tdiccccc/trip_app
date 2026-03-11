<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\Spot;
use Packages\Domain\Enums\SpotCategory;

interface SpotRepositoryInterface
{
    /**
     * @return Spot[]
     */
    public function findAll(int $tripId, ?SpotCategory $category = null): array;

    public function findById(int $id): ?Spot;
}
