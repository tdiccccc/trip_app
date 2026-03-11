<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum Assignee: string
{
    case Self = 'self';
    case Partner = 'partner';
    case Shared = 'shared';
}
