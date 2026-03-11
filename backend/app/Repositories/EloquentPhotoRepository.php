<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Photo as PhotoModel;
use Packages\Domain\Entities\Photo;
use Packages\Domain\Repositories\PhotoRepositoryInterface;
use Packages\Domain\ValueObjects\PhotoPath;

class EloquentPhotoRepository implements PhotoRepositoryInterface
{
    /**
     * @return Photo[]
     */
    public function findAll(int $tripId, ?int $spotId = null, string $sort = 'taken_at', string $order = 'desc'): array
    {
        $query = PhotoModel::query()
            ->where('trip_id', $tripId);

        if ($spotId !== null) {
            $query->where('spot_id', $spotId);
        }

        $query->orderBy($sort, $order);

        return $query->get()
            ->map(fn (PhotoModel $model): Photo => $this->toEntity($model))
            ->all();
    }

    /**
     * @return Photo[]
     */
    public function findBySpotId(int $spotId): array
    {
        return PhotoModel::query()
            ->where('spot_id', $spotId)
            ->orderBy('taken_at', 'desc')
            ->get()
            ->map(fn (PhotoModel $model): Photo => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?Photo
    {
        $model = PhotoModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(Photo $photo): Photo
    {
        $model = $photo->id === 0
            ? new PhotoModel
            : PhotoModel::findOrFail($photo->id);

        $model->fill([
            'trip_id' => $photo->tripId,
            'user_id' => $photo->userId,
            'spot_id' => $photo->spotId,
            'storage_path' => $photo->storagePath->toString(),
            'thumbnail_path' => $photo->thumbnailPath?->toString(),
            'original_filename' => $photo->originalFilename,
            'mime_type' => $photo->mimeType,
            'file_size' => $photo->fileSize,
            'caption' => $photo->caption,
            'taken_at' => $photo->takenAt,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        PhotoModel::findOrFail($id)->delete();
    }

    private function toEntity(PhotoModel $model): Photo
    {
        return new Photo(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            spotId: $model->spot_id,
            storagePath: new PhotoPath($model->storage_path),
            thumbnailPath: $model->thumbnail_path !== null ? new PhotoPath($model->thumbnail_path) : null,
            originalFilename: $model->original_filename,
            mimeType: $model->mime_type,
            fileSize: $model->file_size,
            caption: $model->caption,
            takenAt: $model->taken_at,
        );
    }
}
