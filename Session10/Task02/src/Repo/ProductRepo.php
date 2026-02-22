<?php

declare(strict_types=1);

namespace App\Repo;

use mysqli;

final class ProductRepo
{
    public function __construct(private mysqli $db) {}

    /** @return array<int, array{id:int,name:string,price_ft:int}> */
    public function all(): array
    {
        $res = $this->db->query('SELECT id, name, price_ft FROM products ORDER BY id');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM products WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = (bool)$stmt->get_result()->fetch_row();
        $stmt->close();
        return $ok;
    }
}
