<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum TripMemberRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
