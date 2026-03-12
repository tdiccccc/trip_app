<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\BoardPost;

interface BoardPostRepositoryInterface
{
    /**
     * @return BoardPost[]
     */
    public function findAll(int $tripId): array;

    public function findById(int $id): ?BoardPost;

    public function save(BoardPost $post): BoardPost;

    public function delete(int $id): void;
}
