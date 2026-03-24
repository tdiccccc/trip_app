<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Application\DTOs\TripDto;
use Packages\Application\DTOs\TripMemberDto;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Packages\Domain\Repositories\TripRepositoryInterface;

final class GetTripListUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripMemberRepositoryInterface $tripMemberRepository,
    ) {}

    /**
     * @return TripDto[]
     */
    public function execute(int $userId): array
    {
        $trips = $this->tripRepository->findByUserId($userId);

        return array_map(function ($trip) {
            $members = $this->tripMemberRepository->findByTripId($trip->id);
            $memberDtos = array_map(
                fn ($member) => TripMemberDto::fromEntity($member),
                $members,
            );

            return TripDto::fromEntity($trip, $memberDtos);
        }, $trips);
    }
}
