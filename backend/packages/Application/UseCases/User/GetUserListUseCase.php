<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\User;

use Packages\Application\DTOs\UserDto;
use Packages\Domain\Repositories\UserRepositoryInterface;

final class GetUserListUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return UserDto[]
     */
    public function execute(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(
            fn ($user) => UserDto::fromEntity($user),
            $users,
        );
    }
}
