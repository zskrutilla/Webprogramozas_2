<?php

declare(strict_types=1);
function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function ensure_data_dir(): string
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    return $dir;
}
