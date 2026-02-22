<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Todo;
use mysqli;

final class TodoRepository
{
    public function __construct(private mysqli $db) {}

    /** @return Todo[] */
    public function all(): array
    {
        $res = $this->db->query('SELECT id, title, done, created_at FROM tasks ORDER BY created_at DESC');
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        return array_map(fn($r) => new Todo((int)$r['id'], (string)$r['title'], (bool)$r['done'], (string)$r['created_at']), $rows);
    }

    public function add(string $title): void
    {
        $stmt = $this->db->prepare('INSERT INTO tasks (title) VALUES (?)');
        $stmt->bind_param('s', $title);
        $stmt->execute();
        $stmt->close();
    }

    public function toggle(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE tasks SET done = 1 - done WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
