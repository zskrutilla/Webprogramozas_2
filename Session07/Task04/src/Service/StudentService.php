<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Student;
use App\Repo\StudentRepository;

final class StudentService
{
    public function __construct(private StudentRepository $repo) {}

    /** @return Student[] */
    public function list(string $q = '', string $sort = 'name_asc'): array
    {
        $items = $this->repo->all();

        if (trim($q) !== '') {
            $ql = mb_strtolower($q, 'UTF-8');
            $items = array_values(array_filter($items, function (Student $s) use ($ql) {
                $hay = mb_strtolower($s->name() . ' ' . $s->neptun(), 'UTF-8');
                return mb_strpos($hay, $ql, 0, 'UTF-8') !== false;
            }));
        }

        usort($items, fn(Student $a, Student $b) => match ($sort) {
            'neptun_asc' => strcmp($a->neptun(), $b->neptun()),
            'neptun_desc' => strcmp($b->neptun(), $a->neptun()),
            'name_desc' => strcmp($b->name(), $a->name()),
            default => strcmp($a->name(), $b->name()),
        });

        return $items;
    }

    public function add(string $name, string $neptun): void
    {
        $this->repo->add(Student::create($name, $neptun));
    }

    public function delete(string $id): void
    {
        $this->repo->deleteById($id);
    }
}
