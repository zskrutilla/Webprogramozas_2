<?php

declare(strict_types=1);

require_once __DIR__ . '/CartItem.php';

/**
 * Cart – több CartItem kezelésére.
 * OOP újdonság: objektumok tömbben, metódusokkal kezelve.
 */
final class Cart
{
    /** @var array<string, CartItem> */
    private array $items = [];

    public function add(Product $p, int $qty): void
    {
        if ($qty <= 0) {
            throw new InvalidArgumentException('A mennyiség legyen pozitív.');
        }
        $this->items[$p->id()] = new CartItem($p, $qty);
    }

    /** @return CartItem[] */
    public function items(): array
    {
        return array_values($this->items);
    }

    public function totalFt(): int
    {
        $sum = 0;
        foreach ($this->items as $it) $sum += $it->subtotalFt();
        return $sum;
    }
}
