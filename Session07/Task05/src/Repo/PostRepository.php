<?php

declare(strict_types=1);

namespace App\Repo;

use App\Domain\Post;

interface PostRepository
{
    /** @return Post[] */ public function all(): array;
    public function add(Post $p): void;
    public function delete(string $id): void;
}
