<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum Assignee: string
{
    case Self = 'self';
    case Partner = 'partner';
    case Shared = 'shared';

    public function label(): string
    {
        return match ($this) {
            self::Self => '自分',
            self::Partner => 'パートナー',
            self::Shared => '共有',
        };
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $case): array => ['key' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
