<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Application\DTOs\TripDto;
use Packages\Application\DTOs\TripMemberDto;
use Packages\Domain\Entities\Trip;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Packages\Domain\Repositories\TripRepositoryInterface;

final class UpdateTripUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripMemberRepositoryInterface $tripMemberRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(int $tripId, array $data): ?TripDto
    {
        $existing = $this->tripRepository->findById($tripId);

        if ($existing === null) {
            return null;
        }

        $updated = new Trip(
            id: $existing->id,
            title: $data['title'] ?? $existing->title,
            description: array_key_exists('description', $data) ? $data['description'] : $existing->description,
            destination: array_key_exists('destination', $data) ? $data['destination'] : $existing->destination,
            startDate: $data['start_date'] ?? $existing->startDate,
            endDate: $data['end_date'] ?? $existing->endDate,
            coverImageUrl: $existing->coverImageUrl,
            createdBy: $existing->createdBy,
            createdAt: $existing->createdAt,
            updatedAt: $existing->updatedAt,
        );

        $savedTrip = $this->tripRepository->save($updated);

        $members = $this->tripMemberRepository->findByTripId($tripId);
        $memberDtos = array_map(
            fn ($member) => TripMemberDto::fromEntity($member),
            $members,
        );

        return TripDto::fromEntity($savedTrip, $memberDtos);
    }
}
