<?php

declare(strict_types=1);

namespace App\Report;

use App\Domain\Student;

final class HtmlReport extends ReportGenerator
{
    protected function header(): string
    {
        return "<!doctype html><html lang=\"hu\"><head><meta charset=\"utf-8\"><title>Hallgatók riport</title></head><body>"
            . "<h1>Hallgatók riport</h1><table border=\"1\" cellpadding=\"6\" cellspacing=\"0\">"
            . "<tr><th>Név</th><th>Neptun</th><th>Pont</th></tr>";
    }

    protected function row(Student $s): string
    {
        $h = fn(string $v) => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return "<tr><td>" . $h($s->name()) . "</td><td>" . $h($s->neptun()) . "</td><td>" . $s->points() . "</td></tr>";
    }

    protected function footer(array $students): string
    {
        $sum = 0;
        foreach ($students as $s) $sum += $s->points();
        $avg = $students ? $sum / count($students) : 0;
        return "<tr><th colspan=\"2\">Átlag</th><th>" . number_format($avg, 2, ',', ' ') . "</th></tr></table></body></html>";
    }

    public function contentType(): string
    {
        return 'text/html; charset=UTF-8';
    }
    public function fileExtension(): string
    {
        return 'html';
    }
}
