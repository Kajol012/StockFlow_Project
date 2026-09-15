<?php require __DIR__.'/../../../includes/role_header.php'; ?>
<div class="page-head"><div><p class="eyebrow">Inventory</p><h1>Current Stock</h1><p>Review current product quantities and stock status. Use Stock In, Stock Out or Stock Adjustment to update inventory.</p></div></div>
<section class="panel"><div class="panel-head"><div><h2>Current Stock</h2><span class="panel-sub">Live quantity and reorder status</span></div></div>
<div class="table-wrap"><table class="table"><tr><th>Product</th><th>SKU</th><th>Category</th><th>Quantity</th><th>Reorder</th><th>Status</th></tr>
<?php foreach($products as $p): ?><tr><td><b><?=e($p['name'])?></b></td><td><?=e($p['sku'])?></td><td><?=e($p['category']??'Uncategorized')?></td><td><?=e($p['quantity'])?></td><td><?=e($p['reorder_level'])?></td><td><?=$p['quantity']==0?'<span class="badge red">Out of Stock</span>':($p['quantity']<=$p['reorder_level']?'<span class="badge red">Low Stock</span>':'<span class="badge green">Healthy</span>')?></td></tr><?php endforeach; ?>
<?php if(!$products): ?><tr><td colspan="6" class="empty">No products found.</td></tr><?php endif; ?></table></div></section>
<?php require __DIR__.'/../../../includes/role_footer.php'; ?>
