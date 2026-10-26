<?php
require_once '_inc.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $do = $_POST['do'] ?? ''; $cid = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? ''); $disc = max(0, min(95, (float)($_POST['discount_percent'] ?? 0))); $sort = (int)($_POST['sort_order'] ?? 0);
    if ($do === 'add' && $name !== '') $pdo->prepare("INSERT INTO categories(name,discount_percent,sort_order) VALUES(?,?,?)")->execute([$name,$disc,$sort]);
    elseif ($do === 'save' && $name !== '') $pdo->prepare("UPDATE categories SET name=?,discount_percent=?,sort_order=? WHERE id=?")->execute([$name,$disc,$sort,$cid]);
    elseif ($do === 'delete') {
        $n = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id=?"); $n->execute([$cid]);
        if ($n->fetchColumn()) flash('Category has products. Move or delete them first.');
        else $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$cid]);
    }
    header('Location: categories.php'); exit;
}
$cats = $pdo->query("SELECT * FROM categories ORDER BY sort_order,name")->fetchAll();
admin_head('Categories'); flash(); ?>
<style>
/* Categories: full name visible on desktop and phone */
.cats td:first-child{width:42%}
.cats input[name=name]{min-width:220px}
@media(max-width:760px){
  .cats,.cats tbody,.cats tr,.cats td{display:block;width:100%}
  .cats thead{display:none}
  .cats tr{display:grid!important;grid-template-columns:1fr 1fr;gap:10px 12px;margin:0 0 14px;padding:12px;background:#fff;border:1px solid #D6E4EE;border-left:5px solid var(--sky);border-radius:10px;box-shadow:0 3px 10px rgba(20,34,94,.08)}
  .cats td{display:block!important;padding:0!important;text-align:left!important}
  .cats td:first-child,.cats td:last-child{grid-column:1/-1;width:100%}
  .cats td[data-label]::before{content:attr(data-label);display:block;font-weight:700;color:var(--sky-dark);margin-bottom:4px}
  .cats td input{display:block;width:100%!important;max-width:none!important;margin:0!important;font-size:16px}
  .cats td:last-child{display:flex!important;gap:10px}
  .cats td:last-child button{flex:1;padding:12px 8px}
}
</style>
<section class="card"><p>Discount % is applied to every product MRP in that category (50 = "50% Discount", 0 = net rate).</p>
<table class="rt cats"><thead><tr><th>Name</th><th>Discount %</th><th>Order</th><th></th></tr></thead><tbody>
<?php foreach ($cats as $c): ?><tr><form method="post"><?=csrf_field()?><input type="hidden" name="id" value="<?=$c['id']?>">
<td data-label="Name"><input name="name" value="<?=e($c['name'])?>"></td><td data-label="Discount %"><input name="discount_percent" type="number" step="0.01" value="<?=e($c['discount_percent'])?>"></td><td data-label="Order"><input name="sort_order" type="number" value="<?=e($c['sort_order'])?>"></td>
<td><button name="do" value="save">Save</button> <button name="do" value="delete" onclick="return confirm('Delete category?')">Delete</button></td></form></tr><?php endforeach; ?>
<tr><form method="post"><?=csrf_field()?><td data-label="New category"><input name="name" placeholder="New category" required></td><td data-label="Discount %"><input name="discount_percent" type="number" step="0.01" value="0"></td><td data-label="Order"><input name="sort_order" type="number" value="0"></td><td><button name="do" value="add">Add</button></td></form></tr></tbody></table></section>
<?php admin_foot();