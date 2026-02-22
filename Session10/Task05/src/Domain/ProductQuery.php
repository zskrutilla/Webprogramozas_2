<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Query object: egy lekérdezés paraméterei egy objektumban.
 */
final class ProductQuery
{
    public function __construct(
        public string $q,
        public int $page,
        public int $perPage
    ) {}

    public function like(): string
    {
        return '%' . $this->q . '%';
    }
    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
