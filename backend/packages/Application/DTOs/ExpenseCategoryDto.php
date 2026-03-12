<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\ExpenseCategory;

final readonly class ExpenseCategoryDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public string $name,
        public string $key,
        public ?string $color,
        public int $sortOrder,
    ) {
    }

    public static function fromEntity(ExpenseCategory $category): self
    {
        return new self(
            id: $category->id,
            tripId: $category->tripId,
            name: $category->name,
            key: $category->key,
            color: $category->color,
            sortOrder: $category->sortOrder,
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
            'name' => $this->name,
            'key' => $this->key,
            'color' => $this->color,
            'sort_order' => $this->sortOrder,
        ];
    }
}
