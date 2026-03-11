<?php

declare(strict_types=1);

namespace Packages\Domain\ValueObjects;

use Packages\Domain\Exceptions\DomainException;

final class PhotoPath
{
    public readonly string $path;

    public function __construct(string $path)
    {
        if ($path === '') {
            throw new DomainException('Photo path must not be empty.');
        }

        $this->path = $path;
    }

    public function toString(): string
    {
        return $this->path;
    }

    public function equals(self $other): bool
    {
        return $this->path === $other->path;
    }
}
