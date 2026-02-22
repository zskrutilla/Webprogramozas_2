<?php

declare(strict_types=1);

namespace App\Log;

final class FileLogger
{
    public function __construct(private string $file) {}

    public function log(string $level, string $message, array $ctx = []): void
    {
        $extra = $ctx ? ' ' . json_encode($ctx, JSON_UNESCAPED_UNICODE) : '';
        $line = date('Y-m-d H:i:s') . " [$level] " . $message . $extra . PHP_EOL;
        file_put_contents($this->file, $line, FILE_APPEND | LOCK_EX);
    }
}
