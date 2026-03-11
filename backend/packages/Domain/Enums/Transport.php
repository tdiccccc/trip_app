<?php

declare(strict_types=1);

namespace Packages\Domain\Enums;

enum Transport: string
{
    case Train = 'train';
    case Car = 'car';
    case Walk = 'walk';
    case Bus = 'bus';
    case None = 'none';
}
