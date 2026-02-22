<?php

declare(strict_types=1);

namespace App\Pricing;

interface PriceProvider
{
    public function name(): string;

    /** @throws \RuntimeException ha nincs ilyen termék */
    public function getPriceFt(string $productId): int;
}
