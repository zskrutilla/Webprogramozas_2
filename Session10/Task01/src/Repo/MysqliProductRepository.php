<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Product;
use mysqli;

final class MysqliProductRepository implements ProductRepository
{
    public function __construct(private mysqli $db) {}

    /** @return Product[] */
    public function all(): array
    {
        $res = $this->db->query('SELECT id, name, price_ft FROM products ORDER BY id DESC');
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[] = new Product((int)$r['id'], (string)$r['name'], (int)$r['price_ft']);
        }
        return $out;
    }

    public function add(Product $p): Product
    {
        $stmt = $this->db->prepare('INSERT INTO products (name, price_ft) VALUES (?, ?)');
        $name = $p->name();
        $price = $p->priceFt();
        $stmt->bind_param('si', $name, $price);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $p->withId($id);
    }
}
