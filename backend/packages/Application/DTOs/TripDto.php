<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Trip;

final readonly class TripDto
{
    /**
     * @param  TripMemberDto[]  $members
     */
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $destination,
        public string $startDate,
        public string $endDate,
        public ?string $coverImageUrl,
        public int $createdBy,
        public array $members,
        public ?string $currentUserRole,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  TripMemberDto[]  $members
     */
    public static function fromEntity(
        Trip $trip,
        array $members = [],
        ?string $currentUserRole = null,
    ): self {
        return new self(
            id: $trip->id,
            title: $trip->title,
            description: $trip->description,
            destination: $trip->destination,
            startDate: $trip->startDate,
            endDate: $trip->endDate,
            coverImageUrl: $trip->coverImageUrl,
            createdBy: $trip->createdBy,
            members: $members,
            currentUserRole: $currentUserRole,
            createdAt: $trip->createdAt,
            updatedAt: $trip->updatedAt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'destination' => $this->destination,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'cover_image_url' => $this->coverImageUrl,
            'created_by' => $this->createdBy,
            'members' => array_map(
                fn (TripMemberDto $member) => $member->toArray(),
                $this->members,
            ),
            'current_user_role' => $this->currentUserRole,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
