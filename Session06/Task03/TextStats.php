<?php

declare(strict_types=1);

/**
 * TextStats – statikus és példánymetódusok bemutatása.
 * - static metódusok: példányosítás nélkül hívhatók (TextStats::wordCountOf(...))
 * - példánymetódus: objektum állapotot tarthat (itt: a szöveget)
 */
final class TextStats
{
    public function __construct(private string $text)
    {
        $this->text = (string)$text;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function charCount(): int
    {
        return mb_strlen($this->text, 'UTF-8');
    }

    public function wordCount(): int
    {
        return self::wordCountOf($this->text);
    }

    public static function wordCountOf(string $text): int
    {
        $t = trim($text);
        if ($t === '') return 0;
        $parts = preg_split('/\s+/u', $t);
        return $parts ? count($parts) : 0;
    }

    public static function fromPost(array $post, string $key = 'text'): self
    {
        // „factory” jellegű metódus: a példány létrehozását segíti
        return new self((string)($post[$key] ?? ''));
    }
}
