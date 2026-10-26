<?php
require_once '_inc.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $pdo = db();
    if (empty($_FILES['csv']['tmp_name']) || $_FILES['csv']['size'] > 2*1024*1024) $msg = 'Choose a CSV file under 2 MB.';
    else {
        $h = fopen($_FILES['csv']['tmp_name'], 'r'); $n = 0; $skip = 0; $newCats = [];
        $find = $pdo->prepare("SELECT id FROM categories WHERE name=?");
        $mk = $pdo->prepare("INSERT INTO categories(name,discount_percent,sort_order) VALUES(?,0,99)");
        $up = $pdo->prepare("INSERT INTO products(category_id,code,name,content,actual_price) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE category_id=VALUES(category_id),name=VALUES(name),content=VALUES(content),actual_price=VALUES(actual_price)");
        while (($r = fgetcsv($h)) !== false) {
            $r = array_map('trim', $r);
            if (count($r) < 5 || strtolower($r[0]) === 'category' || (float)$r[4] <= 0 || $r[1] === '' || $r[2] === '') { $skip++; continue; }
            $find->execute([$r[0]]); $cid = $find->fetchColumn();
            if (!$cid) { $mk->execute([$r[0]]); $cid = $pdo->lastInsertId(); $newCats[] = $r[0]; }
            $up->execute([$cid, $r[1], $r[2], $r[3] ?: '1 Box', (float)$r[4]]); $n++;
        }
        fclose($h);
        $msg = "Imported $n products, skipped $skip rows.".($newCats ? ' New categories (discount 0%, set it in Categories): '.implode(', ', array_unique($newCats)) : '');
    }
}
admin_head('Import products (CSV)'); ?>
<section class="card"><?php if ($msg): ?><p><b><?=e($msg)?></b></p><?php endif; ?>
<p>Columns, in this order: <code>category, code, name, content, actual_price</code>. Existing codes are updated. Prices are MRP; the category discount is applied automatically.</p>
<form method="post" enctype="multipart/form-data"><?=csrf_field()?><input type="file" name="csv" accept=".csv" required> <button>Import</button></form></section>
<?php admin_foot();
