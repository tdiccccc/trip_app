<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases\Auth;

use Packages\Application\UseCases\Auth\GetAuthenticatedUserUseCase;
use PHPUnit\Framework\TestCase;

final class GetAuthenticatedUserUseCaseTest extends TestCase
{
    public function test_execute_returns_user_dto(): void
    {
        $useCase = new GetAuthenticatedUserUseCase;

        $dto = $useCase->execute(id: 5, name: '花子', email: 'hanako@example.com');

        $this->assertSame(5, $dto->id);
        $this->assertSame('花子', $dto->name);
        $this->assertSame('hanako@example.com', $dto->email);
    }
}
