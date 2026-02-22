<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Note;
use App\Event\EventDispatcher;
use App\Event\NoteCreated;
use App\Repo\JsonNoteRepository;

/**
 * Service: repository + event dispatcher.
 * OOP: eseményekkel lazán csatolt mellékhatások (log, statisztika, stb.).
 */
final class NoteService
{
    public function __construct(
        private JsonNoteRepository $repo,
        private EventDispatcher $events
    ) {}

    public function add(string $text): Note
    {
        $note = Note::create($text);
        $this->repo->add($note);

        // Esemény kibocsátás: bárki "feliratkozhat" rá.
        $this->events->dispatch(new NoteCreated($note->id(), $note->text(), $note->createdAt()));
        return $note;
    }

    /** @return Note[] */
    public function all(): array
    {
        return $this->repo->all();
    }
}
