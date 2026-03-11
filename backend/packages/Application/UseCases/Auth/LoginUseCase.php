<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Auth;

use Packages\Application\DTOs\UserDto;
use Packages\Domain\Entities\User;

final class LoginUseCase
{
    /**
     * Eloquent User モデルから Domain Entity を経由して DTO を生成する。
     * 認証処理自体は Infrastructure 層（Controller）で行い、
     * このユースケースは認証済みユーザーの変換のみを担当する。
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
