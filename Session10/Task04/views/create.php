<?php

declare(strict_types=1);
/** @var string $titleRaw */
/** @var array $errors */
?>
<div class="card">
  <h2>Új teendő</h2>
  <form method="post" novalidate>
    <label for="title">Cím</label>
    <input id="title" name="title" type="text" value="<?php echo h($titleRaw); ?>">
    <?php if (isset($errors['title'])): ?><div class="field-error"><?php echo h($errors['title']); ?></div><?php endif; ?>
    <div style="margin-top:12px"><button type="submit">Mentés</button></div>
  </form>
</div>