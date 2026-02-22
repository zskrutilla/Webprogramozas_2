<?php

declare(strict_types=1);

namespace App\Shipping;

final class FlatRateStrategy implements ShippingStrategy
{
    public function __construct(private int $flatFt) {}

    public function name(): string
    {
        return 'Fix díj';
    }

    public function costFt(array $ctx): int
    {
        return max(0, $this->flatFt);
    }
}
