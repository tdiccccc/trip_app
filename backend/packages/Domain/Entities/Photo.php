<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\ValueObjects\PhotoPath;

final class Photo
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly ?int $spotId,
        public readonly PhotoPath $storagePath,
        public readonly ?PhotoPath $thumbnailPath,
        public readonly string $originalFilename,
        public readonly string $mimeType,
        public readonly int $fileSize,
        public readonly ?string $caption,
        public readonly ?string $takenAt,
    ) {
    }
}
