<?php
require_once '_inc.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $do = $_POST['do'] ?? '';
    if ($do === 'packing') $pdo->prepare("REPLACE INTO settings(k,v) VALUES('packing_percent',?)")->execute([max(0, min(50, (float)$_POST['packing_percent']))]);
    elseif ($do === 'state_add' && trim($_POST['name']) !== '') $pdo->prepare("INSERT IGNORE INTO states(name,min_order) VALUES(?,?)")->execute([trim($_POST['name']), max(0, (float)$_POST['min_order'])]);
    elseif ($do === 'state_save') $pdo->prepare("UPDATE states SET min_order=?,active=? WHERE id=?")->execute([max(0, (float)$_POST['min_order']), isset($_POST['active']) ? 1 : 0, (int)$_POST['id']]);
    flash('Saved.'); header('Location: settings.php'); exit;
}
$states = $pdo->query("SELECT * FROM states ORDER BY name")->fetchAll();
admin_head('Settings'); flash(); ?>
<section class="card"><h2>Packing charges</h2><form method="post"><?=csrf_field()?><input type="hidden" name="do" value="packing">
<label>Packing % (0 = no packing charge)<input type="number" step="0.01" name="packing_percent" value="<?=e(setting('packing_percent',0))?>"></label><button>Save</button></form></section>
<section class="card"><h2>States &amp; minimum order amount</h2><table><tr><th>State</th><th>Min order ₹</th><th>Enabled</th><th></th></tr>
<?php foreach ($states as $s): ?><tr><form method="post"><?=csrf_field()?><input type="hidden" name="do" value="state_save"><input type="hidden" name="id" value="<?=$s['id']?>">
<td><?=e($s['name'])?></td><td><input type="number" name="min_order" value="<?=e($s['min_order'])?>"></td><td><input type="checkbox" name="active" <?=$s['active']?'checked':''?>></td><td><button>Save</button></td></form></tr><?php endforeach; ?>
<tr><form method="post"><?=csrf_field()?><input type="hidden" name="do" value="state_add"><td><input name="name" placeholder="New state" required></td><td><input type="number" name="min_order" value="0"></td><td></td><td><button>Add</button></td></form></tr></table></section>
<?php admin_foot();
