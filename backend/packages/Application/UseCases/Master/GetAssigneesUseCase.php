<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Master;

use Packages\Domain\Enums\Assignee;

final class GetAssigneesUseCase
{
    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function execute(): array
    {
        return Assignee::toArray();
    }
}
