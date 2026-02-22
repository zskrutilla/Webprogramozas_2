<?php

declare(strict_types=1);

// Műveletek külön függvényekben (modularizáció)
function add(float $a, float $b): float
{
    return $a + $b;
}
function sub(float $a, float $b): float
{
    return $a - $b;
}
function mul(float $a, float $b): float
{
    return $a * $b;
}
function div(float $a, float $b): ?float
{
    // null: ha nem értelmezett (0-val osztás)
    if ($b == 0.0) return null;
    return $a / $b;
}
