<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Spot;

use Packages\Application\DTOs\SpotMemoDto;
use Packages\Domain\Entities\SpotMemo;
use Packages\Domain\Repositories\SpotMemoRepositoryInterface;

final class CreateSpotMemoUseCase
{
    public function __construct(
        private readonly SpotMemoRepositoryInterface $spotMemoRepository,
    ) {}

    public function execute(int $tripId, int $spotId, int $userId, string $body): SpotMemoDto
    {
        $memo = new SpotMemo(
            id: 0,
            spotId: $spotId,
            userId: $userId,
            body: $body,
        );

        $saved = $this->spotMemoRepository->save($memo);

        return SpotMemoDto::fromEntity($saved);
    }
}
