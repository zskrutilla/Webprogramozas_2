<?php

declare(strict_types=1);

/**
 * Abstract osztály – közös felület és megvalósítás.
 * OOP újdonság: abstract class + abstract metódusok.
 */
abstract class Shape
{
    public function __construct(private string $name) {}

    public function name(): string
    {
        return $this->name;
    }

    abstract public function area(): float;
    abstract public function perimeter(): float;
}
