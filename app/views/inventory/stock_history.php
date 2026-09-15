<div class="page-head">
  <h2>Stock Transaction History</h2>
  <p class="muted">Every stock in, stock out, and adjustment, in one place.</p>
</div>

<form method="get" class="filter-bar">
  <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Search product, SKU, or reference">
  <select name="type">
    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All types</option>
    <option value="in" <?= $type === 'in' ? 'selected' : '' ?>>Stock In</option>
    <option value="out" <?= $type === 'out' ? 'selected' : '' ?>>Stock Out</option>
    <option value="adjustment" <?= $type === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
  </select>
  <input type="date" name="from" value="<?= e($dateFrom) ?>">
  <input type="date" name="to" value="<?= e($dateTo) ?>">
  <button class="btn" type="submit">Filter</button>
  <a class="btn btn-sm" href="export.php?export=history&type=<?= e($type) ?>&q=<?= e($keyword) ?>&from=<?= e($dateFrom) ?>&to=<?= e($dateTo) ?>">Export CSV</a>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead>
    <tr><th>Date</th><th>Product</th><th>Type</th><th>Qty</th><th>Reference</th><th>Reason</th><th>By</th></tr>
  </thead>
  <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="7" class="muted">No transactions found.</td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <?php
        $typeLabel = ['in' => 'Stock In', 'out' => 'Stock Out', 'adjustment' => 'Adjustment'][$r['type']] ?? $r['type'];
        $badge = $r['type'] === 'in' ? 'badge-success' : ($r['type'] === 'out' ? 'badge-warning' : 'badge-neutral');
        $qtyDisplay = $r['type'] === 'adjustment' ? (($r['quantity'] > 0 ? '+' : '') . $r['quantity']) : $r['quantity'];
      ?>
      <tr>
        <td><?= e(date('d M Y, H:i', strtotime($r['created_at']))) ?></td>
        <td><?= e($r['product_name']) ?> <small class="muted">(<?= e($r['sku']) ?>)</small></td>
        <td><span class="badge <?= $badge ?>"><?= $typeLabel ?></span></td>
        <td><?= e($qtyDisplay) ?></td>
        <td><?= e($r['reference_no'] ?? '—') ?></td>
        <td><?= e($r['reason'] ?? '—') ?></td>
        <td><?= e($r['user_name'] ?? '—') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
