<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function __construct(private array $get, private array $post, private string $method) {}

    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }
    public function get(string $key, string $default = ''): string
    {
        return (string)($this->get[$key] ?? $default);
    }
    public function post(string $key, string $default = ''): string
    {
        return (string)($this->post[$key] ?? $default);
    }
}
