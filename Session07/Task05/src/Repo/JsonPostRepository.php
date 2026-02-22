<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Post;

final class JsonPostRepository implements PostRepository
{
    public function __construct(private string $file) {}

    /** @return Post[] */
    public function all(): array
    {
        $raw = is_file($this->file) ? file_get_contents($this->file) : '[]';
        $arr = json_decode((string)$raw, true);
        if (!is_array($arr)) $arr = [];

        $out = [];
        foreach ($arr as $row) {
            try {
                $out[] = Post::fromArray(is_array($row) ? $row : []);
            } catch (\Throwable $e) {
            }
        }
        usort($out, fn($a, $b) => strcmp($b->createdAt(), $a->createdAt()));
        return $out;
    }

    public function add(Post $p): void
    {
        $all = $this->all();
        $all[] = $p;
        $this->save($all);
    }

    public function delete(string $id): void
    {
        $all = array_values(array_filter($this->all(), fn($p) => $p->id() !== $id));
        $this->save($all);
    }

    /** @param Post[] $posts */
    private function save(array $posts): void
    {
        $arr = array_map(fn($p) => $p->toArray(), $posts);
        $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->file, (string)$json, LOCK_EX);
    }
}
