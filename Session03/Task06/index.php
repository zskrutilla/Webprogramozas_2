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
// Demó felhasználók (valós rendszerben adatbázis + password_hash/password_verify).
$users = [
  'admin' => ['role' => 'admin', 'pass_sha256' => '3b612c75a7b5048a435fb6ec81e52ff92d6d795a8b5a9c17070f6a63c97a53b2'],
  'user'  => ['role' => 'user',  'pass_sha256' => 'bd5cf8347e036cabe6cd37323186a02ef6c3589d19daaee31eeb2ae3b1507ebe'],
];

$action = (string)($_GET['action'] ?? 'login');
if (!in_array($action, ['login', 'logout', 'secret'], true)) $action = 'login';

if ($action === 'logout') {
  // session_unset(): session változók törlése, session_destroy(): session megszüntetése
  session_unset();
  session_destroy();
  // A böngészőben lévő session cookie-t is érdemes törölni (demó)
  setcookie(session_name(), '', time() - 3600, '/');
  flash_set('success', 'Sikeres kijelentkezés.');
  header('Location: ?action=login');
  exit;
}

$errors = [];
$username = (string)($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
  $username = trim($username);

  if ($username === '' || $password === '') {
    $errors['form'] = 'Kérem, adja meg a felhasználónevet és a jelszót.';
  } elseif (!isset($users[$username])) {
    $errors['form'] = 'Hibás felhasználónév vagy jelszó.';
  } else {
    // hash('sha256', ...): egyszerű demó hash; valós rendszerben password_hash/password_verify szükséges
    $pwHash = hash('sha256', $password);
    if (!hash_equals($users[$username]['pass_sha256'], $pwHash)) {
      // hash_equals(): időzítéses támadás ellen biztonságos összehasonlítás
      $errors['form'] = 'Hibás felhasználónév vagy jelszó.';
    } else {
      // session_regenerate_id(true): belépéskor új session azonosító (session fixáció ellen)
      session_regenerate_id(true);

      $_SESSION['auth'] = [
        'username' => $username,
        'role' => $users[$username]['role'],
        'login_at' => date('Y-m-d H:i:s'),
      ];
      flash_set('success', 'Sikeres bejelentkezés.');
      header('Location: ?action=secret');
      exit;
    }
  }
}

$success = flash_get('success');
$auth = $_SESSION['auth'] ?? null;

function is_logged_in(): bool
{
  return isset($_SESSION['auth']['username']);
}
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task06 – Login (session)</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task06 – Bejelentkezés / kijelentkezés (session)</h1>
  </header>

  <nav class="small">
    <a href="?action=login">Login</a>
    <a href="?action=secret">Védett oldal</a>
    <a href="?action=logout">Logout</a>
  </nav>

  <?php if ($success): ?><div class="card ok"><?php echo h($success); ?></div><?php endif; ?>

  <?php if ($action === 'login'): ?>
    <div class="card">
      <h2>Bejelentkezés</h2>
      <p class="small">Demó fiókok: <code>admin / Admin123</code> és <code>user / User1234</code></p>

      <?php if (isset($errors['form'])): ?><div class="card err"><?php echo h($errors['form']); ?></div><?php endif; ?>

      <form method="post" action="?action=login" novalidate>
        <label for="username">Felhasználónév</label>
        <input id="username" name="username" type="text" value="<?php echo h($username); ?>">

        <label for="password">Jelszó</label>
        <input id="password" name="password" type="password" value="">

        <div style="margin-top:12px"><button type="submit">Belépés</button></div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($action === 'secret'): ?>
    <?php if (!is_logged_in()): ?>
      <div class="card err">
        <p><strong>Hozzáférés megtagadva.</strong> Kérem, jelentkezzen be.</p>
        <p><a href="?action=login">Ugrás a login oldalra</a></p>
      </div>
    <?php else: ?>
      <div class="card ok">
        <h2>Védett oldal</h2>
        <p>Üdvözöljük, <strong><?php echo h((string)$auth['username']); ?></strong>!</p>
        <p class="small">Belépés ideje: <?php echo h((string)$auth['login_at']); ?></p>
        <p><a href="?action=logout">Kijelentkezés</a></p>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <footer class="small">Generated by PHP</footer>
</body>

</html>