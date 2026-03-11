<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum SpotCategory: string
{
    case Sightseeing = 'sightseeing';
    case Food = 'food';
    case Hotel = 'hotel';
    case Other = 'other';
}
