<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\TripMember;

interface TripMemberRepositoryInterface
{
    /**
     * @return TripMember[]
     */
    public function findByTripId(int $tripId): array;

    public function findByTripIdAndUserId(int $tripId, int $userId): ?TripMember;

    public function save(TripMember $member): TripMember;

    public function deleteByTripIdAndUserId(int $tripId, int $userId): void;
}
