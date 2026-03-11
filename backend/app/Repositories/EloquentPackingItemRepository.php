<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PackingItem as PackingItemModel;
use Packages\Domain\Entities\PackingItem;
use Packages\Domain\Enums\Assignee;
use Packages\Domain\Repositories\PackingItemRepositoryInterface;

class EloquentPackingItemRepository implements PackingItemRepositoryInterface
{
    /**
     * @return PackingItem[]
     */
    public function findAll(int $tripId, ?string $assignee = null): array
    {
        $query = PackingItemModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('sort_order', 'asc');

        if ($assignee !== null) {
            $query->where('assignee', $assignee);
        }

        return $query->get()
            ->map(fn (PackingItemModel $model): PackingItem => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?PackingItem
    {
        $model = PackingItemModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(PackingItem $item): PackingItem
    {
        $model = $item->id === 0
            ? new PackingItemModel
            : PackingItemModel::findOrFail($item->id);

        $model->fill([
            'trip_id' => $item->tripId,
            'user_id' => $item->userId,
            'name' => $item->name,
            'is_checked' => $item->isChecked,
            'assignee' => $item->assignee->value,
            'category' => $item->category,
            'sort_order' => $item->sortOrder,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        PackingItemModel::findOrFail($id)->delete();
    }

    private function toEntity(PackingItemModel $model): PackingItem
    {
        return new PackingItem(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            name: $model->name,
            isChecked: $model->is_checked,
            assignee: Assignee::from($model->assignee),
            category: $model->category,
            sortOrder: $model->sort_order,
        );
    }
}
