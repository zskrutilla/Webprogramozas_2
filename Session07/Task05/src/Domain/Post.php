<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Post
{
    public function __construct(private string $id, private string $title, private string $body, private string $createdAt)
    {
        $this->id = trim($this->id) ?: bin2hex(random_bytes(4));
        $this->title = trim($this->title);
        $this->body = trim($this->body);
        $this->createdAt = $this->createdAt ?: date('Y-m-d H:i:s');

        if ($this->title === '') throw new InvalidArgumentException('A cím kötelező.');
        if ($this->body === '') throw new InvalidArgumentException('A tartalom kötelező.');
        if (mb_strlen($this->title, 'UTF-8') > 80) throw new InvalidArgumentException('A cím max 80 karakter.');
        if (mb_strlen($this->body, 'UTF-8') > 800) throw new InvalidArgumentException('A tartalom max 800 karakter.');
    }

    public static function create(string $title, string $body): self
    {
        return new self(bin2hex(random_bytes(4)), $title, $body, date('Y-m-d H:i:s'));
    }

    public function id(): string
    {
        return $this->id;
    }
    public function title(): string
    {
        return $this->title;
    }
    public function body(): string
    {
        return $this->body;
    }
    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'body' => $this->body, 'created_at' => $this->createdAt];
    }
    public static function fromArray(array $a): self
    {
        return new self((string)($a['id'] ?? ''), (string)($a['title'] ?? ''), (string)($a['body'] ?? ''), (string)($a['created_at'] ?? ''));
    }
}
