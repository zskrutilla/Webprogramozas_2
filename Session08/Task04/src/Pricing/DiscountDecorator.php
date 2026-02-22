<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Decorator: egy meglévő provider köré "tekerünk" extra logikát.
 */
final class DiscountDecorator implements PriceProvider
{
    public function __construct(private PriceProvider $inner, private float $percent) {}

    public function name(): string
    {
        return $this->inner->name() . ' + Kedvezmény (' . (int)round($this->percent * 100) . '%)';
    }

    public function getPriceFt(string $productId): int
    {
        $p = $this->inner->getPriceFt($productId);
        $p2 = (int)round($p * (1.0 - max(0.0, min(1.0, $this->percent))));
        return max(0, $p2);
    }
}
