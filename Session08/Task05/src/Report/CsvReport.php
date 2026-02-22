<?php

declare(strict_types=1);

namespace App\Report;

use App\Domain\Student;

final class CsvReport extends ReportGenerator
{
    protected function header(): string
    {
        return "name;neptun;points\n";
    }

    protected function row(Student $s): string
    {
        // CSV-escape egyszerűsítve: pontosvesszőt cserélünk
        $name = str_replace(';', ',', $s->name());
        return $name . ';' . $s->neptun() . ';' . $s->points() . "\n";
    }

    protected function footer(array $students): string
    {
        $sum = 0;
        foreach ($students as $s) $sum += $s->points();
        $avg = $students ? $sum / count($students) : 0;
        return "AVG;;" . number_format($avg, 2, '.', '') . "\n";
    }

    public function contentType(): string
    {
        return 'text/csv; charset=UTF-8';
    }
    public function fileExtension(): string
    {
        return 'csv';
    }
}
