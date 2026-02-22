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

require_once __DIR__ . '/db.php';

$customer = (string)($_POST['customer'] ?? '');
$p1qtyRaw = (string)($_POST['p1qty'] ?? '1');
$p2qtyRaw = (string)($_POST['p2qty'] ?? '0');

$errors = [];
$flash = null;
$error = null;

$products = [];
$orders = [];

try {
  $mysqli = db();

  // Termékek listája a formhoz
  $res = $mysqli->query('SELECT id, name, price_ft FROM products ORDER BY id');
  $products = $res->fetch_all(MYSQLI_ASSOC);

  // CREATE ORDER (transaction)
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c = trim($customer);
    $q1 = filter_var($p1qtyRaw, FILTER_VALIDATE_INT);
    $q2 = filter_var($p2qtyRaw, FILTER_VALIDATE_INT);

    if ($c === '') $errors['customer'] = 'A vevő neve kötelező.';
    if ($p1qtyRaw === '' || $q1 === false || $q1 < 0 || $q1 > 99) $errors['p1qty'] = 'Mennyiség 0–99.';
    if ($p2qtyRaw === '' || $q2 === false || $q2 < 0 || $q2 > 99) $errors['p2qty'] = 'Mennyiség 0–99.';
    if (!$errors && ((int)$q1 + (int)$q2) === 0) $errors['form'] = 'Legalább egy tétel mennyisége legyen > 0.';

    if (!$errors) {
      $mysqli->begin_transaction(); // TRANZAKCIÓ kezdete

      try {
        // 1) orders insert
        $stmt = $mysqli->prepare('INSERT INTO orders (customer) VALUES (?)');
        $stmt->bind_param('s', $c);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        // 2) order_items insert (csak ha qty>0)
        $stmt = $mysqli->prepare('INSERT INTO order_items (order_id, product_id, qty) VALUES (?, ?, ?)');
        foreach ([['pid' => (int)$products[0]['id'], 'qty' => (int)$q1], ['pid' => (int)$products[1]['id'], 'qty' => (int)$q2]] as $it) {
          if ($it['qty'] <= 0) continue;
          $stmt->bind_param('iii', $orderId, $it['pid'], $it['qty']);
          $stmt->execute();
        }
        $stmt->close();

        $mysqli->commit(); // TRANZAKCIÓ commit
        header('Location: ' . $_SERVER['PHP_SELF'] . '?ok=1');
        exit;
      } catch (Throwable $e) {
        $mysqli->rollback(); // TRANZAKCIÓ rollback hiba esetén
        throw $e;
      }
    }
  }

  $flash = isset($_GET['ok']) ? 'Rendelés létrehozva (tranzakcióval).' : null;

  // JOIN + aggregáció: rendelés összesítő (sum)
  $sql = <<<SQL
SELECT
  o.id,
  o.customer,
  o.created_at,
  SUM(oi.qty * p.price_ft) AS total_ft,
  SUM(oi.qty) AS total_qty
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN products p ON p.id = oi.product_id
GROUP BY o.id, o.customer, o.created_at
ORDER BY o.created_at DESC
LIMIT 20
SQL;

  $res = $mysqli->query($sql);
  $orders = $res->fetch_all(MYSQLI_ASSOC);

  $mysqli->close();
} catch (Throwable $e) {
  $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session09 / Task05 – JOIN + tranzakció</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task05 – JOIN + aggregáció + tranzakció (intro)</h1>
  </header>

  <div class="card notice">
    <p>Feladat: rendelés létrehozása <strong>tranzakcióban</strong> (orders + order_items), majd összesítő lista <strong>JOIN</strong> + <strong>SUM</strong> alapján.</p>
    <p class="small">Megjegyzés: a demó a formban az első 2 termékre ad mennyiséget (a könnyebb fókusz miatt).</p>
  </div>

  <?php if ($error): ?><div class="card err"><strong>Hiba:</strong> <?php echo h($error); ?></div><?php endif; ?>
  <?php if ($flash): ?><div class="card ok"><strong><?php echo h($flash); ?></strong></div><?php endif; ?>
  <?php if (isset($errors['form'])): ?><div class="card err"><strong>Hiba:</strong> <?php echo h($errors['form']); ?></div><?php endif; ?>

  <div class="card">
    <h2>Új rendelés</h2>
    <form method="post" novalidate class="row" style="align-items:end">
      <div>
        <label for="customer">Vevő</label>
        <input id="customer" name="customer" type="text" value="<?php echo h($customer); ?>">
        <?php if (isset($errors['customer'])): ?><div class="field-error"><?php echo h($errors['customer']); ?></div><?php endif; ?>
      </div>
      <div>
        <label for="p1qty">Mennyiség: <?php echo h((string)($products[0]['name'] ?? 'Termék 1')); ?></label>
        <input id="p1qty" name="p1qty" type="number" min="0" max="99" value="<?php echo h($p1qtyRaw); ?>">
        <?php if (isset($errors['p1qty'])): ?><div class="field-error"><?php echo h($errors['p1qty']); ?></div><?php endif; ?>
      </div>
      <div>
        <label for="p2qty">Mennyiség: <?php echo h((string)($products[1]['name'] ?? 'Termék 2')); ?></label>
        <input id="p2qty" name="p2qty" type="number" min="0" max="99" value="<?php echo h($p2qtyRaw); ?>">
        <?php if (isset($errors['p2qty'])): ?><div class="field-error"><?php echo h($errors['p2qty']); ?></div><?php endif; ?>
      </div>
      <div><button type="submit">Rendelés létrehozása</button></div>
    </form>
  </div>

  <div class="card">
    <h2>Rendelések összesítő (JOIN + SUM)</h2>
    <?php if (!$orders): ?><p class="small">Még nincs rendelés.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Vevő</th>
            <th>Idő</th>
            <th>Darab</th>
            <th>Összeg</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><?php echo (int)$o['id']; ?></td>
              <td><?php echo h((string)$o['customer']); ?></td>
              <td class="small"><?php echo h((string)$o['created_at']); ?></td>
              <td><?php echo (int)$o['total_qty']; ?></td>
              <td><strong><?php echo h(number_format((int)$o['total_ft'], 0, ',', ' ')); ?> Ft</strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <footer class="small">Generated by PHP</footer>
</body>

</html>