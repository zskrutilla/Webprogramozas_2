<?php

declare(strict_types=1);

namespace App\Domain;

trait TimestampedTrait
{
    private string $createdAt;

    protected function initTimestamp(): void
    {
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }
}
