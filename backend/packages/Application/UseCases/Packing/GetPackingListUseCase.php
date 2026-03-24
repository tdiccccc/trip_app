<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Packing;

use Packages\Application\DTOs\PackingItemDto;
use Packages\Domain\Repositories\PackingItemRepositoryInterface;

final class GetPackingListUseCase
{
    public function __construct(
        private readonly PackingItemRepositoryInterface $packingItemRepository,
    ) {}

    /**
     * @return PackingItemDto[]
     */
    public function execute(int $tripId, ?string $assignee = null): array
    {
        $items = $this->packingItemRepository->findAll($tripId, $assignee);

        return array_map(
            fn ($item) => PackingItemDto::fromEntity($item),
            $items,
        );
    }
}
