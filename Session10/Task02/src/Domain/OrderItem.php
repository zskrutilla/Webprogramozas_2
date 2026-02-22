<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class OrderItem
{
    public function __construct(
        private int $productId,
        private int $qty
    ) {
        if ($this->productId <= 0) throw new InvalidArgumentException('Érvénytelen termék.');
        if ($this->qty <= 0) throw new InvalidArgumentException('A mennyiség legyen pozitív.');
    }

    public function productId(): int
    {
        return $this->productId;
    }
    public function qty(): int
    {
        return $this->qty;
    }
}
