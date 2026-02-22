<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Note
{
    public function __construct(
        private string $id,
        private string $text,
        private string $createdAt
    ) {
        $this->id = trim($this->id) ?: bin2hex(random_bytes(4));
        $this->text = trim($this->text);
        $this->createdAt = $this->createdAt ?: date('Y-m-d H:i:s');

        if ($this->text === '') throw new InvalidArgumentException('A jegyzet szövege kötelező.');
        if (mb_strlen($this->text, 'UTF-8') > 200) throw new InvalidArgumentException('Max 200 karakter.');
    }

    public static function create(string $text): self
    {
        return new self(bin2hex(random_bytes(4)), $text, date('Y-m-d H:i:s'));
    }

    public function id(): string
    {
        return $this->id;
    }
    public function text(): string
    {
        return $this->text;
    }
    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'text' => $this->text, 'created_at' => $this->createdAt];
    }
    public static function fromArray(array $a): self
    {
        return new self((string)($a['id'] ?? ''), (string)($a['text'] ?? ''), (string)($a['created_at'] ?? ''));
    }
}
