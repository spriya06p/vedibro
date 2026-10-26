<?php
// Shared site header: top banner image, then the coloured menu bar.
// Used by index.php and _layout.php (About, Safety Tips, Contact...). Colours follow the active theme.
// Banner files: assets/top-banner.jpg (desktop) and assets/top-banner-m.jpg (phone). Replace them to change the banner.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/_theme.php';

function site_header(string $active = 'home', bool $showAdmin = false, bool $overlay = false): void {
    // $overlay = true (home page, computer screens): no top banner, the menu bar sits on top of the home banner
    $nav = ['home' => ['Home', 'index.php'], 'about' => ['About Us', 'about.php'], 'products' => ['Price List', 'index.php?view=price#products'],
            'safety' => ['Safety Tips', 'safetytips.php'], 'contact' => ['Contact Us', 'contact.php']];
    $hasBanner = file_exists(__DIR__ . '/assets/top-banner.jpg');
    $hasMobile = file_exists(__DIR__ . '/assets/top-banner-m.jpg');
    ?>
<header class="sh<?=$overlay ? ' sh-overlay' : ' sh-dov'?>">
  <?php if ($hasBanner): ?>
  <a class="sh-banner" href="index.php" aria-label="<?=e(SHOP_NAME)?>"><picture>
    <?php if ($hasMobile): ?><source media="(max-width:900px)" srcset="assets/top-banner-m.jpg"><?php endif; ?>
    <img src="assets/top-banner.jpg" alt="<?=e(SHOP_NAME)?>"></picture></a>
  <?php else: ?>
  <a class="sh-banner sh-fallback" href="index.php"><?=e(SHOP_NAME)?></a>
  <?php endif; ?>
  <button type="button" class="sh-btn" id="menuBtn" aria-label="Menu"><b>Menu</b></button>
  <nav class="sh-nav" id="mainNav">
    <?php foreach ($nav as $k => [$label, $href]): ?><a <?=$k === $active ? 'class="on"' : ''?> href="<?=e($href)?>"><?=e($label)?></a><?php endforeach; ?>
    <?php if ($showAdmin): ?><a href="admin/login.php" class="adm">Admin</a><?php endif; ?>
  </nav>
</header>
<?php }