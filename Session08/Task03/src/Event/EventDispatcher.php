<?php

declare(strict_types=1);

namespace App\Event;

/**
 * Observer minta: listener-ek feliratkoznak esemény típusokra.
 */
final class EventDispatcher
{
    /** @var array<class-string, array<int, callable(Event):void>> */
    private array $listeners = [];

    /** @param class-string $eventClass */
    public function addListener(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(Event $event): void
    {
        $cls = $event::class;
        foreach ($this->listeners[$cls] ?? [] as $listener) {
            $listener($event);
        }
    }
}
