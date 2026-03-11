<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Packing;

use Packages\Application\DTOs\PackingItemDto;
use Packages\Domain\Entities\PackingItem;
use Packages\Domain\Enums\Assignee;
use Packages\Domain\Repositories\PackingItemRepositoryInterface;

final class CreatePackingItemUseCase
{
    public function __construct(
        private readonly PackingItemRepositoryInterface $packingItemRepository,
    ) {
    }

    public function execute(
        int $tripId,
        int $userId,
        string $name,
        string $assignee,
        ?string $category,
        ?int $sortOrder,
    ): PackingItemDto {
        $item = new PackingItem(
            id: 0,
            tripId: $tripId,
            userId: $userId,
            name: $name,
            isChecked: false,
            assignee: Assignee::from($assignee),
            category: $category,
            sortOrder: $sortOrder ?? 0,
        );

        $saved = $this->packingItemRepository->save($item);

        return PackingItemDto::fromEntity($saved);
    }
}
