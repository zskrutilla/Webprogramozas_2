<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Note;

final class JsonNoteRepository
{
    public function __construct(private string $file) {}

    /** @return Note[] */
    public function all(): array
    {
        $raw = is_file($this->file) ? file_get_contents($this->file) : '[]';
        $arr = json_decode((string)$raw, true);
        if (!is_array($arr)) $arr = [];
        $out = [];
        foreach ($arr as $row) {
            try {
                $out[] = Note::fromArray(is_array($row) ? $row : []);
            } catch (\Throwable $e) {
            }
        }
        return $out;
    }

    public function add(Note $n): void
    {
        $all = $this->all();
        $all[] = $n;
        $arr = array_map(fn($x) => $x->toArray(), $all);
        file_put_contents($this->file, (string)json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}
