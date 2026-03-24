<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Application\DTOs\TripDto;
use Packages\Application\DTOs\TripMemberDto;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Packages\Domain\Repositories\TripRepositoryInterface;

final class GetTripDetailUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripMemberRepositoryInterface $tripMemberRepository,
    ) {}

    public function execute(int $tripId, int $userId): ?TripDto
    {
        $trip = $this->tripRepository->findById($tripId);

        if ($trip === null) {
            return null;
        }

        $members = $this->tripMemberRepository->findByTripId($tripId);
        $memberDtos = array_map(
            fn ($member) => TripMemberDto::fromEntity($member),
            $members,
        );

        // current_user_role を判定
        $currentUserRole = null;
        $currentMember = $this->tripMemberRepository->findByTripIdAndUserId($tripId, $userId);
        if ($currentMember !== null) {
            $currentUserRole = $currentMember->role->value;
        }

        return TripDto::fromEntity($trip, $memberDtos, $currentUserRole);
    }
}
