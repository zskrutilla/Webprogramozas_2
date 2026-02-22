<?php

declare(strict_types=1);

namespace App\Shipping;

/**
 * Távolság alapú díj: alap + km díj + súlyszorzó.
 */
final class DistanceBasedStrategy implements ShippingStrategy
{
    public function __construct(private int $baseFt, private int $perKmFt, private float $perKgMultiplier) {}

    public function name(): string
    {
        return 'Távolság alapú';
    }

    public function costFt(array $ctx): int
    {
        $km = max(0.0, (float)$ctx['distanceKm']);
        $kg = max(0.0, (float)$ctx['weightKg']);

        $raw = $this->baseFt + $this->perKmFt * $km;
        $raw = $raw * (1.0 + $this->perKgMultiplier * $kg);

        return (int)round(max(0.0, $raw));
    }
}
