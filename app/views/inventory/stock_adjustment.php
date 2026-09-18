<div class="page-head">
  <h2>Stock Adjustment</h2>
  <p class="muted">Correct stock counts for damaged, lost, or miscounted inventory.</p>
</div>

<?php if ($err): ?><div class="flash error"><?= e($err) ?></div><?php endif; ?>

<form method="post" class="card-form" data-validate-form>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <div class="field">
    <label for="product_id">Product</label>
    <select id="product_id" name="product_id" required>
      <option value="">Select product</option>
      <?php foreach ($products as $p): ?>
        <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['sku']) ?>) — current: <?= (int)$p['quantity'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label>Adjustment Direction</label>
    <label class="checkbox-row"><input type="radio" name="direction" value="decrease" checked> Decrease (damaged / lost)</label>
    <label class="checkbox-row"><input type="radio" name="direction" value="increase"> Increase (correction found extra stock)</label>
  </div>

  <div class="field">
    <label for="quantity">Quantity</label>
    <input id="quantity" type="number" name="quantity" min="1" required>
  </div>

  <div class="field">
    <label for="reason">Reason</label>
    <input id="reason" name="reason" required placeholder="e.g. Damaged during handling, stock count correction">
  </div>

  <button class="btn" type="submit">Save Adjustment</button>
</form>
