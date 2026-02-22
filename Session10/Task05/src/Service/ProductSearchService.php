<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\ProductQuery;
use App\Repo\ProductSearchRepository;

final class ProductSearchService
{
    public function __construct(private ProductSearchRepository $repo) {}

    /** @return array{total:int,pages:int,rows:array} */
    public function search(ProductQuery $q): array
    {
        $total = $this->repo->count($q);
        $pages = max(1, (int)ceil($total / $q->perPage));
        $q->page = max(1, min($q->page, $pages));

        $rows = $this->repo->page($q);
        return ['total' => $total, 'pages' => $pages, 'rows' => $rows];
    }
}
