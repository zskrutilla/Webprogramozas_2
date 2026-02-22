<?php

declare(strict_types=1);
require_once __DIR__ . '/Notifier.php';

/**
 * EmailNotifier – "valódi" notifikáció helyett csak logol (demó).
 * OOP: interface implementálása.
 */
final class EmailNotifier implements Notifier
{
    public function __construct(private string $logFile) {}

    public function notify(string $to, string $subject, string $body): void
    {
        $line = date('Y-m-d H:i:s')
            . ' TO=' . $to
            . ' SUBJ=' . $subject
            . ' BODY=' . str_replace(["\r", "\n"], ' ', $body)
            . PHP_EOL;

        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
