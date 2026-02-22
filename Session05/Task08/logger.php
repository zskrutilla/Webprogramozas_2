<?php

declare(strict_types=1);

// logger_write(): egységes napló sor formátum + fájlba írás
function logger_write(string $file, string $level, string $message, array $context = []): void
{
    $time = date('Y-m-d H:i:s');
    $ctx = $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    $line = $time . ' [' . strtoupper($level) . '] ' . $message . ($ctx ? ' ' . $ctx : '') . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}
