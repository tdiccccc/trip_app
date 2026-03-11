<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Trip as TripModel;
use Packages\Domain\Entities\Trip;
use Packages\Domain\Repositories\TripRepositoryInterface;

class EloquentTripRepository implements TripRepositoryInterface
{
    /**
     * @return Trip[]
     */
    public function findAll(): array
    {
        return TripModel::query()
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn (TripModel $model): Trip => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?Trip
    {
        $model = TripModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /**
     * @return Trip[]
     */
    public function findByUserId(int $userId): array
    {
        return TripModel::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn (TripModel $model): Trip => $this->toEntity($model))
            ->all();
    }

    public function save(Trip $trip): Trip
    {
        $model = $trip->id === 0
            ? new TripModel
            : TripModel::findOrFail($trip->id);

        $model->fill([
            'title' => $trip->title,
            'description' => $trip->description,
            'destination' => $trip->destination,
            'start_date' => $trip->startDate,
            'end_date' => $trip->endDate,
            'cover_image_url' => $trip->coverImageUrl,
            'created_by' => $trip->createdBy,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        TripModel::findOrFail($id)->delete();
    }

    private function toEntity(TripModel $model): Trip
    {
        return new Trip(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            destination: $model->destination,
            startDate: $model->start_date->format('Y-m-d'),
            endDate: $model->end_date->format('Y-m-d'),
            coverImageUrl: $model->cover_image_url,
            createdBy: $model->created_by,
            createdAt: $model->created_at?->toIso8601String(),
            updatedAt: $model->updated_at?->toIso8601String(),
        );
    }
}
