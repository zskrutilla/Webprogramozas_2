<?php

declare(strict_types=1);

namespace App\Domain;

use App\Value\Email;
use App\Value\Money;

final class Invoice
{
    /** @var InvoiceItem[] */
    private array $items = [];

    public function __construct(private Email $customerEmail) {}

    public function customerEmail(): Email
    {
        return $this->customerEmail;
    }
    public function addItem(InvoiceItem $item): void
    {
        $this->items[] = $item;
    }
    /** @return InvoiceItem[] */ public function items(): array
    {
        return $this->items;
    }

    public function totalNet(): Money
    {
        $sum = new Money(0);
        foreach ($this->items as $it) $sum = $sum->add($it->subtotal());
        return $sum;
    }

    public function totalGross(float $vatRate = 0.27): Money
    {
        $net = $this->totalNet()->ft();
        return new Money((int)round($net * (1.0 + $vatRate)));
    }
}
