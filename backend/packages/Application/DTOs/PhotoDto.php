<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Photo;

final readonly class PhotoDto
{
    public function __construct(
        public int $id,
        public int $userId,
        public ?int $spotId,
        public string $storagePath,
        public ?string $thumbnailPath,
        public string $originalFilename,
        public string $mimeType,
        public int $fileSize,
        public ?string $caption,
        public ?string $takenAt,
    ) {
    }

    public static function fromEntity(Photo $photo): self
    {
        return new self(
            id: $photo->id,
            userId: $photo->userId,
            spotId: $photo->spotId,
            storagePath: $photo->storagePath->toString(),
            thumbnailPath: $photo->thumbnailPath?->toString(),
            originalFilename: $photo->originalFilename,
            mimeType: $photo->mimeType,
            fileSize: $photo->fileSize,
            caption: $photo->caption,
            takenAt: $photo->takenAt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'spot_id' => $this->spotId,
            'storage_path' => $this->storagePath,
            'thumbnail_path' => $this->thumbnailPath,
            'original_filename' => $this->originalFilename,
            'mime_type' => $this->mimeType,
            'file_size' => $this->fileSize,
            'caption' => $this->caption,
            'taken_at' => $this->takenAt,
        ];
    }
}
