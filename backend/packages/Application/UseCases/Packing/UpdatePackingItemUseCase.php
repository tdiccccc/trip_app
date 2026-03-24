<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Packing;

use Packages\Application\DTOs\PackingItemDto;
use Packages\Domain\Entities\PackingItem;
use Packages\Domain\Enums\Assignee;
use Packages\Domain\Repositories\PackingItemRepositoryInterface;

final class UpdatePackingItemUseCase
{
    public function __construct(
        private readonly PackingItemRepositoryInterface $packingItemRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(int $tripId, int $id, array $data): ?PackingItemDto
    {
        $existing = $this->packingItemRepository->findById($id);

        if ($existing === null) {
            return null;
        }

        $assignee = $existing->assignee;
        if (array_key_exists('assignee', $data)) {
            $assignee = Assignee::from($data['assignee']);
        }

        $updated = new PackingItem(
            id: $existing->id,
            tripId: $existing->tripId,
            userId: $existing->userId,
            name: $data['name'] ?? $existing->name,
            isChecked: array_key_exists('is_checked', $data) ? (bool) $data['is_checked'] : $existing->isChecked,
            assignee: $assignee,
            category: array_key_exists('category', $data) ? $data['category'] : $existing->category,
            sortOrder: $data['sort_order'] ?? $existing->sortOrder,
        );

        $saved = $this->packingItemRepository->save($updated);

        return PackingItemDto::fromEntity($saved);
    }
}
