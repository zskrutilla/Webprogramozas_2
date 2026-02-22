<?php

declare(strict_types=1);

namespace App\Domain;

final class Todo
{
    public function __construct(
        public int $id,
        public string $title,
        public bool $done,
        public string $createdAt
    ) {}
}
