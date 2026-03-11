<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Auth;

use Packages\Application\DTOs\UserDto;
use Packages\Domain\Entities\User;

final class GetAuthenticatedUserUseCase
{
    /**
     * 認証済みユーザー情報を Domain Entity 経由で DTO に変換する。
     */
    public function execute(int $id, string $name, string $email): UserDto
    {
        $entity = new User(
            id: $id,
            name: $name,
            email: $email,
        );

        return UserDto::fromEntity($entity);
    }
}
