<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Album;

use Packages\Domain\Exceptions\UnauthorizedOperationException;
use Packages\Domain\Repositories\PhotoRepositoryInterface;

final class DeletePhotoUseCase
{
    public function __construct(
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {
    }

    public function execute(int $tripId, int $id, int $userId): void
    {
        $photo = $this->photoRepository->findById($id);

        if ($photo === null) {
            return;
        }

        if ($photo->userId !== $userId) {
            throw new UnauthorizedOperationException('この操作は投稿者本人のみ実行できます。');
        }

        $this->photoRepository->delete($id);
    }
}
