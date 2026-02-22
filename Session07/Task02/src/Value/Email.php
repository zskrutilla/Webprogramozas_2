<?php

declare(strict_types=1);

namespace App\Value;

use InvalidArgumentException;

final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $v = trim($value);
        if ($v === '' || !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Az e-mail formátuma nem megfelelő.');
        }
        $this->value = $v;
    }

    public function value(): string
    {
        return $this->value;
    }
    public function __toString(): string
    {
        return $this->value;
    }
}
