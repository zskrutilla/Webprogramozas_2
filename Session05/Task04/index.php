<?php

declare(strict_types=1);
require_once __DIR__ . '/lib.php';

$title = 'Task04 – Template header/footer (include)';
include __DIR__ . '/templates/header.php'; // include: sablon beillesztése

$topic = (string)($_GET['topic'] ?? 'PHP');
$topics = ['PHP', 'HTML', 'CSS', 'JavaScript', 'MySQL'];
?>
<div class="card notice">
  <p>A fejléc és lábléc külön fájlban van (<code>templates/header.php</code>, <code>templates/footer.php</code>).</p>
</div>

<div class="card">
  <form method="get">
    <label for="topic">Válasszon témát</label>
    <select id="topic" name="topic">
      <?php foreach ($topics as $t): ?>
        <option value="<?php echo h($t); ?>" <?php echo $topic === $t ? 'selected' : ''; ?>><?php echo h($t); ?></option>
      <?php endforeach; ?>
    </select>
    <div style="margin-top:12px"><button type="submit">OK</button></div>
  </form>
</div>

<div class="card">
  <p>Kiválasztott téma: <strong><?php echo h($topic); ?></strong></p>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>