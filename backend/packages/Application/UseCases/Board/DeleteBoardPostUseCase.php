<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Board;

use Packages\Domain\Exceptions\UnauthorizedOperationException;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;

final class DeleteBoardPostUseCase
{
    public function __construct(
        private readonly BoardPostRepositoryInterface $boardPostRepository,
    ) {}

    /**
     * @throws UnauthorizedOperationException
     */
    public function execute(int $tripId, int $postId, int $userId): void
    {
        $post = $this->boardPostRepository->findById($postId);

        if ($post === null) {
            throw new \Packages\Domain\Exceptions\DomainException('Not found.');
        }

        if ($post->userId !== $userId) {
            throw new UnauthorizedOperationException('この操作は投稿者本人のみ実行できます。');
        }

        $this->boardPostRepository->delete($postId);
    }
}
