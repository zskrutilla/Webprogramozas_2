<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Order – egyszerű „state machine”.
 * OOP újdonságok: enum + állapotátmenetek szabályozása + kivétel dobás.
 */
final class Order
{
    public function __construct(
        private string $id,
        private string $customer,
        private int $totalFt,
        private OrderStatus $status
    ) {
        $this->id = trim($this->id) ?: bin2hex(random_bytes(4));
        $this->customer = trim($this->customer);
        $this->totalFt = max(0, $this->totalFt);

        if ($this->customer === '') throw new InvalidArgumentException('A vevő neve kötelező.');
    }

    public static function create(string $customer, int $totalFt): self
    {
        return new self(bin2hex(random_bytes(4)), $customer, $totalFt, OrderStatus::NEW);
    }

    public function id(): string
    {
        return $this->id;
    }
    public function customer(): string
    {
        return $this->customer;
    }
    public function totalFt(): int
    {
        return $this->totalFt;
    }
    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function advance(): void
    {
        // Egyszerű workflow: NEW → PAID → PACKED → SHIPPED
        $this->status = match ($this->status) {
            OrderStatus::NEW => OrderStatus::PAID,
            OrderStatus::PAID => OrderStatus::PACKED,
            OrderStatus::PACKED => OrderStatus::SHIPPED,
            OrderStatus::SHIPPED => throw new InvalidArgumentException('A rendelés már kiszállítva van.'),
            OrderStatus::CANCELLED => throw new InvalidArgumentException('Törölt rendelés nem léptethető.'),
        };
    }

    public function cancel(): void
    {
        if ($this->status === OrderStatus::SHIPPED) {
            throw new InvalidArgumentException('Kiszállított rendelés nem törölhető.');
        }
        $this->status = OrderStatus::CANCELLED;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'total_ft' => $this->totalFt,
            'status' => $this->status->value,
        ];
    }

    public static function fromArray(array $a): self
    {
        $status = OrderStatus::tryFrom((string)($a['status'] ?? 'new')) ?? OrderStatus::NEW;
        return new self(
            (string)($a['id'] ?? ''),
            (string)($a['customer'] ?? ''),
            (int)($a['total_ft'] ?? 0),
            $status
        );
    }
}
