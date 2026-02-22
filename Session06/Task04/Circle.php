<?php

declare(strict_types=1);
require_once __DIR__ . '/Shape.php';

final class Circle extends Shape
{
    public function __construct(private float $r)
    {
        parent::__construct('Kör'); // parent::__construct(): szülő konstruktor hívása
        $this->r = max(0.0, $this->r);
    }

    public function area(): float
    {
        return M_PI * $this->r * $this->r;
    }
    public function perimeter(): float
    {
        return 2 * M_PI * $this->r;
    }
    public function r(): float
    {
        return $this->r;
    }
}
