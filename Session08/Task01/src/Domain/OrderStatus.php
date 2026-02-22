<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * PHP 8.1 enum – típusbiztos állapotok.
 */
enum OrderStatus: string
{
    case NEW = 'new';
    case PAID = 'paid';
    case PACKED = 'packed';
    case SHIPPED = 'shipped';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) { // match: biztonságosabb, mint a switch
            self::NEW => 'Új',
            self::PAID => 'Fizetve',
            self::PACKED => 'Csomagolva',
            self::SHIPPED => 'Kiszállítva',
            self::CANCELLED => 'Törölve',
        };
    }
}
