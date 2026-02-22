<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\ProductQuery;
use mysqli;

final class ProductSearchRepository
{
    public function __construct(private mysqli $db) {}

    public function count(ProductQuery $q): int
    {
        $like = $q->like();
        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM products WHERE name LIKE ?');
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $c = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $stmt->close();
        return $c;
    }

    /** @return array<int, array{id:int,name:string,price_ft:int}> */
    public function page(ProductQuery $q): array
    {
        $like = $q->like();
        $limit = $q->perPage;
        $offset = $q->offset();

        $stmt = $this->db->prepare('SELECT id, name, price_ft FROM products WHERE name LIKE ? ORDER BY name LIMIT ? OFFSET ?');
        $stmt->bind_param('sii', $like, $limit, $offset);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
}
