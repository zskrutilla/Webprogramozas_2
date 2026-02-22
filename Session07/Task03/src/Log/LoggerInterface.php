<?php

declare(strict_types=1);

namespace App\Log;

interface LoggerInterface
{
    public function info(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}
