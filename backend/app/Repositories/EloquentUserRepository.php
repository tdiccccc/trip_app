<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Packages\Domain\Entities\User as UserEntity;
use Packages\Domain\Repositories\UserRepositoryInterface;

final class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * @return UserEntity[]
     */
    public function findAll(): array
    {
        return User::query()
            ->orderBy('id')
            ->get()
            ->map(fn (User $user) => new UserEntity(
                id: $user->id,
                name: $user->name,
                email: $user->email,
            ))
            ->all();
    }
}
