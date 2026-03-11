<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Album;

use Packages\Application\DTOs\PhotoDto;
use Packages\Domain\Entities\Photo;
use Packages\Domain\Repositories\PhotoRepositoryInterface;
use Packages\Domain\ValueObjects\PhotoPath;

final class UploadPhotoUseCase
{
    public function __construct(
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {
    }

    public function execute(
        int $tripId,
        int $userId,
        ?int $spotId,
        string $storagePath,
        ?string $thumbnailPath,
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        ?string $caption,
        ?string $takenAt,
    ): PhotoDto {
        $photo = new Photo(
            id: 0,
            tripId: $tripId,
            userId: $userId,
            spotId: $spotId,
            storagePath: new PhotoPath($storagePath),
            thumbnailPath: $thumbnailPath !== null ? new PhotoPath($thumbnailPath) : null,
            originalFilename: $originalFilename,
            mimeType: $mimeType,
            fileSize: $fileSize,
            caption: $caption,
            takenAt: $takenAt,
        );

        $saved = $this->photoRepository->save($photo);

        return PhotoDto::fromEntity($saved);
    }
}
