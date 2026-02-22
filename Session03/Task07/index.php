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
$users = [
  'admin' => ['role' => 'admin', 'pass_sha256' => '3b612c75a7b5048a435fb6ec81e52ff92d6d795a8b5a9c17070f6a63c97a53b2'],
  'user'  => ['role' => 'user',  'pass_sha256' => 'bd5cf8347e036cabe6cd37323186a02ef6c3589d19daaee31eeb2ae3b1507ebe'],
];

$page = (string)($_GET['page'] ?? 'login');
if (!in_array($page, ['login', 'logout', 'dashboard', 'admin'], true)) $page = 'login';

function current_user(): ?array
{
  return $_SESSION['auth'] ?? null;
}
function require_login(): void
{
  if (!isset($_SESSION['auth'])) {
    flash_set('error', 'Kérem, jelentkezzen be a folytatáshoz.');
    header('Location: ?page=login');
    exit;
  }
}
function require_role(string $role): void
{
  require_login();
  if (($_SESSION['auth']['role'] ?? '') !== $role) {
    http_response_code(403);
    echo '<!doctype html><meta charset="utf-8"><link rel="stylesheet" href="style.css">';
    echo '<div class="card err"><strong>403 – Hozzáférés megtagadva.</strong> Ez az oldal csak ' . h($role) . ' szerepkörrel érhető el.</div>';
    echo '<div class="card"><a href="?page=dashboard">Vissza a dashboardra</a></div>';
    exit;
  }
}

if ($page === 'logout') {
  session_unset();
  session_destroy();
  setcookie(session_name(), '', time() - 3600, '/');
  flash_set('success', 'Sikeres kijelentkezés.');
  header('Location: ?page=login');
  exit;
}

$username = (string)($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
  $username = trim($username);
  if ($username === '' || $password === '') {
    $errors['form'] = 'Kérem, adja meg a felhasználónevet és a jelszót.';
  } elseif (!isset($users[$username])) {
    $errors['form'] = 'Hibás felhasználónév vagy jelszó.';
  } else {
    $pwHash = hash('sha256', $password);
    if (!hash_equals($users[$username]['pass_sha256'], $pwHash)) {
      $errors['form'] = 'Hibás felhasználónév vagy jelszó.';
    } else {
      session_regenerate_id(true);
      $_SESSION['auth'] = [
        'username' => $username,
        'role' => $users[$username]['role'],
        'login_at' => date('Y-m-d H:i:s'),
      ];
      flash_set('success', 'Sikeres bejelentkezés.');
      header('Location: ?page=dashboard');
      exit;
    }
  }
}

$success = flash_get('success');
$errorMsg = flash_get('error');
$auth = current_user();
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task07 – Szerepkörök</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task07 – Szerepkör alapú hozzáférés (admin/user)</h1>
  </header>

  <nav class="small">
    <a href="?page=login">Login</a>
    <a href="?page=dashboard">Dashboard</a>
    <a href="?page=admin">Admin</a>
    <a href="?page=logout">Logout</a>
  </nav>

  <?php if ($success): ?><div class="card ok"><?php echo h($success); ?></div><?php endif; ?>
  <?php if ($errorMsg): ?><div class="card err"><?php echo h($errorMsg); ?></div><?php endif; ?>

  <?php if ($page === 'login'): ?>
    <div class="card">
      <h2>Bejelentkezés</h2>
      <p class="small">Demó fiókok: <code>admin / Admin123</code> és <code>user / User1234</code></p>
      <?php if (isset($errors['form'])): ?><div class="card err"><?php echo h($errors['form']); ?></div><?php endif; ?>
      <form method="post" action="?page=login" novalidate>
        <label for="username">Felhasználónév</label>
        <input id="username" name="username" type="text" value="<?php echo h($username); ?>">
        <label for="password">Jelszó</label>
        <input id="password" name="password" type="password" value="">
        <div style="margin-top:12px"><button type="submit">Belépés</button></div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($page === 'dashboard'): ?>
    <?php require_login(); ?>
    <div class="card ok">
      <h2>Dashboard</h2>
      <p>Bejelentkezett felhasználó: <strong><?php echo h((string)$auth['username']); ?></strong>
        <span class="badge notice"><?php echo h((string)$auth['role']); ?></span>
      </p>
      <p class="small">Belépés ideje: <?php echo h((string)$auth['login_at']); ?></p>
      <p>Az admin felület csak <code>admin</code> szerepkörrel érhető el.</p>
    </div>
  <?php endif; ?>

  <?php if ($page === 'admin'): ?>
    <?php require_role('admin'); ?>
    <div class="card ok">
      <h2>Admin felület</h2>
      <p>Ez a rész csak admin számára elérhető.</p>
      <ul>
        <li>Felhasználók listázása (demó)</li>
        <li>Rendszerbeállítások (demó)</li>
      </ul>
    </div>
  <?php endif; ?>

  <footer class="small">Generated by PHP</footer>
</body>

</html>