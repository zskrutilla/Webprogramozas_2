<?php

declare(strict_types=1);

// session_start(): elindítja a PHP session-t, így a $_SESSION tömbben perzisztens állapotot tárolhatunk a felhasználóhoz.
session_start();

/**
 * HTML-escape helper (XSS megelőzés).
 */
function h(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Egyszerű "flash" üzenet kezelés sessionnel:
 * - flash_set(): üzenet eltárolása a következő oldalbetöltésig
 * - flash_get(): egyszeri kiolvasás (utána törlődik)
 */
function flash_set(string $key, string $message): void
{
  $_SESSION['_flash'][$key] = $message; // $_SESSION: session változók tárolása
}
function flash_get(string $key): ?string
{
  if (!isset($_SESSION['_flash'][$key])) return null;
  $msg = (string)$_SESSION['_flash'][$key];
  unset($_SESSION['_flash'][$key]); // unset(): törli a megadott kulcsot
  return $msg;
}
?>
<?php
$catalog = [
  'P001' => ['name' => 'Kenyér', 'price' => 899],
  'P002' => ['name' => 'Tej 2.8%', 'price' => 499],
  'P003' => ['name' => 'Sajt', 'price' => 1299],
  'P004' => ['name' => 'Alma', 'price' => 799],
];

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = []; // kosár: cikkszám => mennyiség

$action = (string)($_POST['action'] ?? '');
$sku = (string)($_POST['sku'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($action === 'add' && isset($catalog[$sku])) {
    $_SESSION['cart'][$sku] = ($_SESSION['cart'][$sku] ?? 0) + 1;
    flash_set('success', 'A termék a kosárba került.');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  if ($action === 'remove' && isset($_SESSION['cart'][$sku])) {
    unset($_SESSION['cart'][$sku]);
    flash_set('success', 'A termék törölve lett a kosárból.');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  if ($action === 'update') {
    // mennyiségek frissítése: sku => qty (POST tömb)
    $qtys = $_POST['qty'] ?? [];
    if (is_array($qtys)) {
      foreach ($qtys as $k => $qRaw) {
        $q = filter_var($qRaw, FILTER_VALIDATE_INT);
        if ($q === false || $q < 0) continue;
        if ($q === 0) unset($_SESSION['cart'][$k]);
        else $_SESSION['cart'][$k] = $q;
      }
    }
    flash_set('success', 'A kosár frissítve lett.');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  if ($action === 'clear') {
    $_SESSION['cart'] = [];
    flash_set('success', 'A kosár kiürítve lett.');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }
}

$success = flash_get('success');
$cart = $_SESSION['cart'];

function money(int $v): string
{
  return number_format($v, 0, ',', ' ') . ' Ft';
}

$total = 0;
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task09 – Session kosár</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task09 – Kosár kezelése sessionben</h1>
  </header>

  <?php if ($success): ?><div class="card ok"><?php echo h($success); ?></div><?php endif; ?>

  <div class="card">
    <h2>Katalógus</h2>
    <table>
      <thead>
        <tr>
          <th>Cikkszám</th>
          <th>Termék</th>
          <th>Ár</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($catalog as $k => $p): ?>
          <tr>
            <td><?php echo h($k); ?></td>
            <td><?php echo h($p['name']); ?></td>
            <td><?php echo h(money((int)$p['price'])); ?></td>
            <td>
              <form method="post" style="margin:0">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="sku" value="<?php echo h($k); ?>">
                <button type="submit">Kosárba</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Kosár</h2>

    <?php if (count($cart) === 0): ?>
      <p class="small">A kosár üres.</p>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="action" value="update">
        <table>
          <thead>
            <tr>
              <th>Termék</th>
              <th>Egységár</th>
              <th>Mennyiség</th>
              <th>Részösszeg</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $k => $qty): ?>
              <?php if (!isset($catalog[$k])) continue; ?>
              <?php
              $p = $catalog[$k];
              $sub = (int)$p['price'] * (int)$qty;
              $total += $sub;
              ?>
              <tr>
                <td><?php echo h($p['name']); ?></td>
                <td><?php echo h(money((int)$p['price'])); ?></td>
                <td>
                  <input name="qty[<?php echo h($k); ?>]" type="number" min="0" value="<?php echo (int)$qty; ?>" style="width:110px">
                </td>
                <td><?php echo h(money($sub)); ?></td>
                <td>
                  <form method="post" style="margin:0">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="sku" value="<?php echo h($k); ?>">
                    <button type="submit">Törlés</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3">Összesen</th>
              <th colspan="2"><?php echo h(money($total)); ?></th>
            </tr>
          </tfoot>
        </table>
        <button type="submit">Mennyiségek frissítése</button>
      </form>

      <form method="post" style="margin-top:10px">
        <input type="hidden" name="action" value="clear">
        <button type="submit">Kosár ürítése</button>
      </form>
    <?php endif; ?>
  </div>

  <footer class="small">Generated by PHP</footer>
</body>

</html>