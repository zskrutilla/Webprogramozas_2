<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Service\BlogService;

final class BlogController
{
    public function __construct(private BlogService $service) {}

    public function list(Request $req): string
    {
        $q = $req->get('q', '');
        $items = $this->service->list($q);
        return $this->render('list.php', ['q' => $q, 'items' => $items]);
    }

    public function create(Request $req): string
    {
        $errors = [];
        $title = $req->post('title', '');
        $body = $req->post('body', '');

        if ($req->method() === 'POST') {
            try {
                $this->service->add($title, $body);
                header('Location: ?action=list&ok=1');
                exit;
            } catch (\Throwable $e) {
                $errors['form'] = $e->getMessage();
            }
        }
        return $this->render('create.php', ['title' => $title, 'body' => $body, 'errors' => $errors]);
    }

    public function delete(Request $req): string
    {
        $id = $req->get('id', '');
        if ($id !== '') $this->service->delete($id);
        header('Location: ?action=list');
        exit;
    }

    private function render(string $view, array $params): string
    {
        extract($params);
        ob_start();
        include __DIR__ . '/../../views/' . $view;
        $content = (string)ob_get_clean();

        $title = 'Task05 – Front controller + router + DI';
        ob_start();
        include __DIR__ . '/../../views/layout.php';
        return (string)ob_get_clean();
    }
}
