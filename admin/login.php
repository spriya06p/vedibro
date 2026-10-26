<?php
require_once '../config.php';
require_once '../_theme.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_SESSION['fails'] ?? 0) >= 5 && time() - ($_SESSION['fail_at'] ?? 0) < 300) $error = 'Too many attempts. Wait 5 minutes.';
    else {
        $s = db()->prepare("SELECT * FROM admin_users WHERE username=?"); $s->execute([trim($_POST['username'] ?? '')]); $u = $s->fetch();
        if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
            session_regenerate_id(true); $_SESSION['admin_id'] = $u['id']; $_SESSION['fails'] = 0; header('Location: index.php'); exit;
        }
        $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1; $_SESSION['fail_at'] = time(); $error = 'Invalid username or password.';
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><link rel="stylesheet" href="../assets/admin.css"><?=theme_admin_link()?></head>
<body><main class="login"><div class="card"><span class="eyebrow"><?=e(SHOP_NAME)?></span><h1>Admin Login</h1>
<?php if ($error): ?><p class="error"><?=e($error)?></p><?php endif; ?>
<form method="post"><?=csrf_field()?><label>Username<input name="username" required></label><label>Password<input type="password" name="password" required></label><button>Sign in</button></form></div></main><?php require __DIR__.'/_eye.php'; ?></body></html>