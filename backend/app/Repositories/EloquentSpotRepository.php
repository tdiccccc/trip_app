<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Spot as SpotModel;
use Packages\Domain\Entities\Spot;
use Packages\Domain\Enums\SpotCategory;
use Packages\Domain\Repositories\SpotRepositoryInterface;
use Packages\Domain\ValueObjects\SpotLocation;

class EloquentSpotRepository implements SpotRepositoryInterface
{
    /**
     * @return Spot[]
     */
    public function findAll(int $tripId, ?SpotCategory $category = null): array
    {
        $query = SpotModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('sort_order', 'asc');

        if ($category !== null) {
            $query->where('category', $category->value);
        }

        return $query->get()
            ->map(fn (SpotModel $model): Spot => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?Spot
    {
        $model = SpotModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    private function toEntity(SpotModel $model): Spot
    {
        return new Spot(
            id: $model->id,
            tripId: $model->trip_id,
            name: $model->name,
            description: $model->description,
            address: $model->address,
            location: ($model->latitude !== null && $model->longitude !== null)
                ? new SpotLocation($model->latitude, $model->longitude)
                : null,
            businessHours: $model->business_hours,
            priceInfo: $model->price_info,
            googleMapsUrl: $model->google_maps_url,
            imageUrl: $model->image_url,
            category: SpotCategory::from($model->category),
            sortOrder: $model->sort_order,
        );
    }
}
