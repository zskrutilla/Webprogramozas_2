<?php

declare(strict_types=1);
require_once __DIR__ . '/Shape.php';

final class Rectangle extends Shape
{
    public function __construct(private float $a, private float $b)
    {
        parent::__construct('Téglalap');
        $this->a = max(0.0, $this->a);
        $this->b = max(0.0, $this->b);
    }

    public function area(): float
    {
        return $this->a * $this->b;
    }
    public function perimeter(): float
    {
        return 2 * ($this->a + $this->b);
    }
    public function a(): float
    {
        return $this->a;
    }
    public function b(): float
    {
        return $this->b;
    }
}
