<?php
require_once '_inc.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $do = $_POST['do'] ?? '';
    try {
        if ($do === 'theme') {
            $t = $_POST['theme'] ?? '';
            if (!isset(THEMES[$t])) throw new RuntimeException('Unknown theme.');
            db()->prepare("REPLACE INTO settings(k,v) VALUES('theme',?)")->execute([$t]);
            flash('Theme changed to: '.THEMES[$t]['name']);
        } elseif ($do === 'logo') {
            $old = setting('logo', '');
            $new = save_image($_FILES['logo'] ?? [], $old);          // also deletes the old uploaded logo
            if (!$new) throw new RuntimeException('Choose a logo image (JPG, PNG or WEBP).');
            db()->prepare("REPLACE INTO settings(k,v) VALUES('logo',?)")->execute([$new]);
            flash('Logo updated.');
        } elseif ($do === 'logo_reset') {
            $old = setting('logo', '');
            if ($old !== '' && is_file(__DIR__.'/../uploads/'.$old)) @unlink(__DIR__.'/../uploads/'.$old);
            db()->prepare("REPLACE INTO settings(k,v) VALUES('logo','')")->execute();
            flash('Logo reset to assets/logo.png.');
        }
        header('Location: theme.php'); exit;
    } catch (RuntimeException $ex) { $msg = $ex->getMessage(); }
}
$cur = current_theme(); $logo = site_logo_url('../');
admin_head('Theme & Logo'); flash(); ?>
<style>
.tgrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin:12px 0}
.topt{display:block;border:2px solid #d5deea;border-radius:12px;padding:14px;cursor:pointer;font-weight:400;margin:0}
.topt input{display:inline;width:auto;margin:0 8px 0 0}
.topt.on{border-color:#EC3A78;box-shadow:0 0 0 3px rgba(236,58,120,.18)}
.sw{display:flex;height:34px;border-radius:8px;overflow:hidden;margin-top:10px}.sw i{flex:1}
.rlogo{width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid #FDE405;box-shadow:0 3px 10px rgba(0,0,0,.25);background:#fff}
</style>
<section class="card"><h2>Colour theme</h2>
<p>Applies to the store, confirmation page and this admin panel. You can switch back any time.</p>
<form method="post"><?=csrf_field()?><input type="hidden" name="do" value="theme">
<div class="tgrid"><?php foreach (THEMES as $k => $t): ?>
  <label class="topt <?=$k === $cur ? 'on' : ''?>"><input type="radio" name="theme" value="<?=e($k)?>" <?=$k === $cur ? 'checked' : ''?>><b><?=e($t['name'])?></b><?=$k === $cur ? ' (current)' : ''?>
  <span class="sw"><?php foreach ($t['sw'] as $c): ?><i style="background:<?=e($c)?>"></i><?php endforeach; ?></span></label>
<?php endforeach; ?></div>
<button>Apply theme</button></form></section>

<section class="card"><h2>Shop logo</h2>
<?php if ($msg): ?><p class="error"><?=e($msg)?></p><?php endif; ?>
<p>Shown round in the website header. A square image works best (the centre is used).</p>
<?php if ($logo): ?><p><img class="rlogo" src="<?=e($logo)?>?v=<?=time()?>" alt="Logo"></p><?php else: ?><p><i>No logo yet, the shop name is shown instead.</i></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?=csrf_field()?><input type="hidden" name="do" value="logo">
<label>Upload new logo (JPG / PNG / WEBP, max 2 MB)<input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" required></label><button>Upload logo</button></form>
<?php if (setting('logo', '') !== ''): ?><form method="post" style="margin-top:10px"><?=csrf_field()?><input type="hidden" name="do" value="logo_reset"><button onclick="return confirm('Remove uploaded logo?')">Use default logo file</button></form><?php endif; ?>
</section>
<?php admin_foot();
