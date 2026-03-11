<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\SpotMemo;

interface SpotMemoRepositoryInterface
{
    /**
     * @return SpotMemo[]
     */
    public function findBySpotId(int $spotId): array;

    public function save(SpotMemo $memo): SpotMemo;
}
