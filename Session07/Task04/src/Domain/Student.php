<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Student
{
    public function __construct(
        private string $id,
        private string $name,
        private string $neptun
    ) {
        $this->id = trim($this->id) ?: bin2hex(random_bytes(4));
        $this->name = trim($this->name);
        $this->neptun = strtoupper(trim($this->neptun));

        if ($this->name === '') throw new InvalidArgumentException('A név kötelező.');
        if (!preg_match('/^[A-Z0-9]{6}$/', $this->neptun)) throw new InvalidArgumentException('A Neptun 6 karakter (A-Z, 0-9).');
    }

    public static function create(string $name, string $neptun): self
    {
        return new self(bin2hex(random_bytes(4)), $name, $neptun);
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

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'neptun' => $this->neptun];
    }
    public static function fromArray(array $a): self
    {
        return new self((string)($a['id'] ?? ''), (string)($a['name'] ?? ''), (string)($a['neptun'] ?? ''));
    }
}
