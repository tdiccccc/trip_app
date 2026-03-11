<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Spot;

use Packages\Application\DTOs\SpotDto;
use Packages\Domain\Enums\SpotCategory;
use Packages\Domain\Repositories\SpotRepositoryInterface;

final class GetSpotListUseCase
{
    public function __construct(
        private readonly SpotRepositoryInterface $spotRepository,
    ) {
    }

    /**
     * @return SpotDto[]
     */
    public function execute(?SpotCategory $category = null): array
    {
        $spots = $this->spotRepository->findAll($category);

        return array_map(
            fn ($spot) => SpotDto::fromEntity($spot),
            $spots,
        );
    }
}
