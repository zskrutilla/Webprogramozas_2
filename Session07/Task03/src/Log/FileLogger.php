<?php

declare(strict_types=1);

namespace App\Log;

final class FileLogger implements LoggerInterface
{
    public function __construct(private string $file) {}

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }
    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $ctx = $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line = date('Y-m-d H:i:s') . " [$level] " . $message . ($ctx ? " $ctx" : '') . PHP_EOL;
        file_put_contents($this->file, $line, FILE_APPEND | LOCK_EX);
    }
}
