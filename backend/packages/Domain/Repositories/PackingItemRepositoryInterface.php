<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\PackingItem;

interface PackingItemRepositoryInterface
{
    /**
     * @return PackingItem[]
     */
    public function findAll(?string $assignee = null): array;

    public function findById(int $id): ?PackingItem;

    public function save(PackingItem $item): PackingItem;

    public function delete(int $id): void;
}
