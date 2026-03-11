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
}
