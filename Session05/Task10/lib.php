<?php

declare(strict_types=1);
function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money_huf(float $v): string
{
    return number_format($v, 0, ',', ' ') . ' Ft';
}
