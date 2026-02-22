<?php

declare(strict_types=1);

namespace App\Domain;

final class Student
{
    public function __construct(
        private string $id,
        private string $name,
        private string $neptun,
        private int $points
    ) {
        $this->id = trim($this->id) ?: bin2hex(random_bytes(4));
        $this->name = trim($this->name);
        $this->neptun = strtoupper(trim($this->neptun));
        $this->points = max(0, $this->points);
    }

    public static function create(string $name, string $neptun, int $points): self
    {
        return new self(bin2hex(random_bytes(4)), $name, $neptun, $points);
    }

    public function id(): string
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function neptun(): string
    {
        return $this->neptun;
    }
    public function points(): int
    {
        return $this->points;
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'neptun' => $this->neptun, 'points' => $this->points];
    }
    public static function fromArray(array $a): self
    {
        return new self((string)($a['id'] ?? ''), (string)($a['name'] ?? ''), (string)($a['neptun'] ?? ''), (int)($a['points'] ?? 0));
    }
}
