<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Application\DTOs\TripDto;
use Packages\Application\DTOs\TripMemberDto;
use Packages\Domain\Entities\Trip;
use Packages\Domain\Entities\TripMember;
use Packages\Domain\Enums\TripMemberRole;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Packages\Domain\Repositories\TripRepositoryInterface;

final class CreateTripUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripMemberRepositoryInterface $tripMemberRepository,
    ) {
    }

    /**
     * @param int[] $memberIds
     */
    public function execute(
        int $userId,
        string $title,
        ?string $description,
        ?string $destination,
        string $startDate,
        string $endDate,
        array $memberIds = [],
    ): TripDto {
        $trip = new Trip(
            id: 0,
            title: $title,
            description: $description,
            destination: $destination,
            startDate: $startDate,
            endDate: $endDate,
            coverImageUrl: null,
            createdBy: $userId,
            createdAt: null,
            updatedAt: null,
        );

        $savedTrip = $this->tripRepository->save($trip);

        // 作成者を owner として登録
        $ownerMember = new TripMember(
            id: 0,
            tripId: $savedTrip->id,
            userId: $userId,
            role: TripMemberRole::Owner,
            joinedAt: null,
            createdAt: null,
            updatedAt: null,
        );
        $savedOwner = $this->tripMemberRepository->save($ownerMember);

        $memberDtos = [TripMemberDto::fromEntity($savedOwner)];

        // 招待メンバーを member として登録
        foreach ($memberIds as $memberId) {
            if ($memberId === $userId) {
                continue;
            }

            $member = new TripMember(
                id: 0,
                tripId: $savedTrip->id,
                userId: $memberId,
                role: TripMemberRole::Member,
                joinedAt: null,
                createdAt: null,
                updatedAt: null,
            );
            $savedMember = $this->tripMemberRepository->save($member);
            $memberDtos[] = TripMemberDto::fromEntity($savedMember);
        }

        return TripDto::fromEntity($savedTrip, $memberDtos);
    }
}
