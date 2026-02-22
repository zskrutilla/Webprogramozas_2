<?php

declare(strict_types=1);

// get_products(): modell réteg – adatforrás (később adatbázis lehet)
function get_products(): array
{
    return [
        ['name' => 'Kenyér', 'price' => 899],
        ['name' => 'Tej', 'price' => 499],
        ['name' => 'Sajt', 'price' => 1299],
        ['name' => 'Alma', 'price' => 799],
    ];
}

// compute_total(): üzleti logika külön függvényben
function compute_total(array $items): int
{
    $sum = 0;
    foreach ($items as $it) $sum += (int)($it['price'] ?? 0);
    return $sum;
}
