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
$step = (string)($_GET['step'] ?? '1');
if (!in_array($step, ['1', '2', 'done'], true)) $step = '1';

$errors = [];

// Step 1 fields
$full = (string)($_POST['full'] ?? ($_SESSION['task04']['full'] ?? ''));
$neptun = (string)($_POST['neptun'] ?? ($_SESSION['task04']['neptun'] ?? ''));

// Step 2 fields
$city = (string)($_POST['city'] ?? ($_SESSION['task04']['city'] ?? ''));
$zip = (string)($_POST['zip'] ?? ($_SESSION['task04']['zip'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($step === '1') {
    $full = trim($full);
    $neptun = strtoupper(trim($neptun));

    if ($full === '') $errors['full'] = 'Kérem, adja meg a teljes nevet.';
    if ($neptun === '') $errors['neptun'] = 'Kérem, adja meg a Neptun-kódot.';
    elseif (!preg_match('/^[A-Z0-9]{6}$/', $neptun)) $errors['neptun'] = 'A Neptun-kód 6 karakter (A–Z, 0–9).';

    if (count($errors) === 0) {
      $_SESSION['task04']['full'] = $full;
      $_SESSION['task04']['neptun'] = $neptun;
      header('Location: ?step=2');
      exit;
    }
  }

  if ($step === '2') {
    $city = trim($city);
    $zip = trim($zip);

    if ($city === '') $errors['city'] = 'Kérem, adja meg a várost.';
    $zipVal = filter_var($zip, FILTER_VALIDATE_INT);
    if ($zip === '') $errors['zip'] = 'Kérem, adja meg az irányítószámot.';
    elseif ($zipVal === false || $zipVal < 1000 || $zipVal > 9999) $errors['zip'] = 'Az irányítószám 4 számjegy legyen (1000–9999).';

    if (count($errors) === 0) {
      $_SESSION['task04']['city'] = $city;
      $_SESSION['task04']['zip'] = $zip;
      header('Location: ?step=done');
      exit;
    }
  }
}

if ($step === 'done' && isset($_GET['reset'])) {
  unset($_SESSION['task04']);
  header('Location: ?step=1');
  exit;
}

$data = $_SESSION['task04'] ?? null;
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task04 – Többlépcsős űrlap</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task04 – Többlépcsős űrlap (session tárolással)</h1>
  </header>

  <nav class="small">
    <a href="?step=1">1. lépés</a>
    <a href="?step=2">2. lépés</a>
    <a href="?step=done">Összegzés</a>
  </nav>

  <?php if ($step === '1'): ?>
    <div class="card">
      <h2>1. lépés – Személyes adatok</h2>
      <form method="post" action="?step=1" novalidate>
        <label for="full">Teljes név</label>
        <input id="full" name="full" type="text" value="<?php echo h($full); ?>">
        <?php if (isset($errors['full'])): ?><div class="field-error"><?php echo h($errors['full']); ?></div><?php endif; ?>

        <label for="neptun">Neptun-kód</label>
        <input id="neptun" name="neptun" type="text" value="<?php echo h($neptun); ?>" placeholder="pl. ABC123">
        <?php if (isset($errors['neptun'])): ?><div class="field-error"><?php echo h($errors['neptun']); ?></div><?php endif; ?>

        <div style="margin-top:12px"><button type="submit">Tovább</button></div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($step === '2'): ?>
    <div class="card">
      <h2>2. lépés – Lakcím adatok</h2>
      <?php if (!isset($_SESSION['task04']['full'])): ?>
        <div class="card err">Kérem, először töltse ki az 1. lépést.</div>
      <?php else: ?>
        <form method="post" action="?step=2" novalidate>
          <label for="city">Város</label>
          <input id="city" name="city" type="text" value="<?php echo h($city); ?>">
          <?php if (isset($errors['city'])): ?><div class="field-error"><?php echo h($errors['city']); ?></div><?php endif; ?>

          <label for="zip">Irányítószám</label>
          <input id="zip" name="zip" type="text" value="<?php echo h($zip); ?>" placeholder="pl. 1117">
          <?php if (isset($errors['zip'])): ?><div class="field-error"><?php echo h($errors['zip']); ?></div><?php endif; ?>

          <div style="margin-top:12px"><button type="submit">Összegzés</button></div>
        </form>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($step === 'done'): ?>
    <div class="card">
      <h2>Összegzés</h2>
      <?php if (!is_array($data) || !isset($data['full'], $data['neptun'], $data['city'], $data['zip'])): ?>
        <p class="small">Nincs teljes adat. Kérem, töltse ki az 1–2. lépést.</p>
      <?php else: ?>
        <table>
          <tr>
            <th>Teljes név</th>
            <td><?php echo h($data['full']); ?></td>
          </tr>
          <tr>
            <th>Neptun</th>
            <td><?php echo h($data['neptun']); ?></td>
          </tr>
          <tr>
            <th>Város</th>
            <td><?php echo h($data['city']); ?></td>
          </tr>
          <tr>
            <th>Irányítószám</th>
            <td><?php echo h((string)$data['zip']); ?></td>
          </tr>
        </table>
        <p><a href="?step=done&reset=1">Űrlap újrakezdése (session törlése)</a></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <footer class="small">Generated by PHP</footer>
</body>

</html>