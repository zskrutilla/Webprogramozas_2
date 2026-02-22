<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, callable():string> */
    private array $routes = [];

    public function add(string $action, callable $handler): void
    {
        $this->routes[$action] = $handler;
    }

    public function dispatch(string $action): string
    {
        if (!isset($this->routes[$action])) $action = 'list';
        return (string)($this->routes[$action])();
    }
}
