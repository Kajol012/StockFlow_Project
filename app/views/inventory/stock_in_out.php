<div class="page-head">
  <h2>Stock In / Out</h2>
  <p class="muted">Record incoming stock (e.g. from purchases) or outgoing stock (e.g. manual issue).</p>
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
    <label>Movement Type</label>
    <label class="checkbox-row"><input type="radio" name="type" value="in" checked> Stock In</label>
    <label class="checkbox-row"><input type="radio" name="type" value="out"> Stock Out</label>
  </div>

  <div class="field">
    <label for="quantity">Quantity</label>
    <input id="quantity" type="number" name="quantity" min="1" required>
  </div>

  <div class="field">
    <label for="reference_no">Reference No. (optional)</label>
    <input id="reference_no" name="reference_no" placeholder="e.g. PO-1042 or Invoice #">
  </div>

  <div class="field">
    <label for="reason">Note / Reason</label>
    <input id="reason" name="reason" placeholder="e.g. Received from supplier, issued to sales counter">
  </div>

  <button class="btn" type="submit">Save Movement</button>
</form>
