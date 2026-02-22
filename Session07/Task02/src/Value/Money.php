<?php

declare(strict_types=1);

namespace App\Value;

use InvalidArgumentException;

final class Money
{
    private int $ft;

    public function __construct(int $ft)
    {
        if ($ft < 0) throw new InvalidArgumentException('Az összeg nem lehet negatív.');
        $this->ft = $ft;
    }

    public function ft(): int
    {
        return $this->ft;
    }

    public function add(self $other): self
    {
        return new self($this->ft + $other->ft);
    }

    public function mul(int $qty): self
    {
        if ($qty < 0) throw new InvalidArgumentException('A mennyiség nem lehet negatív.');
        return new self($this->ft * $qty);
    }

    public function format(): string
    {
        return number_format($this->ft, 0, ',', ' ') . ' Ft';
    }
}
