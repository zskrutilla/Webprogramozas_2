<?php

declare(strict_types=1);

namespace App\Shipping;

/**
 * Strategy interface – többféle számítási algoritmus egységes felületen.
 */
interface ShippingStrategy
{
    public function name(): string;

    /**
     * @param array{weightKg:float, distanceKm:float, subtotalFt:int} $ctx
     */
    public function costFt(array $ctx): int;
}
