<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Trip;

use Packages\Domain\Repositories\TripRepositoryInterface;

final class DeleteTripUseCase
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
    ) {}

    public function execute(int $tripId): void
    {
        $this->tripRepository->delete($tripId);
    }
}
