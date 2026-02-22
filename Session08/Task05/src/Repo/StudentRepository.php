<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Student;

final class StudentRepository
{
    public function __construct(private string $file) {}

    /** @return Student[] */
    public function all(): array
    {
        $raw = is_file($this->file) ? file_get_contents($this->file) : '[]';
        $arr = json_decode((string)$raw, true);
        if (!is_array($arr)) $arr = [];
        $out = [];
        foreach ($arr as $row) {
            try {
                $out[] = Student::fromArray(is_array($row) ? $row : []);
            } catch (\Throwable $e) {
            }
        }
        usort($out, fn($a, $b) => strcmp($a->name(), $b->name()));
        return $out;
    }

    public function add(Student $s): void
    {
        $all = $this->all();
        $all[] = $s;
        $this->save($all);
    }

    /** @param Student[] $students */
    private function save(array $students): void
    {
        $arr = array_map(fn($s) => $s->toArray(), $students);
        file_put_contents($this->file, (string)json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}
