<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Student;

interface StudentRepository
{
    /** @return Student[] */ public function all(): array;
    public function add(Student $s): void;
    public function deleteById(string $id): void;
    public function findByNeptun(string $neptun): ?Student;
}
