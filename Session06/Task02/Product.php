<?php

declare(strict_types=1);

/**
 * Product – értékobjektum jellegű osztály.
 * OOP újdonság: típusos property + getter metódusok.
 */
final class Product
{
    public function __construct(
        private string $id,
        private string $name,
        private int $priceFt
    ) {
        $this->id = trim($this->id);
        $this->name = trim($this->name);
        $this->priceFt = max(0, $this->priceFt);
    }

    public function id(): string
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function priceFt(): int
    {
        return $this->priceFt;
    }
}
