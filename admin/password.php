<?php
require_once '_inc.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $s = db()->prepare("SELECT password_hash FROM admin_users WHERE id=?"); $s->execute([$_SESSION['admin_id']]);
    if (!password_verify($_POST['old'] ?? '', (string)$s->fetchColumn())) $msg = 'Current password is wrong.';
    elseif (strlen($_POST['new'] ?? '') < 8 || $_POST['new'] !== ($_POST['new2'] ?? '')) $msg = 'New password must be 8+ characters and match the confirmation.';
    else { db()->prepare("UPDATE admin_users SET password_hash=? WHERE id=?")->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $_SESSION['admin_id']]); $msg = 'Password changed.'; }
}
admin_head('Change password'); ?>
<section class="card"><?php if ($msg): ?><p><b><?=e($msg)?></b></p><?php endif; ?>
<form method="post"><?=csrf_field()?><label>Current password<input type="password" name="old" required></label><label>New password<input type="password" name="new" required></label><label>Confirm new password<input type="password" name="new2" required></label><button>Change</button></form></section>
<?php require __DIR__.'/_eye.php'; admin_foot();