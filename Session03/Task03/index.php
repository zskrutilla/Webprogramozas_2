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
$fullname = (string)($_POST['fullname'] ?? '');
$level = (string)($_POST['level'] ?? ''); // radio
$topics = $_POST['topics'] ?? [];         // checkbox array
$timezone = (string)($_POST['timezone'] ?? ''); // select

$errors = [];
$result = null;

$allowedLevels = ['bsc' => 'BSc', 'msc' => 'MSc'];
$allowedTopics = ['php' => 'PHP', 'html' => 'HTML', 'css' => 'CSS', 'js' => 'JavaScript'];
$allowedTZ = ['Europe/Budapest', 'Europe/London', 'America/New_York'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($fullname);

  if ($fullname === '') $errors['fullname'] = 'Kérem, adja meg a teljes nevét.';
  if (!isset($allowedLevels[$level])) $errors['level'] = 'Kérem, válasszon képzési szintet.';

  if (!is_array($topics)) $topics = [];
  // array_intersect(): csak a megengedett opciók maradjanak
  $topics = array_values(array_intersect($topics, array_keys($allowedTopics)));
  if (count($topics) === 0) $errors['topics'] = 'Kérem, jelöljön be legalább 1 témát.';

  if (!in_array($timezone, $allowedTZ, true)) $errors['timezone'] = 'Kérem, válasszon időzónát.';

  if (count($errors) === 0) {
    $result = [
      'fullname' => $fullname,
      'level' => $allowedLevels[$level],
      'topics' => array_map(fn($k) => $allowedTopics[$k], $topics),
      'timezone' => $timezone,
    ];
  }
}
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task03 – Űrlapelemek</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task03 – Radio / Checkbox / Select validációval</h1>
  </header>

  <div class="card">
    <form method="post" novalidate>
      <label for="fullname">Teljes név</label>
      <input id="fullname" name="fullname" type="text" value="<?php echo h($fullname); ?>">
      <?php if (isset($errors['fullname'])): ?><div class="field-error"><?php echo h($errors['fullname']); ?></div><?php endif; ?>

      <div style="margin-top:10px">
        <strong>Képzési szint</strong><br>
        <?php foreach ($allowedLevels as $k => $label): ?>
          <label style="display:inline-flex;align-items:center;gap:8px;margin-right:14px">
            <input type="radio" name="level" value="<?php echo h($k); ?>" <?php echo ($level === $k) ? 'checked' : ''; ?>>
            <?php echo h($label); ?>
          </label>
        <?php endforeach; ?>
        <?php if (isset($errors['level'])): ?><div class="field-error"><?php echo h($errors['level']); ?></div><?php endif; ?>
      </div>

      <div style="margin-top:10px">
        <strong>Érdeklődési témák (legalább 1)</strong><br>
        <?php foreach ($allowedTopics as $k => $label): ?>
          <label style="display:inline-flex;align-items:center;gap:8px;margin-right:14px">
            <input type="checkbox" name="topics[]" value="<?php echo h($k); ?>" <?php echo in_array($k, (array)$topics, true) ? 'checked' : ''; ?>>
            <?php echo h($label); ?>
          </label>
        <?php endforeach; ?>
        <?php if (isset($errors['topics'])): ?><div class="field-error"><?php echo h($errors['topics']); ?></div><?php endif; ?>
      </div>

      <label for="timezone" style="margin-top:10px">Időzóna</label>
      <select id="timezone" name="timezone">
        <option value="">— Válasszon —</option>
        <?php foreach ($allowedTZ as $tz): ?>
          <option value="<?php echo h($tz); ?>" <?php echo ($timezone === $tz) ? 'selected' : ''; ?>>
            <?php echo h($tz); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['timezone'])): ?><div class="field-error"><?php echo h($errors['timezone']); ?></div><?php endif; ?>

      <div style="margin-top:12px"><button type="submit">Beküldés</button></div>
    </form>
  </div>

  <?php if ($result !== null): ?>
    <div class="card ok">
      <h2>Összefoglaló</h2>
      <ul>
        <li>Név: <?php echo h($result['fullname']); ?></li>
        <li>Szint: <?php echo h($result['level']); ?></li>
        <li>Témák: <?php echo h(implode(', ', $result['topics'])); ?></li>
        <li>Időzóna: <?php echo h($result['timezone']); ?></li>
      </ul>
    </div>
  <?php endif; ?>

  <footer class="small">Generated by PHP</footer>
</body>

</html>