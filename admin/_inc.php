<?php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../_theme.php';
require_admin();
function admin_head(string $title): void { ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> - Admin</title>
<link rel="stylesheet" href="../assets/admin.css"><link rel="stylesheet" href="../assets/extra.css"><?=theme_admin_link()?></head><body>
<header class="adminbar"><b><?=e(strtoupper(SHOP_NAME))?></b>
<button type="button" class="admbtn" id="admBtn" aria-expanded="false" aria-controls="admNav">&#9776; Menu</button>
<nav class="admnav" id="admNav">
<?php $cur = basename($_SERVER['SCRIPT_NAME'] ?? ''); $cur = ['order.php' => 'index.php', 'product.php' => 'products.php'][$cur] ?? $cur;
foreach (['index.php' => 'Orders', 'products.php' => 'Products', 'categories.php' => 'Categories', 'settings.php' => 'Settings', 'import.php' => 'Import', 'highlight.php' => 'Highlight Bar', 'theme.php' => 'Theme', 'password.php' => 'Password'] as $href => $label): ?>
<a <?=$cur === $href ? 'class="on" ' : ''?>href="<?=$href?>"><?=e($label)?></a>
<?php endforeach; ?>
<a href="../index.php" target="_blank">Store</a><a href="logout.php">Logout</a>
</nav></header>
<main class="admin"><h1><?=e($title)?></h1>
<?php }
function admin_foot(): void { ?>
<script>
(function () {
  var btn = document.getElementById('admBtn'), nav = document.getElementById('admNav');
  function setOpen(o) { nav.classList.toggle('open', o); btn.setAttribute('aria-expanded', o ? 'true' : 'false'); }
  btn.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!nav.classList.contains('open')); });
  document.addEventListener('click', function (e) { if (!nav.contains(e.target)) setOpen(false); });
  // label every table cell so tables can be shown as cards on phones (no sideways scrolling)
  document.querySelectorAll('main.admin table').forEach(function (t) {
    if (t.closest('.pdf-area')) return;
    var heads = [].map.call(t.querySelectorAll('thead th'), function (h) { return h.textContent.trim(); });
    if (!heads.length) return;
    t.classList.add('rt');
    t.querySelectorAll('tbody tr').forEach(function (tr) {
      [].forEach.call(tr.children, function (td, i) { if (td.colSpan === 1 && heads[i]) td.setAttribute('data-label', heads[i]); });
    });
  });
})();
</script>
</main></body></html>
<?php }
function flash(string $m = null) { if ($m !== null) { $_SESSION['flash'] = $m; return; } $f = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); if ($f) echo '<p class="card" style="padding:10px">'.e($f).'</p>'; }
function save_image(array $f, ?string $old): ?string {
    if (empty($f['name']) || $f['error'] === UPLOAD_ERR_NO_FILE) return $old;
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2*1024*1024) throw new RuntimeException('Image must be under 2 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
    if (!$ext || !@getimagesize($f['tmp_name'])) throw new RuntimeException('Only JPG, PNG or WEBP images are allowed.');
    $name = bin2hex(random_bytes(8)).'.'.$ext;
    if (!move_uploaded_file($f['tmp_name'], __DIR__.'/../uploads/'.$name)) throw new RuntimeException('Upload failed. Check uploads/ folder permission.');
    if ($old && is_file(__DIR__.'/../uploads/'.$old)) @unlink(__DIR__.'/../uploads/'.$old);
    return $name;
}