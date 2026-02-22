<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Product;
use App\Repo\ProductRepository;

final class ProductService
{
    public function __construct(private ProductRepository $repo) {}

    /** @return Product[] */
    public function list(): array
    {
        return $this->repo->all();
    }

    public function create(string $name, int $priceFt): Product
    {
        $p = Product::createNew($name, $priceFt);
        return $this->repo->add($p);
    }
}
