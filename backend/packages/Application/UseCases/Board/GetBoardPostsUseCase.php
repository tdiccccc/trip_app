<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Board;

use Packages\Application\DTOs\BoardPostDto;
use Packages\Application\DTOs\ReactionDto;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;
use Packages\Domain\Repositories\ReactionRepositoryInterface;

final class GetBoardPostsUseCase
{
    public function __construct(
        private readonly BoardPostRepositoryInterface $boardPostRepository,
        private readonly ReactionRepositoryInterface $reactionRepository,
    ) {
    }

    /**
     * @return array<int, array{post: BoardPostDto, reactions: ReactionDto[]}>
     */
    public function execute(): array
    {
        $posts = $this->boardPostRepository->findAll();

        return array_map(function ($post) {
            $reactions = $this->reactionRepository->findByBoardPostId($post->id);

            return [
                'post' => BoardPostDto::fromEntity($post),
                'reactions' => array_map(
                    fn ($reaction) => ReactionDto::fromEntity($reaction),
                    $reactions,
                ),
            ];
        }, $posts);
    }
}
