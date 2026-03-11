<?php

declare(strict_types=1);

namespace Packages\Domain\ValueObjects;

use Packages\Domain\Exceptions\DomainException;

final class SpotLocation
{
    public readonly float $latitude;
    public readonly float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        if ($latitude < -90.0 || $latitude > 90.0) {
            throw new DomainException("Latitude must be between -90 and 90. Given: {$latitude}");
        }

        if ($longitude < -180.0 || $longitude > 180.0) {
            throw new DomainException("Longitude must be between -180 and 180. Given: {$longitude}");
        }

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function equals(self $other): bool
    {
        return $this->latitude === $other->latitude
            && $this->longitude === $other->longitude;
    }
}
