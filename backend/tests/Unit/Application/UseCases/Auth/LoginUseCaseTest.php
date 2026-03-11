<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases\Auth;

use Packages\Application\UseCases\Auth\LoginUseCase;
use PHPUnit\Framework\TestCase;

final class LoginUseCaseTest extends TestCase
{
    public function test_execute_returns_user_dto(): void
    {
        $useCase = new LoginUseCase();

        $dto = $useCase->execute(id: 1, name: 'テスト太郎', email: 'taro@example.com');

        $this->assertSame(1, $dto->id);
        $this->assertSame('テスト太郎', $dto->name);
        $this->assertSame('taro@example.com', $dto->email);
    }
}
