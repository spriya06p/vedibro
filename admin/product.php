<?php
require_once '_inc.php';
$pdo = db(); $id = (int)($_GET['id'] ?? 0);
$cats = $pdo->query("SELECT * FROM categories ORDER BY sort_order,name")->fetchAll();
$p = null;
if ($id) { $s = $pdo->prepare("SELECT * FROM products WHERE id=?"); $s->execute([$id]); $p = $s->fetch(); if (!$p) { header('Location: products.php'); exit; } }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $code = trim($_POST['code']); $name = trim($_POST['name']); $cat = (int)$_POST['category_id'];
        $content = trim($_POST['content']) ?: '1 Box'; $actual = (float)$_POST['actual_price'];
        if ($code === '' || $name === '' || $actual <= 0) throw new RuntimeException('Code, name and a MRP above 0 are required.');
        $img = save_image($_FILES['image'] ?? [], $p['image'] ?? null);
        if ($id) $pdo->prepare("UPDATE products SET category_id=?,code=?,name=?,content=?,actual_price=?,image=? WHERE id=?")->execute([$cat,$code,$name,$content,$actual,$img,$id]);
        else $pdo->prepare("INSERT INTO products(category_id,code,name,content,actual_price,image) VALUES(?,?,?,?,?,?)")->execute([$cat,$code,$name,$content,$actual,$img]);
        flash('Product saved.'); header('Location: products.php'); exit;
    } catch (PDOException $ex) { $error = $ex->errorInfo[1] == 1062 ? 'This product code already exists.' : 'Database error.'; }
    catch (RuntimeException $ex) { $error = $ex->getMessage(); }
}
admin_head(($id ? 'Edit' : 'Add').' Product'); ?>
<section class="card"><?php if ($error): ?><p class="error"><?=e($error)?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?=csrf_field()?>
<label>Category<select name="category_id"><?php foreach ($cats as $c): ?><option value="<?=$c['id']?>" <?=($p['category_id'] ?? 0)==$c['id']?'selected':''?>><?=e($c['name'])?> (<?=e(discount_label($c['discount_percent']))?>)</option><?php endforeach; ?></select></label>
<label>Code<input name="code" required value="<?=e($_POST['code'] ?? $p['code'] ?? '')?>"></label>
<label>Product name<input name="name" required value="<?=e($_POST['name'] ?? $p['name'] ?? '')?>"></label>
<label>Content (1 Box / 1 Pkt)<input name="content" value="<?=e($_POST['content'] ?? $p['content'] ?? '1 Box')?>"></label>
<label>Actual price / MRP (selling price is calculated from category discount)<input type="number" step="0.01" min="0.01" name="actual_price" required value="<?=e($_POST['actual_price'] ?? $p['actual_price'] ?? '')?>"></label>
<label>Product image (JPG/PNG/WEBP, max 2 MB)<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></label>
<button>Save Product</button> <a href="products.php">Cancel</a></form></section>
<?php admin_foot();
