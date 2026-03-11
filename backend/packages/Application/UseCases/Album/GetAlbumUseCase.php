<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Album;

use Packages\Application\DTOs\PhotoDto;
use Packages\Domain\Repositories\PhotoRepositoryInterface;

final class GetAlbumUseCase
{
    public function __construct(
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {
    }

    /**
     * @return PhotoDto[]
     */
    public function execute(int $tripId, ?int $spotId = null, string $sort = 'taken_at', string $order = 'desc'): array
    {
        $photos = $this->photoRepository->findAll($tripId, $spotId, $sort, $order);

        return array_map(
            fn ($photo) => PhotoDto::fromEntity($photo),
            $photos,
        );
    }
}
