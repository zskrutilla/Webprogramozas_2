<?php

declare(strict_types=1);

namespace App\Report;

/**
 * Factory: a konkrét riportgenerátort a formátum alapján választjuk ki.
 */
final class ReportFactory
{
    public static function create(string $format): ReportGenerator
    {
        return match ($format) {
            'csv' => new CsvReport(),
            'html' => new HtmlReport(),
            default => throw new \InvalidArgumentException('Ismeretlen formátum.'),
        };
    }
}
