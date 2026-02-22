<?php

declare(strict_types=1);

namespace App\Domain;

use App\Value\Money;
use InvalidArgumentException;

final class InvoiceItem
{
    public function __construct(
        private string $name,
        private Money $unitPrice,
        private int $qty
    ) {
        $this->name = trim($this->name);
        if ($this->name === '') throw new InvalidArgumentException('A tétel neve kötelező.');
        if ($this->qty <= 0) throw new InvalidArgumentException('A mennyiség legyen pozitív.');
    }

    public function name(): string
    {
        return $this->name;
    }
    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }
    public function qty(): int
    {
        return $this->qty;
    }
    public function subtotal(): Money
    {
        return $this->unitPrice->mul($this->qty);
    }
}
