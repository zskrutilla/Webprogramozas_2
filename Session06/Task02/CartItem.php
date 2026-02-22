<?php

declare(strict_types=1);

require_once __DIR__ . '/Product.php';

/**
 * CartItem – egy termék + mennyiség.
 */
final class CartItem
{
    public function __construct(
        private Product $product,
        private int $qty
    ) {
        $this->qty = max(0, $this->qty);
    }

    public function product(): Product
    {
        return $this->product;
    }
    public function qty(): int
    {
        return $this->qty;
    }

    public function subtotalFt(): int
    {
        return $this->product->priceFt() * $this->qty;
    }
}
