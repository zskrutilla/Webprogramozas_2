<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Egyszerű PSR-4 jellegű autoloader (Composer nélkül).
 * - spl_autoload_register(): regisztrál egy függvényt, amit PHP hív, ha ismeretlen osztályt keres.
 */
final class Autoloader
{
    public static function register(string $baseNamespace, string $baseDir): void
    {
        $baseNamespace = trim($baseNamespace, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        spl_autoload_register(function (string $class) use ($baseNamespace, $baseDir): void {
            if (!str_starts_with($class, $baseNamespace)) {
                return;
            }
            $relative = substr($class, strlen($baseNamespace));
            $path = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
            if (is_file($path)) require_once $path;
        });
    }
}
