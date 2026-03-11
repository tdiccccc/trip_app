<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TripMember as TripMemberModel;
use Packages\Domain\Entities\TripMember;
use Packages\Domain\Enums\TripMemberRole;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;

class EloquentTripMemberRepository implements TripMemberRepositoryInterface
{
    /**
     * @return TripMember[]
     */
    public function findByTripId(int $tripId): array
    {
        return TripMemberModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('joined_at', 'asc')
            ->get()
            ->map(fn (TripMemberModel $model): TripMember => $this->toEntity($model))
            ->all();
    }

    public function findByTripIdAndUserId(int $tripId, int $userId): ?TripMember
    {
        $model = TripMemberModel::query()
            ->where('trip_id', $tripId)
            ->where('user_id', $userId)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(TripMember $member): TripMember
    {
        $model = $member->id === 0
            ? new TripMemberModel
            : TripMemberModel::findOrFail($member->id);

        $model->fill([
            'trip_id' => $member->tripId,
            'user_id' => $member->userId,
            'role' => $member->role->value,
            'joined_at' => $member->joinedAt,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function deleteByTripIdAndUserId(int $tripId, int $userId): void
    {
        TripMemberModel::query()
            ->where('trip_id', $tripId)
            ->where('user_id', $userId)
            ->delete();
    }

    private function toEntity(TripMemberModel $model): TripMember
    {
        return new TripMember(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            role: TripMemberRole::from($model->role),
            joinedAt: $model->joined_at?->toIso8601String(),
            createdAt: $model->created_at?->toIso8601String(),
            updatedAt: $model->updated_at?->toIso8601String(),
        );
    }
}
