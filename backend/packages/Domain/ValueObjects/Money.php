<?php

declare(strict_types=1);

namespace Packages\Domain\ValueObjects;

use Packages\Domain\Exceptions\DomainException;

final class Money
{
    public readonly int $amount;

    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new DomainException("Money amount must be 0 or greater. Given: {$amount}");
        }

        $this->amount = $amount;
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }
}
