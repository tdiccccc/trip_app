<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Board;

use Packages\Application\DTOs\BoardPostDto;
use Packages\Application\DTOs\ReactionDto;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;
use Packages\Domain\Repositories\ReactionRepositoryInterface;
use Packages\Domain\Repositories\UserRepositoryInterface;

final class GetBoardPostsUseCase
{
    public function __construct(
        private readonly BoardPostRepositoryInterface $boardPostRepository,
        private readonly ReactionRepositoryInterface $reactionRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return array<int, array{post: BoardPostDto, reactions: ReactionDto[]}>
     */
    public function execute(int $tripId): array
    {
        $posts = $this->boardPostRepository->findAll($tripId);

        // Build user name map
        $users = $this->userRepository->findAll();
        $userNameMap = [];
        foreach ($users as $user) {
            $userNameMap[$user->id] = $user->name;
        }

        return array_map(function ($post) use ($userNameMap) {
            $reactions = $this->reactionRepository->findByBoardPostId($post->id);

            return [
                'post' => BoardPostDto::fromEntity($post, $userNameMap[$post->userId] ?? null),
                'reactions' => array_map(
                    fn ($reaction) => ReactionDto::fromEntity($reaction),
                    $reactions,
                ),
            ];
        }, $posts);
    }
}
