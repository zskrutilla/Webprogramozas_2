<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Order;

interface OrderRepository
{
    /** @return Order[] */
    public function all(): array;
    public function saveAll(array $orders): void;
}
