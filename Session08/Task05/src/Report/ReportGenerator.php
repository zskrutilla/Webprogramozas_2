<?php

declare(strict_types=1);

namespace App\Report;

use App\Domain\Student;

/**
 * Template Method minta:
 * - generate() adja a fix algoritmus vázát
 * - a konkrét lépések (header/row/footer) az alosztályban vannak
 */
abstract class ReportGenerator
{
    /** @param Student[] $students */
    final public function generate(array $students): string
    {
        $out = $this->header();
        foreach ($students as $s) {
            $out .= $this->row($s);
        }
        $out .= $this->footer($students);
        return $out;
    }

    abstract protected function header(): string;
    abstract protected function row(Student $s): string;

    /** @param Student[] $students */
    abstract protected function footer(array $students): string;

    abstract public function contentType(): string;
    abstract public function fileExtension(): string;
}
