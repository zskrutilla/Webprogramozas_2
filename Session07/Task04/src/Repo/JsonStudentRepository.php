<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Student;

final class JsonStudentRepository implements StudentRepository
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
        return $out;
    }

    public function add(Student $s): void
    {
        $all = $this->all();
        foreach ($all as $existing) if ($existing->neptun() === $s->neptun()) throw new \RuntimeException('Ez a Neptun már létezik.');
        $all[] = $s;
        $this->save($all);
    }

    public function deleteById(string $id): void
    {
        $all = array_values(array_filter($this->all(), fn($s) => $s->id() !== $id));
        $this->save($all);
    }

    public function findByNeptun(string $neptun): ?Student
    {
        $n = strtoupper(trim($neptun));
        foreach ($this->all() as $s) if ($s->neptun() === $n) return $s;
        return null;
    }

    /** @param Student[] $students */
    private function save(array $students): void
    {
        $arr = array_map(fn($s) => $s->toArray(), $students);
        $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->file, (string)$json, LOCK_EX);
    }
}
