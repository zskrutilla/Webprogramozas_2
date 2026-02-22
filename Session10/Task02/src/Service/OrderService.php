<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Order;
use App\Repo\OrderRepo;
use App\Repo\ProductRepo;
use mysqli;

/**
 * Service: tranzakció kezelés (Unit of Work jelleg).
 */
final class OrderService
{
    public function __construct(
        private mysqli $db,
        private OrderRepo $orders,
        private ProductRepo $products
    ) {}

    public function create(Order $order): int
    {
        $this->db->begin_transaction(); // tranzakció kezdete

        try {
            // termék id-k ellenőrzése
            foreach ($order->items() as $it) {
                if (!$this->products->exists($it->productId())) {
                    throw new \RuntimeException('Ismeretlen termék: ' . $it->productId());
                }
            }

            $orderId = $this->orders->insert($order);

            $items = array_map(fn($it) => ['productId' => $it->productId(), 'qty' => $it->qty()], $order->items());
            $this->orders->insertItems($orderId, $items);

            $this->db->commit(); // commit
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollback(); // rollback
            throw $e;
        }
    }
}
