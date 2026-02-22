<?php

declare(strict_types=1);

namespace App\Core;

use App\Http\Request;

final class Router
{
    /** @var array<string, callable(Request):string> */
    private array $routes = [];

    public function add(string $action, callable $handler): void
    {
        $this->routes[$action] = $handler;
    }

    public function dispatch(Request $req): string
    {
        $action = $req->get('action', 'list');
        if (!isset($this->routes[$action])) $action = 'list';
        return (string)($this->routes[$action])($req);
    }
}
