<?php

declare(strict_types=1);
require_once __DIR__ . '/Notifier.php';

/**
 * NullNotifier – nem csinál semmit (teszteléshez / kikapcsolt értesítéshez).
 */
final class NullNotifier implements Notifier
{
    public function notify(string $to, string $subject, string $body): void
    {
        // szándékosan üres
    }
}
