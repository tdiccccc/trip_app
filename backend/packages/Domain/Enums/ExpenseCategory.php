<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum ExpenseCategory: string
{
    case Transport = 'transport';
    case Food = 'food';
    case Souvenir = 'souvenir';
    case Accommodation = 'accommodation';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Food => '食事',
            self::Transport => '交通',
            self::Souvenir => 'お土産',
            self::Accommodation => '宿泊',
            self::Other => 'その他',
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
