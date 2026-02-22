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
$username = (string)($_POST['username'] ?? '');
$email = (string)($_POST['email'] ?? '');
$pass = (string)($_POST['password'] ?? '');
$pass2 = (string)($_POST['password2'] ?? '');

$errors = [];
$created = false;

function password_strength_errors(string $p): array
{
  $e = [];
  if (mb_strlen($p, 'UTF-8') < 8) $e[] = 'A jelszó legalább 8 karakter hosszú legyen.';
  if (!preg_match('/[A-Z]/', $p)) $e[] = 'A jelszó tartalmazzon legalább 1 nagybetűt.';
  if (!preg_match('/[a-z]/', $p)) $e[] = 'A jelszó tartalmazzon legalább 1 kisbetűt.';
  if (!preg_match('/[0-9]/', $p)) $e[] = 'A jelszó tartalmazzon legalább 1 számjegyet.';
  return $e;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($username);
  $email = trim($email);

  if ($username === '') $errors['username'] = 'Kérem, adjon meg felhasználónevet.';
  elseif (mb_strlen($username, 'UTF-8') < 3) $errors['username'] = 'A felhasználónév legalább 3 karakter legyen.';

  if ($email === '') $errors['email'] = 'Kérem, adjon meg e-mail címet.';
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Az e-mail formátuma nem megfelelő.';

  if ($pass === '') $errors['password'] = 'Kérem, adjon meg jelszót.';
  else {
    $pe = password_strength_errors($pass);
    if (count($pe) > 0) $errors['password'] = implode(' ', $pe);
  }

  if ($pass2 === '') $errors['password2'] = 'Kérem, ismételje meg a jelszót.';
  elseif ($pass !== $pass2) $errors['password2'] = 'A két jelszó nem egyezik.';

  if (count($errors) === 0) {
    // password_hash(): jelszó biztonságos hash-elése (valós rendszerben kötelező)
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $_SESSION['task02_last_registration'] = [
      'username' => $username,
      'email' => $email,
      'password_hash' => $hash,
      'created_at' => date('Y-m-d H:i:s'),
    ];
    $created = true;
    flash_set('success', 'A regisztráció sikeresen rögzítésre került a sessionben (demó célból).');
    // Redirect-After-POST: frissítéskor ne küldje újra a POST-ot
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }
}

$success = flash_get('success');
$last = $_SESSION['task02_last_registration'] ?? null;
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Session03 / Task02 – Regisztráció validációval</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Task02 – Regisztráció validációval (session mentés – demó)</h1>
  </header>

  <?php if ($success): ?>
    <div class="card ok"><?php echo h($success); ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="post" novalidate>
      <div class="row">
        <div>
          <label for="username">Felhasználónév</label>
          <input id="username" name="username" type="text" value="<?php echo h($username); ?>">
          <?php if (isset($errors['username'])): ?><div class="field-error"><?php echo h($errors['username']); ?></div><?php endif; ?>
        </div>
        <div>
          <label for="email">E-mail</label>
          <input id="email" name="email" type="text" value="<?php echo h($email); ?>">
          <?php if (isset($errors['email'])): ?><div class="field-error"><?php echo h($errors['email']); ?></div><?php endif; ?>
        </div>
      </div>

      <div class="row">
        <div>
          <label for="password">Jelszó</label>
          <input id="password" name="password" type="password" value="">
          <?php if (isset($errors['password'])): ?><div class="field-error"><?php echo h($errors['password']); ?></div><?php endif; ?>
        </div>
        <div>
          <label for="password2">Jelszó ismét</label>
          <input id="password2" name="password2" type="password" value="">
          <?php if (isset($errors['password2'])): ?><div class="field-error"><?php echo h($errors['password2']); ?></div><?php endif; ?>
        </div>
      </div>

      <div style="margin-top:12px">
        <button type="submit">Regisztráció</button>
      </div>
    </form>
  </div>

  <div class="card notice">
    <p class="small">Megjegyzés: a feladat oktatási célú. Valós rendszerben a regisztrációt adatbázisban tárolnák.</p>
  </div>

  <?php if (is_array($last)): ?>
    <div class="card">
      <h2>Utolsó rögzített regisztráció (sessionből)</h2>
      <table>
        <tr>
          <th>Felhasználónév</th>
          <td><?php echo h($last['username']); ?></td>
        </tr>
        <tr>
          <th>E-mail</th>
          <td><?php echo h($last['email']); ?></td>
        </tr>
        <tr>
          <th>Létrehozva</th>
          <td><?php echo h($last['created_at']); ?></td>
        </tr>
        <tr>
          <th>Jelszó hash</th>
          <td class="small"><code><?php echo h($last['password_hash']); ?></code></td>
        </tr>
      </table>
    </div>
  <?php endif; ?>

  <footer class="small">Generated by PHP</footer>
</body>

</html>