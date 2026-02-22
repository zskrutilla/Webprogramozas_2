<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\TodoRepository;

final class TodoController
{
    public function __construct(private TodoRepository $repo) {}

    public function list(): string
    {
        $items = $this->repo->all();

        ob_start();
        include __DIR__ . '/../../views/list.php';
        $content = (string)ob_get_clean();

        $title = 'Task04 – Mini MVC + MySQLi';
        ob_start();
        include __DIR__ . '/../../views/layout.php';
        return (string)ob_get_clean();
    }

    public function create(): string
    {
        $titleRaw = (string)($_POST['title'] ?? '');
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $t = trim($titleRaw);
            if ($t === '') $errors['title'] = 'A cím kötelező.';
            if (mb_strlen($t, 'UTF-8') > 160) $errors['title'] = 'Max 160 karakter.';

            if (!$errors) {
                $this->repo->add($t);
                $_SESSION['flash'] = 'Feladat hozzáadva.';
                header('Location: ?action=list');
                exit;
            }
        }

        ob_start();
        include __DIR__ . '/../../views/create.php';
        $content = (string)ob_get_clean();

        $title = 'Task04 – Új feladat';
        ob_start();
        include __DIR__ . '/../../views/layout.php';
        return (string)ob_get_clean();
    }

    public function toggle(): string
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $this->repo->toggle($id);
        header('Location: ?action=list');
        exit;
    }

    public function delete(): string
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $this->repo->delete($id);
        header('Location: ?action=list');
        exit;
    }
}
