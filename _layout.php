<?php
// Shared header/footer for the simple pages (About Us, Safety Tips).
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/_theme.php';
require_once __DIR__ . '/_highlight.php';
require_once __DIR__ . '/_footer.php';
require_once __DIR__ . '/_banner.php';
require_once __DIR__ . '/_header.php';

function site_head(string $title, string $active = ''): void {
    $saleText = defined('SALE_TEXT') ? SALE_TEXT : '';
    $phone2 = defined('SHOP_PHONE2') ? SHOP_PHONE2 : '';
    $email  = defined('SHOP_EMAIL') ? SHOP_EMAIL : '';
    $logo = site_logo_url();
    $social = array_filter(['f' => defined('SOCIAL_FB') ? SOCIAL_FB : '', 'in' => defined('SOCIAL_IG') ? SOCIAL_IG : '', '▶' => defined('SOCIAL_YT') ? SOCIAL_YT : '']);
    $nav = ['home' => ['Home', 'index.php'], 'about' => ['About Us', 'about.php'], 'products' => ['Products', 'index.php#products'],
            'safety' => ['Safety Tips', 'safetytips.php'], 'contact' => ['Contact', 'contact.php']];
    ?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> | <?=e(SHOP_NAME)?></title>
<link rel="stylesheet" href="assets/extra.css"><link rel="stylesheet" href="assets/store.css"><?=theme_store_link()?><link rel="stylesheet" href="assets/header.css?v=<?=@filemtime(__DIR__.'/assets/header.css')?>"><?=highlight_css_link()?><style>
.topwrap{display:flex;flex-direction:column}
@media(max-width:760px){
  .banner.home-ban .ba{display:none!important}
  /* phone: menu stays on top, above the banner */
  .topwrap .sh-banner{display:none!important}   /* phone: hide the shop-name block on top */
  #pageBanner:not(.has-mob){display:none!important}
  #pageBanner .ba{display:none!important}
  #pageBanner picture.nomob{display:none!important}
  #banner:not(.home-ban){display:none!important}
}
@media(min-width:761px){
  #pageBanner picture.mobonly{display:none!important}
  .banner.nodesk{display:none!important}
}
</style></head>
<body>
<?=highlight_bar_html()?>
<div class="topwrap"><?php site_header($active); ?><?php site_banner($active); ?></div>
<?php }

function site_foot(): void {
    site_footer(); ?>
<script>
document.getElementById('menuBtn').onclick = function () { document.getElementById('mainNav').classList.toggle('open'); };
</script>
</body></html>
<?php }