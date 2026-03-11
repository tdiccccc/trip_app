<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Board;

use Packages\Application\DTOs\BoardPostDto;
use Packages\Domain\Entities\BoardPost;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;

final class PostMessageUseCase
{
    public function __construct(
        private readonly BoardPostRepositoryInterface $boardPostRepository,
    ) {
    }

    public function execute(int $userId, string $body, ?int $photoId, bool $isBestShot): BoardPostDto
    {
        $post = new BoardPost(
            id: 0,
            userId: $userId,
            body: $body,
            photoId: $photoId,
            isBestShot: $isBestShot,
        );

        $saved = $this->boardPostRepository->save($post);

        return BoardPostDto::fromEntity($saved);
    }
}
