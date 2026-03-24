<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\PackingItem;

final readonly class PackingItemDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public string $name,
        public bool $isChecked,
        public string $assignee,
        public ?string $category,
        public int $sortOrder,
    ) {}

    public static function fromEntity(PackingItem $item): self
    {
        return new self(
            id: $item->id,
            tripId: $item->tripId,
            userId: $item->userId,
            name: $item->name,
            isChecked: $item->isChecked,
            assignee: $item->assignee->value,
            category: $item->category,
            sortOrder: $item->sortOrder,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'user_id' => $this->userId,
            'name' => $this->name,
            'is_checked' => $this->isChecked,
            'assignee' => $this->assignee,
            'category' => $this->category,
            'sort_order' => $this->sortOrder,
        ];
    }
}
