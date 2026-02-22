<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Order
{
    /** @var OrderItem[] */
    private array $items;

    public function __construct(
        private ?int $id,
        private string $customer,
        array $items
    ) {
        $this->customer = trim($this->customer);
        if ($this->customer === '') throw new InvalidArgumentException('A vevő neve kötelező.');
        if (!$items) throw new InvalidArgumentException('Legalább 1 tétel kell.');
        $this->items = $items;
    }

    public static function createNew(string $customer, array $items): self
    {
        return new self(null, $customer, $items);
    }

    public function id(): ?int
    {
        return $this->id;
    }
    public function customer(): string
    {
        return $this->customer;
    }
    /** @return OrderItem[] */ public function items(): array
    {
        return $this->items;
    }

    public function withId(int $id): self
    {
        return new self($id, $this->customer, $this->items);
    }
}
