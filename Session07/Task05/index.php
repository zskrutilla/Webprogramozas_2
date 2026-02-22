<?php

declare(strict_types=1);

// htmlspecialchars(): HTML-escape (XSS megelőzés)
function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Utils/Autoloader.php';

use App\Utils\Autoloader;
use App\Core\Container;
use App\Core\Router;
use App\Http\Request;
use App\Repo\JsonPostRepository;
use App\Service\BlogService;
use App\Controller\BlogController;

Autoloader::register('App', __DIR__ . '/src');

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) mkdir($dataDir, 0777, true);
$file = $dataDir . '/posts.json';

$container = new Container();
$container->set('repo.posts', fn() => new JsonPostRepository($file));
$container->set('service.blog', fn(Container $c) => new BlogService($c->get('repo.posts')));
$container->set('controller.blog', fn(Container $c) => new BlogController($c->get('service.blog')));

$router = new Router();
$controller = $container->get('controller.blog');

$router->add('list', fn(Request $r) => $controller->list($r));
$router->add('create', fn(Request $r) => $controller->create($r));
$router->add('delete', fn(Request $r) => $controller->delete($r));
$router->add('clear', function (Request $r) use ($file): string {
    if (is_file($file)) unlink($file);
    header('Location: ?action=list');
    exit;
});

echo $router->dispatch(Request::fromGlobals());
