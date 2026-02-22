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
use App\Repo\StudentRepository;
use App\Report\ReportFactory;

Autoloader::register('App', __DIR__ . '/src');

$dataDir = __DIR__ . '/data';
$file = $dataDir . '/students.json';

$format = (string)($_GET['format'] ?? 'csv');

$repo = new StudentRepository($file);
$students = $repo->all();

$gen = ReportFactory::create($format);
$content = $gen->generate($students);

header('Content-Type: ' . $gen->contentType());
header('Content-Disposition: attachment; filename="report.' . $gen->fileExtension() . '"');
echo $content;
