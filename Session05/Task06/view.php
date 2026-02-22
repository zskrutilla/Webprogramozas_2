<?php

declare(strict_types=1);
/** @var array $data */
/** @var array $errors */
?>
<div class="card">
  <form method="post" novalidate>
    <div class="row">
      <div>
        <label for="name">Név</label>
        <input id="name" name="name" type="text" value="<?php echo h((string)($data['name'] ?? '')); ?>">
        <?php if (isset($errors['name'])): ?><div class="field-error"><?php echo h($errors['name']); ?></div><?php endif; ?>
      </div>
      <div>
        <label for="email">E-mail</label>
        <input id="email" name="email" type="text" value="<?php echo h((string)($data['email'] ?? '')); ?>">
        <?php if (isset($errors['email'])): ?><div class="field-error"><?php echo h($errors['email']); ?></div><?php endif; ?>
      </div>
    </div>

    <label for="msg">Üzenet</label>
    <textarea id="msg" name="msg" rows="5"><?php echo h((string)($data['msg'] ?? '')); ?></textarea>
    <?php if (isset($errors['msg'])): ?><div class="field-error"><?php echo h($errors['msg']); ?></div><?php endif; ?>

    <div style="margin-top:12px"><button type="submit">Küldés</button></div>
  </form>
</div>