<?php

declare(strict_types=1);

// htmlspecialchars(): HTML-escape (XSS megelőzés)
function h(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// __DIR__: az aktuális fájl könyvtára (stabil relatív útvonalakhoz)
// mkdir(): könyvtár létrehozása, ha még nem létezik
function ensure_dir(string $name): string
{
  $dir = __DIR__ . DIRECTORY_SEPARATOR . $name;
  if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
  }
  return $dir;
}
?>
<?php
$data = ensure_dir('data');
$file = $data . DIRECTORY_SEPARATOR . 'books.json';

function load_books(string $file): array
{
  if (!is_file($file)) return [];
  $arr = json_decode((string)file_get_contents($file), true);
  return is_array($arr) ? $arr : [];
}
function save_books(string $file, array $books): void
{
  file_put_contents($file, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

$books = load_books($file);
if (!$books) {
  $books = [
    ['id' => bin2hex(random_bytes(4)), 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'year' => 2008],
    ['id' => bin2hex(random_bytes(4)), 'title' => 'Design Patterns', 'author' => 'GoF', 'year' => 1994],
    ['id' => bin2hex(random_bytes(4)), 'title' => 'The Pragmatic Programmer', 'author' => 'Hunt & Thomas', 'year' => 1999],
  ];
  save_books($file, $books);
}

$title = (string)($_POST['title'] ?? '');
$author = (string)($_POST['author'] ?? '');
$yearRaw = (string)($_POST['year'] ?? '');
$action = (string)($_POST['action'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
  $title = trim($title);
  $author = trim($author);
  $year = filter_var($yearRaw, FILTER_VALIDATE_INT);

  if ($title === '') $errors['title'] = 'Kérem, adja meg a címet.';
  if ($author === '') $errors['author'] = 'Kérem, adja meg a szerzőt.';
  if ($yearRaw === '' || $year === false || $year < 1500 || $year > (int)date('Y') + 1) $errors['year'] = 'Az évszám nem megfelelő.';

  if (!$errors) {
    $books[] = ['id' => bin2hex(random_bytes(4)), 'title' => $title, 'author' => $author, 'year' => (int)$year];
    save_books($file, $books);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
  $id = (string)($_POST['id'] ?? '');
  $books = array_values(array_filter($books, fn($b) => ($b['id'] ?? '') !== $id));
  save_books($file, $books);
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

if (isset($_GET['clear'])) {
  if (is_file($file)) unlink($file);
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit;
}

$q = trim((string)($_GET['q'] ?? ''));
$sort = (string)($_GET['sort'] ?? 'title_asc');

$view = $books;
if ($q !== '') {
  $ql = mb_strtolower($q, 'UTF-8');
  $view = array_values(array_filter($view, function ($b) use ($ql) {
    $hay = mb_strtolower((string)($b['title'] ?? '') . ' ' . (string)($b['author'] ?? ''), 'UTF-8');
    return mb_strpos($hay, $ql, 0, 'UTF-8') !== false; // mb_strpos(): részsztring keresés UTF-8
  }));
}

usort($view, function ($a, $b) use ($sort) {
  switch ($sort) {
    case 'year_desc':
      return ($b['year'] ?? 0) <=> ($a['year'] ?? 0);
    case 'year_asc':
      return ($a['year'] ?? 0) <=> ($b['year'] ?? 0);
    case 'title_desc':
      return strcmp((string)$b['title'], (string)$a['title']);
    default:
      return strcmp((string)$a['title'], (string)$b['title']);
  }
});
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session04 / Task10 – Books</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task10 – Mini „adatbázis” JSON-ban (keresés + rendezés)</h1>
  </header>

  <div class="card notice">
    <p>Fájl: <code>data/books.json</code> · <a href="?clear=1">Törlés (seed újra)</a></p>
  </div>

  <div class="card">
    <h2>Új könyv</h2>
    <form method="post" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="row">
        <div>
          <label for="title">Cím</label>
          <input id="title" name="title" type="text" value="<?php echo h($title); ?>">
          <?php if (isset($errors['title'])): ?><div class="field-error"><?php echo h($errors['title']); ?></div><?php endif; ?>
        </div>
        <div>
          <label for="author">Szerző</label>
          <input id="author" name="author" type="text" value="<?php echo h($author); ?>">
          <?php if (isset($errors['author'])): ?><div class="field-error"><?php echo h($errors['author']); ?></div><?php endif; ?>
        </div>
      </div>
      <label for="year">Év</label>
      <input id="year" name="year" type="number" min="1500" max="<?php echo (int)date('Y') + 1; ?>" value="<?php echo h($yearRaw); ?>">
      <?php if (isset($errors['year'])): ?><div class="field-error"><?php echo h($errors['year']); ?></div><?php endif; ?>
      <div style="margin-top:12px"><button type="submit">Mentés</button></div>
    </form>
  </div>

  <div class="card">
    <h2>Lista</h2>
    <form method="get" class="row" style="align-items:end">
      <div>
        <label for="q">Keresés</label>
        <input id="q" name="q" type="text" value="<?php echo h($q); ?>" placeholder="cím vagy szerző">
      </div>
      <div>
        <label for="sort">Rendezés</label>
        <select id="sort" name="sort">
          <option value="title_asc" <?php echo $sort === 'title_asc' ? 'selected' : ''; ?>>Cím A→Z</option>
          <option value="title_desc" <?php echo $sort === 'title_desc' ? 'selected' : ''; ?>>Cím Z→A</option>
          <option value="year_asc" <?php echo $sort === 'year_asc' ? 'selected' : ''; ?>>Év növekvő</option>
          <option value="year_desc" <?php echo $sort === 'year_desc' ? 'selected' : ''; ?>>Év csökkenő</option>
        </select>
      </div>
      <div><button type="submit">Szűrés</button></div>
    </form>

    <p class="small">Találatok: <?php echo (int)count($view); ?> / összesen: <?php echo (int)count($books); ?></p>

    <table>
      <thead>
        <tr>
          <th>Cím</th>
          <th>Szerző</th>
          <th>Év</th>
          <th>Művelet</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($view as $b): ?>
          <tr>
            <td><?php echo h((string)$b['title']); ?></td>
            <td><?php echo h((string)$b['author']); ?></td>
            <td><?php echo (int)$b['year']; ?></td>
            <td>
              <form method="post" style="margin:0">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo h((string)$b['id']); ?>">
                <button type="submit">Törlés</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <footer class="small">Generated by PHP</footer>
</body>

</html>