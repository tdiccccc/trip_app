<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Spot;

use Packages\Application\DTOs\PhotoDto;
use Packages\Application\DTOs\SpotDto;
use Packages\Application\DTOs\SpotMemoDto;
use Packages\Domain\Repositories\PhotoRepositoryInterface;
use Packages\Domain\Repositories\SpotMemoRepositoryInterface;
use Packages\Domain\Repositories\SpotRepositoryInterface;

final class GetSpotDetailUseCase
{
    public function __construct(
        private readonly SpotRepositoryInterface $spotRepository,
        private readonly SpotMemoRepositoryInterface $spotMemoRepository,
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {
    }

    /**
     * @return array{spot: SpotDto, memos: SpotMemoDto[], photos: PhotoDto[]}|null
     */
    public function execute(int $tripId, int $id): ?array
    {
        $spot = $this->spotRepository->findById($id);

        if ($spot === null) {
            return null;
        }

        $memos = $this->spotMemoRepository->findBySpotId($id);
        $photos = $this->photoRepository->findBySpotId($id);

        return [
            'spot' => SpotDto::fromEntity($spot),
            'memos' => array_map(
                fn ($memo) => SpotMemoDto::fromEntity($memo),
                $memos,
            ),
            'photos' => array_map(
                fn ($photo) => PhotoDto::fromEntity($photo),
                $photos,
            ),
        ];
    }
}
