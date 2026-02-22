<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Post;
use App\Repo\PostRepository;

final class BlogService
{
    public function __construct(private PostRepository $repo) {}

    /** @return Post[] */
    public function list(string $q = ''): array
    {
        $items = $this->repo->all();
        $q = trim($q);
        if ($q === '') return $items;

        $ql = mb_strtolower($q, 'UTF-8');
        return array_values(array_filter($items, function (Post $p) use ($ql) {
            $hay = mb_strtolower($p->title() . ' ' . $p->body(), 'UTF-8');
            return mb_strpos($hay, $ql, 0, 'UTF-8') !== false;
        }));
    }

    public function add(string $title, string $body): void
    {
        $this->repo->add(Post::create($title, $body));
    }
    public function delete(string $id): void
    {
        $this->repo->delete($id);
    }
}
