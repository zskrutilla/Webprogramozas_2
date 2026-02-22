<?php

declare(strict_types=1);
/** @var string $title */
/** @var string $body */
/** @var array $errors */
?>
<div class="card">
  <h2>Új bejegyzés</h2>
  <?php if (isset($errors['form'])): ?><div class="card err"><strong>Hiba:</strong> <?php echo h($errors['form']); ?></div><?php endif; ?>
  <form method="post" novalidate>
    <label for="t">Cím (max 80)</label>
    <input id="t" name="title" type="text" value="<?php echo h($title); ?>">
    <label for="b">Tartalom (max 800)</label>
    <textarea id="b" name="body" rows="6"><?php echo h($body); ?></textarea>
    <div style="margin-top:12px"><button type="submit">Mentés</button></div>
  </form>
</div>