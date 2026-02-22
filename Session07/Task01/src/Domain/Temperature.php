<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Value object: Temperature
 * - namespace használat
 * - immutábilis (új példányt ad vissza a factory)
 */
final class Temperature
{
    private float $celsius;

    private function __construct(float $celsius)
    {
        $this->celsius = $celsius;
    }

    public static function fromCelsius(float $c): self
    {
        return new self($c);
    }

    public static function fromFahrenheit(float $f): self
    {
        $c = ($f - 32.0) * 5.0 / 9.0;
        return new self($c);
    }

    public function celsius(): float
    {
        return $this->celsius;
    }
    public function fahrenheit(): float
    {
        return $this->celsius * 9.0 / 5.0 + 32.0;
    }
}
