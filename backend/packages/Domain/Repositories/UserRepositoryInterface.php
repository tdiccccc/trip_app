<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAll(): array;
}
