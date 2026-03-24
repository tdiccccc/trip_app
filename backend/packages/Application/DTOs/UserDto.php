<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\User;

final readonly class UserDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }

    /**
     * @return array{id: int, name: string, email: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
