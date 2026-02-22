<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Product;

interface ProductRepository
{
    /** @return Product[] */
    public function all(): array;

    public function add(Product $p): Product;
}
