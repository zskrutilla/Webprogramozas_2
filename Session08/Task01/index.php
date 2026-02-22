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
use App\Domain\Order;
use App\Repo\JsonOrderRepository;

Autoloader::register('App', __DIR__ . '/src');

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) mkdir($dataDir, 0777, true);
$file = $dataDir . '/orders.json';

$repo = new JsonOrderRepository($file);

$customer = (string)($_POST['customer'] ?? '');
$totalRaw = (string)($_POST['total'] ?? '');
$action = (string)($_POST['action'] ?? '');
$id = (string)($_POST['id'] ?? '');

$errors = [];
$flash = null;

$orders = $repo->all();

function findIndexById(array $orders, string $id): int
{
  foreach ($orders as $i => $o) if ($o->id() === $id) return $i;
  return -1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if ($action === 'create') {
      $t = filter_var($totalRaw, FILTER_VALIDATE_INT);
      if (trim($customer) === '') $errors['customer'] = 'Kérem, adja meg a vevő nevét.';
      if ($totalRaw === '' || $t === false || $t < 0) $errors['total'] = 'Az összeg legyen 0 vagy pozitív egész.';
      if (!$errors) {
        $orders[] = Order::create($customer, (int)$t);
        $repo->saveAll($orders);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?ok=1');
        exit; // PRG
      }
    } elseif ($action === 'advance') {
      $idx = findIndexById($orders, $id);
      if ($idx < 0) throw new RuntimeException('Ismeretlen rendelés.');
      $orders[$idx]->advance();
      $repo->saveAll($orders);
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    } elseif ($action === 'cancel') {
      $idx = findIndexById($orders, $id);
      if ($idx < 0) throw new RuntimeException('Ismeretlen rendelés.');
      $orders[$idx]->cancel();
      $repo->saveAll($orders);
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    }
  } catch (Throwable $e) {
    $errors['form'] = $e->getMessage();
  }
}

if (isset($_GET['clear'])) {
  if (is_file($file)) unlink($file);
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit;
}

$flash = isset($_GET['ok']) ? 'Rendelés létrehozva.' : null;
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session08 / Task01 – Enum + State machine</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task01 – Enum + state machine (Order)</h1>
  </header>

  <div class="card notice">
    <p>PHP 8.1 <code>enum</code> + szabályozott állapotátmenetek (<code>advance()</code>, <code>cancel()</code>).</p>
    <p class="small">Fájl: <code>data/orders.json</code> · <a href="?clear=1">Összes törlése</a></p>
  </div>

  <?php if ($flash): ?><div class="card ok"><strong><?php echo h($flash); ?></strong></div><?php endif; ?>
  <?php if (isset($errors['form'])): ?><div class="card err"><strong>Hiba:</strong> <?php echo h($errors['form']); ?></div><?php endif; ?>

  <div class="card">
    <h2>Új rendelés</h2>
    <form method="post" class="row" style="align-items:end" novalidate>
      <input type="hidden" name="action" value="create">
      <div>
        <label for="customer">Vevő</label>
        <input id="customer" name="customer" type="text" value="<?php echo h($customer); ?>">
        <?php if (isset($errors['customer'])): ?><div class="field-error"><?php echo h($errors['customer']); ?></div><?php endif; ?>
      </div>
      <div>
        <label for="total">Végösszeg (Ft)</label>
        <input id="total" name="total" type="number" min="0" value="<?php echo h($totalRaw); ?>">
        <?php if (isset($errors['total'])): ?><div class="field-error"><?php echo h($errors['total']); ?></div><?php endif; ?>
      </div>
      <div><button type="submit">Létrehozás</button></div>
    </form>
  </div>

  <div class="card">
    <h2>Rendelések (<?php echo (int)count($orders); ?>)</h2>
    <?php if (!$orders): ?><p class="small">Nincs rendelés.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Vevő</th>
            <th>Összeg</th>
            <th>Állapot</th>
            <th>Művelet</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td class="small"><code><?php echo h($o->id()); ?></code></td>
              <td><?php echo h($o->customer()); ?></td>
              <td><?php echo h(number_format($o->totalFt(), 0, ',', ' ')); ?> Ft</td>
              <td><span class="badge"><?php echo h($o->status()->label()); ?></span></td>
              <td>
                <form method="post" style="display:inline">
                  <input type="hidden" name="action" value="advance">
                  <input type="hidden" name="id" value="<?php echo h($o->id()); ?>">
                  <button type="submit">Következő állapot</button>
                </form>
                <form method="post" style="display:inline">
                  <input type="hidden" name="action" value="cancel">
                  <input type="hidden" name="id" value="<?php echo h($o->id()); ?>">
                  <button type="submit">Törlés</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <footer class="small">Generated by PHP</footer>
</body>

</html>