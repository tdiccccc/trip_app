<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Packing;

use Packages\Domain\Repositories\PackingItemRepositoryInterface;

final class DeletePackingItemUseCase
{
    public function __construct(
        private readonly PackingItemRepositoryInterface $packingItemRepository,
    ) {}

    public function execute(int $tripId, int $id): void
    {
        $this->packingItemRepository->delete($id);
    }
}
