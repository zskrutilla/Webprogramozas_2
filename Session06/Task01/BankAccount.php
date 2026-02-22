<?php

declare(strict_types=1);

/**
 * BankAccount – egyszerű osztály példa
 * OOP újdonságok:
 * - class
 * - private property (encapsulation)
 * - constructor (__construct)
 * - public metódusok (deposit/withdraw/getBalance)
 */
final class BankAccount
{
    private string $owner;
    private float $balance;

    public function __construct(string $owner, float $initialBalance = 0.0)
    {
        $this->owner = trim($owner);
        $this->balance = max(0.0, $initialBalance);
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('A befizetés összege legyen pozitív.');
        }
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('A kifizetés összege legyen pozitív.');
        }
        if ($amount > $this->balance) {
            throw new RuntimeException('Nincs elegendő fedezet.');
        }
        $this->balance -= $amount;
    }
}
