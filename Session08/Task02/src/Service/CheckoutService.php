<?php

declare(strict_types=1);

namespace App\Service;

use App\Shipping\ShippingStrategy;

/**
 * Context osztály: a stratégiát kívülről kapja (dependency injection).
 */
final class CheckoutService
{
    public function __construct(private ShippingStrategy $shipping) {}

    /**
     * @return array{shippingFt:int,totalFt:int}
     */
    public function calculate(float $weightKg, float $distanceKm, int $subtotalFt): array
    {
        $ctx = ['weightKg' => $weightKg, 'distanceKm' => $distanceKm, 'subtotalFt' => $subtotalFt];
        $ship = $this->shipping->costFt($ctx);
        return ['shippingFt' => $ship, 'totalFt' => max(0, $subtotalFt + $ship)];
    }
}
