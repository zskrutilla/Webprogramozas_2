<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Order;

final class JsonOrderRepository implements OrderRepository
{
    public function __construct(private string $file) {}

    /** @return Order[] */
    public function all(): array
    {
        $raw = is_file($this->file) ? file_get_contents($this->file) : '[]';
        $arr = json_decode((string)$raw, true);
        if (!is_array($arr)) $arr = [];
        $out = [];
        foreach ($arr as $row) {
            try {
                $out[] = Order::fromArray(is_array($row) ? $row : []);
            } catch (\Throwable $e) {
            }
        }
        return $out;
    }

    /** @param Order[] $orders */
    public function saveAll(array $orders): void
    {
        $arr = array_map(fn(Order $o) => $o->toArray(), $orders);
        $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->file, (string)$json, LOCK_EX);
    }
}
