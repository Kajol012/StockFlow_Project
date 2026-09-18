<div class="page-head">
  <h2>Current Stock Levels</h2>
  <p class="muted">Search and filter products by stock status.</p>
</div>

<form method="get" class="filter-bar">
  <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Search by name or SKU">
  <select name="status">
    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All statuses</option>
    <option value="ok" <?= $status === 'ok' ? 'selected' : '' ?>>In stock</option>
    <option value="low" <?= $status === 'low' ? 'selected' : '' ?>>Low stock</option>
    <option value="out" <?= $status === 'out' ? 'selected' : '' ?>>Out of stock</option>
  </select>
  <button class="btn" type="submit">Filter</button>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead>
    <tr><th>SKU</th><th>Product</th><th>Category</th><th>Quantity</th><th>Reorder Level</th><th>Status</th></tr>
  </thead>
  <tbody>
    <?php if (!$products): ?>
      <tr><td colspan="6" class="muted">No products found.</td></tr>
    <?php endif; ?>
    <?php foreach ($products as $p): ?>
      <?php
        if ($p['quantity'] <= 0) { $badge = 'badge-danger'; $label = 'Out of stock'; }
        elseif ($p['quantity'] <= $p['reorder_level']) { $badge = 'badge-warning'; $label = 'Low stock'; }
        else { $badge = 'badge-success'; $label = 'In stock'; }
      ?>
      <tr>
        <td><?= e($p['sku']) ?></td>
        <td><?= e($p['name']) ?></td>
        <td><?= e($p['category_name'] ?? '—') ?></td>
        <td><?= e($p['quantity']) ?></td>
        <td><?= e($p['reorder_level']) ?></td>
        <td><span class="badge <?= $badge ?>"><?= $label ?></span></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
