<?php

declare(strict_types=1);

/**
 * Interface – szerződés: milyen metódusoknak kell létezniük.
 * OOP újdonság: interface.
 */
interface Notifier
{
    public function notify(string $to, string $subject, string $body): void;
}
