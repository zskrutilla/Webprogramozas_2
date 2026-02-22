<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Product
{
    public function __construct(
        private ?int $id,
        private string $name,
        private int $priceFt
    ) {
        $this->name = trim($this->name);
        if ($this->name === '') throw new InvalidArgumentException('A név kötelező.');
        if ($this->priceFt < 0) throw new InvalidArgumentException('Az ár nem lehet negatív.');
    }

    public static function createNew(string $name, int $priceFt): self
    {
        return new self(null, $name, $priceFt);
    }

    public function id(): ?int
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

    public function withId(int $id): self
    {
        return new self($id, $this->name, $this->priceFt);
    }
}
