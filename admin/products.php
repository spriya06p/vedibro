<?php
require_once '_inc.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $pid = (int)($_POST['id'] ?? 0);
    if (($_POST['do'] ?? '') === 'toggle') db()->prepare("UPDATE products SET active=1-active WHERE id=?")->execute([$pid]);
    header('Location: products.php'); exit;
}
$q = trim($_GET['q'] ?? '');
$st = db()->prepare("SELECT p.*,c.name cat,c.discount_percent d FROM products p JOIN categories c ON c.id=p.category_id WHERE p.name LIKE ? OR p.code LIKE ? ORDER BY c.sort_order, CAST(p.code AS UNSIGNED), p.code");
$st->execute(["%$q%", "%$q%"]); $rows = $st->fetchAll();
admin_head('Products'); flash(); ?>
<section class="card"><div class="section-title"><form method="get"><input name="q" placeholder="Search" value="<?=e($q)?>"> <button>Search</button></form><a class="button" href="product.php">+ Add Product</a></div>
<table><tr><th>Code</th><th>Product</th><th>Category</th><th>MRP</th><th>Price</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $p): ?><tr><td><?=e($p['code'])?></td><td><?=e($p['name'])?></td><td><?=e($p['cat'])?></td><td>₹<?=money($p['actual_price'])?></td><td>₹<?=money(sell_price($p['actual_price'],$p['d']))?></td>
<td><form method="post"><?=csrf_field()?><input type="hidden" name="do" value="toggle"><input type="hidden" name="id" value="<?=$p['id']?>"><button><?=$p['active']?'Active':'Hidden'?></button></form></td>
<td><a href="product.php?id=<?=$p['id']?>">Edit</a></td></tr><?php endforeach; ?></table></section>
<?php admin_foot();
