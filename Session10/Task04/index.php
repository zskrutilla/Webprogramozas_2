<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/src/Utils/Autoloader.php';

use App\Utils\Autoloader;
use App\Core\Container;
use App\Core\Router;
use App\Repo\TodoRepository;
use App\Controller\TodoController;

Autoloader::register('App', __DIR__ . '/src');

$db = Db::conn();

$container = new Container();
$container->set('repo.todo', fn() => new TodoRepository($db));
$container->set('controller.todo', fn(Container $c) => new TodoController($c->get('repo.todo')));

$controller = $container->get('controller.todo');

$router = new Router();
$router->add('list', fn() => $controller->list());
$router->add('create', fn() => $controller->create());
$router->add('toggle', fn() => $controller->toggle());
$router->add('delete', fn() => $controller->delete());

$action = (string)($_GET['action'] ?? 'list');
echo $router->dispatch($action);
