<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ItineraryItem as ItineraryItemModel;
use Packages\Domain\Entities\ItineraryItem;
use Packages\Domain\Enums\Transport;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;

class EloquentItineraryRepository implements ItineraryRepositoryInterface
{
    /**
     * @return ItineraryItem[]
     */
    public function findAll(int $tripId, ?string $date = null): array
    {
        $query = ItineraryItemModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('date', 'asc')
            ->orderBy('sort_order', 'asc');

        if ($date !== null) {
            $query->where('date', $date);
        }

        return $query->get()
            ->map(fn (ItineraryItemModel $model): ItineraryItem => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?ItineraryItem
    {
        $model = ItineraryItemModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(ItineraryItem $item): ItineraryItem
    {
        $model = $item->id === 0
            ? new ItineraryItemModel
            : ItineraryItemModel::findOrFail($item->id);

        $model->fill([
            'trip_id' => $item->tripId,
            'user_id' => $item->userId,
            'spot_id' => $item->spotId,
            'title' => $item->title,
            'memo' => $item->memo,
            'date' => $item->date,
            'start_time' => $item->startTime,
            'end_time' => $item->endTime,
            'transport' => $item->transport?->value,
            'sort_order' => $item->sortOrder,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        ItineraryItemModel::findOrFail($id)->delete();
    }

    /**
     * @param  array<array{id: int, sort_order: int}>  $items
     */
    public function updateSortOrders(array $items): void
    {
        foreach ($items as $item) {
            ItineraryItemModel::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }
    }

    private function toEntity(ItineraryItemModel $model): ItineraryItem
    {
        return new ItineraryItem(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            spotId: $model->spot_id,
            title: $model->title,
            memo: $model->memo,
            date: $model->date,
            startTime: $model->start_time,
            endTime: $model->end_time,
            transport: $model->transport !== null ? Transport::from($model->transport) : null,
            sortOrder: $model->sort_order,
        );
    }
}
