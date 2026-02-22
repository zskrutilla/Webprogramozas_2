<?php

declare(strict_types=1);
/** @var array $items */
?>
<div class="card notice">
  <p>Front controller + router + controller + repository. A controller csak a repo felületét használja.</p>
</div>

<div class="card">
  <h2>Teendők (<?php echo (int)count($items); ?>)</h2>
  <?php if (!$items): ?><p class="small">Nincs feladat.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Idő</th>
          <th>Cím</th>
          <th>Állapot</th>
          <th>Művelet</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $t): ?>
          <tr>
            <td class="small"><?php echo h($t->createdAt); ?></td>
            <td><?php echo h($t->title); ?></td>
            <td><?php echo $t->done ? 'Kész' : 'Nyitott'; ?></td>
            <td>
              <a href="?action=toggle&id=<?php echo (int)$t->id; ?>">Átvált</a> ·
              <a href="?action=delete&id=<?php echo (int)$t->id; ?>" onclick="return confirm('Törli?')">Törlés</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>