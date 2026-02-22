<?php

declare(strict_types=1);
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/stats.php';

$title = 'Task10 – Moduláris számolás + táblázat (több fájl)';

$nRaw = (string)($_GET['n'] ?? '10');
$n = filter_var($nRaw, FILTER_VALIDATE_INT);
$errors = [];
if ($nRaw === '' || $n === false) $errors['n'] = 'Kérem, adjon meg érvényes egész számot.';
else {
  if ($n < 1 || $n > 50) $errors['n'] = 'Az N értéke 1 és 50 közé essen.';
}

$rows = [];
$sum = null;
if (!$errors) {
  $rows = build_table_data((int)$n); // build_table_data(): sorok tömbje
  $sum = summarize($rows);           // summarize(): összegzés
}

include __DIR__ . '/templates/header.php';
?>
<div class="card notice">
  <p>A számítások több függvényre vannak bontva a <code>stats.php</code> fájlban, a megjelenítés templátokkal történik.</p>
</div>

<div class="card">
  <form method="get" class="row" style="align-items:end">
    <div>
      <label for="n">N (1–50)</label>
      <input id="n" name="n" type="number" min="1" max="50" value="<?php echo h($nRaw); ?>">
      <?php if (isset($errors['n'])): ?><div class="field-error"><?php echo h($errors['n']); ?></div><?php endif; ?>
    </div>
    <div><button type="submit">Generálás</button></div>
  </form>
</div>

<?php if (!$errors): ?>
  <div class="card">
    <h2>Táblázat</h2>
    <table>
      <thead>
        <tr>
          <th>i</th>
          <th>i²</th>
          <th>i³</th>
          <th>√i</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['i']; ?></td>
            <td><?php echo (int)$r['i2']; ?></td>
            <td><?php echo (int)$r['i3']; ?></td>
            <td><?php echo h(number_format((float)$r['sqrt'], 4, ',', ' ')); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th>Átlag</th>
          <th><?php echo h(number_format((float)$sum['avg_i2'], 2, ',', ' ')); ?></th>
          <th><?php echo h(number_format((float)$sum['avg_i3'], 2, ',', ' ')); ?></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>