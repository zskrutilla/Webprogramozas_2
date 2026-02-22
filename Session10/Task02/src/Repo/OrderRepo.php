<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Order;
use mysqli;

final class OrderRepo
{
    public function __construct(private mysqli $db) {}

    public function insert(Order $o): int
    {
        $stmt = $this->db->prepare('INSERT INTO orders (customer) VALUES (?)');
        $c = $o->customer();
        $stmt->bind_param('s', $c);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /** @param array<int, array{productId:int, qty:int}> $items */
    public function insertItems(int $orderId, array $items): void
    {
        $stmt = $this->db->prepare('INSERT INTO order_items (order_id, product_id, qty) VALUES (?, ?, ?)');
        foreach ($items as $it) {
            $pid = $it['productId'];
            $qty = $it['qty'];
            $stmt->bind_param('iii', $orderId, $pid, $qty);
            $stmt->execute();
        }
        $stmt->close();
    }

    /** @return array<int, array{id:int,customer:string,created_at:string,total_ft:int,total_qty:int}> */
    public function summaryList(int $limit = 20): array
    {
        $sql = <<<SQL
                    SELECT
                    o.id, o.customer, o.created_at,
                    SUM(oi.qty * p.price_ft) AS total_ft,
                    SUM(oi.qty) AS total_qty
                    FROM orders o
                    JOIN order_items oi ON oi.order_id = o.id
                    JOIN products p ON p.id = oi.product_id
                    GROUP BY o.id, o.customer, o.created_at
                    ORDER BY o.created_at DESC
                    LIMIT ?
                    SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
}
