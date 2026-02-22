<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;
use mysqli;

/**
 * "Active Record" stílus (tanítási cél): az entitás tartalmaz DB műveleteket is.
 * Megjegyzés: nagy rendszerekben inkább Repository mintát használunk, de itt jó összehasonlításhoz.
 */
final class Student
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $neptun,
        public int $points,
        public ?string $createdAt = null
    ) {
        $this->name = trim($this->name);
        $this->neptun = strtoupper(trim($this->neptun));
        if ($this->name === '') throw new InvalidArgumentException('A név kötelező.');
        if (!preg_match('/^[A-Z0-9]{6}$/', $this->neptun)) throw new InvalidArgumentException('A Neptun 6 karakter (A-Z/0-9).');
        if ($this->points < 0 || $this->points > 100) throw new InvalidArgumentException('A pont legyen 0–100.');
    }

    public static function create(string $name, string $neptun, int $points): self
    {
        return new self(null, $name, $neptun, $points);
    }

    /** @return self[] */
    public static function all(mysqli $db): array
    {
        $res = $db->query('SELECT id, name, neptun, points, created_at FROM students ORDER BY created_at DESC');
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        return array_map(fn($r) => new self((int)$r['id'], (string)$r['name'], (string)$r['neptun'], (int)$r['points'], (string)$r['created_at']), $rows);
    }

    public function insert(mysqli $db): void
    {
        $stmt = $db->prepare('INSERT INTO students (name, neptun, points) VALUES (?, ?, ?)');
        $stmt->bind_param('ssi', $this->name, $this->neptun, $this->points);
        $stmt->execute();
        $this->id = $stmt->insert_id;
        $stmt->close();
    }

    public static function deleteById(mysqli $db, int $id): void
    {
        $stmt = $db->prepare('DELETE FROM students WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
