<?php

declare(strict_types=1);
/** @var string $q */
/** @var array $items */
?>
<div class="card notice">
  <p>Front controller: minden kérés a <code>index.php</code>-n fut át, a router action alapján irányít.</p>
  <?php if (isset($_GET['ok'])): ?><p class="badge ok">Sikeres mentés.</p><?php endif; ?>
</div>

<div class="card">
  <form method="get" class="row" style="align-items:end">
    <input type="hidden" name="action" value="list">
    <div>
      <label for="q">Keresés</label>
      <input id="q" name="q" type="text" value="<?php echo h($q); ?>" placeholder="cím vagy tartalom">
    </div>
    <div><button type="submit">Szűrés</button></div>
  </form>
</div>

<div class="card">
  <h2>Bejegyzések (<?php echo (int)count($items); ?>)</h2>
  <?php if (!$items): ?><p class="small">Nincs bejegyzés.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Idő</th>
          <th>Cím</th>
          <th>Tartalom</th>
          <th>Művelet</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $p): ?>
          <tr>
            <td class="small"><?php echo h($p->createdAt()); ?></td>
            <td><?php echo h($p->title()); ?></td>
            <td><?php echo h($p->body()); ?></td>
            <td><a href="?action=delete&id=<?php echo h(urlencode($p->id())); ?>" onclick="return confirm('Törli?')">Törlés</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>