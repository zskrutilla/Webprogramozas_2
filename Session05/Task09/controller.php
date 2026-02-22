<?php

declare(strict_types=1);
require_once __DIR__ . '/model.php';

// render_products_page(): kontroller – adat lekérése + HTML összerakás
function render_products_page(): array
{
  $products = get_products();
  $total = compute_total($products);

  ob_start(); // output buffer: HTML összegyűjtése stringbe
?>
  <div class="card notice">
    <p>Egyszerű MVC demó: model (adat), controller (logika), view (layout).</p>
  </div>
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Termék</th>
          <th>Ár (Ft)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?php echo h((string)$p['name']); ?></td>
            <td><?php echo (int)$p['price']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th>Összesen</th>
          <th><?php echo (int)$total; ?></th>
        </tr>
      </tfoot>
    </table>
  </div>
<?php
  $content = (string)ob_get_clean();
  return ['title' => 'Task09 – Mini MVC felosztás', 'content' => $content];
}
