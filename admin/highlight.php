<?php
require_once '_inc.php';
require_once __DIR__ . '/../_highlight.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $lines = array_slice(array_values(array_filter(array_map(fn($l) => mb_substr(trim($l), 0, 300), preg_split('/\R/', (string)($_POST['text'] ?? ''))), 'strlen')), 0, 10);
    $speed = in_array($_POST['speed'] ?? '', ['slow', 'normal', 'fast'], true) ? $_POST['speed'] : 'normal';
    $st = db()->prepare("REPLACE INTO settings(k,v) VALUES(?,?)");
    $st->execute(['highlight_on', isset($_POST['on']) ? '1' : '0']);
    $st->execute(['highlight_text', implode("\n", $lines)]);
    $st->execute(['highlight_speed', $speed]);
    flash('Highlight bar saved.'); header('Location: highlight.php'); exit;
}
$text = implode("\n", highlight_messages()); $on = highlight_enabled(); $speed = setting('highlight_speed', 'normal');
admin_head('Highlight Bar'); flash(); ?>
<?=highlight_css_link("../")?>
<style>.pv{overflow:hidden;border-radius:8px;margin:10px 0;--deep:#7A1743;--gold-light:#FFF3A0;--gold:#FDE405}.pv-note{color:#667;font-size:12px}</style>
<section class="card"><h2>Scrolling bar at the top of the website</h2>
<p>Offers and announcements shown at the very top of every page. Add one message per line, they scroll one after another.</p>
<form method="post"><?=csrf_field()?>
<label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="on" style="width:auto;margin:0" <?=$on ? 'checked' : ''?>> Show the bar on the website</label>
<label>Messages (one per line, max 10)
<textarea name="text" rows="5" maxlength="3000" placeholder="Diwali sale is open now! Order early for best offers.&#10;Free delivery above Rs.5000"><?=e($text)?></textarea></label>
<label>Scroll speed<select name="speed"><?php foreach (['slow' => 'Slow', 'normal' => 'Normal', 'fast' => 'Fast'] as $k => $v): ?><option value="<?=$k?>" <?=$speed === $k ? 'selected' : ''?>><?=$v?></option><?php endforeach; ?></select></label>
<button>Save</button></form></section>
<section class="card"><h2>Preview</h2>
<?php $m = highlight_messages(); ?>
<div class="pv"><?=$on && $m ? highlight_bar_html() : '<div class="salebar"><span style="padding:0 28px">(bar is hidden)</span></div>'?></div>
<p class="pv-note">Colours on the real website follow the selected theme.</p></section>
<?php admin_foot();
