<?php

declare(strict_types=1);

namespace App\Event;

final class NoteCreated implements Event
{
    public function __construct(
        public readonly string $id,
        public readonly string $text,
        public readonly string $createdAt
    ) {}
}
