<?php

declare(strict_types=1);

namespace Tests\Unit\Application\DTOs;

use Packages\Application\DTOs\UserDto;
use Packages\Domain\Entities\User;
use PHPUnit\Framework\TestCase;

final class UserDtoTest extends TestCase
{
    public function test_from_entity_creates_dto_correctly(): void
    {
        $entity = new User(id: 1, name: 'テスト太郎', email: 'taro@example.com');

        $dto = UserDto::fromEntity($entity);

        $this->assertSame(1, $dto->id);
        $this->assertSame('テスト太郎', $dto->name);
        $this->assertSame('taro@example.com', $dto->email);
    }

    public function test_to_array_returns_expected_structure(): void
    {
        $dto = new UserDto(id: 1, name: 'テスト太郎', email: 'taro@example.com');

        $this->assertSame([
            'id' => 1,
            'name' => 'テスト太郎',
            'email' => 'taro@example.com',
        ], $dto->toArray());
    }
}
