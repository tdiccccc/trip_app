<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Export;

use Packages\Application\DTOs\PhotoDto;
use Packages\Domain\Repositories\PhotoRepositoryInterface;

final class ExportPhotobookUseCase
{
    public function __construct(
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {}

    /**
     * 全写真を撮影日時順で取得する。
     *
     * @return PhotoDto[]
     */
    public function execute(int $tripId): array
    {
        $photos = $this->photoRepository->findAll($tripId, sort: 'taken_at', order: 'asc');

        return array_map(
            fn ($photo) => PhotoDto::fromEntity($photo),
            $photos,
        );
    }
}
