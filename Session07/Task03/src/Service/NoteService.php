<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Note;
use App\Log\LoggerInterface;

final class NoteService
{
    /** @var Note[] */
    private array $notes = [];

    public function __construct(private LoggerInterface $logger) {}

    public function add(string $text): Note
    {
        $note = new Note($text);
        $this->notes[] = $note;
        $this->logger->info('Jegyzet létrehozva', ['id' => $note->id()]);
        return $note;
    }

    /** @return Note[] */
    public function all(): array
    {
        return $this->notes;
    }
}
