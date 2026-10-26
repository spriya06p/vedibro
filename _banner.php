<?php
// Shared banner for About Us / Safety Tips / Contact / Price List pages. Swipe carousel with dots (see assets/swipe.js).
// COMPUTER: assets/<page>-banner*.png|jpg|webp  (e.g. assets/about-banner.png). If none, the home banners are used.
// PHONE:    assets/mobile/<page>/*.jpg|png|webp (e.g. assets/mobile/about/1.jpg). If this folder is empty/missing, no banner on phone.
require_once __DIR__ . '/config.php';

function site_banner(string $page = ''): void {
    $ext = '{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}';
    $banners = $page ? (glob(__DIR__ . '/assets/' . $page . '-banner*.' . $ext, GLOB_BRACE) ?: []) : [];
    if (!$banners) $banners = glob(__DIR__ . '/assets/banner*.' . $ext, GLOB_BRACE) ?: [];
    $mobFiles = $page ? (glob(__DIR__ . '/assets/mobile/' . $page . '/*.' . $ext, GLOB_BRACE) ?: []) : [];
    usort($mobFiles, 'strnatcasecmp');
    $n = max(count($banners), count($mobFiles)); ?>
<section class="banner<?=$mobFiles ? ' has-mob' : ''?><?=(!$banners && $mobFiles) ? ' nodesk' : ''?>" id="pageBanner">
  <?php if ($n): for ($i = 0; $i < $n; $i++):
      $d = $banners[$i] ?? null; $m = $mobFiles[$i] ?? null;
      $pc = trim((!$d ? 'mobonly ' : '') . (!$m && $mobFiles ? 'nomob' : ''));
      $mUrl = $m ? 'assets/mobile/' . e($page) . '/' . e(rawurlencode(basename($m))) . '?v=' . filemtime($m) : '';
      $src = $d ? 'assets/' . e(basename($d)) : $mUrl; ?><picture<?=$pc ? ' class="' . $pc . '"' : ''?>><?php if ($m): ?><source media="(max-width:760px)" srcset="<?=$mUrl?>"><?php endif; ?><img class="slide<?=$i ? ' off' : ''?>" src="<?=$src?>" alt=""></picture><?php endfor; ?>
  <?php else: ?><div class="banner-fallback"><div><small>DIWALI SALE</small><b>BEST PRICES</b></div></div><?php endif; ?>
</section>
<?php if ($n > 1): ?><script src="assets/swipe.js?v=<?=@filemtime(__DIR__ . '/assets/swipe.js')?>"></script><?php endif;
}