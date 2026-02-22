<?php

declare(strict_types=1);

// htmlspecialchars(): HTML-escape (XSS megelőzés)
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// number_format(): szám formázása (ezres tagolás, tizedesek)
// money_huf(): egységes pénzformátum megjelenítéshez
function money_huf(float $value): string
{
    return number_format($value, 0, ',', ' ') . ' Ft';
}

// clamp_int(): egész szám korlátozása egy tartományba (hasznos validációhoz)
function clamp_int(int $v, int $min, int $max): int
{
    return max($min, min($max, $v));
}
