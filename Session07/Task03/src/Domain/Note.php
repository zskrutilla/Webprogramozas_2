<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Note
{
    use TimestampedTrait;

    private string $id;
    private string $text;

    public function __construct(string $text)
    {
        $text = trim($text);
        if ($text === '') throw new InvalidArgumentException('A jegyzet szövege kötelező.');
        if (mb_strlen($text, 'UTF-8') > 200) throw new InvalidArgumentException('Max 200 karakter.');

        $this->id = bin2hex(random_bytes(4));
        $this->text = $text;
        $this->initTimestamp();
    }

    public function id(): string
    {
        return $this->id;
    }
    public function text(): string
    {
        return $this->text;
    }
}
