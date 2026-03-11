<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Album;

use Packages\Domain\Repositories\PhotoRepositoryInterface;

final class DeletePhotoUseCase
{
    public function __construct(
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {
    }

    public function execute(int $tripId, int $id): void
    {
        $this->photoRepository->delete($id);
    }
}
